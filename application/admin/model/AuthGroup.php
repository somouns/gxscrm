<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Cookie;
use app\admin\model\BusinessLog;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

class AuthGroup extends Model
{

    /*
     * 添加权限组
     */
    public function addGroup($param)
    {
        try {
            if ($this->getRule($param['name'])) {
                return ['code' => -1, 'message' => '权限组名称已经存在'];
            }
            $param['createtime'] = date('Y-m-d H:i:s', time());
            $param['rules'] = @implode(',', $param['rules']);
            $param['datas'] = @implode(',', $param['datas']);
            Db::name('auth_group')->insert($param);
            BusinessLog::addLog(78, [Cookie::get('admin')['nickname'], $param['name']]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * 删除角色
     */
    public function delGroup($id)
    {

        if (empty($id)) {
            return false;
        }
        $param = $this->getOneGroup($id);
        Db::startTrans();
        try {
            $data['status'] = 0;
            $where['id'] = $id;
            // 查询删除权限组操作权限点和数据权限点
            $ids = Db::name('auth_group')->field('rules,datas,id')->where($where)->find();

            
            Db::name('auth_group')->where($where)->update($data);
            // 添加特殊权限队列
            $wherea['group_id'] = $id;
            $uids = Db::name('auth_group_access')->field('uid')->where($wherea)->select();
            if (!empty($uids) && is_array($uids)) {
                if(!empty($ids) && is_array($ids)) {
                    // 添加权限点队列 操作权限
                    foreach ($uids as $k => $v) {
                        if(!empty($ids['rules'])){
                            $datag['name'] = 'D_group';
                            $datag['uid'] = $v['uid'];
                            $datag['group_ids'] = $ids['rules'];
                            $datag['type'] = 1;
                            $datag['create_time'] = date('Y-m-d H:i:s', time());
                            $datag['group_id'] = $ids['id'];
                            Db::name('group_change_queue')->insert($datag);
                        }
                        
                    }
                    // 添加权限点队列 数据权限
                    foreach ($uids as $ks => $vs) {
                        if(!empty($ids['datas'])){
                            $datad['name'] = 'D_group';
                            $datad['uid'] = $vs['uid'];
                            $datad['group_ids'] = $ids['datas'];
                            $datad['type'] = 2;
                            $datad['create_time'] = date('Y-m-d H:i:s', time());
                            $datad['group_id'] = $ids['id'];
                            Db::name('group_change_queue')->insert($datad);
                          }
                    }
                }
            }
            Db::commit();
            if (!empty($uids) && is_array($uids)) {
                foreach ($uids as $key => $val) {
                    $datas['name'] = 'D_group';
                    $datas['create_time'] = date('Y-m-d H:i:s', time());
                    $datas['uid'] = $val['uid'];
                    Db::name('role_queue_special_log')->insert($datas);
                }
            }
            BusinessLog::addLog(80, [Cookie::get('admin')['nickname'], $param['name']]);
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /*
     * 修改权限组
     */
    public function edit($param)
    {
        Db::startTrans();
        try {
            if (empty($param['name']) || empty($param['id'])) {
                throw new Exception("名称不存在");
            }
            $data = $this->getRule($param['name']);
            if ($data && $data['id'] != $param['id']) {
                throw new Exception("权限组名称已经存在");
            }
            $where['id'] = $param['id'];
            //查询权限组修改之前的权限点
            $ids = Db::name('auth_group')->field('rules,datas,id')->where($where)->find();

            $data = [];
            $data['name'] = $param['name'];
            $data['rules'] = @implode(',', $param['rules']);
            $data['describe'] = $param['describe'];
            $data['datas'] = @implode(',', $param['datas']);
            Db::name('auth_group')->where($where)->update($data);

            // 添加特殊权限日志
            $wherea['group_id'] = $param['id'];
            $uids = Db::name('auth_group_access')->field('uid')->where($wherea)->select();
            if (!empty($uids) && is_array($uids)) {
                // 添加改变权限点队列
                foreach ($uids as $k => $v) {
                    if (!empty($param['rules']) && is_array($param['rules'])) {
                        $dd = array_diff(explode(',', $ids['rules']), $param['rules']);
                        $dd = array_values($dd);
                        if (!empty($dd) && is_array($dd)) {
                            $datad['name'] = 'U_group';
                            $datad['uid'] = $v['uid'];
                            $datad['group_ids'] = implode(',', $dd); //获取操作权限修改的权限点
                            $datad['create_time'] = date('Y-m-d H:i:s', time());
                            $datad['type'] = 1;
                            $datad['group_id'] = $ids['id'];
                            Db::name('group_change_queue')->insert($datad);
                        }
                    }
                }

                // 添加改变权限点队列s
                foreach ($uids as $ks => $vs) {
                    if (!empty($param['datas']) && is_array($param['datas'])) {
                        $gg = array_diff(explode(',', $ids['datas']), $param['datas']);
                        $gg = array_values($gg);
                        if (!empty($gg) && is_array($gg)) {
                            $datag['name'] = 'U_group';
                            $datag['uid'] = $vs['uid'];
                            $datag['group_ids'] = implode(',', $gg); //获取数据权限修改的权限点
                            $datag['type'] = 2;
                            $datag['create_time'] = date('Y-m-d H:i:s', time());
                            $datag['group_id'] = $ids['id'];
                            Db::name('group_change_queue')->insert($datag);
                        }
                    }
                }
            }

            Db::commit();
            if (!empty($uids) && is_array($uids)) {
                foreach ($uids as $key => $val) {
                    $datas['name'] = 'U_group';
                    $datas['create_time'] = date('Y-m-d H:i:s', time());
                    $datas['uid'] = $val['uid'];
                    Db::name('role_queue_special_log')->insert($datas);
                }
            }
            BusinessLog::addLog(70, [Cookie::get('admin')['nickname'], $param['name']]);
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }

    }


    /*
     * 权限列表
     */
    public function indexGroup($param)
    {
        if (!empty($param['name'])) {
            $where['name'] = array('like', '%' . $param['name'] . '%');
        }
        if (!empty($param['start_time'])) {
            $where['createtime'] = array(array('EGT', $param['start_time']), array('ELT', $param['stop_time']), 'AND');
        }
        $where['status'] = \app\admin\constant\GroupConstant::GROUP_NORMAL;
        $result = Db::name('auth_group')->field('id,name,describe,createtime')->where($where)->order('createtime desc')->paginate(10, false, ['query' => request()->param()]);
        return $result;
    }

    /*
     * 查询一个权限组
     */
    public function getOneGroup($id){
        return Db::name('auth_group')->field('name')->where("id = $id")->find();
    }
    /*
     * 查询某个角色
     */
    public function selectOneGroup($id)
    {
        $where['id'] = $id;
        $where['status'] = \app\admin\constant\GroupConstant::GROUP_NORMAL;
        $group_array = Db::name('auth_group')->where($where)->find();

        if (!empty($group_array) && is_array($group_array)) {
        }
        $group_array['datas'] = $this->getEditData($group_array['datas']);
        $group_array['rules'] = $this->getEditRule($group_array['rules']);
        return $group_array;
    }

    /*
     * 查询某个角色对应用户
     */
    public function selectOneGroupAccess($id)
    {
        $where['group_id'] = $id;
        $result = Db::name('auth_group_access')->where($where)->find();
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * 判断角色名称唯一
     */
    public function getRule($name)
    {
        $where['name'] = $name;
        $where['status'] = \app\admin\constant\GroupConstant::GROUP_NORMAL;
        $result = Db::name('auth_group')->field('id,name')->where($where)->find();
        return $result;
    }

    /*
     * 处理操作权限选中
     */
    public function getEditRule($datas)
    {
        $ids = explode(',', $datas);
        $list = $this->getrules();

        foreach ($list as $key => $value) {
            if (in_array($value['id'], $ids)) {
                $list[$key]['selected'] = 1;
            } else {
                $list[$key]['selected'] = 0;
            }
        }

        return makeTree($list);
    }

    /*
     * 处理数据权限选中
     */
    public function getEditData($datas)
    {
        $ids = explode(',', $datas);
        $list = $this->getDatas();
        foreach ($list as $key => $value) {
            if (in_array($value['id'], $ids)) {
                $list[$key]['selected'] = 1;
            } else {
                $list[$key]['selected'] = 0;
            }
        }
        return makeTree($list);
    }

    /*
     * 获取所有数据权限点
     */
    public function getDatas()
    {
        $where['status'] = 1;
        return Db::name('auth_data')->field('id,pid,name')->where($where)->select();
    }

    /*
     * 获取所以操作权限点
     */
    public function getrules()
    {
        $where['status'] = \app\admin\constant\GroupConstant::GROUP_NORMAL;
        return Db::name('auth_rule')->field('id,pid,title')->where($where)->select();
    }

    /*
     * 查看权限组
     */
    public function seeGroup($id)
    {
        $where['id'] = $id;
        $data = Db::name('auth_group')->field('name,describe,id,datas,rules')->where($where)->find();
        /* 查询拥有的操作权限点 */
        $wherer['id'] = array('in', $data['rules']);
        $rules = Db::name('auth_rule')->field('id,pid,title')->where($wherer)->select();
        $data['rules'] = makeTree($rules);
        /* 查询拥有的数据权限点 */
        $whered['id'] = array('in', $data['datas']);
        $datas = Db::name('auth_data')->field('id,pid,name')->where($whered)->select();
        $data['datas'] = makeTree($datas);
        return $data;
    }

    /**
     * 查询指定权限组的所有权限点
     * @param $id
     * Created by: Xerxes Sultan
     */
    public function selectOneGroupAuth($id)
    {
        $where['id'] = $id;
        $data = Db::name('auth_group')->field('id,rules')->where($where)->find();
        return $data;
    }

    /**
     * 获取指定权限组下的所有用户信息
     * @param $id
     * Created by: Xerxes Sultan
     */
    public function selectGroupAdmin($id)
    {
        $where['group_id'] = $id;
        $data = Db::name('auth_group_access')->field('uid')->where($where)->select();
        return $data;
    }

    /**
     * 查询多个用户的特殊权限
     * @param $ids
     * Created by: Xerxes Sultan
     */
    public function selectEmployeeSpecialRule($ids)
    {
        $where['employee_id'] = array('in', $ids);
        $data = Db::name('employee_special_rule')->field('id,employee_id,type')->where($where)->select();

        return $data;
    }

    /**
     * 删除多个用户下面的所有特殊权限
     * @param $ids
     * Created by: Xerxes Sultan
     */
    public function deleteSpecialRules($ids)
    {

        $where['employee_id'] = array('in', $ids);
        $deleteBoolean = Db::name('employee_special_rule')->where($where)->delete();
        return $deleteBoolean;
    }

    /**
     * 给该权限组中的所有用户新增一条指定的特殊权限
     * @param $param
     * Created by: Xerxes Sultan
     */
    public function insertSpecailRules($data)
    {
        $insertBoolean = Db::name('employee_special_rule')->insertAll($data);
        return $insertBoolean;
    }


    /**
     * 更新角色组权限表
     * @param $update_where
     * @param $update_data
     * Created by: Xerxes Sultan
     */
    public function updateGroupAuth($update_where, $update_data)
    {
        $updateBoolean = Db::name('auth_group')->where($update_where)->update($update_data);
        return $updateBoolean;
    }
}
