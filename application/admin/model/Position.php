<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Cookie;
use app\admin\model\BusinessLog;

class Position extends Model
{
    const ENABLED = 1; //职位正常
    const DISABLED = 2; //职位被删除
    /*
     * 添加职位
     */
    public function add($param)
    {
        $data['position_name'] = $param;
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = Db::name('position')->insert($data);
        if ($result) {
            BusinessLog::addLog(92, [Cookie::get('admin')['nickname'], $param]);
            return true;
        } else {
            return false;
        }
    }

    /*
     * 修改职位
     */
    public function edit($name, $id)
    {
        $data['position_name'] = $name;
        $where['id'] = $id;
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $result = Db::name('position')->where($where)->update($data);
        if ($result) {
            BusinessLog::addLog(93, [Cookie::get('admin')['nickname'], $name]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 查询职位名称是否重复
     * @param $positionName
     * @param int $id
     */
    public static function checkPositionNameNotRepeat($positionName, $id = 0)
    {
        $where = [];
        if ($id > 0) {
            $where['id'] = ['neq', $id];
        }
        $where['position_name'] = $positionName;
        $where['status'] = self::ENABLED;
        return Db::name('position')->where($where)->find();
    }
    
    /*
     * 删除职位
     */
    public function del($id){
        $data['status'] = 2;
        $where['id'] = $id;
        $result = Db::name('position')->where($where)->update($data);
        if ($result) {
            $param = $this->selectOne($id);
            BusinessLog::addLog(94, [Cookie::get('admin')['nickname'], $param['position_name']]);
            return true;
        } else {
            return false;
        }
    }
    /*
     * 查询所有职位
     */
    public function selectAll($param){
        $where['status'] = 1;
        $where['position_name'] = array('like', '%' . $param . '%');
        $result = Db::name('position')->field('position_name,id')->where($where)->order('create_time desc')->paginate(10,false,['query' => request()->param()]);
        return $result;
    }
    /*
     * 查询单个职位
     */
    public function selectOne($id){
        $where['status'] = 1;
        $where['id'] = $id;
        $result = Db::name('position')->where($where)->find();
        return $result;
    }
}