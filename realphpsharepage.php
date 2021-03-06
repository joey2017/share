<?php
include __DIR__.'/init.php';
if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    header('Location:' . $notwxlink);
    exit();
}

//if (!(isWechat() && isMobile())) {
//    header('Location:' . $notwxlink);
//    exit();
//}
if (!isset($_COOKIE[$vid])) {
    //header('Location:' . $name_link[mt_rand(0, count($name_link) - 1)]);
    //exit();
}

$html = <<<EOT
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>{$videoTitle}</title>
    <link rel="stylesheet" type="text/css" href="/assets/weui.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/main.css?ver=9999">
    <link rel="stylesheet" type="text/css" href="/assets/more.css">
    <link rel="stylesheet" type="text/css" href="/assets/swiper.min.css">
    <script src="/assets/jquery.min.js?ver=999"></script>
    <script src="/assets/jquery.cookie.js"></script>
    <script src="/assets/zepto.min.js"></script>
    <script src="/assets/iscroll-lite.min.js"></script>
</head>
<body id="activity-detail" class="zh_CN mm_appmsg" style="background-color:#333;">
<div id="content-content"  style="height:40px;text-align:center;padding-top:10px;color:#999;font-size:80%;display:block;">网页由 mp.weixin.qq.com 提供</div>
<div id="wrapper" style="position:absolute;top:0;bottom:0;left:0;right:0;">
    <div id="scroll" style="position:absolute;background-color:#f3f3f3;z-index:100;width:100%;">
        <img src="/assets/006V7Vesgy1fjrf04gci1j301s01pwe9.jpg" alt=""/>
        <div id="share" style="position:fixed;left:0;right:0;top:0;bottom:0;background-color:rgba(80,80,80,50);z-index:1000000;">
           <img style="width:100%" src="/assets/fxq.png"/>
        </div>
    </div>
    <div style="display:none">{$statistics}</div>
</div>
<div id="loadingToast" style="display:none;">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast" style="width:11em;margin-left:-5.5em">
        <i class="weui-loading weui-icon_toast"></i>
        <p class="weui-toast__content">
            <span style="font-size:110%;font-weight:bold;line-height:2em;">请稍等哦</span> <br>
            视频正在加载中
        </p>
    </div>
</div>
</body>
<script>
    var pageGlobal = {};
    pageGlobal.vid = '{$vid}';
    // pageGlobal.title = "{$wxtitle}";
    pageGlobal.link = "http://{$safe_link_qun}/index.html";
    // pageGlobal.imgUrl = "{$wximg}";
    // pageGlobal.desc = "{$wxdesc}";
    // pageGlobal.qtitle = "{$pyqtitle}";
    pageGlobal.qlink = "http://{$safe_link_quan}/index.html";
    // pageGlobal.qimgUrl = "{$pyqimg}";
    // pageGlobal.sMode = 'a';
    pageGlobal.dockUrl = 'http://{$_SERVER['HTTP_HOST']}/continue/{$shareUrlArgs}';
    // 分享数据
    var shareADatas = [];
    var shareTDatas = [];
    ajaxGetData(1);
    ajaxGetData(2);
    
    var args_qun = {
        title: shareADatas[0]['title'],
        link: shareADatas[0]['link'] ? shareADatas[0]['link'] : pageGlobal.link,
        imgUrl: location.hostname + shareADatas[0]['link'],
        desc: shareADatas[0]['desc']
    }
    
    var args_quan = {
        title: shareTDatas[0]['title'],
        link: shareTDatas[0]['link'] ? shareTDatas[0]['link'] : pageGlobal.qlink,
        imgUrl: location.hostname + shareTDatas[0]['link']
    };
    
    function ajaxGetData(type) {
        $.ajax({
            type: "POST",
            url: 'http://admin.ryanlaw.cn/index/index/getsharedata',
            dataType: "json",
            async: false,
            data: {type: type},
            success: function (data) {
                if (type == 1) {
                    shareADatas = data;
                } else {
                    shareTDatas = data;
                }
            }
        });
    }
    
</script>
<script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="/assets/continue_c.js?20171113999"></script>
</html>
EOT;
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>正在加载 . . . 请稍等 . . .</title>
    <script src="/assets/jquery.min.js"></script>
    <script src="/assets/base64.min.js"></script>
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
        
