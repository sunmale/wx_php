<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>微信开发</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="//cdn.bootcss.com/weui/1.1.1/style/weui.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/jquery-weui/1.0.1/css/jquery-weui.min.css">
</head>
<body>

<h2 style="text-align: center; font-size: 16px;padding: 5px; margin-top: 10px;">微信支付</h2>
<div class="weui-grids" style="margin-top: 10px;">
    <a href="jsapi.php" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="../assert/img/jsapi.jpg" alt="">
        </div>
        <p class="weui-grid__label">
            JSAPI支付
        </p>
    </a>
    <a href="scan.php" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="../assert/img/scan.jpg" alt="">
        </div>
        <p class="weui-grid__label">
            扫一扫支付
        </p>
    </a>

    <a href="h5.php" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="" alt="">
        </div>
        <p class="weui-grid__label">
            H5支付
        </p>
    </a>

    <a href="h5.php" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="" alt="">
        </div>
        <p class="weui-grid__label">
            企业付款
        </p>
    </a>

    <a href="" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="../assert/img/redpacket.jpg" alt="">
        </div>
        <p class="weui-grid__label">
            微信发红包
        </p>
    </a>
</div>


<h2 style="text-align: center; font-size: 16px;padding: 5px; margin-top: 10px;">微信API</h2>
<div class="weui-grids" style="margin-top: 10px;">
    <a href="handle.php?api=web_licensing" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="../assert/img/jsapi.jpg" alt="">
        </div>
        <p class="weui-grid__label">
            微信网页授权
        </p>
    </a>

     <!-- <a href="handle.php?api=tunnel" class="weui-grid js_grid">
        <div class="weui-grid__icon">
            <img src="../assert/img/jsapi.jpg" alt="">
        </div>
        <p class="weui-grid__label">
            微信中转
        </p>
    </a> -->

</div>


</body>

<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/jquery-weui.min.js"></script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/swiper.min.js"></script>
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/city-picker.min.js"></script>

</html>
