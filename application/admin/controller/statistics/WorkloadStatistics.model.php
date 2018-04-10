<?php
/**
 * Created by PhpStorm.
 * User: Guan
 * Date: 2017-10-19
 * Time: 15:47
 */

namespace app\admin\controller\statistics;


use app\common\controller\Backend;

class WorkloadStatistics extends Backend
{

    public function index(){
        return $this->view->fetch();
    }
    







}