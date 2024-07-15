<?php

namespace Webard\NovaSunEditor;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Http\Middleware\Authorize;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('suneditor', __DIR__.'/../dist/js/field.js');
            Nova::style('suneditor', __DIR__.'/../dist/css/field.css');

            $buttonLists = config('nova-suneditor.buttonLists', []);
            $settings = config('nova-suneditor.settings', []);

            Nova::provideToScript([
                'suneditor-buttonLists' => $buttonLists,
                'suneditor-settings' => $settings,
            ]);
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        // Nova::router(['nova', Authenticate::class, Authorize::class], 'nova-suneditor')
        //     ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-api/webard/nova-suneditor')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publish();
        }

        $this->mergeConfigFrom(__DIR__.'/../config/nova-suneditor.php', 'nova-suneditor');
    }

    protected function publish(): void
    {
        $this->publishes([
            __DIR__.'/../config/nova-suneditor.php' => config_path('nova-suneditor.php'),
        ], 'nova-suneditor-config');

    }
}
