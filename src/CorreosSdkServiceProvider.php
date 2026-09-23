<?php

namespace SmartDato\CorreosSdk;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CorreosSdkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('correos-sdk')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CorreosSdk::class, fn (): CorreosSdk => new CorreosSdk(
            baseUrl: (string) config('correos-sdk.base_url'),
            username: (string) config('correos-sdk.username'),
            password: (string) config('correos-sdk.password'),
        ));
    }
}
