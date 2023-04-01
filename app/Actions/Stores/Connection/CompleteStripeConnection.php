<?php
/**
 * CompleteStripeConnection.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Stores\Connection;

use App\Exceptions\Stores\Connection\StripeConnectionFailedException;
use App\Models\Store;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\OAuth;
use Stripe\StripeClient;

class CompleteStripeConnection
{
    public function __construct(protected StripeClient $stripeClient)
    {

    }

    public function complete(Request $request) : Store
    {
        if ($errorMessage = $this->getErrorMessage($request)) {
            throw new StripeConnectionFailedException($errorMessage);
        }

        $authCode = $request->input('code');
        if (! $authCode) {
            throw new StripeConnectionFailedException('Missing authorization code.');
        }

        $store = $this->getStore($request);
        $store->stripe_account_id = $this->getAccountId($authCode);
        $store->save();

        return $store;
    }

    protected function getErrorMessage(Request $request): ?string
    {
        $possibleKeys = ['error_description', 'error'];
        foreach($possibleKeys as $key) {
            if ($errorMessage = $request->input($key)) {
                return $errorMessage;
            }
        }

        return null;
    }

    protected function getStore(Request $request): Store
    {
        $storeId = $request->input('state');
        if (empty($storeId)) {
            throw new StripeConnectionFailedException('Missing state.');
        }

        $store = Store::query()->where('uuid', $storeId)->first();
        if (! $store instanceof Store) {
            throw new StripeConnectionFailedException('Invalid state: '.$storeId);
        }

        if (! $request->user() || ! $request->user()->is($store->user)) {
            throw new AuthorizationException('You do not have permission to connect this store.');
        }

        return $store;
    }

    protected function getAccountId(string $authCode) : string
    {
        $response = $this->stripeClient->oauth->token([
            'grant_type' => 'authorization_code',
            'code' => $authCode,
        ]);

        if (! empty($response->error) || ! empty($response->error_description)) {
            throw new StripeConnectionFailedException($response->error_description ?? $response->error);
        }

        if (empty($response->stripe_user_id)) {
            throw new StripeConnectionFailedException('Missing account ID');
        }

        return $response->stripe_user_id;
    }
}
