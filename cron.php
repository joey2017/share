<?php

$apiToken = '797698a1ca9af4f3a164403620d47bf0';

try {
    $mysql = new PDO('mysql:host=127.0.0.1;port=3306;dbname=wx;', 'root', 'xiaomi_183..');
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\Exception $e) {
    //throw $e;
}

//域名列表 （可能为空）
$sql = "select * from system_domain where status = 1 and is_deleted = 0 order by sort asc,id desc";

$domainList = getDataFromMysql($mysql, $sql);

if (empty($domainList)) {
    exit();
}

foreach ($domainList as $item) {
    if (false === domainCheck($apiToken, $item['name'])) {
        $sql  = "UPDATE `system_domain` SET `status`=:name WHERE `id`=:id";
        $stmt = $mysql->prepare($sql);
        $stmt->execute(array(':name' => '0', ':id' => $item['id']));
        if ($stmt->rowCount() > 0) {
            file_put_contents('domain.log','域名状态更新成功 '.json_encode($item).'  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
        } else {
            file_put_contents('domain.log','域名状态更新失败 '.json_encode($item).' '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
        }
    }
}

//==========================================================================================================//
/**
 * @param $mysql  mysql资源链接
 * @param $sql    sql语句
 * return mix
 */
function getDataFromMysql($mysql, $sql)
{
    if (empty($mysql) || empty($sql)) {
        return false;
    }
    //获得结果集
    $results = $mysql->query($sql);

    if (empty($results)) {
        return [];
    }

    $data = [];

    //遍历循环数据
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) { //从结果集中取出一组作为数组返回，该数组为一个关联数组
        $data[] = $row;
    }

    return $data;
}

/** 微信域名接口检测
 * @param $apiToken  您的 API Token，在用户中心可查询到
 * @param $reqUrl    需要检测的地址或域名
 * return code    返回码    9900:正常 | 9904:被封 | 9999:系统错误 | 139:token错误或无权限 | 402:超过调用频率  msg    错误消息    返回的错误消息
 */
function domainCheck($apiToken, $reqUrl)
{
    $url = sprintf("http://wz5.tkc8.com/manage/api/check?token=%s&url=%s", $apiToken, $reqUrl);
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $responseBody = curl_exec($ch);
    $responseArr  = json_decode($responseBody, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        // echo "JSON 解析接口结果出错\n";
        file_put_contents('ApidomainCheck.log','JSON 解析出错  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
        return 'JSON 解析出错';
    }
    if (isset($responseArr['code'])) {
        // 接口正确返回
        if ($responseArr['code'] == '9900') {
            //file_put_contents('ApidomainCheck.log',$reqUrl.'域名正常  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
            return true;
        } else if ($responseArr['code'] == '9904') {
            file_put_contents('ApidomainCheck.log',$reqUrl.'域名被封了  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
            return false;
        } else if ($responseArr['code'] == '139') {
            file_put_contents('ApidomainCheck.log','用户没有权限  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
            return '用户没有权限';
        } else if ($responseArr['code'] == '402') {
            file_put_contents('ApidomainCheck.log','频率过快  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
            return '频率过快';
        }
    } else {
        // printf("接口异常：%s\n", var_export($responseArr, true));
        file_put_contents('ApidomainCheck.log','api error  '.date('Y-m-d H:i:s').PHP_EOL,FILE_APPEND);
        return 'api error';
    }
}