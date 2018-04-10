<?php

namespace app\admin\model;

use app\admin\constant\GroupConstant;
use app\admin\constant\UserConstant;
use think\Cookie;
use think\Model;
use think\Session;
use think\Db;
use app\admin\model\AuthGroup;
use app\admin\model\BusinessLog;
use think\Collection;

class Admin extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    /*
     * 验证用户名唯一
     */
    public function getUserById($Id)
    {
        $where['id'] = $Id;
        return Db::name('admin')->where($where)->find();
    }

    /**
     * 重置用户密码
     * @author baiyouwen
     */
    public function updatePsasswd($param)
    {
        $data['password'] = md5(md5($param['newpassword']));
        $wherep['id'] = $param['id'];
        $result = Db::name('admin')->where($wherep)->field('password')->find();
        if($result['password'] == $data['password']){
            return true;
        }
        $where['id'] = $param['id'];
        $ret = Db::name('admin')->where($where)->update($data);
        $user_info = $this->selectOneUser($param['id']);
        BusinessLog::addLog(84, [Cookie::get('admin')['nickname'], $user_info['nickname']]);
        return $ret;
    }

    /*
     * 停止and启动员工
     */
    public function startUser($id, $status)
    {
        $where['id'] = $id;
        $data['status'] = $status;
        $wherea['id'] = $id;
        $user_info = Db::name('admin')->field('username,nickname,mobile,id as uid')->where($wherea)->find();
        if ($status == 0) {
            $type = 88;
        }
        if ($status == 1) {
            $type = 86;
        }
        if ($status == 2) {
            $type = 87;
        }
        if ($status == 0) {
            $whered['id'] = $id;
            Db::name('delete_admin_log')->insert($user_info);
            $result = Db::name('admin')->where($whered)->delete();
        } else {
            $result = Db::name('admin')->where($where)->update($data);
        }
        if ($result) {
            BusinessLog::addLog($type, [Cookie::get('admin')['nickname'], $user_info['username']]);
            return true;
        } else {
            return false;
        }
    }

    /*
     * 添加员工
     */
    public function addUser($param)
    {
        // 开启事务
        Db::startTrans();
        try {
            $rules = $param['rules'];
            $rules = array_values($rules);
            unset($param['rules']);
            $uid = Db::name('admin')->insertGetId($param);

            // 添加默认备注
            $remarks = \think\Config::get('remarks');
            for ($i = 0; $i < count($remarks); $i++) {
                $redata['employee_id'] = $uid;
                $redata['mark_name'] = $remarks[$i];
                $redata['create_time'] = date('Y-m-d H:i:s', time());
                Db::name('employee_custom_mark')->insert($redata);
            }

            // 添加默认员工自定义分组
            $grouping = \think\Config::get('grouping');
            for ($e = 0; $e < count($grouping); $e++) {
                $grdata['employee_id'] = $uid;
                $grdata['name'] = $grouping[$e];
                $grdata['create_time'] = date('Y-m-d H:i:s', time());
                Db::name('employee_custom_group')->insert($grdata);
            }

            if (!empty($rules) && is_array($rules)) {
                for ($i = 0; $i < count($rules); $i++) {
                    $data['uid'] = $uid;
                    $data['group_id'] = $rules[$i];
                    Db::name('auth_group_access')->insert($data);
                }
            }

            $datas['name'] = 'A_user';
            $datas['create_time'] = date('Y-m-d H:i:s', time());
            $datas['uid'] = $uid;
            Db::name('role_queue_special_log')->insert($datas);

            // 添加群组关系
            $group_model = Model('SpecialGroup');
            $uuid[] = $uid;
            $group_model->add($uuid,Cookie::get('admin')['id'],Cookie::get('admin')['nickname']);
            BusinessLog::addLog(81, [Cookie::get('admin')['nickname'], $param['nickname']]);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /*
     * 查询单个员工
     */
    public function selectOneUser($id)
    {
        $where['id'] = $id;
        $where['status'] = UserConstant::USER_NORMAL;
        $_user = Db::name('admin')->field('avatar,username,nickname,wechat,mobile,seat_number,level,position,department,seat_number,build_room')->where($where)->find();

        $one_user['avatar'] = is_null($_user['avatar']) ? "" : $_user['avatar'];
        $one_user['username'] = is_null($_user['username']) ? "" : $_user['username'];
        $one_user['nickname'] = is_null($_user['nickname']) ? "" : $_user['nickname'];
        $one_user['wechat'] = is_null($_user['wechat']) ? "" : $_user['wechat'];
        $one_user['mobile'] = is_null($_user['mobile']) ? "" : $_user['mobile'];
        $one_user['seat_number'] = is_null($_user['seat_number']) ? "" : $_user['seat_number'];
        $one_user['level'] = is_null($_user['level']) ? "" : $_user['level'];
        $one_user['position'] = is_null($_user['position']) ? "" : $_user['position'];
        $one_user['department'] = is_null($_user['department']) ? "" : $_user['department'];
        $one_user['build_room'] = is_null($_user['build_room']) ? "" : $_user['build_room'];

        $rules = $this->selectUserAuth($id);
        $whered['id'] = $one_user['department'];
        $whered['status'] = 1;
        $de = Db::name('department')->field('department_name')->where($whered)->find();
        $one_user['department_name'] = $de['department_name'];
        if (!empty($rules) && is_array($rules)) {
            $ru = [];
            foreach ($rules as $key => $val) {
                $ru[] = $val['group_id'];
            }
            $one_user['rules'] = $ru;

        } else {
            $one_user['rules'] = '';
        }
        if($one_user['avatar'] != '/assets/img/avatar.png') {
            $one_user['avatar'] = \app\admin\model\OssUrl::addOssUrl($one_user['avatar']); //从数据库去除后加上域名
        }
        return $one_user;
    }

    /*
     * 用户列表
     */
    public function selectUser($param)
    {
        $where = [];

        if (!empty($param['nickname'])) {
            $where['nickname'] = array('like', '%' . $param['nickname'] . '%');
        }
        if (!empty($param['username'])) {
            $where['username'] = array('like', '%' . $param['username'] . '%');
        }
        if (!empty($param['mobile'])) {
            $where['mobile'] = array('like', '%' . $param['mobile'] . '%');
        }
        if (!empty($param['seat_number'])) {
            $where['seat_number'] = array('like', '%' . $param['seat_number'] . '%');
        }
        if (!empty($param['status'])) {
            $where['status'] = $param['status'];
        }
        if (!empty($param['level'])) {
            $where['level'] = $param['level'];
        }
        if (!empty($param['position'])) {
            $where['position'] = $param['position'];
        }

        if (!empty($param['start_time']) || !empty($param['stop_time'])) {
            $where['logintime'] = array(array('EGT', strtotime($param["start_time"])), array('ELT', strtotime($param["stop_time"])), 'AND');
        }
        if (!empty($param['department'])) {
            $where['department'] = -1;
            $department_one = Db::name('department')->field('id')->where('department_name', 'like', '%' . $param['department'] . '%')->select();

            if (!empty($department_one)) {
                $temp_arr = [];
                foreach ($department_one as $key => $value) {
                    # code...
                    $temp_arr[] = $value['id'];
                }
                if (!empty($temp_arr)) {
                    $where['department'] = ["in", $temp_arr];
                }

            }

        }
        if (Cookie::get('admin')['id'] != config('admin_user_id')) {
            $where['id'] = ['neq', config('admin_user_id')];
        }

        $field = 'id,avatar,username,nickname,mobile,loginnumber,level,department,position,build_room,seat_number,updatetime,status,logintime';
        $data = $this->name('admin')->field($field)->where($where)->order($param['order'] . " " . $param['asc'])->paginate($param['pagesize'], false, ['query' => request()->param()]);
        foreach ($data as $key => $val) {
            $whered['id'] = $val['department'];
            $whered['status'] = 1;
            $department = Db::name('department')->field('department_name')->where($whered)->find();
            $data[$key]['department'] = empty($department['department_name']) ? "无" : $department['department_name'];

            $wherep['id'] = $val['position'];
            $wherep['status'] = 1;
            $position = Db::name('position')->field('position_name')->where($wherep)->find();
            $data[$key]['position'] = empty($position['position_name']) ? "无" : $position['position_name'];

            $whereg['id'] = $val['level'];
            $whereg['status'] = 1;
            $grade = Db::name('grade')->field('grade_name')->where($whereg)->find();
            $data[$key]['level'] = empty($grade['grade_name']) ? "无" : $grade['grade_name'];

            $build_room = \think\Config::get('build_room');
            foreach ($build_room as $k => $v) {
                if ($data[$key]['build_room'] == $v['id']) {
                    $data[$key]['build_room'] = $v['name'];
                }
            }
            $data[$key]['logintime'] = date('Y-m-d H:i:s', $val['logintime']);
            $data[$key]['updatetime'] = date('Y-m-d H:i:s', $val['updatetime']);
            $data[$key]['avatar'] = \app\admin\model\OssUrl::addOssUrl($val['avatar']); //从数据库去除后加上域名
        }

        return $data;

    }

    /*
     * 查询员工角色
     */
    public function selectUserAuth($id)
    {
        $where['uid'] = $id;
        return Db::name('auth_group_access')->field('group_id')->where($where)->select();
    }

    /*
     * 用户单独加权限
     */
    public function addAuth($param)
    {
        Db::startTrans();
        try {
            $where['employee_id'] = $param['id'];
            $userInfo = Db::name('auth_employee')->field('employee_id')->where($where)->find();
            //------查询是否有记录-------没有添加否则修改
            if (!empty($userInfo) && is_array($userInfo)) {
                $data['rules'] = @implode(',', $param['rules']);
                $data['datas'] = @implode(',', $param['datas']);
                if($data['rules'] == 0){
                    $data['rules'] = '';
                }
                if($data['datas'] == 0){
                    $data['datas'] = '';
                }
                Db::name('auth_employee')->where($where)->update($data);

                $datas['name'] = 'U_special';
                $datas['create_time'] = date('Y-m-d H:i:s', time());
                $datas['uid'] = $param['id'];
                Db::name('role_queue_special_log')->insert($datas);

            } else {
                $data['employee_id'] = $param['id'];
                $data['rules'] = @implode(',', $param['rules']);
                $data['datas'] = @implode(',', $param['datas']);
                $data['create_time'] = date('Y-m-d H:i:s', time());
                if($data['rules'] == 0){
                    $data['rules'] = '';
                }
                if($data['datas'] == 0){
                    $data['datas'] = '';
                }
                Db::name('auth_employee')->insert($data);

                $datas['name'] = 'A_special';
                $datas['create_time'] = date('Y-m-d H:i:s', time());
                $datas['uid'] = $param['id'];
                Db::name('role_queue_special_log')->insert($datas);

            }
            $uids[] = $param['id'];
            $user_info = $this->selectOneUser($param['id']);
            BusinessLog::addLog(85, [Cookie::get('admin')['nickname'], $user_info['nickname']]);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /*
     * 编辑员工
     */
    public function editUser($param)
    {
        // 开启事务
        Db::startTrans();
        try {
            $rules = $param['rules'];
            $rules = array_values($rules);
            unset($param['rules']);
            $where['id'] = $param['id'];
            Db::name('admin')->where($where)->update($param);
            // 查询修改之前的权限组
            $whered['uid'] = $param['id'];
            $result = Db::name('auth_group_access')->where($whered)->select();
            $ads = array_column($result, 'group_id');
            // 减少的权限组
            $ids = array_diff($ads, $rules);
            if (!empty($ids) && is_array($ids)) {
                $wheres['id'] = array('in', $ids);
                $jianshao_roles = Db::name('auth_group')->field('rules,datas,id')->where($wheres)->select();
                //添加队列
                foreach ($jianshao_roles as $ks => $vs) {
                    if (!empty($vs['rules'])) {
                        $datar['uid'] = $param['id'];
                        $datar['name'] = 'D_user';
                        $datar['type'] = 1;
                        $datar['create_time'] = date('Y-m-d H:i:s', time());
                        $datar['group_ids'] = $vs['rules'];
                        $datar['group_id'] = $vs['id'];
                        Db::name('group_change_queue')->insert($datar);
                    }
                }
                foreach ($jianshao_roles as $key => $value) {
                    if (!empty($value['datas'])) {
                        $datad['uid'] = $param['id'];
                        $datad['type'] = 2;
                        $datad['name'] = 'D_user';
                        $datad['create_time'] = date('Y-m-d H:i:s', time());
                        $datad['group_ids'] = $value['datas'];
                        $datad['group_id'] = $value['id'];
                        Db::name('group_change_queue')->insert($datad);
                    }
                }
            }
            if (!empty($result) && is_array($result)) {
                Db::name('auth_group_access')->where($whered)->delete();
            }
            if (!empty($rules)) {
                for ($i = 0; $i < count($rules); $i++) {
                    $data['uid'] = $param['id'];
                    $data['group_id'] = $rules[$i];
                    Db::name('auth_group_access')->insert($data);
                }
            }
            $datas['name'] = 'U_user';
            $datas['create_time'] = date('Y-m-d H:i:s', time());
            $datas['uid'] = $param['id'];
            Db::name('role_queue_special_log')->insert($datas);

            BusinessLog::addLog(83, [Cookie::get('admin')['nickname'], $param['nickname']]);
            //提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /*
     *用户权限处理
     * @param $uid   int    用户id
     * #param $rules string 角色id
     * @return $data array
     */
    public function manageRules($uid, $rules)
    {
        $data = [];
        foreach ($rules as $key => $rule) {
            $data[$key]["uid"] = $uid;
            $data[$key]["group_id"] = $rules[$key];
        }
        return array_values($data);
    }

    /*
     * 导入根据职级名称查询出id
     */
    public function getGrade($name)
    {
        $where['grade_name'] = $name;
        $where['status'] = 1;
        $id = Db::name('grade')->field('id')->where($where)->find();
        return $id['id'];
    }

    /*
     * 导入根据部门名称查询出id
     */
    public function getDepartment($name)
    {
        $where['department_name'] = $name;
        $where['status'] = 1;
        $id = Db::name('department')->field('id')->where($where)->find();
        return $id['id'];
    }

    /*
     * 导入根据职位名称查询出id
     */
    public function getPosition($name)
    {
        $where['position_name'] = $name;
        $where['status'] = 1;
        $id = Db::name('position')->field('id')->where($where)->find();
        return $id['id'];
    }

    /*
     * 导入根据权限名称查询出id
     */
    public function getAuth($name)
    {
        $where['name'] = $name;
        $where['status'] = 1;
        return Db::name('auth_group')->field('id')->where($where)->find();
    }

    /*
     * 用户信息和权限查询
     */
    public function selectAuthUser($uid)
    {
        $data = [];
        $where['id'] = $uid;
        $data = Db::name('admin')->field('username,nickname,id,avatar')->where($where)->find();
        $gid = Db::name('auth_group_access')->field('group_id')->where("uid = $uid")->select();
        $gid = array_column($gid, 'group_id');
        $where1['id'] = array('in', $gid);
        $where1['status'] = \app\admin\constant\GroupConstant::GROUP_NORMAL;
        $group = Db::name('auth_group')->field('id,name,rules,datas')->where($where1)->select();
        //print_r($group);exit;
        $special = Db::name('auth_employee')->field('rules,datas')->where("employee_id = $uid")->find();

        //-----------用户没有权限组的情况下返回所有权限点------------
        $data['rules'] = $this->getEditRule($da = '', $special['rules']);


        $data['datas'] = $this->getEditData($da = '', $special['datas']);
        //--------有权限组的情况下-------
        if (!empty($group) && is_array($group)) {
            foreach ($group as $key => $val) {
                $data['role'][$key]['name'] = $val['name'];
                $data['role'][$key]['id'] = $val['id'];
                $rules[] = $val['rules'];
                $datas[] = $val['datas'];
            }
        }
        if (!empty($datas) && is_array($datas)) {
            $datalist = [];
            foreach ($datas as $key) {
                $datalist = array_merge($datalist, explode(',', $key));
            }
            $datalist = implode(',', $datalist);
            $data['datas'] = $this->getEditData($datalist, $special['datas']);
        }
        if (!empty($rules) && is_array($rules)) {
            $rulelist = [];
            foreach ($rules as $key) {
                $rulelist = array_merge($rulelist, explode(',', $key));
            }

            $rulelist = implode(',', $rulelist);

            $data['rules'] = $this->getEditRule($rulelist, $special['rules']);
        }
        $data['avatar'] = \app\admin\model\OssUrl::addOssUrl($data['avatar']);
        return $data;
    }

    /*
    * 处理数据权限选中
    */
    public function getEditData($datas, $special)
    {

        $model = new AuthGroup();
        $list = $model->getDatas();
        if (!empty($datas)) {
            $ids = explode(',', $datas);
            foreach ($list as $key => $value) {
                if (in_array($value['id'], $ids)) {
                    $list[$key]['selected'] = 1;
                } else {
                    $list[$key]['selected'] = 0;
                }
            }
        }
        $datasa = explode(',', $special);
        foreach ($list as $key => $value) {
            if (in_array($value['id'], $datasa)) {
                $list[$key]['special'] = 1;
            } else {
                $list[$key]['special'] = 0;
            }
        }
        return makeTree($list);
    }

    /*
    * 处理操作权限选中
    */

    public function getEditRule($datas, $special)
    {

        $model = new AuthGroup();
        $list = $model->getrules();

        if (!empty($datas)) {
            $ids = explode(',', $datas);
            foreach ($list as $key => $value) {

                if (in_array($value['id'], $ids)) {
                    $list[$key]['selected'] = 1;
                } else {
                    $list[$key]['selected'] = 0;
                }
            }
        }

        $datas = explode(',', $special);
        foreach ($list as $key => $value) {
            if (in_array($value['id'], $datas)) {
                $list[$key]['special'] = 1;
            } else {
                $list[$key]['special'] = 0;
            }
        }
        return makeTree($list);
    }

    /*
     * 添加用户返回数据
     */
    public function getGrades()
    {
        $where['status'] = 1;
        $data = Db::name('grade')->field('id,grade_name')->where($where)->select();
        return $data;
    }

    /*
     * 添加用户返回数据
     */
    public function getDepartments()
    {
        $data = [];
        $where['status'] = 1;
        $data = Db::name('department')->field('id,pid,department_name')->where($where)->select();
        if(!empty($data) && is_array($data)){
            foreach($data as $key => $value){
                $data[$key]['type'] = 1;
            }
        }
        $data = makeTree($data);
        return $data;
    }

    /*
     * 添加用户返回数据
     */
    public function getPositions()
    {
        $where['status'] = 1;
        $data = Db::name('position')->field('id,position_name')->where($where)->select();
        return $data;
    }

    /*
     * 查询所有权限组
     */
    public function getRules()
    {
        $where['status'] = GroupConstant::GROUP_NORMAL;
        $where['id'] = ['gt',1];
        $data = Db::name('auth_group')->field('id,name')->where($where)->select();
        return $data;
    }

    /*
     * 验证用户名唯一
     */
    public function checkUsername($username, $field = 'username', $id = 0)
    {
        $where = [];
        if ($id > 0) {
            $where['id'] = ['neq', $id];
        }
        $where['username'] = $username;
        return Db::name('admin')->where($where)->field($field)->find();
    }

    /*
     * 验证手机号唯一
     */
    public function checkMobile($mobile, $field = 'mobile', $id = 0)
    {
        $where = [];
        if ($id > 0) {
            $where['id'] = ['neq', $id];
        }
        $where['mobile'] = $mobile;
        return Db::name('admin')->where($where)->field($field)->find();
    }
}
