<?php

namespace App\Providers;

use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Defining a Super-Admin
        // @see https://spatie.be/docs/laravel-permission/v5/basic-usage/super-admin
        Gate::before(function ($user, $ability) {
            return ($user->id == 1 || $user->hasRole('Super Admin')) ? true : null;
        });
    }
}
