<?php
session_start();
if (empty($_SESSION['appsArray']) || $_SESSION['appsArray']['expire_time'] < time()) {
    try {
        $db = new PDO('mysql:host=120.79.150.65;port=3306;dbname=wx;', 'mysql', 'XFkj!@#$8888');
        //设置字符集
        $db->query('set names utf8');
    } catch (PDOException $e) {
        file_put_contents(__DIR__.'/mysql_error.log',$e->getMessage().PHP_EOL,FILE_APPEND);
        die('error');
    }
    $sql  = "select * from system_app where status = 1 and is_deleted = 0 order by sort asc";
    $res  = $db->query($sql);
    $data = $res->fetchAll();

    if (empty($data)) {
        header('Location:http://www.baidu.com/');exit();
    } else {
        $domain  = $data[mt_rand(0, count($data,0) - 1)]['bind_domain_ld'];
        $_SESSION['appsArray'] = $domain;
        $_SESSION['appsArray']['expire_time'] = time() + 60;
    }

} else {
    $domain = $_SESSION['appsArray']['bind_domain_ld'];
}
$orign = $_SERVER['REQUEST_URI'];

$redirect = 'http://' . getRandChar(10) . $domain . '/get'.$orign;

header('Location:' . $redirect);
exit();

function getRandChar($length)
{
    $str    = '';
    $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";//小写字母以及数字
    $max    = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[mt_rand(0, $max)];
    }
    return $str . '.';
}