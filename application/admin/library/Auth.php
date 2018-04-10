<?php

namespace app\admin\library;

use app\admin\constant\UserConstant;
use app\admin\model\Admin;
use app\admin\model\BusinessLog;
use fast\Random;
use fast\Tree;
use think\Cookie;
use think\Request;
use think\Session;

class Auth extends \fast\Auth
{

    protected $requestUri = '';
    protected $breadcrumb = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function __get($name)
    {
        return Cookie::get('admin')[$name];
    }

    public function login($username, $password, $keeptime = 2592000)
    {

        $cond = [];
        if ($username) {
            $cond['username'] = $username;
        }
        $cond['status'] = 1;
        $admin = Admin::get($cond);
        if (!$admin) {
            return ['code' => -1, 'message' => '账号不存在或被停用'];
        }

        if ($admin->password != md5(md5($password)))
        {
            $admin->loginfailure++;
            $admin->save();
            BusinessLog::addLog(99,[$username . '密码错误'], ['id' => $admin->id, 'nickname' => $admin->nickname]);
            return ['code' => -1, 'message' => '密码错误'];
        }
        $admin->loginfailure = 0;
        $admin->logintime = time();
        $admin->token = Random::uuid();
        $admin->loginnumber++;
        $admin->save();
        $cookieInfo = [];
        $cookieInfo['id'] = $admin->id;
        $cookieInfo['username'] = $admin->username;
        $cookieInfo['nickname'] = $admin->nickname;
        $cookieInfo['avatar'] = \app\admin\model\OssUrl::addOssUrl($admin->avatar);;
        $cookieInfo['mobile'] = $admin->mobile;
        $cookieInfo['token'] = $admin->token;
        Cookie::set("admin", $cookieInfo);
        $this->keeplogin($keeptime);
        return ['code' => 1, 'message' => '登陆成功'];;
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $admin = Admin::get(intval($this->id));
        if (!$admin) {
            Cookie::delete("admin");
            Cookie::delete("keeplogin");
            return true;
        }
        $admin->token = '';
        $admin->save();
        Cookie::delete("admin");
        Cookie::delete("keeplogin");
        return true;
    }

    /**
     * 自动登录
     * @return boolean
     */
    public function autologin()
    {
        $keeplogin = Cookie::get('keeplogin');
        if (!$keeplogin)
        {
            return false;
        }
        list($id, $keeptime, $expiretime, $key) = explode('|', $keeplogin);
        if ($id && $keeptime && $expiretime && $key && $expiretime > time())
        {
            $admin = Admin::get($id);
            if (!$admin || !$admin->token)
            {
                return false;
            }
            //token有变更
            if ($key != self::calcSecretToken($id, $keeptime, $expiretime, $admin->token))
            {
                return false;
            }
            Cookie::set("admin", $admin);
            //刷新自动登录的时效
            $this->keeplogin($keeptime);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 刷新保持登录的Cookie
     * @param int $keeptime
     * @return boolean
     */
    protected function keeplogin($keeptime = 0)
    {
        if ($keeptime)
        {
            $expiretime = time() + $keeptime;
            $key = self::calcSecretToken($this->id, $keeptime, $expiretime, $this->token);
            $data = [$this->id, $keeptime, $expiretime, $key];
            Cookie::set('keeplogin', implode('|', $data));
            return true;
        }
        return false;
    }

    /**
     * 计算cookie加密token信息
     * @param $adminId 管理员ID
     * @param $keepTime cookie保存时间
     * @param $token    秘钥信息
     * @return string
     */
    protected static function calcSecretToken($adminId, $keepTime, $expireTime, $token)
    {
        $key = md5(md5($adminId) . md5($keepTime) . md5($expireTime) . $token);
        return $key;
    }

    public function check($name, $uid = '', $relation = 'or', $mode = 'url')
    {
        return parent::check($name, $this->id, $relation, $mode);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr)
        {
            return FALSE;
        }

        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr))
        {
            return TRUE;
        }

        // 没找到匹配
        return FALSE;
    }

    /**
     * 检测是否登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        $adminInfo = Cookie::get('admin');
        $checkAdmin = Admin::get(intval($adminInfo['id']));
        if (!$checkAdmin || $checkAdmin->status != 1) {
            Cookie::delete('admin');
            Cookie::delete('keeplogin');
            return false;
        }
        return true;
        //return Cookie::get('admin') ? true : false;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    public function getGroups($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getGroups($uid);
    }

    public function getRuleList($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getRuleList($uid);
    }

    public function getUserInfo($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;

        return $uid != $this->id ? Admin::get(intval($uid)) : Session::get('admin');
    }

    public function getRuleIds($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getRuleIds($uid);
    }

    public function isSuperAdmin()
    {
        return in_array('*', $this->getRuleIds()) ? TRUE : FALSE;
    }

    public function getIds($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getRuleIds($uid);
    }
    /**
     * 获得面包屑导航
     * @param string $path
     * @return array
     */
    public function getBreadCrumb($path = '')
    {
        if ($this->breadcrumb || !$path)
            return $this->breadcrumb;
        $path_rule_id = 0;
        foreach ($this->rules as $rule) {
            $path_rule_id = $rule['name'] == $path ? $rule['id'] : $path_rule_id;
        }
        if ($path_rule_id)
        {
            $this->breadcrumb = Tree::instance()->init($this->rules)->getParents($path_rule_id, true);
            foreach ($this->breadcrumb as $k => &$v) {
                $v['url'] = url($v['name']);
            }
        }
        return $this->breadcrumb;
    }

    /**
     * 获取左侧菜单栏
     *
     * @param array $params URL对应的badge数据
     * @return string
     */
    public function getSidebar()
    {
        $result = [];

        // 读取当前登录用户的权限节点
        $userRule = $this->getRuleList();
        if(empty($userRule)){
            return $result;
        }
        //获取相关权限节点的需要字段
        $where['id']=['in', array_keys($userRule)];
        $result = collection(model('AuthRule')->field('title,name as url,icon')->where($where)->order('weigh', 'desc')->select())->toArray();

        if(!empty($result)){
            foreach ($result as $k =>&$v) {
                $v['icon'] = "http://".$_SERVER['HTTP_HOST'] .$v['icon'];
            }
        }else{
            $result = [];
        }

        return $result;
    }
  //构造菜单数据
    //$colorArr = ['red', 'green', 'yellow', 'blue', 'teal', 'orange', 'purple'];
        // Tree::instance()->init($ruleList);
        // $menu = Tree::instance()->getTreeMenu(0, '<li class="@class"><a href="@url" addtabs="@id" url="@url" py="@py" pinyin="@pinyin"><i class="@icon"></i> <span>@title</span> <span class="pull-right-container">@caret @badge</span></a> @childlist</li>', $select_id, '', 'ul', 'class="treeview-menu"');
    public function getRule()
    {
        $ret = [];
        $rule = $this->getRuleList();
        array_walk($rule, function($item) use(&$ret) {
            $p = '/' . $this->request->module() . '/';
            $url = $item != '*' ? explode('/', strtolower(substr($item, strpos($item, $p) + strlen($p)))) : '*';
            $ret[] = rtrim(implode('/', [isset($url[0]) ? trim($url[0]) : '', isset($url[1]) ? trim($url[1]) : '',isset($url[2]) ? trim($url[2]) : '']), '/');
        });
        return $ret;
    }

}
