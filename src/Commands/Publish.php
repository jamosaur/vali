<?php

declare(strict_types=1);

namespace Jamosaur\Vali\Commands;

use Illuminate\Console\Command;

class Publish extends Command
{
    /**
     * @var string
     */
    protected $signature = 'vali:publish';

    /**
     * @var string
     */
    protected $description = 'Publish vali docker files';

    public function handle(): void
    {
        $this->call('vendor:publish', ['--tag' => 'vali']);

        file_put_contents(
            $this->laravel->basePath('docker-compose.yml'),
            str_replace(
                './vendor/jamosaur/vali/runtimes/8.0',
                './docker/8.0',
                file_get_contents($this->laravel->basePath('docker-compose.yml'))
            )
        );
    }
}
