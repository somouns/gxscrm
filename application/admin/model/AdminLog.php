<?php

namespace app\admin\model;

use think\Config;
use think\Log;
use think\Model;
use think\Cookie;

class AdminLog extends Model
{

    // 开启自动写入时间戳字段
   // protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
   // protected $createTime = 'createtime';
   // protected $updateTime = '';
    //自定义日志标题
    protected static $title = '';
    //自定义日志内容
    protected static $content = '';

    public static function setTitle($title)
    {
        self::$title = $title;
    }

    public static function setContent($content)
    {
        self::$content = $content;
    }

    public static function record($title = '')
    {
        $admin = \think\Cookie::get('admin');
        $admin_id = $admin ? $admin['id'] : 0;
        $username = $admin ? $admin['nickname'] : __('Unknown');
        $content = self::$content;
        if (!$content)
        {
            $content = request()->param();
            foreach ($content as $k => $v)
            {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false)
                {
                    unset($content[$k]);
                }
            }
        }
        $title = self::$title;
        if (!$title)
        {
            $title = [];
            $breadcrumb = \app\admin\library\Auth::instance()->getBreadcrumb();
            foreach ($breadcrumb as $k => $v)
            {
                $title[] = $v['title'];
            }
            $title = implode(' ', $title);
        }
        $table = self::createMonthTable();
        $insertData = [
            'title'     => $title,
            'content'   => !is_scalar($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : $content,
            'url'       => request()->url(),
            'operate_id'  => $admin_id,
            'operate_real_name'  => $username,
            'module'              => request()->module(),
            'controller'          => request()->controller(),
            'action'              => request()->action(),
            'method'              => request()->isPost() ? 1 : 2,
            'useragent' => request()->server('HTTP_USER_AGENT'),
            'ip'        => request()->ip()
        ];
        try{
            self::table("$table")->insert($insertData);
        }catch (\Exception $e) {
            self::createMonthTable(true);
            Log::record(date("Ym") . ' 1号未创建日志表，现在补建日志表');
            self::table("$table")->insert($insertData);
        }

    }

    /**
     * 简单的分表模块代码
     * 系统日志按照每月的方式来分表
     * @return string
     */
    public static function createMonthTable($create = false)
    {
        $config = Config::get('database.prefix');//数据表前缀
        $dateMonth = date('Ym');
        $tableName = $config . 'admin_log'. $dateMonth;
        if (date('Ymd') == date('Ym01') || $create) {
            $createTableSql = <<<EOD
             CREATE TABLE IF NOT EXISTS  `$tableName`(
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
                  `operate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
                  `operate_real_name` varchar(50) DEFAULT NULL,
                  `station_id` tinyint(1) NOT NULL DEFAULT '1' COMMENT '访问站点： 1  tp站点  2  crm.api站点',
                  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名字',
                  `module` varchar(50) NOT NULL COMMENT '模型名称',
                  `controller` varchar(50) NOT NULL COMMENT '控制器',
                  `action` varchar(50) NOT NULL COMMENT '操作',
                  `url` varchar(5000) NOT NULL DEFAULT '' COMMENT '操作页面',
                  `method` varchar(10) NOT NULL COMMENT '请求类型：1 post 2 get  3 request',
                  `title` varchar(1000) NOT NULL DEFAULT '' COMMENT '日志标题',
                  `content` text NOT NULL COMMENT '内容',
                  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
                  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
                  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
                   PRIMARY KEY (`id`),
                   KEY `name` (`username`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='管理员日志表';
EOD;
            self::execute($createTableSql);
            return $tableName;
        } else {
            return $tableName;
        }

    }

    /**
     * 请求crm.api.guxiansheng.cn站点时，记录的系统日志
     * @param $controller
     * @param $action
     */
    public static function crmApirecord($controller, $action)
    {

        $module = request()->module();
        $controllerName = request()->controller();
        $actionName =  request()->action();
        if (strtolower($module) == 'admin' && strtolower($controllerName) == 'sms.sms' && strtolower($actionName) == 'lists') {
            return;
        }

        $admin = \think\Cookie::get('admin');
        $admin_id = $admin ? $admin['id'] : 0;
        $username = $admin ? $admin['nickname'] : __('Unknown');
        $content = self::$content;
        if (!$content)
        {
            $content = request()->param();
            foreach ($content as $k => $v)
            {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false)
                {
                    unset($content[$k]);
                }
            }
        }
        $tableName = self::createMonthTable();
        $insertData = [
            'title'     => '访问crm.api&c=' . $controller . '&a=' . $action,
            'content'   => !is_scalar($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : $content,
            'url'       => Config::get('crm_api_url') . '?c=' . $controller . '&a=' . $action,
            'operate_id'  => $admin_id,
            'operate_real_name'  => $username,
            'module'              => 'crm.api',
            'controller'          => $controller,
            'action'              => $action,
            'method'              => 1,
            'station_id'          => 2,
            'useragent' => request()->server('HTTP_USER_AGENT'),
            'ip'        => request()->ip()
        ];
        try {
            self::table("$tableName")->insert($insertData);
        } catch (\Exception $e) {
            self::createMonthTable(true);
            Log::record(date("Ym") . ' 1号未创建日志表，现在补建日志表');
            self::table("$tableName")->insert($insertData);
        }
    }

    public function admin()
    {
        return $this->belongsTo('Admin', 'admin_id')->setEagerlyType(0);
    }

}
