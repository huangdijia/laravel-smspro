<?php
if (!function_exists('smspro')) {
    function smspro()
    {
        return app('sms.smspro');
    }
}

if (!function_exists('smspro_send')) {
    function smspro_send($mobile = '', $message = '')
    {
        $smspro = app('sms.smspro');

        if (!$smspro->send($mobile, $message)) {
            return $smspro->getError();
        }
        
        return true;
    }
}