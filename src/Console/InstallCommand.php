<?php

namespace Huangdijia\Smspro\Console;

use Huangdijia\Smspro\SmsproServiceProvider;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature   = 'smspro:install';
    protected $description = 'Install config.';

    public function handle()
    {
        $this->info('Installing Package...');

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => SmsproServiceProvider::class,
            '--tag'      => "config",
        ]);

        $this->info('Installed Package');
    }
}
