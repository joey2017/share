$(function () {
    var h = $('#scroll').height();
    $('#scroll').css('height', h > window.screen.height ? h : window.screen.height + 1);
    new IScroll('#wrapper', {useTransform: false, click: true});

    // 设置自动跳转
    var delayId;
    delayId = setTimeout(function () {
        $('#loadingToast').show();
        delayId = setTimeout(function () {
            jump(pageGlobal.dockUrl);
        }, 5000);
    }, 8000);

    vuxalert('网速不好，请分享到 <span style="font-size: 30px;color: #f5294c">3</span> 个不同的微信群才可以继续观看！');

    var globalConfig = {};
    globalConfig.jssdkUrl = "/jssdkphpversion/getversion.php";
    var pars = {};
    pars.url = location.href.split('#')[0];
    var shareATimes = 0;
    var shareTTimes = 0;

    $.ajax({
        type: "POST",
        url: globalConfig.jssdkUrl,
        dataType: "json",
        data: pars,
        success: function (dat) {
            wx.config({
                debug: false,
                appId: dat.appid,
                timestamp: parseInt(dat.timestamp),
                nonceStr: dat.nonce,
                signature: dat.signature,
                jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'hideAllNonBaseMenuItem', 'showMenuItems', 'closeWindow']
            });

            wx.ready(function () {
                clearTimeout(delayId);
                wx.hideAllNonBaseMenuItem();
                wx.showMenuItems({menuList: ['menuItem:share:appMessage']});
                args_qun.success = function () {
                    shareATimes += 1;
                    if (shareATimes == 1) {
                        args_qun.title  = shareADatas[1]['title'];
                        args_qun.imgUrl = shareADatas[1]['link'];
                        args_qun.desc   = shareADatas[1]['desc'];
                    } else if (shareATimes == 2) {
                        args_qun.title  = shareADatas[2]['title'];
                        args_qun.imgUrl = shareADatas[2]['link'];
                        args_qun.desc   = shareADatas[2]['desc'];
                    }
                    //wx.hideAllNonBaseMenuItem();
                    //wx.showMenuItems({menuList: ['menuItem:share:timeline']});
                    share_tip(shareATimes, shareTTimes);
                    //vuxalert('分享成功，请分享到 <span style="font-size: 30px;color: #f5294c">朋友圈</span> 即可继续观看！');
                };

                wx.onMenuShareAppMessage(args_qun);

                args_quan.success = function () {
                    shareTTimes += 1;
                    if (shareTTimes == 1) {
                        args_quan.title  = shareTDatas[1]['title'];
                        args_quan.imgUrl = shareTDatas[1]['link'];
                    }
                    share_tip(shareATimes, shareTTimes);
                    //jump(pageGlobal.dockUrl);
                };
                wx.onMenuShareTimeline(args_quan);
            });

            wx.error(function (res) {
                console.log(res);
            });
        }
    });
});

function jump(url) {
    var a = document.createElement('a');
    a.setAttribute('rel', 'noreferrer');
    a.setAttribute('id', 'm_noreferrer');
    a.setAttribute('href', url);
    document.body.appendChild(a);
    document.getElementById('m_noreferrer').click();
    document.body.removeChild(a);
}

function share_tip(share_app_times, share_timeline_times) {
    if (share_app_times < 3) {
        if (share_app_times == 2) {
            vuxalert(shareADatas[2]['content'])
        } else {
            vuxalert(shareADatas[1]['content']);
        }
    } else {
        wx.hideOptionMenu();
        wx.showMenuItems({menuList: ['menuItem:share:timeline']});
        if (share_timeline_times < 1) {
            vuxalert(shareTDatas[0]['content'])
        } else {
            if (share_timeline_times == 1) {
                vuxalert(shareTDatas[1]['content'])
            } else {
                jump(pageGlobal.dockUrl);
            }
        }
    }
}