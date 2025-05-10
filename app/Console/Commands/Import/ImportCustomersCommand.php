<?php

namespace App\Console\Commands\Import;

use App\Console\Commands\Traits\HasDryRunOption;
use App\Console\Commands\Traits\HasMaxOption;
use App\Imports\Database\ImportQuery;
use App\Imports\DataObjects\LegacyCustomer;
use App\Imports\Enums\DataSource;
use App\Imports\Enums\ImportedDataType;
use App\Imports\Repositories\DataSourceRepository;
use App\Imports\Repositories\MappingRepository;
use App\Models\LegacyMapping;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportCustomersCommand extends AbstractImportCommand
{
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

    protected function getItemsToImportQuery() : Builder
    {
        return ImportQuery::make()->table('wp_edd_customers');
    }

    protected function makeItemObject(object $itemRow): LegacyCustomer
    {
        return new LegacyCustomer(
            id: $itemRow->id,
            customerEmail: $itemRow->email,
            userAccountEmail: $this->getUserAccountEmail($itemRow->user_id ?? null),
            name: $itemRow->name ?? null,
            dateCreated: $itemRow->date_created ?? date('Y-m-d H:i:s'),
            stripeCustomerId: $this->getStripeCustomerId((int) $itemRow->id)
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

    /**
     * @param  LegacyCustomer  $item
     */
    protected function itemExists(object $item) : bool
    {
        return $this->mappingRepository->hasMapping(
            source: Config::get('imports.currentSource'),
            sourceId: $item->id,
            dataType: new User()
        );
    }

    protected function getOrCreateUser(LegacyCustomer $customer) : User
    {
        $user = User::query()
            ->where('email', $customer->userAccountEmail ?: $customer->customerEmail)
            ->first();

        if ($user) {
            $this->line("-- Found existing user #{$user->id}");
        } else {
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

        }

        return $user;
    }

    /**
     * @param  LegacyCustomer  $item
     */
    protected function importItem(object $item) : void
    {
        DB::transaction(function() use($item) {
            $user = $this->getOrCreateUser($item);

            $mapping = $this->makeLegacyMapping($item);
            $this->line('-- Mapping: '.$mapping->toJson());
            if (! $this->isDryRun()) {
                $user->legacyMapping()->save($mapping);
            }

            if ($item->stripeCustomerId) {
                $args = [
                    'stripe_id' => $item->stripeCustomerId,
                    'currency' => DataSourceRepository::getCurrentCurrency(),
                ];

                $this->line('-- Inserting Stripe customer: '.json_encode($args));

                if (! $this->isDryRun()) {
                    $user->stripeCustomers()->create($args);
                }
            }
        });
    }
}
