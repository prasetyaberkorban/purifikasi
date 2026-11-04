<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Policies\AssetLoanPolicy;
use App\Policies\AssetPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(AssetLoan::class, AssetLoanPolicy::class);

        Paginator::useTailwind();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}