<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\financial;


use app\common\controller\Backend;

class Financial extends Backend
{

    /**
     * 财务客户列表
     */
    public function financial_list()
    {
        return $this->view->fetch();
    }

    /**
     * 财务全部审核记录
     */
    public function financial_record_list()
    {
        return $this->view->fetch('financial');
    }

    /**
     * 财务审核页面
     */
    public function financial_view()
    {
        return $this->view->fetch();
    }

    /**
     * 财务审核提交
     */
    public function financial_post()
    {
        return $this->view->fetch();
    }


    //-------------------------------------------------------分割线---------------------------------------------------
    /**
     * 财务客户列表
     */
    public function financial_lists()
    {print_r('112');exit;

    }


    /**
     * 财务全部审核记录
     */
    public function financial_record_lists()
    {

    }

    /**
     * 财务审核页面
     */
    public function financial_views()
    {

    }

    /**
     * 财务审核提交
     */
    public function financial_posts()
    {

    }

    //-------------------------------------------------------分割线---------------------------------------------------


    /*
   * 导出文件
   */
    public function export_csv(){
        $params = $this->request->request();
//         print_r($params);exit;
        $data = [];
        $data['start_time'] = trim($params['start_time']);
        $data['end_time'] = trim($params['end_time']);
        if ($data['start_time'] == $data['end_time']) {
            header("Content-type:text/html;charset=utf8");
            exit("导出开始时间和结束时间不能相等<br>请用插件选择时间，不要手动输入<br>开始时间：" . $data['start_time'] . "<br>结束时间：" . $data['end_time']  );
        }
        $csv_data = $this->visitCrmApi("financial","financial_data",$data);
        $csv_data = json_decode(html_entity_decode($csv_data),true);
        if($csv_data['code'] == -1) {
            $list = [['无'],['无'],['无'],['无'],['无'],['无'],['无'],['无'],['无'],['无'],['无'],['无'],['无']];
            $title_arr = ["日期","理财顾问","部门","职务","客户","电话","购买产品" ,"订单量","订单金额","支付时间","支付方式","支付渠道","订单号（三方支付单号）"];
            $file_name = "财务报表".date("Y-m-d H:i:s",time());
            $this->deal_csv($list,$title_arr,$file_name);
        } else {
            $list = $csv_data['data'];
            if(empty($list)){
                header("Content-type:text/html;charset=utf8");
                exit("数据为空！");
            }
            $title_arr = ["日期","理财顾问","部门","职务","客户","电话","购买产品" ,"订单量","订单金额","支付时间","支付方式","支付渠道","订单号（三方支付单号）"];
            $file_name = "财务报表".date("Y-m-d H:i:s",time());
            $this->deal_csv($list,$title_arr,$file_name);
        }
    }

    /*
     * 处理数据
     * $data 二维属猪
     * $title_arr 一维
     */
    public function deal_csv($data,$title_arr,$file_name = ""){
        if(!empty($data) && !empty($title_arr) && is_array($data) && is_array($title_arr)){
            // 标题
            ini_set("max_execution_time", "300");
            $csv_data = '';
            $nums = count($title_arr);
            for($i=0;$i<$nums-1;++$i){
                $csv_data .= '"'.$title_arr[$i].'",';
            }
            if($nums>0){
                $csv_data .= '"'.$title_arr[$nums-1]."\"\n";
            }
            //数据
            foreach ($data as $key => $row) {
                $i = 0;
                foreach ($row as $_key => $_val) {
                    $_val = str_replace("\"", "\"\"", $_val);
                    if ($_key == 'order_id') {
                        $_val = is_numeric($_val)?$_val."\t":$_val;
                    }
                    if ($i < ($nums - 1)) {
                        $csv_data .= '"' . $_val . '",';
                    } elseif ($i == ($nums - 1)) {
                        $csv_data .= '"' . $_val . "\"\n";
                    }
                    ++$i;
                }
                unset($data[$key]);
            }
            $file_name = empty($file_name) ? date("Y-m-d H:i:s",time()) : $file_name;
            $csv_data = iconv("utf-8","gb2312//IGNORE",$csv_data);
            header("Content-type:text/csv;");
            header("Content-Disposition:attachment;filename=" . $file_name.".csv");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            echo $csv_data;die();
        }else{
            return json_encode(['code'=>'-1','message'=>'格式错误']);
        }
    }
}