<?php
/**
* Created by PhpStorm.
* User: Guan
* Date: 2017-09-25
* Time: 15:47
*/

namespace app\admin\controller\service;


use app\common\controller\Backend;

class ServiceRecord extends Backend
{

    public function index()
    {
        return $this->view->fetch();
    }

    public function getlist(){

    }
    public function criterion(){

    }

    /**
     * 导出服务记录
     * Created by:guan
     */
    public function export()
    {
        $param = $this->request->request();
        $pname = !empty($param['product_name']) ? $param['product_name'] : "";
        $stime = !empty($param['start_time']) ? $param['start_time'] : "";
        $etime = !empty($param['end_time']) ? $param['end_time'] : "";
        $oname = !empty($param['operate_real_name']) ? $param['operate_real_name'] : "";
        $type = !empty($param['type']) ? $param['type'] : "";
        $educe = $param['educe'];
//        print_r($param);exit;
        if(empty($educe)){
            die("参数错误");
        }
        if(!empty($educe) && $educe == 1){
            $data = $this->visitCrmApi("service_record","criterion",['product_name'=>$pname,"start_time"=>$stime,"end_time"=>$etime,"operate_real_name"=>$oname,"followup_content"=>$type,"educe"=>$educe]);
        }
        $list = json_decode($data, true);
        $new = $list['data']['list'];
//        print_r($new);exit;
        $title = array("客户姓名","产品名称","服务内容","操作时间","操作人");
        $file_name = "服务记录".date("Y-m-d H:i:s",time());
        $this->csv_export($new, $title, $file_name);
    }


//    public function getCsvData($url)
//    {
////        $url = $_POST['url'];
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        // post数据
//        curl_setopt($ch, CURLOPT_POST, 1);
//        // post的变量
//        curl_setopt($ch, CURLOPT_POSTFIELDS,1);
//        //执行请求
//        $output = curl_exec($ch);
//        //打印获得的数据
//        curl_close($ch);
//        return $output;
//    }

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