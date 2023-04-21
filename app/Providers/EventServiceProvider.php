<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\ProvisionOrderItems;
use App\Listeners\SendOrderReceiptNotification;
use App\Models\License;
use App\Observers\LicenseObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderCreated::class => [
            ProvisionOrderItems::class,
            SendOrderReceiptNotification::class,
        ],
    ];

    protected $observers = [
        License::class => [LicenseObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
