<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-18
 * Time: 09:14
 */
namespace app\admin\model;
use think\Model;

class BusinessLog extends Model
{
    public static function addLog($logId, $array, $admin = [])
    {
        $logInfo = BusinessType::getBusinessType($logId);
        if ($logInfo === false) {
            return true;
        }
        $logTemplate = $logInfo['template'];
        foreach ($array as $v) {
            $logTemplate = preg_replace("/#CODE#/", $v, $logTemplate,1);
        }
        $data = [];
        $data['title'] = $logInfo['title'];
        $data['info'] = $logTemplate;
        $data['type'] = $logInfo['type'];
        $data['business_type'] = $logInfo['id'];
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['operate_id'] = \think\Cookie::get('admin')['id'] ? \think\Cookie::get('admin')['id'] : $admin['id'];
        $data['operate_real_name'] = \think\Cookie::get('admin')['nickname'] ? \think\Cookie::get('admin')['nickname'] : $admin['nickname'];

        return self::insert($data);
    }
}