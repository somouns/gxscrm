<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-18
 * Time: 09:13
 */

namespace app\admin\model;
use think\Cache;
use think\Db;
use think\Model;

class BusinessType extends Model
{
    const TABLE_NAME = 'crm_business_type';
    const BUSINESS_TYPE_CACHE_KEY = 'business_type_cache_key';
    const ENABLED = 1; //正常
    const DISABLED = 0 ; //禁用
    /**
     * 查询业务日志事件类型
     * @param $logId    业务日志ID 可以为空  为空返回所有的业务日志类型
     * @return array|bool|mixed|unknown
     */
    public static function getBusinessType($logId = 0)
    {
        $result = Cache::get(self::BUSINESS_TYPE_CACHE_KEY);
        if ($result) {
            if ($logId > 0) {
                if ($result[$logId]) {
                    return $result[$logId];
                } else {
                    return false;
                }
            } else {
                return $result;
            }
        }

        $list = Db::table(self::TABLE_NAME)
            ->field('id,title,type,template,status')
            ->where(['status' => self::ENABLED])
            ->select();
        $data = [];
        foreach ($list as $v) {
            $data[$v['id']] = $v;
        }
        Cache::set(self::BUSINESS_TYPE_CACHE_KEY, $data, 300);
        if ($logId > 0) {
            if ($data[$logId]) {
                return $data[$logId];
            } else {
                return false;
            }
        } else {
            return $data;
        }

    }
}