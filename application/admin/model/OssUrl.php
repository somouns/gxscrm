<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-11-03
 * Time: 12:06
 */
namespace app\admin\model;

use think\Config;
use think\Model;

class OssUrl extends Model
{
    /**
     * 将图片中的oss地址替换为空值
     * @param $url
     * @param int $type
     * @return mixed
     */
    public static function replaceOssUrl($url, $type = 1)
    {
        if ($type == 1) {
            $oss = Config::get('aliyunoss_image_remoteurl') ? Config::get('aliyunoss_image_remoteurl') : 'http://gxscrm-static.oss-cn-shenzhen.aliyuncs.com';
        }
        return str_replace($oss,'', trim($url));
    }

    /**
     * 将URL中的oss地址加上
     * @param $url
     * @param int $type
     * @return string
     */
    public static function addOssUrl($url, $type = 1)
    {
        if ($type == 1) {
            $oss = Config::get('aliyunoss_image_remoteurl') ? Config::get('aliyunoss_image_remoteurl') : 'http://gxscrm-static.oss-cn-shenzhen.aliyuncs.com';
        }
        return $oss . trim(self::replaceOssUrl($url));
    }
}