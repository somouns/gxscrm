<?php
/**
 * Created by PhpStorm.
 * User: xiao hui
 * Date: 2017-10-09
 * Time: 16:19
 */
namespace app\admin\controller;

use app\common\controller\Backend;
use think\Controller;
use app\admin\model\Departments;
use think\Db;

class Department extends Backend
{

    /*
     * 添加公司
     * @param department_name 公司名称
     * @param linkman 联系人
     * @param mobile 联系人手机号
     */
    public function addBranch()
    {

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $param['department_name'] = $data['department_name'];
            $param['linkman'] = $data['linkman'];
            $param['mobile'] = $data['mobile'];
            $validate = $this->validate($param, 'Department.add');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $model = new Departments();
            $check = $model->checkName($param['department_name']);
            if (!empty($check)) {
                return json(array('code' => '-1', 'message' => '已存在'));

            }
            $result = $model->add($param);
            if ($result) {
                return json(array('code' => 1, 'message' => '添加成功'));
            } else {
                return json(array('code' => '-1', 'message' => '添加失败'));
            }
        }
    }

    /*
     * 删除公司
     */
    public function delBranch()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('index');
        }
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $modle = new Departments();
            $result = $modle->del($id);
            if ($result) {
                return json(array('code' => 1, 'message' => '删除成功'));
            } else {
                return json(array('code' => '-1', 'message' => '删除失败'));
            }
        }
    }

    /*
     * 编辑公司
     */
    public function editBranch()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('edit');
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $param['id'] = $data['id'];
            $param['department_name'] = $data['department_name'];
            $param['linkman'] = $data['linkman'];
            $param['mobile'] = $data['mobile'];
            $validate = $this->validate($param, 'Department.edit');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $param['updatetime'] = date("Y-m-d H:i:s",time());
            $model = new Departments();
            $result = $model->edit($param);
            if ($result) {
                return json(array('code' => 1, 'message' => '修改成功'));
            } else {
                return json(array('code' => '-1', 'message' => '修改失败'));
            }
        }
    }

    /*
     * 查询公司列表
     * @param department_name 查询公司名称
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('index');
        }
        $param = $this->request->request();
        $model = new Departments();
        $result = $model->getAll($param);
        if ($result) {
            return json(array('code' => 1, 'data' => $result));
        } else {
            return json(array('code' => '-1', 'data' => ''));
        }
    }

    /*
     * 查询单个公司or部门信息
     */
    public function selectOne()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $modle = new Departments();
            $result = $modle->getOne($id);
            if ($result) {
                return json(array('code' => 1, 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' => '不存在'));
            }
        }
    }

    /*
     * 添加下级部门  去重 加事务 和异常处理
     */
    public function addDown()
    {   
        $result_add=true;
        $result_up=true;
        //fortest 验证特殊字符|；
        // $param['pid']=670;
        // $param['department_name']=["修改11|818","修改22|819","新增11|0","新增121|0"];

        if ($this->request->isPost()) {
            $param = $this->request->request();
            if (empty($param['pid'])) {
                return json(array('code' => '-1', 'message' => '父级不能为空'));
            }
            if (empty($param['department_name'])) {
                return json(array('code' => '-1', 'message' => '提交无效'));
            }
            foreach($param['department_name'] as $value){
                $temp=explode('|', $value);
                if(empty($temp[0])){
                    return json(array('code' => '-1', 'message' => '名称不能为空'));
                }
            }
            //获取哪些部门是要修改名字 ，哪些部门需要新增
            $forup=[];
            $foradd=[];
            foreach($param['department_name'] as $value){
                $temp=explode('|', $value);
                if($temp[1]==0){
                    $foradd[]=['department_name'=>$temp[0],
                            'pid'=>$param['pid'],
                            'createtime'=>date('Y-m-d H:i:s',time())];
                }else{
                    $forup[]=['department_name'=>$temp[0],
                            'id'=>$temp[1],
                            'pid'=>$param['pid']];
                }
            }
            //print_r($foradd);exit;
            $model = new Departments();
            if(!empty($foradd)){
                $result_add = $model->addDown($foradd);
            }
            if(!empty($forup)){
                $result_up = $model->updateDown($forup);
            }

            if ($result_add!==FALSE&&$result_up!==FALSE) {
                return json(array('code' => 1, 'message' => '更新成功'));
            } else {
                return json(array('code' => '-1', 'message' => '更新失败'));
            }
        }
    }

    /*
     * 根据PID查询下级部门
     */
    public function selectDown()
    {
        if ($this->request->isPost()) {
            $pid = $this->request->post('pid');
            $model = new Departments();
            $result = $model->getDown($pid);
            $department = $model->getOne($pid);
            if ($result) {
                return json(array('code' => 1, 'data' => $result,'name'=>$department['department_name']));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

//    /*
//     * 添加下级部门完成之后
//     */
//    public function getDown()
//    {
//        if ($this->request->isPost()) {
//            $pid = $this->request->post('pid');
//            $model = new Departments();
//            $result = $model->getDown($pid);
//            if ($result) {
//                return json(array('code' => 1, 'data' => $result));
//            } else {
//                return json(array('code' => '-1', 'data' => ''));
//            }
//        }
//    }

    /*
     * 添加下级部门处理
     */
    public function manage($param)
    {

        $count = count($param['department_name']);
        for ($i = 0; $i < $count; $i++) {
            $params[$i]['pid'] = $param['pid'];
            $params[$i]['createtime'] = date('Y-m-d H:i:s', time());
            $params[$i]['department_name'] = $param['department_name'][$i];
        }
        return $params;
    }

    /*
     * 查询部门结构
     */
    public function getTree()
    {
        $model = new Departments();
        $list = $model->getlist();
        $result = makeTree($list);
        return json(array('code' => 1, 'data' => $result));
    }

    /*
     * 公司下部门结构
     */
    public function getPidTree()
    {
        if ($this->request->isPost()) {
            $pid = $this->request->post('id');
            $model = new Departments();
            $list = $model->getPidTree($pid);
            $result = makeTree($list);
            return json(array('code' => 1, 'data' => $result));
        }
    }

    /*
     * 搜索组织架构
     */
    public function selectDepartment()
    {
        if ($this->request->isPost()) {
            $name = $this->request->post('department_name');
            $model = new Departments();
            $list = $model->getlists($name);
            $result = makeTree($list);
            return json(array('code' => 1, 'data' => $result));
        }
    }

    /*
     * 组织架构带人员
     */
    public function getDepartment()
    {
        $model = new Departments();
        $wenjianjia = $model->getlist();
       
        $wenjianjia = makeTree($wenjianjia);

       
        $wenjianjia_wenjian = $model->getDepartment($wenjianjia);//员工

        return json(array('code' => 1, 'data' => $wenjianjia_wenjian));
    }

    /*
     * 编辑部门
     */
    public function editDepartment(){
        if ($this->request->isGet()) {
            return $this->view->fetch('edit');
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $param['pid'] = $data['pid'];
            $param['id'] = $data['id'];
            $param['department_name'] = $data['department_name'];
            $validate = $this->validate($param, 'Department.editdepartment');
            if (true !== $validate) {
                return json(['code' => -1, 'message' => $validate]);
            }
            $model = new Departments();
            $result = $model->editDepartment($param);
            if ($result) {
                return json(array('code' => 1, 'message' => '修改成功'));
            } else {
                return json(array('code' => '-1', 'message' => '修改失败'));
            }
        }
    }

    function get_attr($a, $pid)
    {
        $tree = array();                                //每次都声明一个新数组用来放子元素
        foreach ($a as $v) {
            if ($v['pid'] == $pid) {                      //匹配子记录
                $v['child'] = $this->get_attr($a, $v['id']); //递归获取子记录
                if ($v['child'] == null) {
                    unset($v['child']);             //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                $tree[] = $v;                           //将记录存入新数组
            }
        }
        return $tree;                                  //返回新数组
    }
}