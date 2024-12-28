<?php

namespace Igniter\User\Auth;

use Igniter\Flame\Support\Facades\Igniter;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/auth.php', 'igniter-auth');
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../../config/auth.php' => config_path('igniter-auth.php')], 'igniter-config');
        }

        config()->set('auth.guards', array_merge(config('auth.guards', []), config('igniter-auth.mergeGuards', [])));
        config()->set('auth.providers', array_merge(config('auth.providers', []), config('igniter-auth.mergeProviders', [])));

        $this->configureAuthGuards();

        $this->configureAuthProvider();

        $this->configureGateCallback();

        $this->app->booted(function() {
            (new Middleware)->redirectGuestsTo(
                fn() => route(Igniter::runningInAdmin() ? 'igniter.admin.login' : 'igniter.theme.account.login'),
                fn() => route(Igniter::runningInAdmin() ? 'igniter.admin.dashboard' : 'igniter.theme.account.account'),
            );
        });
    }

    protected function configureAuthGuards()
    {
        Auth::resolved(function($auth) {
            $auth->extend('igniter-admin', function($app, $name, array $config) use ($auth) {
                return $this->createGuard(UserGuard::class, $name, $config, $auth);
            });
        });

        Auth::resolved(function($auth) {
            $auth->extend('igniter-customer', function($app, $name, array $config) use ($auth) {
                return $this->createGuard(CustomerGuard::class, $name, $config, $auth);
            });
        });
    }

    protected function configureAuthProvider()
    {
        Auth::provider('igniter', function($app, $config) {
            return new UserProvider($config);
        });
    }

    protected function configureGateCallback()
    {
        Gate::after(function($user, $ability) {
            if (Igniter::isAdminUser($user)) {
                return $user->hasAnyPermission($ability) === true ? true : null;
            }
        });
    }

    protected function createGuard($guardClass, $name, array $config, $auth)
    {
        $guard = new $guardClass($name,
            $auth->createUserProvider($config['provider']),
            $this->app['session.store'],
        );

        $guard->setCookieJar($this->app['cookie']);
        $guard->setDispatcher($this->app['events']);
        $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));

        return $guard;
    }
}
