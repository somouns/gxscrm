<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\contract;

use think\Cache;
use app\common\controller\Backend;

class Contract extends Backend
{

    /**
     * 合同列表
     */
    public function contract_list()
    {
        return $this->view->fetch();
    }

    /**
     * 合同全部处理记录
     */
    public function contract_record_list()
    {
        return $this->view->fetch('contract_list');
    }

    /**
     * 合同处理页面
     */
    public function contract_view()
    {
        return $this->view->fetch();
    }

    /**
     * 合同信息添加
     */
    public function contract_add()
    {
        return $this->view->fetch();
    }

    /**
     * 合同信息编辑
     */
    public function contract_edit()
    {
        return $this->view->fetch();
    }


    /**
     * 合同审核提交
     */
    public function contract_post()
    {
        return $this->view->fetch();
    }


    /**
     * 合同页面导出
     *
     */
    public function export()
    {
        $admin = $this->adminInfo;
        $employee_id = $admin['id'];
        //$data = $this->getCsvData("crm.api.guxiansheng.cn/index.php?c=contract&a=contract_record_list&from_station=crm_admin&employee_id=$employee_id");
        $keywords = isset($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';
        $mark_id = isset($_REQUEST['mark_id']) ? trim($_REQUEST['mark_id']) : '';
        $status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
        $start_time = isset($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';
        $end_time = isset($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';
        $post_start_time = isset($_REQUEST['post_start_time']) ? trim($_REQUEST['post_start_time']) : '';
        $post_end_time = isset($_REQUEST['post_end_time']) ? trim($_REQUEST['post_end_time']) : '';

        $param["pagesize"] = 1000;

        $param['keywords'] = $keywords;
        $param['mark_id'] = $mark_id;
        $param['status'] = $status;
        $param['start_time'] = $start_time;
        $param['end_time'] = $end_time;
        $param['post_start_time'] = $post_start_time;
        $param['post_end_time'] = $post_end_time;
        $param['is_csv'] = 1;
        $data = $this->visitCrmApi('contract','contract_record_list',$param);
        $list = json_decode($data, true);
        $new = $list['data']['list'];

        if(empty($list['data']['list'])){
            echo json_encode(array('code' => -1, 'message' => '没有任何数据！'));
            exit;
        }

        $keys = array_keys($list['data']['list'][0]);
        $header = array(
            '客户姓名',
            '购买产品',
            '购买数量',
            '购买金额(元)',
            '移交时间',
            '销售人员',
            '合同状态',
            '合同邮寄时间',
            '快递公司及运单号',
            '审核备注',
            '自定义备注'
        );

//        $file_name = md5(time() . rand(1000, 9999)) . ".csv";
        $file_name = '合同信息'.datetime(time(),'Y-m-d H-i-s') . ".csv";
        $this->export_csv_1($new, $keys,$header, $file_name);
    }


    /**
     * 28  * 导出CSV文件
     * 29  * @param array $data 数据
     * 30  * @param array $header_data 首行数据
     * 31  * @param string $file_name 文件名称
     * 32  * @return string
     * 33  */
    public function export_csv_1($data = [], $keys,$header_data = [], $file_name = '')
    {
        if (!empty($header_data)) {
            echo iconv('utf-8', 'gbk//TRANSLIT', '"' . implode('","', $header_data) . '"' . "\n");
        }
        foreach ($data as $key => $value) {
            $output = array();
            foreach ($keys as $item) {
                $output[] = $value[$item];
            }

            echo iconv('utf-8', 'gbk//TRANSLIT', '"' . implode('","', $output) . "\"\n");
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name);
    }

/**
     * 导入客户是点击下载失败数据
     */
    public function down_list(){
        $data_time = isset($_REQUEST['data_time']) ? $_REQUEST['data_time'] : '';
        $file_name = isset($_REQUEST['file_name']) ? $_REQUEST['file_name'] : '';
        $path = $file_name;

        if(!file_exists($path)){
            echo "没有该文件文件";
            return ;
        }

        //下载zip
        if (strstr($file_name,'.zip')) {
            header("Content-type:application/octet-stream");
            $filename = basename($file_name);
            header("Content-Disposition:attachment;filename = ".$filename);
            header("Accept-ranges:bytes");
            header("Accept-length:".filesize($file_name));
            readfile($file_name);die;

            $file_name=iconv("utf-8","gb2312",$file_name);
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header('Content-disposition: attachment; filename='.basename(time().'.zip')); //文件名  
            header('Content-Type: application/octet-stream'); //zip格式的  
            header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件  
            header('Content-Length: '. filesize($file_name)); //告诉浏览器，文件大小  
            @readfile($file_name);
            exit();
        }

        //下载csv
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=$file_name".'.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);

    }
    /**
     * 导入客户是点击下载失败数据
     */
    public function dow_error_fied(){
        $data_time = isset($_REQUEST['data_time']) ? $_REQUEST['data_time'] : '';
        $file_name = isset($_REQUEST['file_name']) ? $_REQUEST['file_name'] : '';
        $path = config('err_csv').$data_time.'/'.$file_name.'.csv';

        if(!file_exists($path)){
            echo "没有该文件文件";
            return ;
        }
        /*$file_size=filesize($path);
        Header( "Content-type:  application/vnd.ms-excel;charset=UTF-8 ");
        Header( "Accept-Ranges:  bytes ");
        Header( "Accept-Length: " .$file_size);
        header( "Content-Disposition:  attachment;  filename=$file_name".'.csv');
        //iconv("utf-8","gb2312",$path);
        //$path = fopen($path, 'w');
        //fwrite($path,chr(0xEF).chr(0xBB).chr(0xBF));
        //echo file_get_contents($path);
        readfile($path);*/

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=$file_name".'.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);die;
        //fclose($path);
    }

    /**
     * 需要从接口站点请求数据
     * Created by: Xerxes Sultan
     */
    public function getCsvData($url)
    {
//        $url = $_POST['url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"msg_id":"id007"}');
        //执行请求
        $output = curl_exec($ch);
        //打印获得的数据
        curl_close($ch);
        return $output;
    }


}