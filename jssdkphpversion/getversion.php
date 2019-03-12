<?php
include '../init.php';
$jssdk       = new JSSDK($appid, $appsecret, $mysql, $appsArray);
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
    private $mysql;
    private $info;

    public function __construct($appId, $appSecret, $mysql, $appsArray)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->mysql     = $mysql;
        $this->info      = $appsArray;
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

    public function getUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url      = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
        if (empty($this->info['jsapi_ticket']) || strtotime($this->info['jsapi_ticket_expire_time']) < time()) {
            $accessToken = $this->getAccessToken();
            $url         = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res         = json_decode($this->httpGet($url));
            $ticket      = $res->ticket;
            if ($ticket) {
                $updateSql = 'update system_app set jsapi_ticket=:jsapi_ticket,jsapi_ticket_expire_time=:jsapi_ticket_expire_time where appid=:appid';
                $data      = array(':jsapi_ticket' => $ticket, ':jsapi_ticket_expire_time' => date('Y-m-d H:i:s', time() + 5000), ':appid' => $this->appId);
                $stmt      = $this->mysql->prepare($updateSql);
                try {
                    $stmt->execute($data);
                } catch (\Exception $e) {
                    file_put_contents(__DIR__ . '/error.log', $this->appId . '  jsapi_ticket状态更新失败 ' . '  错误信息：'.json_encode($e->getTraceAsString()) . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
                }

                if ($stmt->rowCount() > 0) {
                    file_put_contents(__DIR__ . '/update.log', $this->appId . '  jsapi_ticket状态更新成功 ' . ' ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents(__DIR__ . '/update.log', $this->appId . '  jsapi_ticket状态更新失败 ' . ' ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
                }
            }
        } else {
            $ticket = $this->info['jsapi_ticket'];
        }

        return $ticket;
    }

    private function getAccessToken()
    {
        if (empty($this->info['access_token']) || strtotime($this->info['access_token_expire_time']) < time()) {
            $url          = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res          = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $updateSql = 'update system_app set access_token=:access_token,access_token_expire_time=:access_token_expire_time where appid=:appid';
                $data      = array(':access_token' => $access_token, ':access_token_expire_time' => date('Y-m-d H:i:s', time() + 5000), ':appid' => $this->appId);
                $stmt      = $this->mysql->prepare($updateSql);
                try {
                    $stmt->execute($data);
                } catch (\Exception $e) {
                    file_put_contents(__DIR__ . '/error.log', $this->appId . '  access_token状态更新失败 ' . '  错误信息：'.json_encode($e->getTraceAsString()) . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
                }
                if ($stmt->rowCount() > 0) {
                    file_put_contents(__DIR__ . '/update.log', $this->appId . '  access_token状态更新成功 ' . ' ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents(__DIR__ . '/update.log', $this->appId . '  access_token状态更新失败 ' . ' ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
                }
            }
        } else {
            $access_token = $this->info['access_token'];
        }

        return $access_token;
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