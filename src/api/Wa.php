<?php
/**
 * Created by PhpStorm.
 * User: sunmale
 * Date: 2017/8/7
 * Time: 10:54
 *
 * 微信常用的开发接口 （如微信网页开发，公众号自定义菜单等）
 *
 */
namespace wx\api;

use wx\Config;

class Wa
{

    /**
     * 网页授权得到用户信息
     * @param $code
     * @return int|mixed
     *
     */
    public static function wxWebLicensing($code)
    {
        try {
            $config = Config::getConfig()['wx_oa'];
            //通过微信code
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$config[appid]&secret=$config[secret]&code=$code&grant_type=authorization_code";
            $res = json_decode(self::init_curl($url));
            if (isset($res->errcode)) {
                $res = json_decode(json_encode($res), true);
                return $res;
            }
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$res->access_token&openid=$res->openid&lang=zh_CN";
            $res = self::init_curl($url);
            return json_decode($res, true);
        } catch (\Exception $e) {
            echo 'try catch wxWebLicensing method in Wa.php ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
            return -1;
        }
    }

    /**
     * 获取公众号的全局唯一接口调用凭据access_token
     * @return int
     */
    public static function getAccessToken()
    {
        try {
            //保存文件路径
            $path = __DIR__ . DIRECTORY_SEPARATOR . 'data';
            if (!is_dir($path)) {
                mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
            }
            //文件名称
            $access_token_file = $path . DIRECTORY_SEPARATOR . 'access_token.json';
            if (!is_file($access_token_file)) {
                self::set_php_file($access_token_file, "");
            }
            $data = json_decode(self::get_php_file($access_token_file));
            if (empty($data) || $data->expire_time < time()) {
                $config = Config::getConfig()['wx_oa'];
                //通过微信code
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$config[appid]&secret=$config[secret]";
                $res = json_decode(self::init_curl($url));
                if (isset($res->errcode)) {
                    return json_decode(json_encode($res), true);
                }
                $access_token = $res->access_token;
                if ($access_token) {
                    $content = new \stdClass();
                    $content->access_token = $access_token;
                    $content->expire_time = time() + 7200;
                    self::set_php_file($access_token_file, json_encode($content));
                }
            } else {
                $access_token = $data->access_token;
            }
            return $access_token;
        } catch (\Exception $e) {
            echo 'try catch getAccessToken method in Wa.php ' . $e->getMessage() . ',' . $e->getFile() . ',' . $e->getLine();
            return -1;
        }
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

    //获取文件内容
    public static function get_php_file($filename)
    {
        return trim(file_get_contents($filename));
    }

    //写入文件内容
    public static function set_php_file($filename, $content)
    {
        file_put_contents($filename, $content);
    }

}
