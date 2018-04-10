<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\complaint;


use app\common\controller\Backend;

class Complaint extends Backend
{
    /*
     * 发起投诉页面
     */
    public function complaint_maked(){
        return $this->view->fetch();
    }


    /*
     * 投诉记录页面
     */
    public function complaint_list(){
        return $this->view->fetch();
    }

    /*
     * 投诉详情页面
     */
    public function complaint_matter(){
        return $this->view->fetch();
    }















    /*
     * 导出文件
     */
    public function import_csv(){
        echo 1;exit;
        $params = $this->request->request();
        $data['mark_id'] = trim($params['mark_id']);
        $data['employee_id'] = trim($params['employee_id']);//$this->employeeId;
        $data['complaint_type'] = trim($params['complaint_type']);
        $data['status'] = trim($params['status']);
        $data['start_time'] = trim($params['start_time']);
        $data['end_time'] = trim($params['end_time']);
        $data['search_value'] = trim($params['search_value']);
        $data['type'] = trim($params['type']);
        $data['flag'] = trim($params['flag']);
        if(!empty($data['type']) && $data['type'] == 1){
            $csv_data = $this->visitCrmApi("complaint","complaintRecord",$data);
        }else{
            return json_encode(['code'=>'-1','message'=>'数据错误']);
        }
        $title_arr = ["投诉人","投诉时间","投诉类型（1：服务，2：产品）","投诉内容","投诉发起人","投诉处理人","处理状态（1:待处理，2:处理中，3：已处理）","处理结果（1：用户满意，2：投诉升级，3：需销售协助，4：线下退款，5：其它）","投诉说明"];
        $file_name = "投诉数据".date("Y-m-d H:i:s",time());
        $this->deal_csv($csv_data,$title_arr,$file_name);
    }

    /*
     * 处理数据
     * $data 二维属猪
     * $title_arr 一维
     */
    public function deal_csv($data,$title_arr,$file_name = ""){
        if(empty($data) && empty($title_arr) && is_array($data) && is_array($title_arr)){
             // 标题
            ini_set("max_execution_time", "300");
            $csv_data = '';
            $nums = count($title_arr);
            for($i=0;$i<$nums-1;++$i){
                $csv_data .= ' " '.$title_arr[$i].' ", ';
            }
            if($nums>0){
                $csv_data .= ' " '.$title_arr[$nums-1]. "\"\r\n";
            }
            //数据
            foreach($data as $key=>$val){
                for($i=0;$i<$nums-1;++$i){
                    $val[$i] = str_replace("\"","\"\"",$val[$i]);
                    $csv_data .= ' " '.$val[$i].' ", ';
                }
                $csv_data .= '"'.$val[$nums-1].'",';
                unset($data[$key]);
            }
            $file_name = empty($file_name) ? date("Y-m-d H:i:s",time()) : $file_name;
            $csv_data = iconv("utf-8","gb2312",$csv_data);
            header("Content-type:text/csv;");
            header("Content-Disposition:attachment;filename=" . $file_name."csv");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            echo $csv_data;die();
        }else{
            return json_encode(['code'=>'-1','message'=>'格式错误']);
        }
    }






}