<?php

namespace Huangdijia\Smspro\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static boolean send($mobile, $message)
 * @see \Huangdijia\Smspro\Smspro
 */

class Smspro extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms.smspro'; 
    }
}