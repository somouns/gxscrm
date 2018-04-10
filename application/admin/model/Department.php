<?php

namespace app\admin\model;

use think\Model;
use think\Session;
use think\Db;

class Department extends Model
{
    public function add($param){
        $param['createtime'] = date('Y-m-d H:i:s',time());
    }
    /*
     * 删除部门
     */
    public function del($id){
        $where['id'] = $id;
        $data['status'] = 2;
        return Db::name('department')->where($where)->update($data);
    }
}