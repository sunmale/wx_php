<?php

/**
 * 微信支付异步回调的方法，处理自己的业务
 */
//require '../vendor/autoload.php';

require '../../AutoLoad.php';
use wx\Log;
use wx\pay\Util;
use wx\pay\Wp;

//异常处理
error_reporting(E_ALL);
ini_set('date.timezone', 'Asia/Shanghai');
set_error_handler(array('Wx\\WxException', 'myErrorHandler'));
set_exception_handler(array('Wx\\WxException', 'myExceptionHandler'));
register_shutdown_function(array('Wx\\WxException', 'myShutdownHandler'));

/**
 * Class Notify
 * 微信支付回调（处理自己的业务逻辑）
 */

class Notify
{

    //微信支付回调方法
    public function callback()
    {
        $xmlString = file_get_contents("php://input");
        $xmlData = Util::xmlToArray($xmlString);
        Log::info('微信支付后回调的数据:' . var_export($xmlData, true));
        //验证签名是否正确，防止伪造来源
        $sign = Util::makeSign($xmlData);
        if ($xmlData['sign'] != $sign) {
            Log::info('签名错误');
            exit();
        }
        //验证订单是否存在,没有查询到直接返回 （微信有验证订单的接口）
        $query = Wp::queryOrder($xmlData);
        if (!$query) {
            return false;
        }
        //1.处理自己的业务逻辑
        // 2.因为微信会一直回调，如果自己的业务处理成功后， 返回微信需要的xml给微信服务端 停止回调
        $xml = Wp::returnXml();
        Log::info('结束回调:' . $xml . "\n\n");
        echo $xml;

    }

    //ajax轮询判断支付是否真正完成
    public function ajaxLoop()
    {

    }
}

$notify = new Notify();
//调用回调方法
$notify->callback();
