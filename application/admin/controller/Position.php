<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Controller;
use think\Db;
use think\Loader;
class Position extends Backend
{

    /*
     * 添加职位
     */
    public function addPosition()
    {
        if ($this->request->isPost()) {
            $param['position_name'] = $this->request->post("position_name");
            $validate = $this->validate($param, 'Position.add');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $check = \app\admin\model\Position::checkPositionNameNotRepeat($param['position_name']);
            if ($check) {
                return json(['code' => -1, 'message' => $param['position_name'].'职位已经存在']);
            }
            $result = Loader::model('Position')->add($param['position_name']);
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
    * 修改职位
    */
    public function editPosition()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $name['id'] = $data['id'];
            $name['position_name'] = $data['position_name'];
            $validate = $this->validate($name, 'Position.add');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $id = $this->request->post('id');
            $check = \app\admin\model\Position::checkPositionNameNotRepeat($name['position_name'], $id);
            if ($check) {
                return json(['code' => -1, 'message' => '职位名称已经存在']);
            }
            $result = Loader::model('Position')->edit($name['position_name'], $id);
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
    * 查询全部职位
    */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('index');
        }
        if ($this->request->isPost()) {
            $name = $this->request->post('position_name');
            $result = Loader::model('Position')->selectAll($name);
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
    public function indexPosition(){
        return $this->index();
    }

    /*
    * 查询单个职位
    */
    public function selectOnePosition()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $result = Loader::model('Position')->selectOne($id);
            if ($result) {
                return json(array('code' => '1', 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

    /*
    * 删除职位
    */
    public function delPosition()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $result = Loader::model('Position')->del($id);
            if ($result) {
                return json(array('code' => '1', 'message' => '删除成功'));
            } else {
                return json(array('code' => '-1', 'message' => '删除失败'));
            }
        }
    }
}