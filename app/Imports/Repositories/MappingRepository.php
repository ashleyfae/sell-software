<?php
/**
 * MappingRepository.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Imports\Repositories;

use App\Imports\Enums\DataSource;
use App\Models\Bundle;
use App\Models\LegacyMapping;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class MappingRepository
{
    public function hasMapping(DataSource $source, int $sourceId, Model $dataType) : bool
    {
        $id = $this->getSingleMappingQuery($source, $sourceId, $dataType)
            ->value('id');

        return ! empty($id);
    }

    public function getSingleMappingQuery(DataSource $source, int $sourceId, Model $dataType)
    {
        return LegacyMapping::query()
            ->where('source', $source->value)
            ->where('source_id', $sourceId)
            ->where('mappable_type', $dataType->getMorphClass());
    }

    public function getUserIdFromLegacyCustomerId(int $legacyCustomerId) : int
    {
        return (int) $this->getSingleMappingQuery(
            source: Config::get('imports.currentSource'),
            sourceId: $legacyCustomerId,
            dataType: new User()
        )
            ->valueOrFail('mappable_id');
    }

    public function getOrderIdFromLegacy(int $legacyOrderId) : int
    {
        return (int) $this->getSingleMappingQuery(
            source: Config::get('imports.currentSource'),
            sourceId: $legacyOrderId,
            dataType: new Order()
        )
            ->valueOrFail('mappable_id');
    }

    public function getBundleId(int $legacyProductId) : ?int
    {
        $cacheKey = sprintf(
            'legacy-%s-product-%d-is-bundle',
            Config::get('imports.currentSource')->value,
            $legacyProductId
        );

        $value = Cache::remember(
            key: $cacheKey,
            ttl: 3600, // 1 hour
            callback: function() use($legacyProductId) {
                $mapping = $this->getSingleMappingQuery(
                    source: Config::get('imports.currentSource'),
                    sourceId: $legacyProductId,
                    dataType: new Bundle()
                )
                    ->first();

                return $mapping?->mappable_id;
            }
        );

        return $value ?: null;
    }

    /**
     * @throws Exception
     */
    public function getNewProductIdFromLegacyProductId(int $legacyProductId) : int
    {
        $cacheKey = sprintf(
            'legacy-%s-product-%s',
            Config::get('imports.currentSource')->value,
            $legacyProductId
        );

        $value = Cache::remember(
            key: $cacheKey,
            ttl: 3600, // 1 hour
            callback: function() use($legacyProductId) {
                /** @var LegacyMapping $mapping */
                $mapping = $this->getSingleMappingQuery(
                    source: Config::get('imports.currentSource'),
                    sourceId: $legacyProductId,
                    dataType: new Product()
                )
                    ->first();

                return $mapping?->mappable_id;
            }
        );

        if ($value) {
            return (int) $value;
        } else {
            throw new Exception("New product ID not found for legacy product ID #{$legacyProductId}");
        }
    }

    /**
     * @throws Exception
     */
    public function getNewPriceIdFromLegacyProductId(int $legacyProductId, ?int $legacyPriceIndex) : int
    {
        $priceIndexKey = is_null($legacyPriceIndex) ? 'default' : $legacyPriceIndex;
        $cacheKey = sprintf(
            'legacy-%s-product-%s-price-%s',
            Config::get('imports.currentSource')->value,
            $legacyProductId,
            $priceIndexKey
        );

        $value = Cache::remember(
            key: $cacheKey,
            ttl: 3600, // 1 hour
            callback: function() use($legacyProductId, $legacyPriceIndex) {
                /** @var ?LegacyMapping $mapping */
                $mapping = $this->getSingleMappingQuery(
                    source: Config::get('imports.currentSource'),
                    sourceId: $legacyProductId,
                    dataType: new ProductPrice()
                )
                    ->when(
                        value: is_null($legacyPriceIndex),
                        callback: fn(\Illuminate\Database\Eloquent\Builder $builder) => $builder->whereNull('secondary_source_id'),
                        default: fn(\Illuminate\Database\Eloquent\Builder $builder) => $builder->where('secondary_source_id', $legacyPriceIndex)
                    )
                    ->first();

                if (! $mapping && $legacyPriceIndex === 0) {
                    // try null instead :faceplam:
                    /** @var ?LegacyMapping $mapping */
                    $mapping = $this->getSingleMappingQuery(
                        source: Config::get('imports.currentSource'),
                        sourceId: $legacyProductId,
                        dataType: new ProductPrice()
                    )
                        ->whereNull('secondary_source_id')
                        ->first();
                }

                return $mapping?->mappable_id;
            }
        );

        if ($value) {
            return (int) $value;
        } else {
            throw new Exception("New price ID not found for legacy product ID #{$legacyProductId} and price index {$legacyPriceIndex}");
        }
    }
}
