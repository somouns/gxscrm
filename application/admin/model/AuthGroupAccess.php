<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Exception;
use think\Cookie;
use app\admin\model\BusinessLog;

class AuthGroupAccess extends Model
{

    /*
     *获取单个权限组的下面所有的员工关系
     */
    public function  group_permissions($param){
        $data = DB::table('crm_auth_group_access')->where('group_id = :param' ,['param'=>$param])->select();
        return $data;
    }

    public function  group_employee($e_id,$condition,$pagesize){
        try{
            $department1 = [];
            $position1 = [];
            $level1 = [];
            $condition['id'] = array('in',$e_id);
            $data = DB::table('crm_admin')->field("id,nickname,username,mobile,department,position,level,build_room,status")->where($condition)->paginate($pagesize);
            $ll = $data->toArray();
            if(!empty($ll['data'])){
                $department =  DB::table('crm_department')->field("id,department_name")->where(['status'=>1])->select();
                $position = DB::table('crm_position')->field("id,position_name")->where(['status'=>1])->select();
                $level =  DB::table('crm_grade')->field("id,grade_name")->where(['status'=>1])->select();
                foreach ($department as $k1=>$v1){
                    $department1[$v1['id']] = $v1['department_name'];
                }
                foreach ($position as $k2=>$v2){
                    $position1[$v2['id']] = $v2['position_name'];
                }
                foreach ($level as $k3=>$v3){
                    $level1[$v3['id']] = $v3['grade_name'];
                }
                foreach ($ll['data'] as $key=>$val){
                    $ll['data'][$key]['id'] = $val['id'];
                    $ll['data'][$key]['nickname'] = $val['nickname'];
                    $ll['data'][$key]['username'] = $val['username'];
                    $ll['data'][$key]['mobile'] = $val['mobile'];
                    $ll['data'][$key]['department'] = @$department1[$val['department']];
                    $ll['data'][$key]['position'] = @$position1[$val['position']];
                    $ll['data'][$key]['level'] = @$level1[$val['level']];
                    $ll['data'][$key]['build_room'] = $val['build_room'];
                    $ll['data'][$key]['status'] = $val['status'];
                }
            }
            return $ll;
        }catch(Exception $e){
            return $e->getMessage();
        }
    }




}
