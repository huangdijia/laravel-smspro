<?php

namespace Huangdijia\Smspro\Console;

use Exception;
use Illuminate\Console\Command;

class InfoCommand extends Command
{
    protected $signature   = 'smspro:info';
    protected $description = 'Get info of smspro';

    public function handle()
    {
        try {
            $result = $this->laravel->make('sms.smspro')->info();
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->info('Account Info:');
        $this->table(array_keys($result), [array_values($result)]);
    }
}
