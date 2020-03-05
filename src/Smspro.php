<?php

namespace Huangdijia\Smspro;

use Exception;
use Illuminate\Support\Facades\Http;

class Smspro
{
    private $config = [];
    private $apis   = [
        'send_sms'    => 'https://api3.hksmspro.com/service/smsapi.asmx/SendSMS',
        'get_balance' => 'https://api3.hksmspro.com/service/smsapi.asmx/GetBalance',
    ];
    protected static $stateMap = [
        1   => 'Message	Sent',
        0   => 'Missing	Values',
        10  => 'Incorrect Username or Password',
        20  => 'Message content too long',
        30  => 'Message content too long',
        40  => 'Telephone number too long',
        60  => 'Incorrect Country Code',
        70  => 'Balance not enough',
        80  => 'Incorrect date time',
        100 => 'System error, please try again',
    ];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Send sms
     * @param string $mobile
     * @param string $message
     * @return true
     * @throws Exception
     */
    public function send($mobile = '', $message = '')
    {
        throw_if(empty($this->config['username']), new Exception("config smspro.username is undefined", 101));

        throw_if(empty($this->config['password']), new Exception("config smspro.password is undefined", 102));

        throw_if(!$this->checkMobile($mobile), new Exception("invalid mobile", 103));

        throw_if(!$this->checkMessage($message), new Exception("message is empty", 104));

        $data = array(
            "Username"     => $this->config['username'],
            "Password"     => $this->config['password'],
            "Telephone"    => $mobile,
            "UserDefineNo" => "00000",
            "Hex"          => "",
            "Message"      => $message, //self::msgEncode($message),
            "Sender"       => $this->config['sender'],
        );

        $response = Http::asForm()->post($this->apis['send_sms'], $data)->throw();
        $content  = trim($response->body());

        throw_if($content == '', new Exception('Response body is empty!', 402));

        try {
            $result = simplexml_load_string($content);
            $result = json_encode($result);
            $result = json_decode($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 403);
        }

        throw_if($result->State != 1, new Exception(self::$stateMap[$result->State] ?? 'Unknown error', 500));

        return true;
    }

    /**
     * Get info
     * @return false|array
     */
    public function info(array $config = [])
    {
        $config = array_merge($this->config, $config);

        $data = [
            'Username'     => $config['username'] ?? '',
            'Password'     => $config['password'] ?? '',
            'ResponseID'   => '',
            'UserDefineNo' => '',
        ];

        $response = Http::asForm()->post($this->apis['get_balance'], $data)->throw();
        $content  = trim($response->body());

        throw_if($content == '', new Exception('Response body is empty!', 402));

        try {
            $result = simplexml_load_string($content);
            $result = json_encode($result);
            $result = json_decode($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 403);
        }

        return [
            'account' => $config['username'],
            'balance' => $result->CurrentBalance ?? 0,
            'credit'  => $result->CreditLine ?? 0,
        ];
    }

    /**
     * Check mobile
     * @param string $mobile
     * @return bool
     */
    private function checkMobile($mobile = '')
    {
        return preg_match('/^(4|5|6|7|8|9)\d{7}$/', $mobile) ? true : false;
    }

    /**
     * Check message
     * @param string $message
     * @return bool
     */
    private function checkMessage($message = '')
    {
        return !empty($message) ? true : false;
    }

    private static function msgEncode($str = '')
    {
        return self::unicodeGet(self::convert(2, $str));
    }

    private static function unicodeGet($str)
    {
        $str = preg_replace("/&#/", "%26%23", $str);
        $str = preg_replace("/;/", "%3B", $str);
        return $str;
    }

    private static function convert($language, $cell)
    {
        $str = "";
        preg_match_all("/[\x80-\xff]?./", $cell, $ar);

        switch ($language) {
            case 0: // 繁体中文
                foreach ($ar[0] as $v) {
                    $str .= "&#" . self::chineseUnicode(iconv("big5-hkscs", "UTF-8", $v)) . ";";
                }
                return $str;
                break;
            case 1: // 简体中文
                foreach ($ar[0] as $v) {
                    $str .= "&#" . self::chineseUnicode(iconv("gb2312", "UTF-8", $v)) . ";";
                }
                return $str;
                break;
            case 2: // 二进制编码
                $cell = self::utf8Unicode($cell);
                foreach ($cell as $v) {
                    $str .= "&#" . $v . ";";
                }
                return $str;
                break;
        }
    }

    private static function chineseUnicode($c)
    {
        switch (strlen($c)) {
            case 1:
                return ord($c);
            case 2:
                $n = (ord($c[0]) & 0x3f) << 6;
                $n += ord($c[1]) & 0x3f;
                return $n;
            case 3:
                $n = (ord($c[0]) & 0x1f) << 12;
                $n += (ord($c[1]) & 0x3f) << 6;
                $n += ord($c[2]) & 0x3f;
                return $n;
            case 4:
                $n = (ord($c[0]) & 0x0f) << 18;
                $n += (ord($c[1]) & 0x3f) << 12;
                $n += (ord($c[2]) & 0x3f) << 6;
                $n += ord($c[3]) & 0x3f;
                return $n;
        }
    }

    private static function utf8Unicode($str)
    {
        $unicode    = [];
        $values     = [];
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);

            if ($thisValue < 128) {
                $unicode[] = $thisValue;
            } else {
                if (count($values) == 0) {
                    $lookingFor = ($thisValue < 224) ? 2 : 3;
                }

                $values[] = $thisValue;

                if (count($values) == $lookingFor) {
                    $number = ($lookingFor == 3) ? (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) : (($values[0] % 32) * 64) + ($values[1] % 64);

                    $unicode[]  = $number;
                    $values     = [];
                    $lookingFor = 1;
                }
            }
        }

        return $unicode;
    }
}
