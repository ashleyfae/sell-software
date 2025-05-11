<?php

namespace App\Console\Commands\Import;

use App\Enums\LicenseStatus;
use App\Imports\Database\ImportQuery;
use App\Imports\DataObjects\LegacyLicense;
use App\Imports\Repositories\MappingRepository;
use App\Models\License;
use App\Models\ProductPrice;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

class ImportLicensesCommand extends AbstractImportCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:licenses {--dry-run} {--max=} {--legacy-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports licenses';

    public function __construct(MappingRepository $mappingRepository)
    {
        parent::__construct($mappingRepository);

        $this->dataType = new License();
    }

    protected function getItemsToImportQuery(): Builder
    {
        return ImportQuery::make()->table('wp_edd_licenses')
            ->when($this->option('legacy-id'), function(Builder $builder) {
                $builder->where('id', $this->option('legacy-id'));
            });
    }

    protected function makeItemObject(object $itemRow): object
    {
        $license = new License();
        $license->user_id = $this->mappingRepository->getUserIdFromLegacyCustomerId($itemRow->customer_id);
        $license->license_key = $itemRow->license_key;
        $license->status = $this->convertLicenseStatus($itemRow->status ?? null);
        $license->order_id = $this->mappingRepository->getOrderIdFromLegacy($itemRow->payment_id);
        $license->product_id = $this->mappingRepository->getNewProductIdFromLegacyProductId($itemRow->product_id);
        $license->product_price_id = $this->mappingRepository->getNewPriceIdFromLegacyProductId(
            legacyProductId: $itemRow->product_id,
            legacyPriceIndex: $itemRow->price_id
        );
        $license->activation_limit = ProductPrice::findOrFail($license->product_price_id)->activation_limit;
        $license->expires_at = empty($license->expires) ? null : Carbon::createFromTimestamp($license->expires);
        $license->created_at = $license->date_created;

        return new LegacyLicense(
            id: $itemRow->id,
            license: $license
        );
    }

    protected function convertLicenseStatus(?string $legacyStatus) : LicenseStatus
    {
        return match($legacyStatus) {
            'disabled' => LicenseStatus::Disabled,
            'expired' => LicenseStatus::Expired,
            default => LicenseStatus::Active
        };
    }

    protected function importItem(object $item): void
    {
        // TODO: Implement importItem() method.
    }
}
