<?php

namespace Huangdijia\Smspro\Facades;

use Illuminate\Support\Facades\Facade;
use Huangdijia\Smspro\Smspro as Accessor;

/**
 * @method static boolean send($mobile, $message)
 * @see \Huangdijia\Smspro\Smspro
 */

class Smspro extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Accessor::class; 
    }
}