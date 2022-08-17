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

</body>
</html>