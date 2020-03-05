<?php

namespace Huangdijia\Smspro;

use Illuminate\Support\ServiceProvider;

class SmsproServiceProvider extends ServiceProvider
{
    // protected $defer = true;

    protected $commands = [
        Console\SendCommand::class,
        Console\InfoCommand::class,
        Console\InstallCommand::class,
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/smspro.php' => $this->app->basePath('config/smspro.php')], 'config');
        }
    }

    public function register()
    {
        $this->app->singleton(Smsrpo::class, function () {
            return new Smspro($this->app['config']->get('smspro'));
        });

        $this->app->alias(Smsrpo::class, 'sms.smspro');

        $this->commands($this->commands);
    }

    public function provides()
    {
        return [
            Smsrpo::class,
            'sms.smspro',
        ];
    }
}
