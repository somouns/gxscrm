<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-12 10:43
 * Author: SteveGao
 * Company: Mr.Stock CRM Project Team
 */
namespace fast;
use EasyWeChat\Core\Exception;
use think\Config;
use think\Db;
use think\Log;

class Forward
{
    /**
     * http curl post 请求
     * @param $uri
     * @param $data
     * @return mixed
     */
    static function httpCurlPost($uri, $data, $is_secret=0,$time = 15)
    {
        try {
            if ($is_secret) {
                $input = self::generateSign($data);
            } else {
                $input = $data;
            }
            foreach ($input as $k => $v) {
                if (is_array($v)) {
//                $input[$k] = \GuzzleHttp\json_encode($v, JSON_UNESCAPED_UNICODE);
                    $input[$k] = str_replace("\\/", "/", json_encode($v, JSON_UNESCAPED_UNICODE));
                }
            }
            $url = rtrim($uri, '?') . '?' . self::buildUrlParams($input);
            Log::write("curl-post请求接口站点开始：" . microtime(true) . $url . print_r($input, true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //严格校验
            curl_setopt($ch, CURLOPT_TIMEOUT, $time);
            $return = curl_exec($ch);
            Log::write("curl-post请求接口站点结束：" . microtime(true) . $url . print_r($return, true));
            if($return === false)
            {
                $errorNo = curl_errno($ch);
                $admin = \think\Cookie::get('admin');
                $error = [];
                $error['operate_id'] = $admin['id'];
                $error['operate_real_name'] = $admin['nickname'];
                $error['error'] = $errorNo;
                $error['url'] = $url;
                $insert = Db::name('curl_error')->insert($error);
                if (!$insert) {
                    throw new  \Exception('网络异常:tti');
                }
                curl_close($ch);
                return json_encode(['code' => -1, 'message' => '网络异常:tt' . $errorNo,'data' => []]);
            }
            curl_close($ch);
            return $return;
        } catch(\Exception $e) {
            $admin = \think\Cookie::get('admin');
            $str = "";
            $str = $admin['nickname'] . "____" . $url . '###' . $e->getMessage() . "\n";
            @file_put_contents(date("Y-m-d") . "curl_error_list.log",$str, FILE_APPEND);
            return json_encode(['code' => -1, 'message' => $e->getMessage(),'data' => []]);
        }
    }

    /**
     * http curl get 请求
     * @param $uri  请求的url
     * @param $data 请求的数据，数组
     * @return mixed
     */
    static function httpCurlGet($uri, $data, $is_secret=0)
    {
        try {
            $uri = rtrim($uri, '?') . '?';
            $ch = curl_init();
            if ($is_secret) {
                $input = self::generateSign($data);
            } else {
                $input = $data;
            }

            foreach ($input as $k => $v) {
                if (is_array($v)) {
                    $input[$k] = \GuzzleHttp\json_encode($v, JSON_UNESCAPED_UNICODE);
                }
            }

            $url = $uri . self::buildUrlParams($input);

            Log::write("curl-get请求接口站点开始：" . $url . print_r($input, true));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //严格校验
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $return = curl_exec($ch);
            if($return === false)
            {
                $errorNo = curl_errno($ch);
                $admin = \think\Cookie::get('admin');
                $error = [];
                $error['operate_id'] = $admin['id'];
                $error['operate_real_name'] = $admin['nickname'];
                $error['error'] = $errorNo;
                $error['url'] = $url;
                $insert = Db::name('curl_error')->insert($error);
                if (!$insert) {
                    throw new  \Exception('网络异常:tti');
                }
                curl_close($ch);
                return json_encode(['code' => -1, 'message' => '网络异常:tt' . $errorNo,'data' => []]);
            }
            curl_close($ch);
            return $return;
        }catch (\Exception $e) {
            $admin = \think\Cookie::get('admin');
            $str = "";
            $str = $admin['nickname'] . "____" . $url . '###' . $e->getMessage() . "\n";
            @file_put_contents(date("Y-m-d") . "curl_error_list.log",$str, FILE_APPEND);
            return json_encode(['code' => -1, 'message' => $e->getMessage(),'data' => []]);
        }
    }

    static function buildUrlParams($data)
    {
        $str = '';
        foreach ($data as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $str = trim($str, '&');
        return $str;
    }

    static function generateSign($input)
    {
        $data = [];
        $data['employee_id'] = $input['employee_id'];
        $data['c'] = $input['c'];//control名称
        $data['a'] = $input['a'];//action名称
        $data['random'] = mt_rand(1000,99999);
        ksort($data);
        $data['crm_api_key'] = Config::get('crm_api_key');
        $sign = md5(self::buildUrlParams($data));
        $signArr = ['sign' => $sign, 'random' => $data['random']];
        $input = array_merge($input, $signArr);
        return $input;
    }
}