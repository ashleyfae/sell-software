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
use Illuminate\Support\Facades\Config;
use Stripe\StripeClient;

class ConnectStoreToStripe
{
    public function __construct(protected StripeClient $stripeClient)
    {

    }

    /**
     * @param  Store  $store
     *
     * @return string
     * @throws StoreAlreadyConnectedException
     */
    public function connect(Store $store) : string
    {
        if ($this->storeAlreadyConnected($store)) {
            throw new StoreAlreadyConnectedException();
        }

        $args = http_build_query([
            'response_type' => 'code',
            'client_id' => urlencode(Config::get('services.stripe.oauth.clientId')),
            'scope' => 'read_write',
            'state' => $store->uuid,
            'stipe_user[email]' => $store->user->email,
        ]);

        return Config::get('services.stripe.oauth.authorizeUrl').'?'.$args;
    }

    protected function storeAlreadyConnected(Store $store): bool
    {
        return ! empty($store->stripe_account_id);
    }
}
