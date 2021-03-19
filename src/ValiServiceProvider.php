<?php

declare(strict_types=1);

namespace Jamosaur\Vali;

use Jamosaur\Vali\Commands\Install;
use Jamosaur\Vali\Commands\Publish;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class ValiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->registerCommands();
        $this->configurePublishing();
    }

    /**
     * @return string[]
     */
    public function provides()
    {
        return [
            Install::class,
            Publish::class,
        ];
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
                Publish::class,
            ]);
        }
    }

    protected function configurePublishing(): void
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
