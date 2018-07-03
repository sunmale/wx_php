<?php
/**
 * Created by PhpStorm.
 * User: PVer
 * Date: 2017/8/7
 * Time: 10:54
 * 常用方法的工具类
 */
namespace wx\pay;

use wx\Config;

class Util
{

    /**
     * 把数组转换成需要的xml数据
     * @param $data
     * @return string
     * @throws \Exception
     */
    public static function toXml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            throw new \Exception("需要转换的数据不符合条件！");
        }
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 把xml数据转换成数组
     * @param  $xml
     * @return string
     */
    public static function xmlToArray($xml)
    {
        $xmlData = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $xmlData;
    }

    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string //产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 解析参数拼接参数
     * @param $data
     * @return string
     */
    public static function toUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 数据签名
     * @param $data
     * @return string
     */
    public static function makeSign($data)
    {
        $config = Config::getConfig()['wx_oa'];
        //按照ASIC码排序
        ksort($data);
        //把数组返回拼接成需要字符串
        $string = self::toUrlParams($data);
        $string = $string . "&key=" . $config['shop_key']; //拼接上商户生成的key
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     *h5调起网页支付(只限在微信支付中)
     * @param $order
     * @return string
     */
    public static function getJsApiParameters($order)
    {
        $jsapi['appId'] = $order['appid'];
        $time = time();
        //timeStamp必须是string   不然苹果手机下无法支付
        $jsapi['timeStamp'] = "$time";
        $jsapi['nonceStr'] = self::getNonceStr();
        $jsapi['package'] = "prepay_id=" . $order['prepay_id'];
        $jsapi['signType'] = 'MD5';
        $jsapi['paySign'] = self::makeSign($jsapi);
        return $jsapi;
    }

    /**
     * 支付请求的curl
     * @param $xml   //需要post的xml数据
     * @param $url  //url请求地址
     * @param bool $useCert   //是否需要证书，默认不需要
     * @param int $second  //url执行超时时间，默认30s
     * @return mixed
     *   * @throws \Exception
     */
    public static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $config = Config::getConfig();
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $config['wx_oa']['wx_sslcert_path']);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $config['wx_oa']['wx_sslkey_path']);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        return $data;

    }

    //调用curl模拟请求数据
    public static function init_curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //   curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        //   $jsondecode = json_decode($output); //对JSON格式的字符串进行编码
        //   $array = get_object_vars($jsondecode);//转换成数组
        //print_r($array);
        curl_close($curl);
        return $output;
    }

    //https请求（支持GET和POST）
    public static function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}
