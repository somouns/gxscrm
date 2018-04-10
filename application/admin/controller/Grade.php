<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Controller;
use think\Db;
use think\Loader;

class Grade extends Backend
{

    /*
     * 添加职级
     */
    public function addGrade()
    {
        if ($this->request->isPost()) {
            $param['grade_name'] = $this->request->post('grade_name');
            $validate = $this->validate($param, 'Grade.add');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $getGrade = \app\admin\model\Grade::checkGradeNameNoRepeat($param['grade_name']);
            if (!empty($getGrade)) {
                return json(['code' => -1, 'message' => $param['grade_name'].'职级已经存在']);
            }
            $result = Loader::model('Grade')->add($param['grade_name']);
            if ($result) {
                return json(array('code' => '1', 'message' => '添加成功'));
            } else {
                return json(array('code' => '-1', 'message' => '添加失败'));
            }
        } else {
            return json(array('code' => '-1', 'message' => '添加失败'));
        }
    }

    /*
    * 修改职级
    */
    public function editGrade()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $name['id'] = $data['id'];
            $name['grade_name'] = $data['grade_name'];
            $validate = $this->validate($name, 'Grade.edit');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $id = $this->request->post('id');
            $check = \app\admin\model\Grade::checkGradeNameNoRepeat($name['grade_name'], $id);
            if (!empty($check) && $check['id'] != $id ) {
                return json(array('code' => '-1', 'message' => '职级名称已经存在'));
            }
            $result = Loader::model('Grade')->edit($name['grade_name'], $id);
            if ($result) {
                return json(array('code' => '1', 'message' => '修改成功'));
            } else {
                return json(array('code' => '-1', 'message' => '修改失败'));
            }
        } else {
            return json(array('code' => '-1', 'message' => '修改失败'));
        }
    }

    /*
    * 查询全部职级
    */
    public function index()
    {
        if($this->request->isGet()){
            return $this->view->fetch('index');
        }
        if ($this->request->isPost()) {
            $name = $this->request->post('grade_name');
            $result = Loader::model('Grade')->select($name);
            if ($result) {
                return json(array('code' => '1', 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

    /**
     * 权限中转
     */
    public function indexGrade(){
        return $this->index();
    }


    /*
    * 查询单个职级
    */
    public function selectOneGrade()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $result = Loader::model('Grade')->selectOne($id);
            if ($result) {
                return json(array('code' => '1', 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' =>''));
            }
        }
    }

    /*
    * 删除职级
    */
    public function delGrade()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $result = Loader::model('Grade')->del($id);
            if ($result) {
                return json(array('code' => '1', 'message' => '删除成功'));
            } else {
                return json(array('code' => '-1', 'message' => '删除失败'));
            }
        }
    }
}