<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/pubstatic/css/common.css?122">
    <link rel="stylesheet" href="/pubstatic/css/home_page.css?2021112504">
    <title>栏目名称-首页</title>
    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u = "//tongji.yiqiqw.com/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d = document,
                g = d.createElement('script'),
                s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
    <!-- End Matomo Code -->
</head>

<body class="player-live">

    <img class="home-log" src="{{$redData['logo_pic']}}" />
    <div id="top_bar_area" class="top-bar-area">
        <div class="top-btn-area" id="top-btn-area">
            <a id="top_btn__0" class="top-btn" href="javascript:openPage('./myhistory.php')">
                <div class="top-btn-icon"></div>
                <div class="top-btn-text">观看记录</div>
            </a>
            <a id="top_btn__1" class="top-btn short-btn" href="javascript:openPage('')">
                <div class="top-btn-icon"></div>
                <div class="top-btn-text">&nbsp;设&nbsp;置</div>
            </a>

            <a id="top_btn__2" class="top-btn short-btn">
                <div class="top-btn-icon"></div>
                <div class="top-btn-text">&nbsp;关&nbsp;于</div>
            </a>
            <a id="top_btn__3" style="display:none" class="top-btn short-btn" href="javascript:openPage('')">
                <div class="top-btn-icon"></div>
                <div class="top-btn-text">&nbsp;大&nbsp;屏</div>
            </a>
            <script>
                var screenUrl = "";
            </script>
        </div>

        <div id="time_show" class="time-show"></div>
    </div>
    <div id="category_area" class="category-area">
        <a id="category_item__0" class="category-item" href="javascript:openPage('/lanmu-1.html')">首页</a><a
            id="category_item__1" class="category-item" href="javascript:openPage('/lanmu-2.html')">乡村振兴</a><a
            id="category_item__2" class="category-item" href="javascript:openPage('/lanmu-3.html')">党务管理</a><a
            id="category_item__3" class="category-item" href="javascript:openPage('/lanmu-4.html')">直播入口</a><a
            id="category_item__4" class="category-item" href="javascript:openPage('/lanmu-5.html')">党晓云介绍</a>
    </div>
    <div class="rec-content-area">
        <div id="rec_move_area" class="rec-move-area">
            <div class="rec-group rec-group-first">
                <div id="rec_group_first__0" class="rec-play-win">
                    <a id="play_url" style="display: none"
                        href="http://lvs.hunancatv.com:8060/live/CCTV-13_2000.m3u8?channelid=cl-chan_101_8a3ecebc-b630-4254-8531-b80c00711c46&token=u1%2FtkGu%2BGAY%2FG7jMRpZlxg%3D%3D&userid=310000010100006156&platform=8&location=074504&deviceid=310000010100006156&errorcode=0&resultCode=0000&sid=NBCF1ifjnD6LrAKz6k2juA%3D%3D&nonce=ta4Eh2LwdeAc&acl=1111&errorReason=&previewduration"></a>
                </div>
                <a id="rec_group_first__1" class="rec-first-poster-0"
                    href="javascript:openPage('http://dxy.yiqiqw.com/ztxqy-10.html')">
                    <img class="poster-img" src="https://v.static.yiqiqw.com/pic/9582672d0487d65468a314cd1737b131.jpg" alt="">
                </a>
                <a id="rec_group_first__2" class="rec-first-poster-1"
                    href="javascript:openPage('https://v.static.yiqiqw.com/pic/b2aaaa9dc89273bcdbf8df637d1b6dcf.jpg')">
                    <img class="poster-img" src="https://v.static.yiqiqw.com/pic/6a7bf331930e972385b9019de31d5269.jpg" alt="">
                </a>
                <a id="rec_group_first__3" class="rec-first-poster-2"
                    href="javascript:openPage('https://v.static.yiqiqw.com/pic/0c7e2e3d108369714d25b9981c24324a.png')">
                    <img class="poster-img" src="https://v.static.yiqiqw.com/pic/023486bc2c17c8a71fb64aae07001f51.jpg" alt="">
                </a>
                <a id="rec_group_first__4" class="rec-first-poster-3"
                    href="javascript:openPageOut('http://dxy.yiqiqw.com/fourzty-7.html')">
                    <img class="poster-img" src="https://v.static.yiqiqw.com/pic/b248221efc42df3a948742ad613bbd1d.jpg" alt="">
                </a>
                <a id="rec_group_first__5" class="rec-first-poster-4"
                    href="javascript:openPageOut('http://dxy.yiqiqw.com/fourzty-8.html')">
                    <img class="poster-img" src="https://v.static.yiqiqw.com/pic/1d2d91afa63a68543ba6ad44fdc7aa25.jpg" alt="">
                </a>
                <a id="rec_group_first__6" class="rec-first-poster-5"
                    href="javascript:openPage('https://www.12371.cn/2022/10/17/ARTI1665990592023497.shtml')">
                    <img class="poster-img" src="https://v.static.yiqiqw.com/pic/a47444bfbb4e9c34975784d3c665e30a.jpg" alt="">
                </a>
                <a id="rec_group_first__7" class="rec-first-poster-6"
                    href="javascript:openPage('https://www.12371.cn/2022/10/19/ARTI1666182797421631.shtml')">
                    <img class="poster-img"
                        src="https://v.static.yiqiqw.com/pic/5e06d4c4ce2f2944a779aeb03630ce46.jpg" alt="">
                </a>
            </div>
        </div>
    </div>
    <!--预加载背景用-->
    <img src="https://v.static.yiqiqw.com/pic/33b5e533ef2df86337f6c6becbf152f0.png" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/b1c9e677f74f53173039b4e57f5ab6d0.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/af487888ed4de371c8a5917d0b6b2dae.png" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/3d1e83ca1116854d244f9b89388c8e2e.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/66615f9ccd61a59ffc84e0b58263bd71.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/2434e349e95e97ca27dad76ebb6db4b9.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/3bb80c999453374549bc51d8038f03b0.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/4bb9e05e1d394e01bf564589b327f0c2.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/e801c83575711739f38679d8ea823e87.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/96862488f39fe061e8038472d11b447f.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/23bdec16123a24efaa9cf013ba8851b3.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/23bdec16123a24efaa9cf013ba8851b3.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/5329219492a7fdcac84f70741377baa9.jpg" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/af487888ed4de371c8a5917d0b6b2dae.png" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/3d1e83ca1116854d244f9b89388c8e2e.jpg" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/af487888ed4de371c8a5917d0b6b2dae.png" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/3d1e83ca1116854d244f9b89388c8e2e.jpg" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/af487888ed4de371c8a5917d0b6b2dae.png" style="display: none;">
    <img src="https://v.static.yiqiqw.com/pic/3d1e83ca1116854d244f9b89388c8e2e.jpg" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <img src="" style="display: none;">
    <div id="log_box"></div>
</body>

</html>
<script type="text/javascript" src="./pubstatic/js/common.js?20221016"></script>
<script>
    var scrollTop = 0;
    var goPageCatch = {
        st: scrollTop,
        fa: "category_item",
        fi: 0
    };
</script>

<script type="text/javascript" src="/pubstatic/js/page/home_page.js?2022102301"></script>
<script>
    function openPageOut(url) {
        try {
            sessionStorage.setItem("home_page", JSON.stringify(goPageCatch));
            //跳转页面需要关闭播放器
            playObj.stop();
        } catch (e) {
            //出异常了，就存不了了而已
        }
        window.location.href = url;
    }

    function openAppPage(url) {
        var gx_oui = xlab.setprop("XLAB_SYSTEM_TRY_GETPROP", "OUI");
        if (gx_oui == "21" && url.indexOf("tohome") != -1) {
            var keys = "URL";
            var packInfo =
                "{'packageName':'cn.ccdt.vodplayer','className':'cn.ccdt.vodplayer.vodbrowser.VodBrowser','URL':'http://10.2.127.4/tangdou/tohome.html'}";
            xlab.setAppInfo(packInfo, keys);
        } else if (gx_oui == "26" && url.indexOf("tohome") != -1) {
            var keys = "url";
            var packInfo =
                '{"className":"com.ipanel.webapp.BrowserActivity","packageName":"com.ipanel.android_webview.shell","url":"http://10.2.127.4/tangdou/tohome.html"}';
            xlab.setAppInfo(packInfo, keys);
        } else if (url.indexOf(".php") != -1 || url.indexOf(".yiqiqw") != -1 || url.indexOf("http://10") != -1) {
            try {
                playObj.stop();
            } catch (e) {

            }
            openPage(url)
            //window.location.href = url;
        } else if (url == 'app') {
            var keys = "";
            var packInfo = '{"className":"","packageName":"com.hpplay.happyplay.aw"}';
            xlab.setAppInfo(packInfo, keys);
        } else {
            openPageOut(url)
        }
    }

    $("touch_back_btn").style.display = "none";

    function openFocusAppPage(url) {
        try {
            sessionStorage.setItem("home_page", JSON.stringify(goPageCatch));
            //跳转页面需要关闭播放器
            playObj.stop();
        } catch (e) {
            //出异常了，就存不了了而已
        }
        //openPage(url)
        openAppPage(url);
    }
</script>

<script>
    fm.addArea("rec_group_first", {
        0: [null, 1, 4, null],
        1: [null, 2, 6, 0],
        2: [null, null, 3, 1],
        3: [2, null, 7, 1],
        4: [0, 5, null, null],
        5: [0, 6, null, 4],
        6: [1, 7, null, 5],
        7: [3, null, null, 6],
        total: 8,
        type: 2,
        idx: 0
    }).on("focus", function(ele, idx, key) {
        if (scrollTop !== 0) {
            scrollTop = 0;
            document.body.className = "player-live";
            $("rec_move_area").style.top = "10px";
            //如果播放成功了，这里下面获取焦点的时候播放器暂停
            if (playResultFlag == "1") {
                playObj.resume();
            }
        }
    }).on("focusout", function(ele, idx, key) {
        if (key === "down") {
            var tempIdx = 0;
            if (idx === 5) {
                tempIdx = 2;
            } else if (idx >= 6) {
                tempIdx = 3;
            }
            fm.setFocus("rec_list_item", tempIdx);
        } else if (key === "up") {
            fm.setFocus("category_item", 0);
        }
        return null;
    }).on("click", function(ele, idx) {
        if (idx === 0) {
            goPageCatch.st = scrollTop;
            goPageCatch.fa = "rec_group_first";
            goPageCatch.fi = idx;
            openPage("http://dxy.yiqiqw.com/play_live_page-1.html?idx=0");
        } else {
            goPageCatch.st = scrollTop;
            goPageCatch.fa = "rec_group_first";
            goPageCatch.fi = idx;
            ele.click();
        }
    });
    $("rec_group_first__0").addEventListener("touchstart", function() {
        goPageCatch.st = scrollTop;
        goPageCatch.fa = "rec_group_first";
        goPageCatch.fi = 0;
        openPage("http://dxy.yiqiqw.com/play_live_page-1.html?idx=0");

    });
</script>


<script>
    var tmp_date_show = $("date_show");
    if (tmp_date_show) {
        $("top-btn-area").setAttribute('style', "right:200px");
        $("date_show").setAttribute('style', "width:100px;right:93px");
    }

    fm.setFocus(goPageCatch.fa, goPageCatch.fi);
</script>

<style type="text/css">
    body {
        background: url("https://v.static.yiqiqw.com/pic/b1c9e677f74f53173039b4e57f5ab6d0.jpg") no-repeat;
    }

    body.player-live {
        background: url("https://v.static.yiqiqw.com/pic/33b5e533ef2df86337f6c6becbf152f0.png") no-repeat;
    }
</style>