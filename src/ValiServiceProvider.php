<?php

declare(strict_types=1);

namespace Jamosaur\Vali;

use Jamosaur\Vali\Commands\Install;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class ValiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->registerCommands();
        $this->configurePublishing();
    }

    public function provides()
    {
        return [
            Install::class,
        ];
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }
    }

    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../runtimes' => $this->app->basePath('docker'),
                ],
                'vali'
            );
        }
    }
}
