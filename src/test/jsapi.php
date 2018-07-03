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
<h2 style="text-align: center;font-size: 16px; padding: 10px;">微信公众号支付</h2>
<div class="weui-form-preview">
    <div class="weui-form-preview__hd">
        <label class="weui-form-preview__label">付款金额</label>
        <em class="weui-form-preview__value">¥0.01</em>
    </div>
    <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">商品</label>
            <span class="weui-form-preview__value">电动打蛋机</span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">标题标题</label>
            <span class="weui-form-preview__value">名字名字名字</span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">标题标题</label>
            <span class="weui-form-preview__value">很长很长的名字很长很长的名字很长很长的名字很长很长的名字很长很长的名字</span>
        </div>
    </div>
    <div class="weui-form-preview__ft">
        <button type="submit" id="pay" class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">支付</button>
    </div>
</div>
</body>

<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/jquery-weui.min.js"></script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/swiper.min.js"></script>
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/city-picker.min.js"></script>

<script>
    $(function () {
        $('#pay').on('click',function () {
            $.post("handle.php",{pay:'jsapi'},function (res) {
                var data = eval('(' + res + ')');
                if(data.code==1){
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest', {
                            "appId":data.appId,     //公众号名称，由商户传入
                            "timeStamp":data.timeStamp,         //时间戳，自1970年以来的秒数
                            "nonceStr":data.nonceStr, //随机串
                            "package":data.package,
                            "signType":"MD5",         //微信签名方式：
                            "paySign":data.paySign //微信签名
                        },
                        function(res){
                            if (res.err_msg == "get_brand_wcpay_request:ok") {
                                $.toast("支付成功");
                            } else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                                $.toptip('取消支付', 'error');
                            }else{
                                $.alert(/*res.err_code+*/res.err_desc/*+res.err_msg*/);
                            }
                        }
                    );

                }else{
                    $.toast(data.msg, "cancel");
                }
            });

        });

    })

</script>

</html>