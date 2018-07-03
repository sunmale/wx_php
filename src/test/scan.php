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
<h2 style="text-align: center;font-size: 16px; padding: 10px;">微信扫一扫支付</h2>
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
  <button type="submit" id="scan"  data-target="#show" class="weui-form-preview__btn weui-form-preview__btn_primary open-popup" href="javascript:">生成二维码</button>
 </div>
</div>

<div id="show" class="weui-popup__container" style="background-color: #fff;">
 <div class="weui-popup__overlay"></div>
 <div class="weui-popup__modal">
  <h2 style="font-size: 16px; text-align: center;padding: 10px;">你购买商品的二维码是</h2>

  <div style="text-align: center;margin-top: 50px; ">
   <img id="img"  style="display: inline-block; " />
      <div id="qrcodeCanvas" style="display: none;" ></div>
  </div>
  <a href="javascript:;" class="weui-btn weui-btn_primary close-popup" style="margin-top: 50px; width: 98%;margin-left: 1%;">关闭</a>
 </div>
</div>
</body>

<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/jquery-weui.min.js"></script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/swiper.min.js"></script>
<script src="//cdn.bootcss.com/jquery-weui/1.0.1/js/city-picker.min.js"></script>
<!--引入生成二维码的js库-->
<script src="../assert/js/jquery.qrcode.min.js"></script>
  <script>
    $(function () {
        $('#scan').on('click',function () {
            $.toast("订单生成中..", 500);
            $.ajax({
                type : "post",
                url : "handle.php",
                data:{
                  pay:'scan'
                },
                success : function(res){
                    var data = eval('(' + res + ')');
                    /*生成二维码的方法*/
                    var  qr =   jQuery('#qrcodeCanvas').qrcode({
                        render :"canvas",
                        text : data.url,
                        width : "200",               //二维码的宽度
                        height : "200",              //二维码的高度
                        background : "#ffffff",       //二维码的后景色
                        foreground : "#000000"  //二维码的前景色
                    });
                    var canvas=qr.find('canvas').get(0);
                    $('img#img').attr('src',canvas.toDataURL('image/jpg'));
                }
            });

        })
    })

  </script>

</html>