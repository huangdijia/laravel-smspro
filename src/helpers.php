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

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $name
     * @return string
     */
    function config_path($name = '')
    {
        if (is_callable(app(), 'getConfigurationPath')) {
            return app()->getConfigurationPath($name);
        } elseif (app()->has('path.config')) {
            return app()->make('path.config') . ($name ? DIRECTORY_SEPARATOR . $name : $name);
        } else {
            return app()->make('path') . '/../config' . ($name ? DIRECTORY_SEPARATOR . $name : $name);
        }
    }
}
