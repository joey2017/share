<?php
include '../init.php';
$jssdk       = new JSSDK($appid, $appsecret);
$signPackage = $jssdk->getSignPackage($_POST['url']);
//$signPackage = $jssdk->getSignPackage($jssdk->getUrl());
header('content-type:application/json;charset=utf8');
echo json_encode(array(
    'appid'     => $signPackage['appId'],
    'timestamp' => $signPackage['timestamp'],
    'nonce'     => $signPackage['nonceStr'],
    'signature' => $signPackage['signature']
));

class JSSDK
{
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
    }

    public function getSignPackage($url)
    {
        $jsapiTicket = $this->getJsApiTicket();
        $timestamp   = time();
        $nonceStr    = $this->createNonceStr();
        $string      = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    public function getUrl(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $url;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        $file = 'jsapi_ticket.php';
        if (!file_exists($file)) {
            $this->set_php_file($file, '');
        }
        $data = json_decode($this->get_php_file($file));
        if (empty($data) || $data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            $url         = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res         = json_decode($this->httpGet($url));
            $ticket      = $res->ticket;
            if ($ticket) {
                $data               = new stdClass();
                $data->expire_time  = time() + 5000;
                $data->jsapi_ticket = $ticket;
                $this->set_php_file($file, json_encode($data));
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    private function getAccessToken()
    {
        $file = 'access_token.php';
        if (!file_exists($file)) {
            $this->set_php_file($file, '');
        }
        $data = json_decode($this->get_php_file($file));
        if (empty($data) || $data->expire_time < time()) {
            $url          = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res          = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data               = new stdClass();
                $data->expire_time  = time() + 5000;
                $data->access_token = $access_token;
                $this->set_php_file($file, json_encode($data));
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    private function get_php_file($filename)
    {
        return trim(substr(file_get_contents($filename), 15));
    }

    private function set_php_file($filename, $content)
    {
        if ($fp = fopen($filename, 'w+')) {
            $startTime = microtime();
            do {
                $canWrite = flock($fp, LOCK_EX);
                if (!$canWrite) usleep(round(rand(0, 100) * 1000));
            } while ((!$canWrite) && ((microtime() - $startTime) < 1000));

            if ($canWrite) {
                fwrite($fp, "<?php exit();?>" . $content);
            }
            fclose($fp);
        }
    }

    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}

?>