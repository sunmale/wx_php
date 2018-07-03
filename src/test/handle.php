<?php

//composer 下载引用
//require '../vendor/autoload.php';

//下载直接引用
require '../../AutoLoad.php';

use wx\api\Wa;
use wx\Config;
use wx\Log;
use wx\pay\Util;
use wx\pay\Wp;

//异常处理
error_reporting(E_ALL);
ini_set('date.timezone', 'Asia/Shanghai');
set_error_handler(array('wx\\WxException', 'myErrorHandler'));
set_exception_handler(array('wx\\WxException', 'myExceptionHandler'));
register_shutdown_function(array('wx\\WxException', 'myShutdownHandler'));

//不同的支付类型
if (isset($_POST['pay'])) {
    switch ($_POST['pay']) {
        case 'jsapi':
            jsapi_pay();
            break;
        case 'scan':
            scan_pay();
            break;
        case 'h5':
            h5_pay();
            break;
    }
}

/**
 * get请求
 * 通过不同的api参数调用不同的方法实现相应的功能
 */
if (isset($_GET['api'])) {
    switch ($_GET['api']) {
        case 'web_licensing':
            web_licensing();
            break;
}

//获取到code后的处理
if (isset($_GET['code'])) {
    web_licensing_handle($_GET['code']);
}

//微信公众号支付
function jsapi_pay()
{
    try {
        //以下参数参考微信支付统一下单api https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
        //定义下单数组
        $under = array();
        //读取配置信息
        $config = Config::getConfig()['wx_oa'];
        //获取微信appid
        $under['appid'] = $config['appid'];
        //获取微信商户号
        $under['mch_id'] = $config['mchid'];
        //随机字符串
        $under['nonce_str'] = Util::getNonceStr();
        //主体名称
        $under['body'] = '支付商品名称';
        //生成不重复的32订单号  商户号+时间+随机数
        $under['out_trade_no'] = $config['mchid'] . date('YmdHis') . rand(10000000, 99999999);
        //支付类型
        $under['trade_type'] = 'JSAPI';
        //支付回调地址
        $under['notify_url'] = Config::getConfig()['url'] . "/php_wx/test/notify.php";
        //这里的openid通过网页授权获取到   具体参考微信官方文档    支付类型是JSAPI必须要填写
        //唯一确认用户身份的标识符
        $under['openid'] = 'oPvMSuGvZ5Tltum-2_Mk7SZ7BjBI';
        //支付金额  以分为单位
        $under['total_fee'] = 1;
        //获取客户端ip地址
        $under['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        //制作签名
        $under['sign'] = Util::makeSign($under);
        //微信请求日志
        Log::info('微信请求下单:' . var_export($under, true));
        //微信下单
        $order = Wp::unifiedOrder($under);
        //微信应答日志
        Log::info('微信应答日志:' . var_export($order, true));
        if ($order['return_code'] == "SUCCESS") {
            //微信应答成功，让网页调起微信支付界面
            $params = Util::getJsApiParameters($order);
            $params['code'] = 1;
            $json = json_encode($params);
            echo $json;
            exit();
        } else {
            $params['code'] = -1;
            $params['msg'] = $order['return_msg'];
            $json = json_encode($params);
            echo $json;
            exit();
        }
    } catch (\Exception $e) {
        echo 'try catch  jsapi_pay method in handle.php : ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
    }
}

//微信扫码支付（模式二 不需要在微信后台设置回调地址）
function scan_pay()
{
    try {
        //以下参数参考微信支付统一下单api https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
        //定义下单数组
        $under = array();
        //读取微信支付配置信息
        $config = Config::getConfig()['wx_oa'];
        //获取微信appid
        $under['appid'] = $config['appid'];
        //获取微信商户号
        $under['mch_id'] = $config['mchid'];
        //随机字符串
        $under['nonce_str'] = Util::getNonceStr();
        //主体名称
        $under['body'] = '扫码支付';
        //生成不重复的32订单号  商户号+时间+随机数
        $under['out_trade_no'] = $config['mchid'] . date('YmdHis') . rand(10000000, 99999999);
        //支付类型
        $under['trade_type'] = 'NATIVE';
        //回调地址
        $under['notify_url'] = Config::getConfig()['url'] . "/php_wx/test/notify.php";
        $under['total_fee'] = 1; //支付金额  以分为单位
        //获取客户端ip地址
        $under['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        //制作签名
        $under['sign'] = Util::makeSign($under);
        //微信请求日志
        Log::info('微信请求下单:' . var_export($under, true));
        //微信下单
        $order = Wp::unifiedOrder($under);
        //微信应答日志
        Log::info('微信应答日志:' . var_export($order, true));
        if ($order['return_code'] == "SUCCESS") {
            $params['code'] = 1;
            $params['url'] = $order['code_url'];
            echo json_encode($params);
            exit();
        } else {
            $params['code'] = -1;
            $params['msg'] = $order['return_msg'];
            echo json_encode($params);
            exit();
        }
    } catch (\Exception $e) {
        echo 'try catch  scan_pay method in handle.php : ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
    }
}

//H5支付（用于微信外部浏览器）
function h5_pay()
{
    try {
        //以下参数参考微信支付统一下单api https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
        //定义下单数组
        $under = array();
        //读取微信支付配置信息
        $config = Config::getConfig()['wx_oa'];
        //获取微信appid
        $under['appid'] = $config['appid'];
        //获取微信商户号
        $under['mch_id'] = $config['mchid'];
        //随机字符串
        $under['nonce_str'] = Util::getNonceStr();
        //主体名称
        $under['body'] = 'H5支付';
        //生成不重复的32订单号  商户号+时间+随机数
        $under['out_trade_no'] = $config['mchid'] . date('YmdHis') . rand(10000000, 99999999);
        //支付类型
        $under['trade_type'] = 'MWEB';
        //回调地址
        $under['notify_url'] = Config::getConfig()['url'] . "/php_wx/test/notify.php";
        $under['total_fee'] = 1; //支付金额  以分为单位
        //获取客户端ip地址
        $under['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        //制作签名
        $under['sign'] = Util::makeSign($under);
        //微信请求日志
        Log::info('微信请求下单:' . var_export($under, true));
        //微信下单
        $order = Wp::unifiedOrder($under);
        //微信应答日志
        Log::info('微信应答日志:' . var_export($order, true));
        if ($order['return_code'] == "SUCCESS") {
            $params['code'] = 1;
            $params['url'] = $order['code_url'];
            echo json_encode($params);
            exit();
        } else {
            $params['code'] = -1;
            $params['msg'] = $order['return_msg'];
            echo json_encode($params);
            exit();
        }
    } catch (\Exception $e) {
        echo 'try catch  scan_pay method in handle.php : ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
    }
}

//调用微信网页授权的接口
function web_licensing()
{
    $config = Config::getConfig()['wx_oa'];
    $url = Config::getConfig()['url'];
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$config[appid]&redirect_uri=$url/wx_php/src/test/handle.php&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
    header("location:$url");
}

//处理微信网页回调获取用户信息
function web_licensing_handle($code)
{
    $res = Wa::wxWebLicensing($code);
    print_r($res);
}
