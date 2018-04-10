<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Cookie;
use app\admin\model\BusinessLog;

class Grade extends Model
{
    const ENABLED = 1; //职位正常
    const DISABLED = 2; //职位被禁用
    /*
     * 添加职位
     */
    public function add($param)
    {
        $data['grade_name'] = $param;
        $result = Db::name('Grade')->insert($data);
        if ($result) {
            BusinessLog::addLog(95, [Cookie::get('admin')['nickname'], $param]);
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
        $data['grade_name'] = $name;
        $where['id'] = $id;
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $result = Db::name('Grade')->where($where)->update($data);
        if ($result) {
            BusinessLog::addLog(96, [Cookie::get('admin')['nickname'], $name]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新时，查询职级是否存在
     * @param $gradeName
     * @param $Id
     * @return array|false|\PDOStatement|string|Model
     */
    public static function checkGradeNameNoRepeat($gradeName, $Id = '')
    {
        $where = [];
        if ($Id != 0) {
            $where['id'] = $Id;
        }
        $where['grade_name'] = $gradeName;
        $where['status'] = 1;
        return Db::name('Grade')->where($where)->find();
    }

    /*
     * 删除职位
     */
    public function del($id){
        $data['status'] = 2;
        $where['id'] = $id;
        $result = Db::name('Grade')->where($where)->update($data);
        if ($result) {
            $param = $this->selectOne($id);
            BusinessLog::addLog(97, [Cookie::get('admin')['nickname'], $param['grade_name']]);
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * 查询所有职位
     */
    public function select($name){
        $where['status'] = 1;
        $where['grade_name'] = array('like', '%' . $name . '%');
        $result = Db::name('Grade')->where($where)->order('create_time desc')->paginate(10,false,['query' => request()->param()]);
        return $result;
    }
    /*
     * 查询单个职位
     */
    public function selectOne($id){
        $where['status'] = 1;
        $where['id'] = $id;
        $result = Db::name('Grade')->where($where)->find();
        return $result;
    }
}