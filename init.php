<?php

//=====================测试服务器哦===========================//
//define('SQL_HOST', '127.0.0.1');//数据库地址
//define("SQL_USER", "wx");//数据库用户名
//define("SQL_PASSWORD", "root");//数据库密码
//define("SQL_DATABASE", "xiaomi_183..");//连接的数据库名字
//define("SQL_PORT", "3306");//数据库端口号,默认为3306
//================================================//

//=====================本地服务器哦===========================//
//define('SQL_HOST', '127.0.0.1');//数据库地址
//define("SQL_USER", "root");//数据库用户名
//define("SQL_PASSWORD", "root");//数据库密码
//define("SQL_DATABASE", "admin_v3");//连接的数据库名字
//define("SQL_PORT", "3306");//数据库端口号,默认为3306
//================================================//

// redis（保留项）
//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);
//echo "Connection to server successfully";
////查看服务是否运行
//echo "Server is running: " . $redis->set('aaa','{"name":"liming"}');
//print_r(json_decode($redis->get('aaa'),true));

// php7
try {
    $mysql = new PDO('mysql:host=127.0.0.1;port=3306;dbname=admin_v3;', 'root', 'root');
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\Exception $e) {
    //throw $e;
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


//公众号查询语句
$sql = "select * from system_app where status = 1 and is_deleted = 0 order by id desc limit 1";

$appsArray = [];

$tempdata = getDataFromMysql($mysql, $sql);

foreach ($tempdata as $v) {
    $appsArray = $v;
}


//视频列表
$sql = "select * from system_video where status = 1 and is_deleted = 0 order by sort asc,id desc limit 1";

$videoList = [];

$tempdata = getDataFromMysql($mysql, $sql);

foreach ($tempdata as $v) {
    $videoList = $v;
}

//域名列表 （可能为空）
$sql = "select * from system_domain where status = 1 and is_deleted = 0 order by sort asc,id desc";

$domainList = getDataFromMysql($mysql, $sql);

if (empty($domainList)) {
    exit();
}

//分享设置
//$sql = "select * from system_share where status = 1 and is_deleted = 0 order by type asc,sort asc,id desc";

//$shareList = getDataFromMysql($mysql, $sql);
//
//$friendTime  = 0;
//$circlesTime = 0;
//foreach ($shareList as $item) {
//    if ($item['type'] == 1) {
//        $friendTime += 1;
//    } else {
//        $circlesTime += 1;
//    }
//}
//
//$shareTime_first  = $shareList[0]['content'];
//$shareTime_second = $shareList[1]['content'];
//$shareTime_third  = $shareList[2]['content'];
//$shareTime_fourth = $shareList[3]['content'];
//$shareTime_fifth  = $shareList[4]['content'];

//系统配置数据
//$appid     = isset($systemSetting['appid']) ? $systemSetting['appid'] : '';
//$appsecret = isset($systemSetting['appsecret']) ? $systemSetting['appsecret'] : '';

//使用公众号列表的数据
$appid     = isset($appsArray['appid']) ? $appsArray['appid'] : '';
$appsecret = isset($appsArray['appsecret']) ? $appsArray['appsecret'] : '';

//非微信访问跳转
$notwxlink = isset($systemSetting['not_wx_link']) ? $systemSetting['not_wx_link'] : 'http://bbs.sasadown.cn/?id=not';

//入口域名
$safe_link = [];

//落地域名
$share_link = [];

foreach ($domainList as $do) {
    if ($do['type'] == 1) { //入口域名（公众号安全域名）
        $safe_link[] = $do['name'];
    } else if ($do['type'] == 2) { //落地域名（公众号安全域名）
        $share_link[] = $do['name'];
    }
}

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
//公众号名称对应链接(绑定的js安全域名)
$name_link = array(
    'http://bbs.sasadown.cn/?id=100',
    'http://bbs.sasadown.cn/?id=200',
    'http://bbs.sasadown.cn/?id=300'
);
//阅读全文对应链接
$read_link = array(
    'http://bbs.sasadown.cn/?id=abc100',
    'http://bbs.sasadown.cn/?id=abc200',
    'http://bbs.sasadown.cn/?id=abc300'
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
//好友分享
$wxtitle = $systemSetting['friend_title'];
$wxdesc  = $systemSetting['friend_desc'];
$wximg   = $systemSetting['friend_image'];

//朋友圈分享
$pyqtitle = $systemSetting['circles_title'];
$pyqdesc  = $systemSetting['circles_desc'];
$pyqimg   = $systemSetting['circles_image'];

//腾讯视频VID
$vid = $videoList['vid'];

//视频标题
$videoTitle = $videoList['title'];

//日期
$date = date('Y-m-d');

//统计代码
$statistics = <<<EOT
<script type="text/javascript" src="https://s23.cnzz.com/z_stat.php?id=1276340612&web_id=1276340612"></script>
EOT;

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

/** 删除数组中指定的值
 * @param $arr      目标一维数组
 * @param $value    需要删除的数组值
 * return array     删除特定值后的数组
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