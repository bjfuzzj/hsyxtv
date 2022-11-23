<html style="height: 100%;">
<head>
<meta name="viewport" content="width=device-width, minimum-scale=0.1">
<title>激活二维码展示</title>
</head>

<style type="text/css">
    body {
        background-color:black;
    }
    .content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 600px;
        margin-top: 30px;
        padding-bottom: 50px;
    }
    .title {
        display: flex;
        color:white;
        
    }
    .qrcode{
        display: flex;
        width: 300px;
        height: 300px;
        margin-top: 30px;
    }
    .qrcode img{
        width: 300px;
        height: 300px;
    }
    </style>
<body>

    <div class="content">
        <div class="title">请扫码关注公众号激活</div>
        <div class="qrcode">
            {{-- <img src="https://v.static.yiqiqw.com/pic/5d3a5de64f2a61648829f876feb180c1.jpg"> --}}
            <img src="{{$url}}">
        </div>
        
    </div>
    <div id="log_box"></div>


   


<script type="text/javascript" src="/js/common.js?20221017"></script>
<script>

    function openPage( url ){
        var pageParams = util.getUrlParamObj(location.href);
        if(pageParams.params.userid){
        if (url.indexOf("?") !=-1) {
                url += '&userid='+pageParams.params.userid
            }else{
                url += '?userid='+pageParams.params.userid
            }
        }
        if(pageParams.params.mac){
        url += '&mac='+pageParams.params.mac
        }
        if(pageParams.params.ip){
        url += '&ip='+pageParams.params.ip
        }
        if(pageParams.params.groupid){
        url += '&groupid='+pageParams.params.groupid
        }
        if(pageParams.params.session){
        url += '&session='+pageParams.params.session
        }
        window.location.href = url;
    }

    function checkUser(){
        var pageParams = util.getUrlParamObj(location.href);
        util.request("https://tv.yiqiqw.com/check_user", {
        userid : pageParams.params.userid || "0",
        }, function(res){
            console.log(res);
            if(res.code==200){
                var url = res.data.url;
                openPage(url);
            }else{
                setTimeout(() => {
                    checkUser()
                }, 2000);
            }
        });
    }
    checkUser();
    
</script>

</body>
</html>