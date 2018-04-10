<?php
/**
 * Created by PhpStorm.
 * User: Guan
 * Date: 2017-09-25
 * Time: 15:47
 */

namespace app\admin\controller\globalsetting;


use app\common\controller\Backend;
use think\Db;


class Setting extends Backend
{


    public function index()
    {
        return $this->view->fetch();
    }
    /**
     * 配置列表展示页
     */
    public function getlist(){

    }
    public function  operation(){

    }
    public function edit_acc(){

    }
    public function edit_online(){

    }
    public function edit_type(){

    }
    public function edit_sales(){

    }

    public function add(){

    }

    public function delpayee(){

    }
    public function upper(){

    }

    public function edit_source(){

    }

    public function back_source(){

    }

    public function del_source(){

    }

    public function check_del(){

    }
    public function choice(){

    }
    public function backNotice(){

    }
    public function updateNotice(){

    }




        /**
     * 编辑配置
     */

    public function source(){
        return $this->view->fetch();
    }


    public function online(){
        return $this->view->fetch();
    }

    public function line(){
        return $this->view->fetch();
    }

    public function followup(){
        return $this->view->fetch();
    }

    public function payee()
    {
        return $this->view->fetch();
    }

    public function process(){
        return $this->view->fetch();
    }

    public function notice(){
        return $this->view->fetch();
    }

    /**
     * 修改配置新年资源分配
     * 参数nums：分配数量。
     * @return \think\response\Json
     */
    public function allotnewyeal(){
        $param = request()->param();
        $admin = cookie('admin');
        if($admin['mobile'] === "18782107004"){
            $nums = isset($param['nums']) ? intval($param['nums']) : 0;
            $data = [
                'value'=>$nums
            ];
            $map['name'] = "xnxzy_assign_count";
            DB::table('crm_setting')->where($map)->update($data);
            $str = date('Y-m-d H:i:s') . '_' . $admin['nickname'] . "_" . $nums . "\n";
            @file_put_contents("xnxzy_setting.ini", $str,FILE_APPEND);
            return json(['code'=>1,'message'=>"修改成功[" . $nums . "]个"]);
        }else{
            return json(['code'=>-1,'message'=>"无此权限！"]);
        }
    }
}