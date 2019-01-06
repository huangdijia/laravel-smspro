<?php

namespace Huangdijia\Smspro\Console;

use Illuminate\Console\Command;

class SendCommand extends Command
{
    protected $signature   = 'smspro:send {mobile} {message}';
    protected $description = 'Send a message by smspro';

    public function handle()
    {
        $mobile  = $this->argument('mobile');
        $message = $this->argument('message');
        $smspro  = app('sms.smspro');

        if (!$smspro->send($mobile, $message)) {
            $this->error($smspro->getError());
        }

        $this->info('send success!');
    }
}
