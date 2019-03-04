<?php

//=====================测试服务器哦===========================//
//define('SQL_HOST', '127.0.0.1');//数据库地址
//define("SQL_USER", "wx");//数据库用户名
//define("SQL_PASSWORD", "root");//数据库密码
//define("SQL_DATABASE", "xiaomi_183..");//连接的数据库名字
//define("SQL_PORT", "3306");//数据库端口号,默认为3306
//================================================//

//=====================本地服务器哦===========================//
define('SQL_HOST', '127.0.0.1');//数据库地址
define("SQL_USER", "root");//数据库用户名
define("SQL_PASSWORD", "root");//数据库密码
define("SQL_DATABASE", "admin_v3");//连接的数据库名字
define("SQL_PORT", "3306");//数据库端口号,默认为3306
//================================================//

// php5
/*$mysql = mysqli_connect(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_DATABASE, SQL_PORT) or die(mysqli_error());

//选择数据库
mysqli_select_db($mysql, "wx");

//查询语句
$sql = "select * from system_config";
//查询
$results = $mysql->query($sql);

$systemSetting = [];

//遍历循环数据
while ($row = mysqli_fetch_array($results)) {
    $systemSetting[$row['name']] = $row['value'];
}

//公众号查询语句
$sql = "select * from system_app where status=1 and is_deleted = 0 order by id desc limit 1";
//查询
$results = $mysql->query($sql);

$appsArray = [];

//遍历循环数据
while ($row = mysqli_fetch_array($results)) {
    $appsArray = $row;
}

//释放
mysqli_free_result($results);
//关闭连接
mysqli_close($mysql);*/

// php7
$con = new mysqli(SQL_HOST, SQL_USER, SQL_PASSWORD, SQL_DATABASE, SQL_PORT);

$con->query('set names utf8;');

$sql           = "SELECT * FROM system_config";
$results       = $con->query($sql);
$systemSetting = [];

while ($row = mysqli_fetch_array($results)) {
    $systemSetting[$row['name']] = $row['value'];
}

//公众号查询语句
$sql = "select * from system_app where status = 1 and is_deleted = 0 order by id desc limit 1";
//查询
$results = $con->query($sql);

$appsArray = [];

//遍历循环数据
while ($row = mysqli_fetch_array($results)) {
    $appsArray = $row;
}

$con->close();

//系统配置数据
$appid     = isset($systemSetting['appid']) ? $systemSetting['appid'] : '';
$appsecret = isset($systemSetting['appsecret']) ? $systemSetting['appsecret'] : '';

//使用公众号列表的数据
$appid     = isset($appsArray['appid']) ? $appsArray['appid'] : '';
$appsecret = isset($appsArray['appsecret']) ? $appsArray['appsecret'] : '';

//所在目录 根目录时留空
$diretory = explode('/', str_replace('\\', '/', __DIR__));
$dir      = array_pop($diretory);

//非微信访问跳转
$notwxlink = isset($systemSetting['not_wx_link']) ? $systemSetting['not_wx_link'] : 'http://bbs.sasadown.cn/?id=not';

//落地域名（公众号安全域名）
$safe_link = array(
    $systemSetting['safe_link']
);
//入口域名（公众号安全域名）
$share_link = array(
    $systemSetting['share_link']
);
//阅读量范围
$min_readcou = $systemSetting['read_min'];
$max_readcou = $systemSetting['read_max'];

//点赞数
$stars = $systemSetting['stars'];

//播放暂停时间
$video_play_seconds = $systemSetting['video_play_seconds'];
//后退链接
$back_link = array(
    $systemSetting['back_link_1'],
    $systemSetting['back_link_2'],
    $systemSetting['back_link_3'],
);
//公众号名称对应链接
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
//好友分享
$wxtitle = $systemSetting['friend_title'];
$wxdesc  = $systemSetting['friend_desc'];
$wximg   = $systemSetting['friend_image'];

//朋友圈分享
$pyqtitle = $systemSetting['circles_title'];
$pyqdesc  = $systemSetting['circles_desc'];
$pyqimg   = $systemSetting['circles_image'];

//腾讯视频VID
$vid = $systemSetting['video_link'];

//视频标题
$videoTitle = $systemSetting['video_title'];

//统计代码
$statistics = <<<EOT
<script type="text/javascript" src="https://s23.cnzz.com/z_stat.php?id=1276340612&web_id=1276340612"></script>
EOT;
?>