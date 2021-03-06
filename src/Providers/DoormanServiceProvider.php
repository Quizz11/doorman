<?php

namespace Clarkeash\Doorman\Providers;

use Clarkeash\Doorman\Doorman;
use Clarkeash\Doorman\Manager;
use Clarkeash\Doorman\Models\BaseInvite;
use Clarkeash\Doorman\Validation\DoormanValidator;
use Illuminate\Support\ServiceProvider;
use Validator;

class DoormanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Clarkeash\Doorman\Commands\CleanupCommand::class,
            ]);
        }
        
        $this->publishes([
            __DIR__ . '/../../resources/config/doorman.php' => config_path('doorman.php'),
        ], 'doorman-config');

        $this->publishes([
            __DIR__ . '/../../resources/translations' => resource_path('lang/vendor/doorman'),
        ], 'doorman-translations');

        $this->publishes([
            __DIR__ . '/../../resources/migrations' => database_path('migrations')
        ], 'doorman-migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../../resources/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/translations', 'doorman');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../resources/config/doorman.php',
            'doorman'
        );

        $this->app->bind(BaseInvite::class, function($app) {
            return new $app['config']['doorman.invite_model'];
        });

        $this->app->bind('doorman', Doorman::class);
        $this->app->singleton(Doorman::class, Doorman::class);
    }
}
