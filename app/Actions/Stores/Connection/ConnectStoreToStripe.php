<?php
/**
 * ConnectStoreToStripe.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Stores\Connection;

use App\Exceptions\Stores\Connection\StoreAlreadyConnectedException;
use App\Models\Store;
use Stripe\Account;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class ConnectStoreToStripe
{
    public function __construct(protected StripeClient $stripeClient)
    {

    }

    public function connect(Store $store) : string
    {
        if ($this->storeAlreadyConnected($store)) {
            throw new StoreAlreadyConnectedException();
        }

        $account = $this->getOrCreateAccount($store);

        $store->stripe_account_id = $account->id;
        $store->save();

        return $this->createAccountLink($account->id, $store);
    }

    protected function storeAlreadyConnected(Store $store): bool
    {
        return ! empty($store->stripe_account_id) && $store->stripe_connected;
    }

    /**
     * @throws ApiErrorException
     */
    protected function getOrCreateAccount(Store $store): Account
    {
        if ($store->stripe_account_id) {
            return $this->stripeClient->accounts->retrieve($store->stripe_account_id);
        }

        return $this->stripeClient->accounts->create([
            'type' => 'standard',
            'email' => $store->user->email,
        ]);
    }

    protected function createAccountLink(string $accountId, Store $store): string
    {
        return $this->stripeClient->accountLinks->create([
            'account' => $accountId,
            'refresh_url' => route('stores.connect', $store),
            'return_url' => route('stores.verifyConnection', $store),
            'type' => 'account_onboarding',
        ])->url;
    }
}
