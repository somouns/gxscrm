<?php
namespace app\admin\controller;

use app\admin\library\Auth;
use app\common\controller\Backend;
use Doctrine\Common\Cache\Cache;
use think\Config;
use think\Controller;
use app\admin\model\Admin;
use fast\Random;
use think\Db;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-21
 * Time: 17:47
 */
class Role extends Backend
{

    const TABLE_NAME = 'auth_rule'; // 表名

    public function index()
    {
        $where['status'] = 1;
        $list = Db::name(self::TABLE_NAME)->field('id,pid,name,ismenu,title,status,createtime')->where($where)->select();
        $this->view->assign('list',$list);
        return $this->view->fetch('auth/rule/index');
    }

    /*
     * 获取所有title
     */
    public function getAll()
    {
        $where['status'] = 1;
        $list = Db::name(self::TABLE_NAME)->field('id,pid,title as name')->where($where)->select();
        $list = makeTree($list);
        return json(['code'=>1,'data'=>$list]);
    }

    /*
     * 添加
     */
    public function add()
    {
        if($this->request->isGet()){
            return $this->view->fetch('auth/rule/add');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $result = false;
            for($i = 0; $i<count($param['authData']);$i++){
                $data['pid'] = $param['id'];
                $data['title'] = $param['authData'][$i]['title'];
                $data['name'] = $param['authData'][$i]['name'];
                $data['remark'] = $param['authData'][$i]['remark'];
                $data['createtime'] = time();
                $result = Db::name('auth_rule')->insert($data);
            }
            if($result){
                return  json(array('code'=>1,'message'=>'成功'));
            }else{
                return  json(array('code'=>-1,'message'=>'失败'));
            }
        }
    }

    // 编辑
    public function edit(){
        if($this->request->isGet()){
            $id = $this->request->get('id');
            return $this->view->fetch('auth/rule/edit');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $where['id'] = $param['id'];
            $data['pid'] = $param['pid'];
            $data['title'] = $param['title'];
            $data['name'] = $param['name'];
            $data['updatetime'] = time();
            $data['remark'] = $param['remark'];
            $result = Db::name(self::TABLE_NAME)->where($where)->update($data);
            if($result){
                return json(array('code'=>1,'message'=>'修改成功'));
            }else{
                return json(array('code'=>-1,'message'=>'修改失败'));
            }
        }
    }
    // 查询操作权限单个
    public function getOne(){
        $one = [];
        $datap = [];
        $id = $this->request->post('id');
        $one = Db::name(self::TABLE_NAME)->where(['id'=>$id])->find();
        $where['id'] = $one['pid'];
        $pid_name = Db::name(self::TABLE_NAME)->where($where)->field('id,title')->find();
        return json(array('code'=>1,'data'=>$one,'dataname'=>$pid_name));
    }


    // -----------数据权限-----------
    // 数据权限列表
    public function indexData(){
        $list = Db::name('auth_data')->select();
        $this->view->assign('list',$list);
        return $this->view->fetch('auth/rule/indexdata');
    }
    // 获取数据权限树形
    public function getData(){
        $list = Db::name(self::TABLE_NAME)->field('id,pid,name')->where(['status'=>1])->select();
        $list = makeTree($list);
        return json(array('code'=>1,'data'=>$list));
    }
    // 数据权限添加
    public function addData(){
        if($this->request->isGet()){
            return $this->view->fetch('auth/rule/adddata');
        }
        if ($this->request->isPost()) {
            $result = false;
            $param = $this->request->post();
            for($i = 0; $i<count($param['authData']);$i++){
                $data['name'] = $param['authData'][$i]['name'];
                $data['pid'] = $param['id'];
                $data['field_name'] = $param['authData'][$i]['field_name'];
                $data['table_name'] = $param['authData'][$i]['table_name'];
                $result = Db::name('auth_data')->insert($data);
            }
            if($result){
                return json(array('code'=>1,'message'=>'成功'));
            }else{
                return json(array('code'=>-1,'message'=>'失败'));
            }
        }
    }
    // 数据权限修改
    public function editData(){
        if($this->request->isGet()){
            return $this->view->fetch('auth/rule/editdata');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $data['name'] = $param['name'];
            $data['pid'] = $param['pid'];
            $data['field_name'] = $param['field_name'];
            $data['table_name'] = $param['table_name'];
            $where['id'] = $param['id'];
            $result = Db::name('auth_data')->where($where)->update($data);
            if($result){
                return json(array('code'=>1,'message'=>'成功'));
            }else{
                return json(array('code'=>-1,'message'=>'失败'));
            }
        }
    }
    // 查询单个数据权限点
    public function getDataOne(){
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $one = Db::name('auth_data')->where(['id'=>$id])->find();
            $where['id'] = $one['pid'];
            $pid_name = Db::name('auth_data')->where($where)->field('id,name')->find();
            return json(array('code'=>1,'data'=>$one,'dataname'=>$pid_name));
        }
    }
}