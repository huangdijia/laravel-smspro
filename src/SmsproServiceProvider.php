<?php

namespace Huangdijia\Smspro;

use Illuminate\Support\ServiceProvider;

class SmsproServiceProvider extends ServiceProvider
{
    protected $defer = true;
    protected $commands = [
        'Huangdijia\\Smspro\\Console\\SmsproSendCommand',
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/config.php' => config_path('smspro.php')]);
        }
    }

    public function register()
    {
        $this->app->singleton('sms.smspro', function () {
            return new Smspro(config('smspro'));
        });
        $this->commands($this->commands);
    }

    public function provides()
    {
        return [
            'sms.smspro'
        ];
    }
}