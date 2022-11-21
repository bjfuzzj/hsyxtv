<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/pubstatic/css/common.css?122">
    <link rel="stylesheet" href="/pubstatic/css/home_page.css?2021112504">
    <title>{{ $resData['tv_user']['username'] }}-首页</title>
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

    <img class="home-log" src="{{ $resData['liveIndex']['logo_pic'] }}" />
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
        @foreach ($resData['lanmu_list'] as $lanmu)
            <a id="category_item__{{$loop->index}}" class="category-item" href="javascript:openPage('{{$lanmu->url_1}}')">{{$lanmu->name}}</a>
        @endforeach
        {{-- <a id="category_item__0" class="category-item" href="javascript:openPage('/lanmu-1.html')">首页</a>
        <a id="category_item__1" class="category-item" href="javascript:openPage('/lanmu-2.html')">乡村振兴</a>
        <a id="category_item__2" class="category-item" href="javascript:openPage('/lanmu-3.html')">党务管理</a>
        <a id="category_item__3" class="category-item" href="javascript:openPage('/lanmu-4.html')">直播入口</a>
        <a id="category_item__4" class="category-item" href="javascript:openPage('/lanmu-5.html')">党晓云介绍</a> --}}
    </div>
    <div class="rec-content-area">
        <div id="rec_move_area" class="rec-move-area">
            <div class="rec-group rec-group-first">
                <div id="rec_group_first__0" class="rec-play-win">
                    <a id="play_url" style="display: none"
                        href="http://lvs.hunancatv.com:8060/live/CCTV-13_2000.m3u8?channelid=cl-chan_101_8a3ecebc-b630-4254-8531-b80c00711c46&token=u1%2FtkGu%2BGAY%2FG7jMRpZlxg%3D%3D&userid=310000010100006156&platform=8&location=074504&deviceid=310000010100006156&errorcode=0&resultCode=0000&sid=NBCF1ifjnD6LrAKz6k2juA%3D%3D&nonce=ta4Eh2LwdeAc&acl=1111&errorReason=&previewduration"></a>
                </div>

               
                @for ($i = 1; $i < 8; $i++)
                    <a id="rec_group_first__{{$i}}" class="rec-first-poster-{{$i-1}}" href="javascript:openPageOut('{{$resData['liveIndex']["link_$i"]}}')">
                    <img class="poster-img" src="{{ $resData['liveIndex']["pic_$i"] }}" alt="">
                    </a>
                @endfor



                {{-- <a id="rec_group_first__1" class="rec-first-poster-0"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_1']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_1']}}" alt="">
                </a>
                <a id="rec_group_first__2" class="rec-first-poster-1"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_2']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_2']}}" alt="">
                </a>
                <a id="rec_group_first__3" class="rec-first-poster-2"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_3']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_3']}}" alt="">
                </a>
                <a id="rec_group_first__4" class="rec-first-poster-3"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_4']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_4']}}" alt="">
                </a>
                <a id="rec_group_first__5" class="rec-first-poster-4"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_5']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_5']}}" alt="">
                </a>
                <a id="rec_group_first__6" class="rec-first-poster-5"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_6']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_6']}}" alt="">
                </a>
                <a id="rec_group_first__7" class="rec-first-poster-6"
                    href="javascript:openPageOut('{{$resData['liveIndex']['link_7']}}')">
                    <img class="poster-img" src="{{$resData['liveIndex']['pic_7']}}" alt="">
                </a> --}}
            </div>
        </div>
    </div>
    <!--预加载背景用-->
    @foreach ($resData['other_images'] as $images)
            <img src="{{$images}}" style="display: none;">
    @endforeach
    <div id="log_box"></div>
</body>

</html>
<script type="text/javascript" src="/pubstatic/js/common.js?20221016"></script>
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
        if (url.indexOf(".yiqiqw") != -1 || url.indexOf("http://10") != -1) {
            openPage(url)
        }else{
            window.location.href = url;
        }
    }
    $("touch_back_btn").style.display = "none";
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
