<?php

namespace app\common\controller;

use app\admin\library\Auth;
use fast\Curl;
use fast\Forward;
use think\Config;
use think\Controller;
use think\Cookie;
use think\Session;

//load_trait('library/traits/Backend');

/**
 * 后台控制器基类
 */
class Backend extends Controller
{
    protected $code = null;     //返回码,默认为null,当设置了该值后将输出json数据

    protected $data = null;     // 返回内容,默认为null,当设置了该值后将输出json数据

    protected $msg = '';        //返回文本,默认为空

    protected $noNeedLogin = [];    //无需登录的方法,同时也就不需要鉴权了

    protected $noNeedRight = [];    //无需鉴权的方法,但需要登录

    protected $layout = '';  //布局模板

    protected $auth = null;         //权限控制类

    protected $searchFields = 'id'; //快速搜索时执行查找的字段

    protected $relationSearch = false; //是否是关联查询

    protected $modelValidate = false;   //是否开启Validate验证

    protected $modelSceneValidate = false;  //是否开启模型场景验证

    protected $multiFields = 'status';  //Multi方法可批量修改的字段
    
    protected $adminInfo = null;

//    use \app\admin\library\traits\Backend;  //引入后台控制器的traits

    public function _initialize()
    {
        $modulename = $this->request->module();
        $controllername = strtolower($this->request->controller());
        $actionname = strtolower($this->request->action());
        $path = '/' . $modulename . '/' . str_replace('.', '/', $controllername) . '/' . $actionname;
        $this->visitAuth($path);
        // 设置面包屑导航数据
        $breadcrumb = $this->auth->getBreadCrumb($path);
        array_pop($breadcrumb);
        $this->view->breadcrumb = $breadcrumb;
        $this->getBaseConfigInfo($modulename, $controllername, $actionname);
        $this->requestCrmApi();
        $this->addSecret();
    }

    protected  function addSecret()
    {
        $random = mt_rand(10000,99999);
        $data = [];
        $data['random'] = $random;
        $data['member_id'] = $this->adminInfo['id'];
        $data['mobile'] = $this->adminInfo['mobile'];
        $data['crm_api_key'] = Config::get('crm_api_key');
        ksort($data);
        $str = "";
        foreach ($data as $k => $v) {
            $str .= $k.$v;
        }
        $secret = md5(md5($str));
        $this->assign("visithidden", $secret);
        $this->assign("crmrandom",$random);
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
        //Lang::load(APP_PATH . $this->request->module() . '/lang/' . Lang::detect() . '/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 访问鉴权
     * @param $path
     */
    protected function visitAuth($path)
    {
        $this->auth = Auth::instance();

        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)){
            //检测是否登录
            if (!$this->auth->isLogin())
            {
                //$url = $this->request->url();
                header("Location: " . url('index/login'));exit;
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                $auth = $this->auth->check($path,Cookie::get('admin'));
                if ($auth == false)
                {
                    exit("没有权限");
                }
            }
            $this->adminInfo = Cookie::get('admin');
        }
    }

    /**
     * 加载基本配置信息
     * @param $modulename
     * @param $controllername
     * @param $actionname
     * @param $lang
     */
    protected function getBaseConfigInfo($modulename, $controllername, $actionname, $lang='')
    {
        $site = Config::get("site");

        // 配置信息
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'upload'         => \app\common\model\Config::upload(),
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => 'backend/' . str_replace('.', '/', $controllername),
            'moduleurl'      => rtrim(url("/{$modulename}", '', false), '/'),
            'language'       => $lang,
            'referer'        => Session::get("referer")
        ];
        $ruleList = $this->auth->getRule();
        $this->assign('ruleList', $ruleList);
        $this->loadlang($controllername);
        $this->assign('site', $site);
        $this->assign('config', $config);

        $this->assign('admin', Session::get('admin'));
    }

    /**
     * 请求api接口站点
     * 读取extend扩展里面的 curlapi文件夹下的路由访问内容
     * @param $modulename
     * @param $controllername
     * @param $actionname
     */
    protected function requestCrmApi()
    {
        if((isset($_REQUEST['c']) && !empty($_REQUEST['c'])) 
             && (isset($_REQUEST['a']) && !empty($_REQUEST['a']))) {
            $url = Config::get('crm_api_url');
            $_REQUEST['employee_id'] = $this->adminInfo['id'];
            $_REQUEST['from_station'] = 'crm_admin';
            $_REQUEST['nickname'] = $this->adminInfo['nickname'];
            \app\admin\model\AdminLog::crmApirecord($_REQUEST['c'], $_REQUEST['a']);

            $result = Forward::httpCurlPost($url, $_REQUEST, 1);
            // print_r(json_decode($result,true));die;
           // $result=false;
            if(isset($_REQUEST['csv'])&&!empty($_REQUEST['csv'])){
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=export.csv' );
            }
        
            echo $result;exit;
        }
    }

    /**
     * 手动访问接口站点
     * @param $c
     * @param $a
     * @param $param
     * @return mixed
     */
    protected function visitCrmApi($c,$a, $param = [],$time = 10)
    {

        //手动调用接口
        $url = Config::get('crm_api_url') . "?c={$c}&a={$a}";
        $requestData = [];
        $requestData['employee_id'] = $this->adminInfo['id'];
        $requestData['from_station'] = 'crm_admin';
        $requestData['nickname'] = $this->adminInfo['nickname'];
        if ($param) {
            foreach ($param as $k => $v) {
                $requestData[$k] = $v;
            }
        }
        $requestData['c'] = $c;
        $requestData['a'] = $a;

        \app\admin\model\AdminLog::crmApirecord($c, $a);
        $result = Forward::httpCurlPost($url, $requestData, 1,$time);
        return $result;
    }

    

    /**
     * 析构方法
     *
     */
    public function __destruct()
    {
//        //判断是否设置code值,如果有则变动response对象的正文
//        if (!is_null($this->code))
//        {
//            $result = [
//                'code' => $this->code,
//                'message'  => $this->msg,
//                'time' => $_SERVER['REQUEST_TIME'],
//                'data' => $this->data,
//            ];
//            if (isset($_REQUEST['callback']))
//            {
//                echo $_REQUEST['callback'] . "(" . json_encode($result) . ")";
//            }
//            else
//            {
//                echo json_encode($result);
//            }
//        }
    }

}
