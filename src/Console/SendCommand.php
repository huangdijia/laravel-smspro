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

        try {
            $this->laravel->make('sms.smspro')->send($mobile, $message);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->info('send success!');
    }
}
