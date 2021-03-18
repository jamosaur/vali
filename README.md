# vali

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

An easy way to get started with docker in your laravel installation with local https support.

## Requirements

- docker
- [mkcert](https://github.com/FiloSottile/mkcert) (if you wish to use https locally)

## Install

Via Composer

```sh
$ composer require jamosaur/vali
$ php artisan vali:install
$ ./vendor/bin/vali up
```

The set up wizard will ask you some questions about your requirements. Answer the questions and config will be generated for you.

The first time you run `up` may take a while as it will have to download docker images, subsequent runs will be much faster.

## Adding a bash alias

While not necessary, it is _highly_ recommended to add a bash alias for vali.

By adding an alias, you can invoke vali by `vali` instead of `./vendor/bin/vali`.

To do this, add into your shell config file (~/.zshrc, ~/.bashrc) the following:

```sh
alias vali='./vendor/bin/vali'
```

## Using HTTPS

To use https locally, I recommend using [mkcert](https://github.com/FiloSottile/mkcert) to generate your certificates.

From your project directory, run the following:

```shell
vali certificates
```

vali will ask you for a domain name to use. **It will automatically create a wildcard certificate for you**.

Entering `vali.test` will genereate a single certificate that works for `vali.test` and also `*.vali.test`


Alternatively, if you'd like to create these manually:

```shell
$ mkdir certificates # We will store the certificates in this folder
# For a single domain (e.g. vali.test)
$ mkcert -key-file certificates/server.key -cert-file certificates/server.crt vali.test
# For a wildcard
$ mkcert -key-file certificates/server.key -cert-file certificates/server.crt vali.test \*.vali.test
```

## Usage

#### If you plan on using https locally, it is best to read through the [HTTPS](#using https) section first.

Get started by running the `vali:install` artisan command.
```bash
php artisan vali:install
```
This will create a `docker-composer.yml` file in your project root. It will also create an `nginx-config.conf` file inside of your config folder. You can make any changes you'd like to your nginx config here.

## Available commands
```shell
help          Show this output
certificates  Create HTTPS certificates.
up            Start the containers
up -d         Start the containers in the background
down          Stop all of the running containers
build         Build all of the containers
php           Run a PHP command in the container
artisan       Run an artisan command. e.g. vali artisan test
composer      Run composer in the container
migrate       Migrate database
mfs           Refresh the database and seed
test          Run tests via artisan
tinker        Launch a tinker session in the container
shell         Launch a bash session in the container
rootshell     Launch a root bash session in the container
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jamosaur/vali.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jamosaur/vali.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jamosaur/vali
[link-downloads]: https://packagist.org/packages/jamosaur/vali
[link-author]: https://github.com/jamosaur
[link-contributors]: ../../contributors
