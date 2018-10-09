<?php

namespace Huangdijia\Smspro;

class Smspro
{
    private $config = [];
    private $api    = 'http://api.accessyou.com/sms/sendsms.php';
    private $init   = true;
    private $errno;
    private $error;
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

    public function __construct($config)
    {
        $this->config = $config;
        if (empty($config['username'])) {
            $this->error = "config smspro.username is undefined";
            $this->errno = 101;
            $this->init  = false;
            return;
        }
        if (empty($config['password'])) {
            $this->error = "config smspro.password is undefined";
            $this->errno = 102;
            $this->init  = false;
            return;
        }
    }

    public function send($mobile = '', $message = '')
    {
        if (!$this->init) {
            return false;
        }

        $this->error = null;
        $this->errno = null;

        if (!$this->checkMobile($mobile)) {
            $this->error = "invalid mobile";
            $this->errno = 103;
            return false;
        }
        if (!$this->checkMessage($message)) {
            $this->error = "message is empty";
            $this->errno = 104;
            return false;
        }

        $data = array(
            "Username"     => $this->config['username'],
            "Password"     => $this->config['password'],
            "Telephone"    => $mobile,
            "UserDefineNo" => "00000",
            "Hex"          => "",
            "Message"      => $message, //self::msgEncode($message),
            "Sender"       => $this->config['sender'],
        );

        $ch      = curl_init();
        $options = array(
            CURLOPT_URL            => $this->config['api'],
            CURLOPT_HEADER         => 0,
            CURLOPT_VERBOSE        => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS     => http_build_query($data),
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);

        if (false === $response) {
            $this->error = "request faild";
            $this->errno = 401;
            return false;
        }

        if ($errno) {
            $this->error = "request faild";
            $this->errno = 401;
            return false;
        }

        $response = trim($response);
        if ($response == '') {
            $this->error = "empty response";
            $this->errno = 402;
            return false;
        }

        try {
            $response = simplexml_load_string($response);
            $response = json_encode($response);
            $response = json_decode($response);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->errno = 403;
            return false;
        }

        if ($response->State != 1) {
            $this->error = self::$stateMap[$response->State] ?? 'Unknown error';
            $this->errno = 500;
            return false;
        }

        return true;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrno()
    {
        return $this->errno;
    }

    private function checkMobile($mobile = '')
    {
        return preg_match('/^(4|5|6|7|8|9)\d{7}$/', $mobile);
    }

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
