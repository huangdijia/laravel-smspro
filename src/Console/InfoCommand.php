<?php

namespace Huangdijia\Smspro\Console;

use Illuminate\Console\Command;

class InfoCommand extends Command
{
    protected $signature   = 'smspro:info';
    protected $description = 'Get info of smspro';

    public function handle()
    {
        $smspro = app('sms.smspro');

        if (!$result = $smspro->info()) {
            $this->error($smspro->getError());
            return;
        }

        $this->info('Account Info:');
        $this->table(array_keys($result), [array_values($result)]);
    }
}
