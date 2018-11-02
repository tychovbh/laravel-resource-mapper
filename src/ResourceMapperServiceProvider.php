<?php
declare(strict_types=1);

namespace Tychovbh\ResourceMapper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class ResourceMapperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = __DIR__ . '/../config/resource-mapper.php';
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('resource-mapper.php')], 'laravel-resource-mapper');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('resource-mapper');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('resource-mapper', function (Container $app) {
            return new ResourceMapper($app['config']);
        });
    }
}
