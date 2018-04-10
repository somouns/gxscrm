<?php
/**
 * Created by PhpStorm.
 * User: Guan
 * Date: 2017-10-19
 * Time: 15:47
 */

namespace app\admin\controller\statistics;


use app\common\controller\Backend;

class PerformanceStatistics extends Backend
{

    public function index(){
        return $this->view->fetch();
    }
    /**
     *个人业绩
     */
    public function personal()
    {

    }
    /*
     * 团队业绩
     */
    public function team(){

    }

    /**
     * 导出统计
     * Created by:guan
     */

    public function export()
    {
        $param = $this->request->request();
//        print_r($param);exit;
        $name = isset($param['product_name']) ? $param['product_name'] : "";
        $cid = isset($param['cid']) ? $param['cid'] : "";
        $stime = isset($param['start_time']) ? $param['start_time'] : "";
        $etime = isset($param['end_time']) ? $param['end_time'] : "";
        $volume = isset($param['volume']) ? $param['volume'] : "";
        $turnover = isset($param['turnover']) ? $param['turnover'] : "";
        $educe = $param['educe'];
        if(empty($educe)){
            die("参数错误");
        }
        if(!empty($educe) && $educe == 1){
            $data =$this->visitCrmApi("performance_statistics","personalLook",['product_name'=>$name,"start_time"=>$stime,"end_time"=>$etime,"volume"=>$volume,"turnover"=>$turnover,'cid'=>$cid]);
            $title = array("部门","销售人员","成交量","成交额");
            $list = json_decode($data, true);
            $new = $list['data'];
            if(!empty($new)){
                foreach ($new['list'] as $item){
                    $con[] = $item;
                }
            }
        }elseif(!empty($educe) && $educe == 2){
            $data =self::visitCrmApi("performance_statistics","teamLook",['product_name'=>$name,"start_time"=>$stime,"end_time"=>$etime,"volume"=>$volume,"turnover"=>$turnover]);
            $title = array("团队","成交量","成交额");
            $list = json_decode($data, true);
            $new = $list['data'];
//        print_r($new);exit;
            if(!empty($new)){
                foreach ($new['list'] as $item){
                    $con[] = $item;
                }
            }
            array_unshift($con,array("all"=>"全部","allnum"=>$new['allnum'],"allmoney"=>$new['allmoney']));
        }
//        print_r($con);exit;
        $file_name = md5(time() . rand(1000, 9999));
        $this->csv_export($con, $title, $file_name);
    }




    function csv_export(&$data, $titleList = array(), $fileName = '')
    {
        ini_set("max_execution_time", "3600");
        $csvData = '';
        // 标题
        $nums = count($titleList);
        for ($i = 0; $i < $nums - 1; $i++)
        {
            $csvData .= '"' . $titleList[$i] . '",';
        }
        $csvData .= '"' . $titleList[$nums - 1] . "\"\r\n";
        if(!empty($data)&&is_array($data)){
            foreach ($data as $key => $row)
            {
                $i = 0;
                foreach ($row as $_key => $_val)
                {
                    $_val = str_replace("\"", "\"\"", $_val);
                    if ($i < ($nums - 1))
                    {
                        $csvData .= '"' . $_val . '",';
                    }
                    elseif ($i == ($nums - 1))
                    {
                        $csvData .= '"' . $_val . "\"\r\n";
                    }
                    $i++;
                }
                unset($data[$key]);
            }
        }
        $csvData = mb_convert_encoding($csvData, "cp936", "UTF-8");
        $fileName = empty($fileName) ? date('Y-m-d-H-i-s', time()) : $fileName;
        $fileName = $fileName . '.csv';
        header("Content-type:text/csv;");
        header("Content-Disposition:attachment;filename=" . $fileName);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $csvData;die();
    }







}