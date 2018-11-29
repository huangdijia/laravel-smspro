<?php

namespace Huangdijia\Smspro\Console;

use Exception;
use Illuminate\Console\Command;

class SendCommand extends Command
{
    protected $signature   = 'smspro:send {mobile} {message}';
    protected $description = 'Send a message by smspro';

    public function handle()
    {
        $mobile  = $this->argument('mobile');
        $message = $this->argument('message');

        if (!$error = smspro_send($mobile, $message)) {
            $this->error($error);
        }

        $this->info('send success!');
    }
}