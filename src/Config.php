<?php

namespace wx;

class Config
{
    //默认配置
    protected static $_config = array(
        //当前测试url地址
        'url' => 'http://sunmale.tunnel.51lzyl.com',
        //微信公众号的配置
        'wx_oa' => array(
            'appid' => 'wx28ba99faee7b52b3',
            'secret' => '5b606af1ea27796321addddd8abfd6b8',
            'mchid' => '1259438601',
            'shop_key' => 'QWERTYUIOPASDFGHJKLZXCVBNMQWERTY',
            'sslcert_path' => '',
            'sslkey_path' => '',
        ),
        //微信开发接口api
        'wx_api' => array(
            //微信统一下订单的地址
            'unifiedOrderUrl' => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
            //微信统一查询订单url
            'queryOrderUrl' => 'https://api.mch.weixin.qq.com/pay/orderquery',
        ),
    );

    //得到需要的配置信息，封装成一个数组返回
    public static function getConfig()
    {
        return self::$_config;
    }

}
