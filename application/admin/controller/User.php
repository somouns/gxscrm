<?php

namespace app\admin\controller;

use app\admin\constant\UserConstant;
use app\admin\library\Auth;
use app\common\controller\Backend;
use Doctrine\Common\Cache\Cache;
use think\Config;
use think\Controller;
use app\admin\model\Admin;
use fast\Random;
use think\Cookie;
use think\Db;
use app\admin\model\CooperSituation;

/**
 * 员工管理
 *
 * @icon fa fa-circle-o
 * @internal
 */
class User extends Backend
{
    public function test()
    {
        $uid = 1256;
        $group_model = Model('SpecialGroup');
        $uuid[] = $uid;
        $group_model->add($uuid,Cookie::get('admin')['id'],Cookie::get('admin')['nickname']);
    }

    /*
     * 添加员工
     * @param username 用户名
     * @param nickname 真实姓名
     * @param password 密码
     * @param mobile 手机号
     * @param wechar 微信号
     * @param seat_number 坐席号
     * @param department 部门
     * @param level 职级
     * @param position 职位
     */
    public function addUser()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('auth/admin/add');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $data = $this->validateParam($param,"add");
            $result = $this->validate($data, 'Admin.add');
            if (true !== $result) {
                return json(['code' => -1, 'message' => $result]);
            }
            $model = new Admin();
            $check = $model->checkUsername($data['username']);
            if ($check) {
                return json(['code' => -1, 'message' => '用户名已经存在']);
            }

            $checkMobile = $model->checkMobile($data['mobile']);
            if ($checkMobile) {
                return json(['code' => -1, 'message' => '手机号码已经存在']);
            }
            if (empty($data['avatar'])) {
                $data['avatar'] = '/assets/images/avatar2.png'; //设置员工默认头像
            }
            $data['avatar'] = \app\admin\model\OssUrl::replaceOssUrl($data['avatar']); //存入数据库之前去掉域名
            $data['password'] = md5(md5($data['password']));
            $data['createtime'] = time();
            $data['updatetime'] = time();
            foreach ($data as $key => $val) {
                $keys[] = $key;
            }
            // 添加没有选择权限组
            if (!array_search('rules', $keys)) {
                $data['rules'] = [];
            }
            $data['salt'] = Random::alnum();
            unset($data['fileUpload']); // 删除多余key
            $result = $model->addUser($data);
            if ($result === true) {
                return json(array('code' => 1, 'message' => "添加成功"));
            } else {
                return json(array('code' => '-1', 'message' => '网络错误[' . $result . ']'));
            }
        }
    }

    public function oneuser()
    {
        return $this->view->fetch('/auth/admin/see');
    }

    /*
     * 删除员工
     */
    public function delUser()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            if ($id == config('admin_user_id')) {
                return json(array('code' => '-1', 'message' => "禁止删除"));
            }
            $model = new CooperSituation();
            $status = $model->isCandeleteUser($id);
            if (!empty($status) && is_array($status)) {
                return json($status);
            }
            $model = new Admin();
            $result = $model->startUser($id, 0);
            if ($result) {
                return json(array('code' => 1, 'message' => "删除成功"));
            } else {
                return json(array('code' => -1, 'message' => "删除失败"));
            }
        }
    }

    /*
     * 查询员工列表
     * last_page 页数
     * current_page 当前页
     * last_page 最后页
     * total 总条数
     */
    //pagesize  page order  asc
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('auth/admin/index');
        }
        $param = $this->request->request();
        $param['pagesize'] = empty($param['pagesize']) ? 10 : $param['pagesize'];
        $param['page'] = empty($param['page']) ? 1 : $param['page'];
        $param['order'] = empty($param['order']) ? "createtime" : $param['order'];
        $param['asc'] = empty($param['asc']) ? "desc" : $param['asc'];
        $model = new Admin();
        $data = $model->selectUser($param);
        if ($data) {
            return json(array('code' => 1, 'data' => $data));
        }
    }
    public function showIndex(){
        return $this->selectOneUser();
    }
    /*
     * 查询单个员工
     */
    public function selectOneUser()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('auth/admin/see');
        }
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $model = new Admin();
            $result = $model->selectOneUser($id);
            if ($result) {
                return json(array('code' => '1', 'data' => $result));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

    /*
     * 首页员工修改资料
     */
    public function editUserIndex(){
        return $this->editUser();
    }
    /*
     * 修改员工
     */
    public function editUser()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('auth/admin/edit');
        }
        if ($this->request->isPost()) {
            $param = $this->request->post();
            $data = $this->validateParam($param,"edit");
            if ($data['id'] == config('admin_user_id')) {
                return json(['code' => -1, 'message' => '禁止修改']);
            }
            $result = $this->validate($data, 'Admin.edit');
            if (true !== $result) {
                return json(['code' => -1, 'message' => $result]);
            }
            $model = new Admin();
            $adminInfo = $model->getUserById($data['id']);
            if (!$adminInfo) {
                return json(['code' => -1, 'message' => '用户信息不存在']);
            }
            $repeatInfo = $model->checkUsername($data['username'], 'id,username', $data['id']);
            if ($repeatInfo && $repeatInfo['id'] != $data['id']) { //查找出此姓名的用户，但是不能是当前修改的用户
                return json(['code' => -1, 'message' => '用户名重复']);
            }
            $checkMobile = $model->checkMobile($data['mobile'], 'mobile', $data['id']);
            if ($checkMobile && $checkMobile['mobile'] == $data['mobile']) {
                return json(['code' => -1, 'message' => '手机号码已经存在']);
            }
            if (empty($data['avatar'])) {
                $data['avatar'] = '/assets/images/avatar2.png'; //设置员工默认头像。
            }
            if (isset($data['password']) && $data['password'] != $adminInfo['password']) { //是想要修改密码
                $data['password'] = md5(md5($data['password']));
            }
            foreach ($data as $key => $val) {
                $keys[] = $key;
            }
            if (!array_search('rules', $keys)) {
                $data['rules'] = [];
            }
            $data['avatar'] = \app\admin\model\OssUrl::replaceOssUrl($data['avatar']); //存入数据库之前去掉域名
            $data['updatetime'] = time();
            unset($data['fileUpload'], $data['password']);
            //print_r($param);die;
            $result = $model->editUser($data);
            if ($result === true) {
                return json(array('code' => 1, 'message' => "修改成功"));
            } else {
                return json(array('code' => '-1', 'message' => "网络问题[" . $result . ']'));
            }
        }
    }

    /*
     * 停用员工
     */
    public function stopUser()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            if ($id == config('admin_user_id')) {
                return json(array('code' => '-1', 'message' => "禁止停用"));
            }
            $model = new Admin();
            $result = $model->startUser($id, 2);
            if ($result) {
                return json(array('code' => '1', 'message' => '停用成功'));
            } else {
                return json(array('code' => '-1', 'message' => '停用失败'));
            }
        }
    }

    /*
     * 启动员工
     */
    public function startUser()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $model = new Admin();
            $result = $model->startUser($id, 1);
            if ($result) {
                return json(array('code' => '1', 'message' => '启动成功'));
            } else {
                return json(array('code' => '-1', 'message' => '启动成功'));
            }
        }
    }

    /*
     * 修改密码
     */
    public function editPassword()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post();
            if ($param['id'] == config('admin_user_id') && Cookie::get('admin')['id'] != config('admin_user_id')) {
                return json(array('code' => '-1', 'message' => "无法修改"));
            }
            $result = $this->validate($param, 'Admin.editpassword');
            if (true !== $result) {
                return json(['code' => -1, 'message' => $result]);
            }
            $model = new Admin();
            $result = $model->updatePsasswd($param);
            if ($result) {
                return json(array('code' => '1', 'message' => '修改成功'));
            } else {
                return json(array('code' => '-1', 'message' => '修改失败'));
            }
        }
    }

    /*
     * 验证输入的旧密码是否正确
     */
    public function verifyPwd()
    {
        if($this->request->ispost()){
            $param = $this->request->post();
            $employee_id = $param['id'];
            $old_pwd = trim(md5(md5($param['oldpassword'])));
            $res = DB::name("admin")->where("id = ".$employee_id)->find();
            !empty($res) ? $data_pwd = trim($res['password']) : $data_pwd = "";
            if($old_pwd != $data_pwd){
                return json(array('code' => -1,'message'=>"旧密码不正确"));
            }else{
                return json(array('code' => 1,'message'=>"旧密码正确"));
            }
        }
    }

    /*
     * 用户添加权限数据展示
     */
    public function selectUserAuth()
    {
        if ($this->request->isPost()) {
            $uid = $this->request->post('id');
            $model = new Admin();
            $data = $model->selectAuthUser($uid);
            if ($data) {
                return json(array('code' => 1, 'data' => $data));
            } else {
                return json(array('code' => '-1', 'data' => ''));
            }
        }
    }

    /*
     * 用户添加权限
     */
    public function addUserAuth()
    {
        if ($this->request->isGet()) {
            return $this->view->fetch('auth/admin/auth');
        }
        if ($this->request->isPost()) {
            $model = new Admin();
            $param = $this->request->post();
            $result = $model->addAuth($param);
            //var_dump($result);die;
            if ($result) {
                return json(array('code' => 1, 'message' => '设置成功'));
            } else {
                return json(array('code' => '-1', 'message' => '设置失败'));
            }
        }
    }

    /*
     * 批量导入员工
     */
    public function import()
    {
        if ($this->request->isPost()) {
            $files = pathinfo($_FILES['import_csv']['name']);
            if ($files['extension'] != 'csv') {
                return json(array('code' => '-1', 'message' => '文件格式不正确'));
            }
            $filename = $_FILES['import_csv']['tmp_name'];
            if (empty($filename)) {
                return json(array('code' => '-1', 'message' => '请选择上传的文件'));
            }
            $handle = fopen($filename, 'r');
            $array = $this->input_csv($handle); //解析csv
            $result = $this->importUser($array);
            return $result;
        }
    }

    /*
     * 解析csv
     */
    function input_csv($handle)
    {
        $out = array();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }

    /*
       * 导入用户empty($array[$j][0]) || empty($array[$j][1]) || empty($array[$j][2]) ||empty($array[$j][3]) ||
       */
    public function importUser($array)
    {
        set_time_limit ("1000");
        ini_set('memory_limit','1000M');
        $model = new Admin();
        $count = count($array);
        $datass = '';
        // 剔除本次上传表里有的数据
        for ($j = 1; $j < $count; $j++) {
            $param['loginusername'] = $array[$j][0];
            $param['password'] = $array[$j][1];
            $param['mobile'] = $array[$j][2];
            $param['nickname'] = $array[$j][3];
            if (isset($array[$j][9]) && $array[$j][9]) {
                $param['seat_number'] = $array[$j][9] ? $array[$j][9] : '';
            }
            // 判断用户
            $username = $model->checkUsername($array[$j][0]);
            // 判断手机号
            $mobile = $model->checkMobile($array[$j][2]);
            $check = $this->validate($param, 'Admin.import');
            if (true != $check || !empty($username) || !empty($mobile)) {
                $datass[] = $array[$j][2];
                unset($array[$j]);
            }
        }

        // 记录剔除的手机号，最终返回
        if (!empty($datass)) {
            $datass = @implode(',', $datass);
        }
        // 数组key从0开始
        $array = array_values($array);
        $counts = count($array);
        // 没有要导入的
        if ($counts <= 1){
            return json(array('code' => '-2', 'data' => $datass));
        }
        Db::startTrans();
        try {
            for ($i = 1; $i < $counts; $i++) {
                // 判断用户
                $username = $model->checkUsername($array[$i][0]);
                // 判断手机号
                $mobile = $model->checkMobile($array[$i][2]);
                if (!empty($username) || !empty($mobile)) {
                    $datass .= ",".$array[$i][2];
                    continue;
                }
                $data['username'] = @$array[$i][0];
                $data['password'] = @md5(md5($array[$i][1]));
                $data['mobile'] = @$array[$i][2];
                $data['nickname'] = mb_convert_encoding(@$array[$i][3], 'utf-8', 'gb2312');
                $roles = $model->getAuth(mb_convert_encoding(@$array[$i][4], 'utf-8', 'gb2312'));
                $data['wechat'] = mb_convert_encoding(@$array[$i][5], 'utf-8', 'gb2312');
                $data['department'] = $model->getDepartment(mb_convert_encoding(@$array[$i][6], 'utf-8', 'gb2312'));
                $data['position'] = $model->getPosition(mb_convert_encoding(@$array[$i][7], 'utf-8', 'gb2312'));
                $data['level'] = $model->getGrade(mb_convert_encoding(@$array[$i][8], 'utf-8', 'gb2312'));
                $data['seat_number'] = @$array[$i][9] ? mb_convert_encoding(@$array[$i][9], 'utf-8', 'gb2312') : '';
                $data['createtime'] = time();
                $data['updatetime'] = time();
                $data['avatar'] = '/assets/img/avatar.png'; //设置员工默认头像
                $data['salt'] = Random::alnum();
                $data['build_room'] = @$array[$i][10] ? mb_convert_encoding(@$array[$i][10], 'utf-8', 'gb2312') : '';

                $uid = Db::name('admin')->insertGetId($data);
                if (!empty($roles['id']) && is_array($roles)) {
                    // 添加权限表
                    $datas['uid'] = $uid;
                    $datas['group_id'] = $roles['id'];
                    Db::name('auth_group_access')->insert($datas);

                    // 添加默认备注
                    $remarks = \think\Config::get('remarks');
                    for ($j = 0; $j < count($remarks); $j++) {
                        $redata['employee_id'] = $uid;
                        $redata['mark_name'] = $remarks[$j];
                        $redata['create_time'] = date('Y-m-d H:i:s', time());
                        Db::name('employee_custom_mark')->insert($redata);
                    }

                }
                // 添加默认员工自定义分组
                $grouping = \think\Config::get('grouping');
                for ($e = 0; $e < count($grouping); $e++) {
                    $grdata['employee_id'] = $uid;
                    $grdata['name'] = $grouping[$e];
                    $grdata['create_time'] = date('Y-m-d H:i:s', time());
                    Db::name('employee_custom_group')->insert($grdata);
                }
                // 添加队列
                $datasq['name'] = 'A_user';
                $datasq['create_time'] = date('Y-m-d H:i:s', time());
                $datasq['uid'] = $uid;
                Db::name('role_queue_special_log')->insert($datasq);
                $uuid[] = $uid;
            }
            if(!empty($uuid)){
                // 添加群组关系
                $group_model = Model('SpecialGroup');
                $group_model->add($uuid,Cookie::get('admin')['id'],Cookie::get('admin')['nickname']);
            }
            if (!empty($datass)) {
                Db::commit();
                return json(array('code' => '-2', 'data' => $datass));
            } else {
                Db::commit();
                return json(array('code' => 1, 'message' => '导入成功'));
            }

        } catch (\Exception $e) {
            Db::rollback();
            return json(array('code' => '-1', 'message' => $e->getMessage()));
        }

    }

    /*
     * 查询用户名唯一
     */
    public function checkUsername()
    {
        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $model = new Admin();
            $result = $model->checkUsername($username);
            if (!empty($result)) {
                return json(array('code' => -1, 'message' => '用户名已存在'));
            } else {
                return json(array('code' => 1, 'message' => 'ok'));
            }
        }
    }

    /*
     * 查询手机号唯一
     */
    public function checkMobile()
    {
        if ($this->request->isPost()) {
            $mobile = $this->request->post('mobile');
            $model = new Admin();
            $result = $model->checkMobile($mobile);
            if (!empty($result)) {
                return json(array('code' => -1, 'message' => '手机号已存在'));
            } else {
                return json(array('code' => 1, 'message' => 'ok'));
            }
        }
    }

    /*
     * 添加员工基础的获取 职位，职级，权限组，部门结构
     */
    public function getBasic()
    {
        $data = [];
        $model = new Admin();
        $data['position'] = $model->getPositions();
        $data['grade'] = $model->getGrades();
        $data['department'] = $model->getDepartments();
        $data['rule'] = $model->getRules();
        $data['build_room'] = Config::get('build_room');
        if ($data) {
            return json(array('code' => 1, 'data' => $data));
        } else {
            return json(array('code' => '-1', 'data' => $data));
        }
    }

    public function validateParam($param,$type){
        $data = [];
        if($type =="add" ){
            $data['username'] = $param['username'];
            $data['mobile'] = $param['mobile'];
            $data['nickname'] = $param['nickname'];
            $data['password'] = $param['password'];
            $data['wechat'] = $param['wechat'];
            $data['department'] = $param['department'];
            $data['position'] = $param['position'];
            $data['level'] = $param['level'];
            $data['build_room'] = $param['build_room'];
            $data['seat_number'] = $param['seat_number'];
            $data['avatar'] = $param['avatar'];
            $data['rules'] = isset($param['rules']) ? $param['rules'] : [];
            $data['fileUpload'] = $param['fileUpload'];
        }elseif($type == "edit"){
            $data['username'] = $param['username'];
            $data['mobile'] = $param['mobile'];
            $data['nickname'] = $param['nickname'];
            $data['wechat'] = $param['wechat'];
            $data['department'] = $param['department'];
            $data['position'] = $param['position'];
            $data['level'] = $param['level'];
            $data['build_room'] = $param['build_room'];
            $data['seat_number'] = $param['seat_number'];
            $data['avatar'] = $param['avatar'];
            $data['id'] = $param['id'];
            $data['rules'] = isset($param['rules']) ? $param['rules'] : [];
            $data['fileUpload'] = $param['fileUpload'];
        }
        return $data;
    }

}