<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-12-18
 * Time: 13:32
 */

namespace app\admin\model;

use think\Model;
use think\Db;

class SpecialGroup extends Model
{
    /*
     * 添加
     */
    public function add($uid, $employee_id, $nickname)
    {
        if (!empty($uid)) {
            $group_id = Db::name('group')->field('id')->where(['operate_id' => 200000000, 'status' => 0])->find();
            $i = 0;
            Db::startTrans();
            foreach ($uid as $val) {
                $data[$i]['group_id'] = $group_id['id'];
                $data[$i]['employee_id'] = $val;
                $data[$i]['operate_real_name'] = $nickname;
                $data[$i]['operate_id'] = $employee_id;
                $data[$i]['type'] = 1;
                $dataQueue[$i]['employee_id'] = $val;
                $i++;
            }
            $result = Db::name('group_employee')->insertAll($data);
            $add_group = $this->addGroup($dataQueue,5);
            if ($result && $add_group) {
                Db::commit();
                return true;
            }
            Db::rollback();
        }
        return false;
    }

    /**
     * 队列表新增数据
     * @param $data
     * @return bool
     */
    public function addGroup($data, $type)
    {
        $result = false;
        if ($this->isMultiArray($data)) {
            $insert = [];
            $i = 0;
            foreach ($data as $v) {
                $insert[$i]['info'] = serialize($v);
                $insert[$i]['type'] = $type;
                $insert[$i]['create_time'] = date('Y-m-d H:i:s');
                $i++;
            }
            $result = Db::name('queue')->insertAll($insert);
        } else {
            $insert = [];
            $insert['info'] = serialize($data);
            $insert['type'] = $type;
            $insert['create_time'] = date('Y-m-d H:i:s');
            $result = Db::name('queue')->insert($insert);
        }
        //Log::record("特殊群组新增合作情况队列任务结果：" . print_r($result,true) . var_export($data, true), Log::ROUTE);
        return $result;

    }

    /**
     * 判断是否数组，是数组判断是 一维 or 多维
     * @param $data
     * @return bool  true|false   是|否
     */
    public static function isMultiArray($data)
    {
        if (count($data) == count($data, 1)) {
            return false;
        } else {
            return true;
        }
    }
}