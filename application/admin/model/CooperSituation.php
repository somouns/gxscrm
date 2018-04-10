<?php

namespace app\admin\model;

use think\Model;
use think\Db;
class CooperSituation extends Model
{
    const ENABLED = 1;
    const DISABLED = -1;

    /**
     * 查询员工是否能被删除
     * @param $employeeId
     * @return bool  true|false  能|不能
     */
    public static function isCandeleteUser($employeeId)
    {
        $wherea = [];
        $wherea['employee_id'] = $employeeId;
        $userInfo = Db::name('customer_employee')->field('customer_id')->where($wherea)->select();
        if(!empty($userInfo) && is_array($userInfo)){
            return array('code'=>-3,'message'=>'此账号还有相关联的客户资料尚未移交，请处理妥善之后，再进行操作','data'=>'');
        }
        $where = [];
        $where['employee_id'] = $employeeId;
        $where['status'] = self::ENABLED;
        $cooperInfo = Db::name('customer_cooper_situation')->field('id')->where($where)->select();
        if (!$cooperInfo) {
            return true;
        }
        $cooperIds = array_column($cooperInfo, 'id');
        $where = [];
        $where['e.customer_cooper_situation_id'] = ['in', $cooperIds];
        $where['t.tag_id'] = 'hfytg';
        $list = Db::table('crm_customer_tag_cooper_extends')->alias('e')
            ->join('crm_customer_tag t', 'e.customer_tag_id = t.id')
            ->where($where)->field('e.customer_cooper_situation_id as id')->select();
        if (!$list) {
            return array('code'=>-2,'message'=>'此账号还有相关联的移交单尚未处理完成，请处理妥善之后，再进行操作','data'=>'');
        }
        $coopers = array_column($list, 'id');
        if (array_diff($cooperIds, $coopers)) {
            return array('code'=>-2,'message'=>'此账号还有相关联的移交单尚未处理完成，请处理妥善之后，再进行操作','data'=>'');
        } else {
            return true;
        }
    }

}
