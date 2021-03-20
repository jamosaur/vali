<?php

declare(strict_types=1);

namespace Jamosaur\Vali\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Install extends Command
{
    /**
     * @var string
     */
    protected $signature = 'vali:install {--services= : The services that should be included}';

    /**
     * @var string
     */
    protected $description = 'Install Vali\'s default docker-compose.yml';

    public function handle()
    {
        if ($this->option('services')) {
            $services = $this->option('services') === 'none' ? [] : explode(',', $this->option('services'));
        } else {
            $services = $this->requestServicesMenu();
        }

        $this->buildNginxConfig($services);
        $this->buildDockerComposeFile($services);
        $this->replaceEnv($services);

        $this->info('Vali installed successfully');
    }

    public function buildDockerComposeFile(array $services): void
    {
        $dependencies = collect($services)
            ->filter(function ($service) {
                // Only return the ones that will cause a dependency, mailhog will not for example.
                return in_array($service, ['mysql', 'redis']);
            })->map(function ($service) {
                return "            - " . $service;
            })->whenNotEmpty(function (Collection $collection) {
                return $collection->prepend('        depends_on:');
            })->implode("\n");

        $stubs = rtrim(collect($services)
            ->filter(function ($service) {
                return $service !== 'https';
            })
            ->map(function ($service) {
            return file_get_contents(__DIR__ . '/../../stubs/' . $service . '.stub');
        })->implode(''));

        $volumes = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'redis']);
            })->map(function ($service) {
                return "    vali{$service}:\n        driver: local";
            })->whenNotEmpty(function (Collection $collection) {
                return $collection->prepend('volumes:');
            })->implode("\n");

        $httpsVolume = collect($services)->contains('https') ?
            '            - \'./certificates:/etc/nginx/certificates\'' :
            '';

        $composeYaml = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');
        $composeYaml = str_replace(
            ['{{depends}}', '{{services}}', '{{volumes}}', '{{httpsvolume}}'],
            [empty($dependencies) ? '' : $dependencies, $stubs, $volumes, $httpsVolume],
            $composeYaml
        );

        file_put_contents($this->laravel->basePath('docker-compose.yml'), $composeYaml);
    }

    /**
     * @return array|string
     */
    protected function requestServicesMenu()
    {
        return $this->choice('Which services would you like to install?',
            [
                'mysql',
                'redis',
                'mailhog',
                'https'
            ],
            0,
            null,
            true);
    }

    protected function replaceEnv(array $services)
    {
        $env = file_get_contents($this->laravel->basePath('.env'));

        $env = str_replace(
            ['DB_HOST=127.0.0.1', 'REDIS_HOST=127.0.0.1'],
            ['DB_HOST=mysql', 'REDIS_HOST=redis'],
            $env
        );

        file_put_contents($this->laravel->basePath('.env'), $env);
    }

    protected function buildNginxConfig(array $services): void
    {
        $nginxFilePath = $this->laravel->basePath('config');
        $file = 'http.stub';
        if (collect($services)->contains('https')) {
            $filesExist = true;
            $basePath =  $this->laravel->basePath('certificates');
            $filesToCheck = ['server.key', 'server.crt'];

            foreach ($filesToCheck as $file) {
                if (!file_exists($basePath . '/' . $file)) {
                    $this->error('Missing ' . $file);
                    //$filesExist = false;
                }
            }
            if (!$filesExist) {
                $this->info('Please copy these files to ' . $basePath);
                exit;
            }

            $file = 'https.stub';
        }

        file_put_contents(
            $nginxFilePath . '/' . 'nginx-config.conf',
            file_get_contents(__DIR__ . '/../../stubs/nginx/' . $file)
        );
    }
}
