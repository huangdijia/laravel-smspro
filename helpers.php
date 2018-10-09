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
        return app('sms.smspro')->send($mobile, $message) ?: app('sms.smspro')->getError();
    }
}