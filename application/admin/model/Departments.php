<?php

namespace app\admin\model;

use think\Model;
use think\Session;
use think\Db;
use think\Cookie;
use app\admin\model\BusinessLog;

class Departments extends Model
{
    /*
     * 查询一级部门
     */
    public function getAll($param)
    {
        if (!empty($param['department_name'])) {
            $where['department_name'] = array('like', '%' . $param['department_name'] . '%');
        }
        if (!empty($param['linkman'])) {
            $where['linkman'] = array('like', '%' . $param['linkman'] . '%');
        }
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        $where['pid'] = 0;
        return Db::name('department')->field('id,department_name,linkman,mobile,createtime')->where($where)->order('createtime desc')->paginate(10, false, ['query' => request()->param()]);
    }

    /*
     * 添加公司
     */
    public function add($param)
    {
        $param['createtime'] = date('Y-m-d H:i:s', time());
        $result = Db::name('department')->insert($param);
        if ($result) {
            BusinessLog::addLog(89, [Cookie::get('admin')['nickname'], $param['department_name']]);
            return true;
        } else {
            return false;
        }
    }

    /*
     * 验证公司名称
     */
    public function checkName($param)
    {
        $where['department_name'] = $param;
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        return Db::name('department')->where($where)->find();
    }

    /*
     * 删除子公司
    */
    public function delDowns($id)
    {
        $where['pid'] = $id;
        return Db::name('department')->where($where)->delete();
    }

    /*
     * 编辑部门
     */
    public function edit($param)
    {
        $where['id'] = $param['id'];
        $result = Db::name('department')->where($where)->update($param);
        if ($result) {
            BusinessLog::addLog(90, [Cookie::get('admin')['nickname'], $param['department_name']]);
            return true;
        } else {
            return false;
        }
    }

    /*
     * 查询单个公司or部门
     */
    public function getOne($id)
    {
        $where['id'] = $id;
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        return Db::name('department')->field('id,department_name,linkman,mobile,createtime')->where($where)->find();
    }

    /*
     * 查询部门是否有子部门
     */
    public function checkPid($id)
    {
        $where['pid'] = $id;
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        $result = Db::name('department')->where($where)->select();
        return $result;
    }

    /*
     * 删除部门
     */
    public function del($id)
    {
        $wherea['status'] = 1;
        $res = Db::name('department')->field('id,pid,department_name as name')->where($wherea)->select();
        $aa = $this->get_attraa($res,$id);
        $eid = $id . $aa;
        Db::startTrans();
        try {
            $where['id'] = ['in',$eid];
            $data['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_DISABLED;
            Db::name('department')->where($where)->update($data);
            $datas['department'] = '';
            $whereq['department'] = ['in',$eid];
            Db::name('admin')->where($whereq)->update($datas);
            Db::commit();
            $param = $this->getOne($id);
            BusinessLog::addLog(91, [Cookie::get('admin')['nickname'], $param['department_name']]);
            return true;
        }catch(\Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }

    /*
     * 添加下级部门
     */
    public function addDown($params)
    {
        return Db::name('department')->insertAll($params);
    }

    /*
         * 添加下级部门
         */
    public function updateDown($params)
    {
        foreach ($params as $key => $value) {
            $rs = Db::name('department')->where("id=" . $value['id'])->update($value);
        }
        return $rs;

    }

    /*
     * 查询下级部门
     */
    public function getDown($pid)
    {
        $down = [];
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        $data = Db::name('department')->field('id,pid,department_name as name')->where($where)->select();
        if (!empty($data) && is_array($data)) {
            $down = $this->get_attr($data, $pid);
        }
        return $down;
    }

    /*
     * 全部
     */
    public function getlist()
    {
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        $list = Db::name('department')->field('id,pid,department_name as name')->where($where)->select();
        return $list;
    }

    /*
     * 添加用户搜索部门
     */
    public function getlists($name)
    {
        $where['department_name'] = array('like', '%' . $name . '%');
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        $list = Db::name('department')->field('id,pid,department_name as name')->where($where)->select();
        return $list;
    }

    /*
     * 公司下全部部门
     */
    public function getPidTree($pid)
    {
        $where['status'] = \app\admin\constant\DepartmentConstant::DEPARTMENT_NORMAL;
        $where['pid'] = $pid;
        $list = Db::name('department')->field('id,pid,department_name as name')->where($where)->select();
        return $list;
    }

    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    public function arraySequence($array, $field, $sort = 'SORT_ASC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }


    public function getTreeForChild($wenjianjia, $user_dep_arr)
    {
//      print_r($wenjianjia);exit;
        foreach ($wenjianjia as $key => &$value) {

            if (!empty($user_dep_arr[$value['id']])) {
                foreach ($user_dep_arr[$value['id']] as $k => $v) {

                    if (!isset($value['type'])) {
                        $value['child'][] = $v;
                    }
                }
            }
            if (isset($value['child']) && is_array($value['child'])) {

                $value['child'] = $this->getTreeForChild($value['child'], $user_dep_arr);
            }

        }
        return $wenjianjia;
    }

    public function getuser()
    {
        $wherea['status'] = 1;
        $data = Db::name('admin')->field('id,nickname as name,department')->where($wherea)->select();

        if (!empty($data)) {
            foreach ($data as $key => &$value) {
                # code...
                $value['type'] = "member";
            }
        }
        return $data;
    }

//数组转换
    public function array_to_array_key($arr, $field, $group = 0)
    {
        $array = [];
        if (empty($arr)) {
            return $array;
        }
        if ($group == 0) {
            foreach ($arr as $v) {
                if (array_key_exists($field, $v)) {
                    $array[$v[$field]] = $v;
                }
            }
        } else {
            foreach ($arr as $v) {
                if (array_key_exists($field, $v)) {
                    $array[$v[$field]][] = $v;
                }
            }
        }
        return $array;
    }

    /*
     * 公司下全部部门和人员  _child文件夹 child文件
     */
    public function getDepartment($wenjianjia)
    {
        $user_dep = $this->getuser();
        $type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
        //过滤群组管理员
        if ($type == 2) {
            $sql = "select employee_id from crm_group_employee where type = 2";
            $result = $this->query($sql);
            $group_manager_id_arr = array_flip(array_flip(array_column($result,'employee_id')));
            $user_dep_tmp = [];
            foreach ($user_dep as $k => $v) {
                if (!in_array($v['id'], $group_manager_id_arr)) {
                    $user_dep_tmp[] = $v;
                }
            }
            $user_dep = $user_dep_tmp;
        }//过滤群组管理员 end

        $user_dep_arr = $this->array_to_array_key($user_dep, 'department', 1);

        $rs = $this->getTreeForChild($wenjianjia, $user_dep_arr);

        return $rs;
    }

    /*
     * 树形结构
     */
    public function get_attr($a, $pid)
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

    // 查询下级部门id
    public function get_attraa($a, $pid)
    {
        $aa = '';
        foreach ($a as $v) {
            if ($v['pid'] == $pid) {
                $aa .= $this->get_attraa($a,$v['id']).",";
                $aa .= $v['id'];
            }
        }
        return $aa;
    }

    // 数组转换
    public function array_to_array_key_bb($arr, $field, $group = 0)
    {
        $array = [];
        if (empty($arr)) {
            return $array;
        }
        if ($group == 0) {

            foreach ($arr as $v) {
                if (array_key_exists($field, $v)) {
                    $array[$v[$field]] = $v;
                }
            }
        } else {
            foreach ($arr as $v) {
                if (array_key_exists($field, $v)) {
                    $array[$v[$field]][] = $v;
                }
            }
        }

        return $array;
    }

    /*
     * 修改下级部门
     */
    public function editDepartment($param)
    {
        $where['id'] = $param['id'];
        if (!empty($param['pid'])) {
            $data['pid'] = $param['pid'];
        }
        $data['department_name'] = $param['department_name'];
        $data['updatetime'] = date('Y-m-d H:i:s', time());
        $result = Db::name('department')->where($where)->update($data);
        BusinessLog::addLog(90, [Cookie::get('admin')['nickname'], $param['department_name']]);
        if (!empty($data['department_name'])) {
            return true;
        } else {
            return false;
        }

    }
}