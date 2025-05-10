<?php

namespace App\Console\Commands\Import;

use App\Enums\PeriodUnit;
use App\Imports\Database\ImportQuery;
use App\Imports\DataObjects\LegacyCustomer;
use App\Imports\DataObjects\LegacyPrice;
use App\Imports\DataObjects\LegacyProduct;
use App\Imports\Repositories\DataSourceRepository;
use App\Models\LegacyMapping;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImportProductsCommand extends AbstractImportCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {--dry-run} {--max=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports products';

    protected string $idProperty = 'ID';

    protected function getItemsToImportQuery(): Builder
    {
        return ImportQuery::make()
            ->table('wp_posts')
            ->where('post_type', 'download');
    }

    protected function makeItemObject(object $itemRow): object
    {
        $meta = ImportQuery::make()
            ->table('wp_postmeta')
            ->where('post_id', $itemRow->ID)
            ->get();

        $isBundle = false;
        $bundleMeta = $meta->firstWhere('meta_key', '_edd_bundled_products')?->meta_value;
        if (! empty($bundleMeta)) {
            $bundleMeta = unserialize($bundleMeta);
            $isBundle = is_array($bundleMeta) && ! empty(array_filter($bundleMeta));
        }

        $legacyProduct = new LegacyProduct(
            id: $itemRow->ID,
            name: $itemRow->post_title,
            dateCreated: $itemRow->post_date_gmt,
            prices: $this->makePrices($itemRow, $meta),
            isBundle: $isBundle
        );

        $this->line(json_encode($legacyProduct->toArray()));

        return $legacyProduct;
    }

    protected function makePrices(object $itemRow, Collection $meta) : array
    {
        $prices = [];
        $variablePricesMeta = $meta->firstWhere('meta_key', 'edd_variable_prices')?->meta_value;
        $variablePricesMeta = $variablePricesMeta ? unserialize($variablePricesMeta) : [];

        if (empty($variablePricesMeta) || ! is_array($variablePricesMeta)) {
            return [$this->makeDefaultPrice($itemRow, $meta)];
        } else {
            foreach($variablePricesMeta as $variablePriceData) {
                $prices[] = $this->makePriceFromVariableData($itemRow, $meta, $variablePriceData);
            }

            return $prices;
        }
    }

    protected function makeDefaultPrice(object $itemRow, Collection $meta) : LegacyPrice
    {
        $activationLimit = $meta->firstWhere('meta_key', 'edd_sl_limit')?->meta_value;
        $licensePeriod = $meta->firstWhere('meta_key', '_edd_sl_exp_length')?->meta_value;

        return new LegacyPrice(
            newPriceId: null,
            index: null,
            name: $itemRow->post_title,
            activationLimit: ! empty($activationLimit) ? (int) $activationLimit : null,
            licensePeriod: ! empty($licensePeriod) ? (int) $licensePeriod : null,
            licensePeriodUnit: ! empty($licensePeriod) ? PeriodUnit::Year : PeriodUnit::Lifetime,
            isActive: 'publish' === $itemRow->post_status,
            currency: DataSourceRepository::getCurrentCurrency()
        );
    }

    protected function makePriceFromVariableData(object $itemRow, Collection $meta, array $variablePriceData) : LegacyPrice
    {
        $activationLimit = null;
        if (array_key_exists('license_limit', $variablePriceData)) {
            if (! empty($variablePriceData['license_limit'])) {
                $activationLimit = $variablePriceData['license_limit'];
            }
        } else {
            $activationLimit = ($meta->firstWhere('meta_key', 'edd_sl_limit')?->meta_value) ?: null;
        }

        $licensePeriod = $meta->firstWhere('meta_key', '_edd_sl_exp_length')?->meta_value;

        return new LegacyPrice(
            newPriceId: null,
            index: (int) Arr::get($variablePriceData, 'index', 0),
            name: Arr::get($variablePriceData, 'name', $itemRow->post_title),
            activationLimit: $activationLimit,
            licensePeriod: ! empty($licensePeriod) ? (int) $licensePeriod : null,
            licensePeriodUnit: ! empty($licensePeriod) ? PeriodUnit::Year : PeriodUnit::Lifetime,
            isActive: 'publish' === $itemRow->post_status,
            currency: DataSourceRepository::getCurrentCurrency()
        );
    }

    /**
     * @param  LegacyProduct  $item
     */
    protected function maybeImportItem(object $item): void
    {
        if ($item->isBundle) {
            $this->warn("########### SKIPPED BUNDLE: {$item->name}");
            return;
        }

        parent::maybeImportItem($item);
    }

    /**
     * @param  LegacyProduct  $item
     */
    protected function itemExists(object $item): bool
    {
        return $this->mappingRepository->hasMapping(
            source: Config::get('imports.currentSource'),
            sourceId: $item->id,
            dataType: new Product()
        );
    }

    /**
     * @param  LegacyProduct  $item
     */
    protected function importItem(object $item): void
    {
        DB::transaction(function() use ($item) {
            $product = $this->getOrCreateProduct($item);

            if ($item->prices) {
                foreach($item->prices as $key => $legacyPrice) {
                    $newPrice = $this->importLegacyPrice($item, $legacyPrice, $product);

                    $item->prices[$key]->newPriceId = $newPrice->id;
                }
            }

            $mapping = $this->makeLegacyMapping($item);
            $this->line('-- Mapping: '.$mapping->toJson());
            if (! $this->isDryRun()) {
                $product->legacyMapping()->save($mapping);
            }
        });
    }

    protected function getOrCreateProduct(LegacyProduct $legacyProduct) : Product
    {
        $product = Product::query()
            ->where('name', $legacyProduct->name)
            ->first();

        if ($product) {
            $this->line("-- Found existing product #{$product->id}");
        } else {
            $product = new Product();
            $product->name = $legacyProduct->name;
            $product->created_at = $legacyProduct->dateCreated;

            $this->line('-- Inserting product');
            if (! $this->isDryRun()) {
                $product->save();

                $this->line("-- Inserted product ID #{$product->id}");
            }
        }

        return $product;
    }

    protected function importLegacyPrice(LegacyProduct $legacyProduct, LegacyPrice $legacyPrice, Product $product) : ProductPrice
    {
        $newPrice = new ProductPrice();
        $newPrice->name = $legacyPrice->name;
        $newPrice->license_period = $legacyPrice->licensePeriod;
        $newPrice->license_period_unit = $legacyPrice->licensePeriodUnit;
        $newPrice->is_active = $legacyPrice->isActive;
        $newPrice->currency = $legacyPrice->currency;
        $newPrice->created_at = $legacyProduct->dateCreated;

        $this->line('-- Inserting price');
        if (! $this->isDryRun()) {
            $product->prices()->save($newPrice);

            $this->line("-- Inserted price ID #{$newPrice->id}");
        }

        return $newPrice;

        /*$mapping = $this->makeLegacyMapping($legacyPrice);
        $this->line('-- Mapping: '.$mapping->toJson());
        if (! $this->isDryRun()) {
            $newPrice->legacyMapping()->save($mapping);
        }*/
    }
}
