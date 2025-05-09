<?php

namespace App\Console\Commands\Import;

use App\Console\Commands\Traits\HasDryRunOption;
use App\Imports\Database\ImportQuery;
use App\Imports\DataObjects\Customer;
use App\Imports\Enums\DataSource;
use App\Imports\Enums\ImportedDataType;
use App\Imports\Repositories\DataSourceRepository;
use App\Imports\Repositories\MappingRepository;
use App\Models\LegacyMapping;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportCustomersCommand extends Command
{
    use HasDryRunOption;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers {--dry-run} {--max=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports customers.';

    protected int $numberImported = 0;

    public function __construct(
        protected MappingRepository $mappingRepository
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Starting import...');
        $count = ImportQuery::make()->table('wp_edd_customers')->count();
        $this->line("Found {$count} customer(s) to import.");

        ImportQuery::make()->table('wp_edd_customers')->chunkById(100, function(Collection $customers) {
           foreach($customers as $customer) {
               if ( $this->option('max') && $this->numberImported >= $this->option('max')) {
                   break;
               }

               $this->line("Processing customer ID {$customer->id}");
               $this->maybeImportCustomer($this->makeCustomerObject($customer));
               $this->numberImported++;
           }
        });
    }

    protected function makeCustomerObject(object $customerRow): Customer
    {
        return new Customer(
            id: $customerRow->id,
            customerEmail: $customerRow->email,
            userAccountEmail: $this->getUserAccountEmail($customerRow->user_id ?? null),
            name: $customerRow->name ?? null,
            dateCreated: $customerRow->date_created ?? date('Y-m-d H:i:s'),
            stripeCustomerId: $this->getStripeCustomerId((int) $customerRow->id)
        );
    }

    protected function getUserAccountEmail(?int $userId) : ?string
    {
        if (! $userId) {
            return null;
        }

        $userEmail = ImportQuery::make()
            ->table('wp_users')
            ->where('ID', $userId)
            ->value('user_email');

        return ! empty($userEmail) ? $userEmail : null;
    }

    protected function getStripeCustomerId(int $customerId) : ?string
    {
        $stripeId = ImportQuery::make()
            ->table('wp_edd_customermeta')
            ->where('edd_customer_id', $customerId)
            ->where('meta_key', '_edd_stripe_customer_id')
            ->value('meta_value');

        return ! empty($stripeId) ? $stripeId : null;
    }

    protected function maybeImportCustomer(Customer $customer) : void
    {
        $this->line('-- Parsed customer data: '.json_encode($customer->toArray()));

        if (! $this->customerExists($customer)) {
            $this->line('-- Customer does not exist; importing.');
            $this->importCustomer($customer);
        } else {
            $this->line('-- Customer already exists; skipping.');
        }
    }

    protected function customerExists(Customer $customer) : bool
    {
        return $this->mappingRepository->hasMapping(
            source: Config::get('imports.currentSource'),
            sourceId: $customer->id,
            dataType: new User()
        );
    }

    protected function importCustomer(Customer $customer) : void
    {
        $user = new User();
        $user->name = $customer->name;
        $user->email = $customer->userAccountEmail ?: $customer->customerEmail;
        $user->password = Hash::make(Str::random(20));
        $user->created_at = $customer->dateCreated;

        $this->line('-- Inserting user: '.$user->toJson());
        if (! $this->isDryRun()) {
            $user->save();

            $this->line('-- Inserted user ID #'.$user->id);
        }

        $mapping = $this->makeLegacyMapping($customer);
        $this->line('-- Mapping: '.$mapping->toJson());
        if (! $this->isDryRun()) {
            $user->legacyMapping()->save($mapping);
        }

        if ($customer->stripeCustomerId) {
            $args = [
                'stripe_id' => $customer->stripeCustomerId,
                'currency' => DataSourceRepository::getCurrentCurrency(),
            ];

            $this->line('-- Inserting Stripe customer: '.json_encode($args));

            if (! $this->isDryRun()) {
                $user->stripeCustomers()->create($args);
            }
        }
    }

    protected function makeLegacyMapping(Customer $customer) : LegacyMapping
    {
        $legacyMapping = new LegacyMapping();
        $legacyMapping->source_id = $customer->id;
        $legacyMapping->source_data = $customer->toArray();

        return $legacyMapping;
    }
}
