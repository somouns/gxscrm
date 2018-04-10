<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\visit;


use app\common\controller\Backend;

class Visit extends Backend
{

    /**
     * 回访用户列表
     */
    public function visit_list()
    {
        /*$pram['from_station'] = 'crm_admin';
        $data = $this->curlExecute('http://localhost/jjcrm/branches/deving/app/index.php?c=fileGroup&a=list',$pram);*/
        /*$data = $this->requestCrmApi();
        return json_encode($data);*/
        //var_dump($data);exit;
        return $this->view->fetch();
    }

    /**
     * 回访全部记录
     */
    public function visit_record_list()
    {
        return $this->view->fetch('visit_list');
    }

    /**
     * 回访处理页面
     */
    public function visit_view()
    {
        return $this->view->fetch();
    }

    /**
     * 回访审核提交
     */
    public function visit_post()
    {
        return $this->view->fetch();
    }

}