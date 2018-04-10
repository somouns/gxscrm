<?php

namespace app\admin\controller\customers;

use app\common\controller\Backend;
use think\Cookie;
use think\Db;
use think\Loader;
use think\Validate;
use think\Request;
use fast\Curl;
use think\Config;
use think\Log;
use fast\Forward;


/**
 * 客户
 * @internal
 */
class Customer extends Backend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = [];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        self::predis();
    }

    public static function predis()
    {
        //dump(\think\Session::get());exit;
        #Predis::init();//初始化redis集群
    }


    /**
     * 员工客户列表
     */
    public function index()
    {
        return $this->view->fetch();
    }


    /**
     * 员工新增客户
     * @param int $from_channel 客户来源： 1 股先生  2 微信推广  3 其他,
     * @param string $mark 来源备注,
     * @param string $real_name 真实姓名,
     * @param int $sex 性别：1 男  2 女,
     * @param string $qq QQ号,
     * @param string $weixin 微信号,
     * @param string $mobile 手机号码(多个用逗号分隔) '13211112222,13311112222',
     * @param int $customer_type 客户分组，系统和自定义表中的ID
     * @param string $custom_mark 客户备注id(多个用逗号分隔) '1,2',
     */
    public function add()
    {
        return $this->view->fetch();
    }


    //解析csv文件
    public function input_csv($filename)
    {
        $handle = fopen($filename, 'r');
        $out = array();
        $n = 0;
        while (($data = fgetcsv($handle, 10000)) && !feof($handle)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        fclose($handle);
        return $out;
    }


    /**
     * 员工编辑客户
     * @param int $from_channel 客户来源： 1 股先生  2 微信推广  3 其他,
     * @param string $mark 来源备注,
     * @param string $real_name 真实姓名,
     * @param string $id_card 身份证,
     * @param int $sex 性别：1 男  2 女 3 保密,
     * @param int $age 年龄,
     * @param string $career 职业,
     * @param int $age 年龄,
     * @param string $qq QQ号,
     * @param string $weixin 微信号,
     * @param string $mobile 手机号码(多个用逗号分隔) '13211112222,13311112222',
     * @param int $province 省,
     * @param int $city 市,
     * @param int $area 县,
     * @param string $address 通讯地址,
     * @param int $customer_type 客户分组，系统和自定义表中的ID
     * @param string $custom_mark 客户备注id(多个用逗号分隔) '1,2',
     */
    public function update()
    {
        return $this->view->fetch();
    }


    /**
     * 员工导入客户
     * @param int $from_channel 客户来源： 1 股先生  2 微信推广  3 其他,
     * @param string $id_card 身份证,
     * @param string $real_name 真实姓名,
     * @param string $qq QQ号,
     * @param string $weixin 微信号,
     * @param string $mobile 手机号码(多个用逗号分隔) '13211112222,13311112222',
     * @param int $customer_type 客户分组，系统和自定义表中的ID
     */
    public function import_old()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('memory_limit','1024m');
        //-------------------------------处理csv文件---s------------------------------------
        $csv_array = explode('.', $_FILES['import_csv']['name']);
        $file_type = end($csv_array);
        if (strtolower($file_type) <> "csv") {
            echo json_encode(array('code' => -1, 'message' => '请选择要导入的CSV文件！'));
            exit;
        }
        if (!empty($_FILES['import_csv']) && !empty($_FILES['import_csv']['name'])) {
            $filename = $_FILES['import_csv']['tmp_name'];
            if (empty ($filename)) {
                echo '请选择要导入的CSV文件！';
                exit;
            }
            $result = $this->input_csv($filename); //解析csv
        }
        if (count($result) == 1) {//只有标题栏
            echo json_encode(array('code' => -1, 'message' => '没有任何数据！'));
            exit;
        }
        $newarr = array();
        foreach ($result as $k => $v) {
            $flag = false;
            foreach ($v as $k2 => $v2) {
                if (!empty($result[$k][$k2])) {
                    $flag = true;
                }
                $result[$k][$k2] = mb_convert_encoding($result[$k][$k2],'utf-8','gb2312');//中文转码
            }
            if (!$flag) {
                $result[$k] = array();
            }
            if ($k > 0) {
                $newarr[] = $result[$k];
            }
        }
        //手动调用接口
        $url = Config::get('crm_api_url') . '?c=customer&a=import';
        $requestData['employee_id'] = $this->adminInfo['id'];
        $requestData['from_station'] = 'crm_admin';
        $requestData['nickname'] = $this->adminInfo['nickname'];
        $requestData['customerData'] = $newarr;
        $requestData['customer_type'] = $_REQUEST['customer_type'];
        $requestData['c'] = 'customer';
        $requestData['a'] = 'import';
        \app\admin\model\AdminLog::crmApirecord('customer', 'import');
//        print_r($requestData);die;
        $results = Forward::httpCurlPost($url, $requestData, 1);
        echo $results;
        exit;
        //-------------------------------处理csv文件---e------------------------------------


    }
    public function import()
    {

        $info = Cookie::get('admin');
        $request = request();
        $uname =  $info['nickname'];
        $type = intval($request->param("customer_type"));
        /*if(empty($type)){
            echo json_encode(array('code' => -1, 'message' => '请选择群组！'));
            exit;
        }*/

        //来源id
        $channel_id = intval($request->param("channel_id"));
        if(empty($channel_id)){
            echo json_encode(array('code' => -1, 'message' => '请选择来源！'));
            exit;
        }

        $encode = mb_detect_encoding($uname, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        if ($encode == "GBK"){
            $uname = iconv("GBK","UTF-8",$uname);
        }
        $data = [];
        //-------------------------------处理csv文件---s------------------------------------
        $csv_array = explode('.', $_FILES['import_csv']['name']);

        $file_type = end($csv_array);
        if (strtolower($file_type) <> "csv") {
            echo json_encode(array('code' => -1, 'message' => '请选择要导入的CSV文件！'));
            exit;
        }
        if (!empty($_FILES['import_csv']) && !empty($_FILES['import_csv']['name'])) {
            $filename = $_FILES['import_csv']['tmp_name'];
            if (empty ($filename)) {
                echo '请选择要导入的CSV文件！';
                exit;
            }
            $ende = mb_detect_encoding($filename, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
            if ($ende == "GBK"){
                $filename = iconv("GBK","UTF-8",$filename);
            }
            $path = "/data/share/importcustomer/".date('Ymd')."/";
            if(!file_exists($path)){
                mkdir($path,0777,true);
            }
            $name = md5(microtime(true)).'-'.$_FILES['import_csv']['name'];
            $address = $path.$name;
            $result = move_uploaded_file($filename, $address);
            if ($result){
                $data = [
                    'infilename'=>$address,
                    'uname'=>$uname,
                    'uid'=>$info['id'],
                    'type'=>$type,
                    'create_time'=> date("Y-m-d H:i:s",time()),
                    'channel_id' => $channel_id
                ];
                Db::table("crm_import_queue")->insert($data);
                return json(['code' => 1, 'message' => '导入客户成功加入队列中，请稍后查看导入结果。']);
            }else{
                echo '文件上传失败！';
                exit;
            }
        }else{
            echo '请导入文件！';
            exit;
        }

        //-------------------------------处理csv文件---e------------------------------------


    }
    /**
     * 导出客户的权限点
     * Created by: Xerxes Sultan
     */
    public function export()
    {
        $pagesize = $this->request->get("pagesize");
        $params['csv'] = "down";
        $params['csv_pagesize'] = $pagesize;
        $data = $this->visitCrmApi("customer", "csvCustomer", $params,0);
        $list = json_decode($data, true);
        if (!$list['data']) {
            echo json_encode(array("code" => -1, "message" => "客户列表没有数据", "data" => []));
            die;
        }
        $new = $list['data'];
        $keys = array_keys($new[0]);
        $header = array(
            "客户名字",
            "客户性别",
            "来源",
            "最近联系",
            "最近动态",
            "最近动态时间",
            "最近成交",
            "所买商品"
        );
//        $file_name = md5(time() . rand(1000, 9999)) . ".csv";
        $file_name = '客户资料'.datetime(time(),'Y-m-d H-i-s') . ".csv";
        $this->export_csv_1($new, $keys, $header, $file_name);
    }


    /**
     * 28  * 导出CSV文件
     * 29  * @param array $data 数据
     * 30  * @param array $header_data 首行数据
     * 31  * @param string $file_name 文件名称
     * 32  * @return string
     * 33  */
    public function export_csv_1($data = [], $keys, $header_data = [], $file_name = '')
    {
        if (!empty($header_data)) {
            echo iconv('utf-8', 'gbk//TRANSLIT//IGNORE', '"' . implode('","', $header_data) . '"' . "\n");
        }
        foreach ($data as $key => $value) {
            $output = array();
            foreach ($keys as $item) {
                if ($item == 'lately_contact_time') {
                    $value[$item] = $value[$item] == '0000-00-00 00:00:00' ? '无' :$value[$item];
                }elseif ($item == 'lately_dynamic_time') {
                    $value[$item] = $value[$item] == '0000-00-00 00:00:00' ? '无' :$value[$item];
                }elseif ($item == 'product_name') {
                    $value[$item] = $value[$item]  ? $value[$item] : '无';
                }elseif ($item == 'cooper_situation') {
                    $value[$item] = $value[$item]  ? $value[$item] : '无';
                }
                $output[] = $value[$item]  ? $value[$item] : '无';
            }
            echo iconv('utf-8', 'gbk//TRANSLIT//IGNORE', '"' . implode('","', $output) . "\"\n");
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name);
    }


    /**
     * 共享客户
     */
    public function share_customer()
    {
    }

    /**
     * 客户转移
     */
    public function transfer_customer()
    {
    }

    /**
     * 移入客户池
     */
    public function move_customer_to_pool()
    {
    }

    /**
     * 删除客户
     */
    public function delete_customer()
    {
    }

    /**
     * 清理客户
     */
    public function batch_delete_customer_pool()
    {
    }

    /**
     * 移动客户分组
     */
    public function move_customer_group()
    {
    }

    /**
     * 客户分配
     */
    public function customer_allocation()
    {
    }


    /**
     * 接口转发接口站点方法
     */
    public function crmAdmin()
    {
    }

    /**
     * 基本信息
     */
    public function detail()
    {
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
    

    /**
     * [bundling 查询绑定用户]
     * @Author   W_wang
     * @email    1352255400@qq.com
     * @DateTime 2017-11-21T17:57:00+0800
     * @return   [type]                   [description]
     */
    public function bundling()
    {
        return $this->view->fetch();
    }


    /**
     * [export_cooper_situation 导出合作情况]
     * @Author   W_wang
     * @email    1352255400@qq.com
     * @DateTime 2017-11-29T10:05:45+0800
     * @return   [type]                   [description]
     */
    public function export_cooper_situation()
    {
        $params = [];
        $fields = [];
        $fields['time_start'] = '请选择开始时间！';
        $fields['time_end'] = '请选择结束时间！';
        foreach ($fields as $k => $v) {
            $val = isset($_REQUEST[$k]) ? trim($_REQUEST[$k]) : '';
            if (empty($val)) {
                echo json_encode(array('code'=>'1000','data'=>[],'msg'=>$v));die;
            }
            $params[$k] = $val;
        }
        // p($params);die;
        $data = $this->visitCrmApi("customer", "export_cooper_situation", $params);
        $list = json_decode($data, true);
        // p($list);die;
        /*if ($list['code'] == -1) {
            echo json_encode($list);
            die;
        }*/


        //输出表头
        $header = array(
           '客户姓名',
           '购买电话',
           '购买产品',
           '购买数量',
           '购买金额',
           '支付日期',
           '移交时间' ,
           '销售人员',
           '销售人员部门',
           '支付方式',
           '合同类型',
           '合同状态',
           '合同处理时间',
           '销售备注'
        );
        echo iconv('utf-8', 'gbk//TRANSLIT', '"' . implode('","', $header) . '"' . "\n");
        //输出表头 end

        $data = isset($list['data']) ? $list['data'] : [];
        // p($data);die;
        $fields = [
                'real_name',
                'pay_mobile',
                'product_name',
                'pay_num',
                'pay_money',
                'pay_time',
                'move_time',
                'nickname',
                'department_name',
                'pay_type',
                'contract_type',
                'compliance_status',
                'customer_id',
                'remark'
                ];
        foreach ($data as $k => $v) {
            $output = array();
            foreach ($fields as $val) {
                $output[] = $v[$val];
            }
            echo iconv('utf-8', 'gbk//TRANSLIT', '"' . implode('","', $output) . "\"\n");
        }


        $file_name = "服务数据.csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name);
    }

}
