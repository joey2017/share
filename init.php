<?php

// redis（保留项）
//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);
//echo "Connection to server successfully";
////查看服务是否运行
//echo "Server is running: " . $redis->set('aaa','{"name":"liming"}');
//print_r(json_decode($redis->get('aaa'),true));

// php7
try {
    //$mysql = new PDO('mysql:host=127.0.0.1;port=3306;dbname=wx;', 'root', 'XFkj!@#$8888');
    $mysql = new PDO('mysql:host=127.0.0.1;port=3306;dbname=admin_v3;', 'root', 'root');
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    file_put_contents(__DIR__.'/mysql_error.log',$e->getMessage().PHP_EOL,FILE_APPEND);
    die('error');
}

$sql = "SELECT * FROM system_config";

$systemSetting = [];

$tempdata = getDataFromMysql($mysql, $sql);

if (!empty($tempdata)) {
    foreach ($tempdata as $v) {
        $systemSetting[$v['name']] = $v['value'];
    }
} else {
    exit();
}

$hostname = explode('.',$_SERVER['HTTP_HOST'],2);
$domain = $hostname[1];

//公众号查询语句
//$sql = "select * from system_app where status = 1 and is_deleted = 0 order by sort asc, id desc limit 1";
$sql = "select * from system_app where status = 1 and is_deleted = 0 and bind_domain_ld = '".$domain."' order by sort asc";

$appsList = getDataFromMysql($mysql, $sql);
//$appsArray = $appsList[0];
$appsArray = $appsList[mt_rand(0, count($appsList,0) - 1)];

//视频列表
$sql = "select * from system_video where status = 1 and is_deleted = 0 order by sort asc,id desc limit 1";

$videoList = [];

$tempdata = getDataFromMysql($mysql, $sql);

foreach ($tempdata as $v) {
    $videoList = $v;
}

//使用公众号列表的数据
$appid     = isset($appsArray['appid']) ? $appsArray['appid'] : '';
$appsecret = isset($appsArray['appsecret']) ? $appsArray['appsecret'] : '';

//非微信访问跳转
$notwxlink = isset($systemSetting['not_wx_link']) ? $systemSetting['not_wx_link'] : '//open.tencent.com/';

//群入口域名
$safe_link_qun = $appsArray['bind_domain_qun'];

//圈入口域名
$safe_link_quan = $appsArray['bind_domain_quan'];

//落地域名
$share_link = $appsArray['bind_domain_ld'];

//阅读量范围
$min_readcou = $videoList['read_min'];
$max_readcou = $videoList['read_max'];

//点赞数
$stars = $videoList['stars'];

//播放暂停时间
$video_play_seconds = $videoList['pause'];

//后退链接
$back_link = array(
    $systemSetting['back_link_1'],
    $systemSetting['back_link_2'],
    $systemSetting['back_link_3'],
);
//热门劲爆视频链接
$name_link = array(
    $notwxlink,
    $notwxlink,
    $notwxlink
);
//阅读全文对应链接
$read_link = array(
    $systemSetting['back_link_1'],
    $systemSetting['back_link_2'],
    $systemSetting['back_link_3'],
);
//底部广告对应链接
$footer_link = array(
    $systemSetting['ad_link_1'],
    $systemSetting['ad_link_2'],
    $systemSetting['ad_link_3'],
);
$footer_img  = array(
    $systemSetting['ad_link_img_1'],
    $systemSetting['ad_link_img_2'],
    $systemSetting['ad_link_img_3'],
);

//腾讯视频VID
$vid = $videoList['vid'];

//视频标题
$videoTitle = $videoList['title'];

//日期
$date = date('Y-m-d');

$wxdesc = '今日爆点';

//统计代码
$statistics = <<<EOT
<script type="text/javascript" src="https://s23.cnzz.com/z_stat.php?id=1276340612&web_id=1276340612"></script>
EOT;

include 'crypt.php';

Urlencry::setKey('8888.xunfeng.com.cn');

$shareUrlArgs = Urlencry::encrypt_url('share=true&t=' . time());
//==========================================================================================================//
/**
 * @param $mysql  mysql资源链接
 * @param $sql    sql语句
 * @return mix
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

/** 删除数组中指定的值
 * @param $arr      目标一维数组
 * @param $value    需要删除的数组值
 * @return array     删除特定值后的数组
 */
function delByValue($arr, $value)
{
    if (!is_array($arr)) {
        return $arr;
    }
    foreach ($arr as $k => $v) {
        if ($v == $value) {
            unset($arr[$k]);
        }
    }
    return $arr;
}

/**微信浏览器检测
 * @version  1.0
 * @param
 * @return array | boolean
 */
function isWechat()
{
    if (array_key_exists('HTTP_USER_AGENT', $_SERVER) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && false === stripos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools')) {
        # code...
        return true;
    }
    return false;
}

/**移动端检测
 * @version  1.0
 * @param
 * @return array | boolean
 */
function isMobile()
{
    $useragent               = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    function CheckSubstrs($substrs, $text)
    {
        foreach ($substrs as $substr)
            if (false !== strpos($text, $substr)) {
                return true;
            }
        return false;
    }

    $mobile_os_list    = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

    $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
        CheckSubstrs($mobile_token_list, $useragent);

    if ($found_mobile) {
        return true;
    } else {
        return false;
    }
}

?>
