<?php
return [
    "api"      => 'https://api3.hksmspro.com/service/smsapi.asmx/SendSMS',
    "username" => env('SMSPRO_USERNAME', ''),
    "password" => env('SMSPRO_PASSWORD', ''),
    "sender"   => env('SMSPRO_SENDER', ''),
];
