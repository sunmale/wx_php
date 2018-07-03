<?php
/**
 * Created by PhpStorm.
 * User: sunmale
 * Date: 2017/8/7
 * Time: 10:54
 *
 *  微信支付各种实现方法
 */
namespace wx\pay;

use wx\Config;

class Wp
{

    /**
     * 微信统一下单方法
     * @param $data  //支付订单的所有参数
     * @return array|int
     */
    public static function unifiedOrder($data)
    {
        try {
            //把数组格式转换成xml格式
            $xml = Util::toXml($data);
            //获取微信统一下单的url地址
            $url = Config::getConfig()['wx_api']['unifiedOrderUrl'];
            //调用curl请求微信下单地址得到返回数据
            $resXml = Util::postXmlCurl($xml, $url);
            //把xml数据转换成数组
            $xmlData = Util::xmlToArray($resXml);
            return $xmlData;
        }
        //异常处理
         catch (\Exception $e) {
            echo 'try catch unifiedOrder method in Wp.php ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
            return -1;
        }
    }

    /**
     * 查询订单是否存在
     * @param $xmlData
     *@return bool
     */
    public static function queryOrder($xmlData)
    {
        try {
            $query['appid'] = $xmlData['appid'];
            $query['mch_id'] = $xmlData['mch_id'];
            $query['transaction_id'] = $xmlData['transaction_id'];
            $query['nonce_str'] = Util::getNonceStr();
            $query['sign'] = Util::makeSign($query);
            //把数组格式转换成xml格式
            $xml = Util::toXml($query);
            //获取微信查询订单的url地址
            $url = Config::getConfig()['wx_api']['queryOrderUrl'];
            //调用curl请求微信下单地址得到返回数据
            $resXml = Util::postXmlCurl($xml, $url);
            //把xml数据转换成数组
            $queryData = Util::xmlToArray($resXml);
            if ($queryData['return_code'] = "SUCCESS" && $queryData['return_msg'] = "OK") {
                return true;
            } else {
                return false;
            }
        }
        //异常处理
         catch (\Exception $e) {
            echo 'try catch queryOrder method in Wp.php ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
            return -1;
        }
    }

    /**
     * 回调后处理业务成功结束当前一直回调的动作
     */
    public static function returnXml()
    {
        $data['return_code'] = 'SUCCESS';
        $data['return_msg'] = 'OK';
        $xml = Util::toXml($data);
        return $xml;
    }

}
