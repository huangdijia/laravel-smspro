<?php

namespace Huangdijia\Smspro;

use Illuminate\Support\ServiceProvider;
use Huangdijia\Smspro\Console\InfoCommand;
use Huangdijia\Smspro\Console\SendCommand;

class SmsproServiceProvider extends ServiceProvider
{
    protected $defer = true;

    protected $commands = [
        SendCommand::class,
        InfoCommand::class,
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/config.php' => config_path('smspro.php')]);
        }
    }

    public function register()
    {
        $this->app->singleton(Smsrpo::class, function () {
            return new Smspro(config('smspro'));
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
