<?php

namespace app\admin\controller\auth;

use app\admin\controller\Position;
use app\admin\model\AuthGroupAccess;
use app\common\controller\Backend;
use fast\Tree;
use think\Controller;
use app\admin\model\AuthGroup;
use think\Db;

class Group extends Backend
{
    /*
     * 添加角色
     * @param describe 描述
     * @param name 权限名称
     * @param datas 数据权限
     * @param rules 操作权限
     */
    public function addGroup()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('add');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $result = $this->validate($param, 'Group.add');
            if (true !== $result) {
                return json(['code' => -1, 'message' => $result]);
            }
            $model = new AuthGroup();
            $result = $model->addGroup($param);
            if ($result === true) {
                return json(array('code' => '1', 'message' => '添加成功'));
            } else {
                return json($result);
            }
        }
    }

    /*
     * 删除角色
     */
    public function delGroup()
    {
        if ($this->request->isPost()) {
            $model = new AuthGroup();
            if ($this->request->post('id') == 1) {
                return json(array('code' => '-1', 'message' => '禁止删除'));
            }
            $id = $this->request->post('id');
            $result = $model->delGroup($id);
            if ($result) {
                return json(array('code' => '1', 'message' => '删除成功'));
            } else {
                return json(array('code' => '-1', 'message' => '删除失败'));
            }
        }
    }

    /*
     * 修改角色
     */
    public function editGroup()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('edit');
        }

        if ($this->request->isPost()) {
            $param = $this->request->post();
            $result = $this->validate($param, 'Group.edit');
            if (true !== $result) {
                return json(['code' => -1, 'message' => $result]);
            }
            $model = new AuthGroup();
            $boolean = $model->edit($param);
            if ($boolean) {
                return json(['code' => "1", "message" => "权限编辑修改成功", "data" => $boolean]);
            } else {
                return json(['code' => "-1", "message" => "权限编辑修改失败", "data" => $boolean]);
            }
        }
    }

    /**
     * 对特殊群组的处理
     * @param $model
     * @param $param
     * @return bool
     * Created by: Xerxes Sultan
     */
    private function specialGroup_jiu($model, $param)
    {
        //单元测试（数据四种情况(语法错误可以抛出异常),异常（mysql查询(tp如果连接丢失直接中断了，如果查询语句错误会通过异常类抛出),mysqlup，mysqlin,事务1(事务中update会影响后面select的查询)））

        Db::startTrans();
        try {
            //处理数据同步
            $rules_front = isset($param['rules']) ? $param['rules'] : [];//为了兼容前端
            $auth_ids = $model->selectOneGroupAuth($param['id']);
            $auth_ids['rules'] = empty($auth_ids['rules']) ? "" : $auth_ids['rules'];//为了兼容数据库
            $rules = explode(",", $auth_ids['rules']);
            //没考虑异常情况
            $config_special = $this->transSpecial();
            $config = \think\Config::get("specialrile");
            foreach ($config as $item) {
                $ids[] = $item["id"];
            }
            $arr_1 = array_intersect($ids, $rules);
            $arr_2 = array_intersect($ids, $rules_front);

            if ($arr_1 != $arr_2) {

                $admin_ids = $model->selectGroupAdmin($param['id']);
                $empIds = $this->arrayTransKey($admin_ids, 'uid');
                if (!empty($arr_1)) {
                    $model->deleteSpecialRules($empIds);
                }
                if (!empty($arr_2)) {
                    $insert = $this->beforeInsertData($empIds, $arr_2, $config_special);
                    $insertBoolean = $model->insertSpecailRules($insert);
                }
            }

            $model->edit($param);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }

    }

    /**
     * 对特殊群组的处理
     * @param $model
     * @param $param
     * @return bool
     * Created by: Xerxes Sultan
     */
    private function specialGroup($model, $param)
    {

        //前端传过来的所有权限点
        $rules_front = $param['rules'];
        //1.查询当前角色组所有的权限点信息
        $auth_ids = $model->selectOneGroupAuth($param['id']);
        $rules = explode(",", $auth_ids['rules']);
        //将配置文件中的type信息转换成一维数组
        $config_special = $this->transSpecial();
        $config = \think\Config::get("specialrile");
        foreach ($config as $item) {
            $ids[] = $item["id"];
        }
        $arr_1 = (array_intersect($ids, $rules));
        $arr_2 = (array_intersect($ids, $rules_front));

        //1.查询当前角色组下的所有用户列表
        $admin_ids = $model->selectGroupAdmin($param['id']);
        //获取所有用户的ID
        $empIds = $this->arrayTransKey($admin_ids, 'uid');
        //判断前端传过来的特殊群组的ID是否在已有的中是否一致，list不为空的话，说明有变化

        if (empty($arr_2) && empty($arr_1)) {
            $result = $model->edit($param);
            if ($result) {
                return true;
            } else {
                return false;
            }
        }

        if ($arr_1 != $arr_2) {
            if (empty($arr_2)) {
                $result = $model->edit($param);
                $deleteBoolean = $model->deleteSpecialRules($empIds);
                if ($deleteBoolean == 0) {
                    $deleteBoolean = true;
                }
                if ($result && $deleteBoolean) {
                    Db::commit();
                    return true;
                } else {
                    Db::rollback();
                    return false;
                }
            }
            Db::startTrans();
            //删除$ids用户特殊权限所有记录
            $deleteBoolean = $model->deleteSpecialRules($empIds);
            if ($deleteBoolean == 0) {
                $deleteBoolean = true;
            }
            //删除之后再新增前端传入的特殊权限点
            $insert = $this->beforeInsertData($empIds, $arr_2, $config_special);
            $insertBoolean = $model->insertSpecailRules($insert);
            //最后更新权限表中的数据

            $result = $model->edit($param);

            if ($deleteBoolean && $insertBoolean && $updateBoolean && $result) {
                Db::commit();
                return true;
            } else {
                Db::rollback();
                return false;
            }
        } else {
            $result = $model->edit($param);
            return $result;
        }
    }


    /**
     * 插入数据前组装数据
     * @param $ids
     * @param $list
     * @param $config_special
     * @return array
     * Created by: Xerxes Sultan
     */
    private function beforeInsertData($ids, $list, $config_special)
    {
        $insert = [];
        $i = 0;
        foreach ($ids as $id) {
            foreach ($list as $item) {
                $insert[$i]['employee_id'] = $id;
                $insert[$i]['type'] = $config_special[$item];
                $insert[$i]['create_time'] = date("Y-m-d H:i:s", time());
                $i++;
            }
        }
        return $insert;
    }


    /**
     * 根据数据转换数组
     * @param $admin_ids
     * Created by: Xerxes Sultan
     */

    private function arrayTransKey($admin_ids, $field)
    {
        if (!$admin_ids) {
            return [];
        }
        $ids = [];
        foreach ($admin_ids as $admin_id) {
            $ids[] = $admin_id[$field];
        }
        return $ids;
    }


    /**
     * 将配置文件转换成一维数组
     * Created by: Xerxes Sultan
     */
    private function transSpecial()
    {
        $config = \think\Config::get("specialrile");
        $config_type = [];
        foreach ($config as $item) {
            $config_type[$item['id']] = $item['type'];
        }
        return $config_type;
    }


    /*
     * 查询某个权限组
     */

    public function selectOneGroup()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('details');
        }
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            if ($id == 1) {
                return json(array('code' => '-1', 'message' => '不可编辑'));
            }
            $model = new AuthGroup();
            $result = $model->selectOneGroup($id);
            if ($result) {
                return json(array('code' => '1', 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

    /*
     * 查看单个权限组只展示
     */
    public function seeGroup()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $model = new AuthGroup();
            $result = $model->seeGroup($id);
            if ($result) {
                return json(array('code' => '1', 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

    /*
     * 列表
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('index');
        }
        $model = new AuthGroup();
        $param = $this->request->post();
        $result = $model->indexGroup($param);
        if ($result) {
            return json(array('code' => '1', 'data' => $result));
        } else {
            return json(array('code' => '-1', 'data' => ''));
        }
    }

    /**
     * 读取角色权限树
     *
     * @internal
     */
    public function roletree()
    {
        $model = new AuthGroup();
        $list = $model->getrules();
        $newlist = makeTree($list);
        return json(array('code' => 1, 'data' => $newlist));
    }

    /*
     * 数据权限点
     */
    public function dataTree()
    {
        $model = new AuthGroup();
        $list = $model->getDatas();
        $newlist = makeTree($list);
        return json(array('code' => 1, 'data' => $newlist));
    }


    /*
     * 权限组用户管理列表页面
     */
    public function limit()
    {
        return $this->view->fetch('limit');
    }


    /*
     * 权限组用户管理列表数据
     */
    public function limitUser()
    {
        $request = request();
        $params = $request->param();
        $pagesize = $params['pagesize'];
        if (empty($pagesize)) {
            $pagesize = 10;
        }
        $nickname = $params['nickname'];
        $username = $params['username'];
        $mobile = $params['mobile'];
        $department = $params['department'];
        $position = $params['position'];
        $level = $params['level'];
        $build_room = $params['build_room'];
        $status = $params['status'];
        $start_time = $params['start_time'];
        $end_time = $params['end_time'];
        $condition = [];
        if (!empty($nickname)) {
            $condition['nickname'] = ['like', "%$nickname%"];
        }
        if (!empty($username)) {
            $condition['username'] = ['like', "%$username%"];
        }
        if (!empty($mobile)) {
            $condition['mobile'] = ['like', "%$mobile%"];
        }
        if (!empty($department)) {
            $map['department_name'] = ['like', "%$department%"];
            $map['status'] = 1;
            $department_id = DB::table('crm_department')->field("id,department_name")->where($map)->select();
            $dd = array_column($department_id, "id");
            $condition['department'] = ['in', $dd];
        }
        if (!empty($position)) {
            $condition['position'] = $position;
        }
        if (!empty($level)) {
            $condition['level'] = $level;
        }
        if (!empty($build_room)) {
            $condition['build_room'] = $build_room;
        }
        if (!empty($status)) {
            $condition['status'] = $status;
        }
        if (!empty($start_time) && !empty($end_time)) {
            $condition['createtime'] = ['between', [$start_time, $end_time]];
        }
        if (empty($start_time) && !empty($end_time)) {
            $condition['createtime'] = ['elt', $end_time];
        }
        if (!empty($start_time) && empty($end_time)) {
            $condition['createtime'] = ['egt', $start_time];
        }
        $model = new AuthGroupAccess();
        $data = $model->group_permissions($params['gid']);
        $gname = DB::table('crm_auth_group')->field("id,name")->where('id = :param', ['param' => $params['gid']])->find();
        if (!empty($data)) {
            $e_id = array_column($data, "uid");
            if ($e_id) {
                $employee = $model->group_employee($e_id, $condition, $pagesize);
                $employee['gname'] = $gname['name'];
                return json(array('code' => 1, 'data' => $employee));
            }
        }
        return json(['code' => 1, 'data' => ['total' => 0, 'per_page' => $pagesize, 'current_page' => 1, 'last_page' => 0, 'data' => [], 'gname' => $gname['name']]]);
    }

    /*
     * 移除员工出权限组
     */
    public function rmLimit()
    {
        $request = request();
        $employee_id = $request->param('employee_id');
        $group_id = $request->param('group_id');
        $map['uid'] = ['in', $employee_id];
        $map['group_id'] = $group_id;
        $res = DB::table('crm_auth_group_access')->where($map)->delete();
        if ($res) {
            return json(['code' => 1, 'message' => "移除成功！"]);
        } else {
            return json(['code' => -1, 'message' => "移除失败！"]);
        }


    }


    /*
     * 添加员工进权限组
     */
    public function addLimit()
    {
        $data = [];
        $request = request();
        $employee_id = explode(",", $request->param('employee_id'));
        $group_id = $request->param('group_id');
        $limit = DB::table('crm_auth_group_access')->where(['uid' => ['in', $employee_id], 'group_id' => $group_id])->select();
        if ($limit) {
            $ss = array_column($limit, 'uid');
            $eid = array_diff($employee_id, $ss);
            if ($eid) {
                foreach ($eid as $k => $v) {
                    $data[$k]['uid'] = $v;
                    $data[$k]['group_id'] = $group_id;
                }
                $res = DB::table('crm_auth_group_access')->insertAll($data);
            } else {
                return json(['code' => 1, 'message' => "添加成功！"]);
            }
        } else {
            foreach ($employee_id as $k => $v) {
                $data[$k]['uid'] = $v;
                $data[$k]['group_id'] = $group_id;
            }
            $res = DB::table('crm_auth_group_access')->insertAll($data);
        }
        if ($res) {
            return json(['code' => 1, 'message' => "添加成功！"]);
        } else {
            return json(['code' => -1, 'message' => "添加失败！"]);
        }
    }


}
