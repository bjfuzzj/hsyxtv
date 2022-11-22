<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/pubstatic/css/common.css">
    <link rel="stylesheet" href="/pubstatic/css/special_page.css?20221008">
    <title>横屏专题详情页-五象孵化器</title>
    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
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

<body>
    <!--为了方便模板去填地址，专题的背景一般是可以配合的，模板渲染时自己处理即可-->
    <img id="special_bg" src="{{ $resData['bg'] }}" />
    <!--为了方便模板去填地址，填代码，这里所有的焦点元素全部使用a标签处理-->
    <div class="content-area">
        <div id="content_list" class="content-list">

            @foreach ($resData['video_list'] as $video)
                <a id="video_list_item__{{$loop->index}}" class="video-item" 
                    href="javascript:openPage('./detail.php?id={{$video['meida_id']}}')">
                    <img class="poster-img" src="{{$video->poster}}" alt="">
                    <div class="video-item-name">{{$resData['title_list'][$video['meida_id']]}}</div>
                </a> 
            @endforeach
        </div>
    </div>
    <!--日志盒子-->
    <div id="log_box"></div>
</body>

</html>
<script type="text/javascript" src="/pubstatic/js/common.js"></script>
<script type="text/javascript" src="/pubstatic/js/page/special_page.js?2011"></script>
<script>
    $("touch_back_btn").style.display = "none";
</script>
