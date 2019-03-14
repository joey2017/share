<?php

/**
 * 参数加密类
 */

class Urlencry
{
    public static $encrypt_key = null;  //加密的密钥

    public static function setKey($key)
    {
        self::$encrypt_key = $key;
    }

    //密钥处理
    public static function keyED($txt)
    {
        $encrypt_key = self::$encrypt_key;
        $encrypt_key = md5($encrypt_key);
        $ctr         = 0;
        $tmp         = "";
        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key))
                $ctr = 0;
            $tmp .= substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1);
            $ctr++;
        }
        return $tmp;
    }

    //地址加密
    public static function encrypt($txt)
    {
        $encrypt_key = md5(mt_rand(0, 100));
        $ctr         = 0;
        $tmp         = "";
        for ($i = 0; $i < strlen($txt); $i++) {
            if ($ctr == strlen($encrypt_key))
                $ctr = 0;
            $tmp .= substr($encrypt_key, $ctr, 1) . (substr($txt, $i, 1) ^ substr($encrypt_key, $ctr, 1));
            $ctr++;
        }
        return self::keyED($tmp);
    }

    //地址解密
    public static function decrypt($txt)
    {
        $txt = self::keyED($txt);
        $tmp = "";
        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = substr($txt, $i, 1);
            $i++;
            $tmp .= (substr($txt, $i, 1) ^ $md5);
        }
        return $tmp;
    }

    //调用加密链接
    public static function encrypt_url($url)
    {
        $url = self::encrypt($url);
        return rawurlencode(base64_encode($url));
    }

    //调用解密链接
    public static function decrypt_url($url)
    {
        return self::decrypt(base64_decode(rawurldecode($url)));
    }

    public static function geturl($str)
    {
        $vars      = array();
        $str       = self::decrypt_url($str);
        $url_array = explode('&', $str);
        if (is_array($url_array)) {
            foreach ($url_array as $var) {
                $var_array           = explode("=", $var);
                $vars[$var_array[0]] = $var_array[1];
            }
        }
        return $vars;
    }
}

