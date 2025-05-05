<?php

namespace App\Providers;

use App\Models\License;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Policies\LicensePolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductPricePolicy;
use App\Policies\ReleasePolicy;
use Ashleyfae\LaravelGitReleases\Models\Release;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        License::class      => LicensePolicy::class,
        Product::class      => ProductPolicy::class,
        ProductPrice::class => ProductPricePolicy::class,
        Release::class      => ReleasePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function(User $user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}
