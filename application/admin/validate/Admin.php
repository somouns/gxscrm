<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-18
 * Time: 18:05
 */
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    //验证规则
    protected $rule = [
    'loginusername'  =>  'require|min:4|max:30|regex:[a-zA-Z0-9_]+|checkEmpty',
    'loginpassword' =>  'require|min:6|max:30',
    'mobile' =>  'require|max:11|regex:^1[1-9][0-9]{9}$',
    'nickname' =>  'require',
    'password' =>  'require|min:6|max:30',
    // 'seat_number' => 'number',

];

    //返回错误信息
    protected $message = [
        'loginusername.require'  =>  '用户名必须',
        'loginusername.min'  =>  '用户名最少4位',
        'loginusername.max'  =>  '用户名最多30个字符',
        'loginusername.regex'  =>  '用户名只能数字字母下划线',
        'loginpassword' =>  '密码必须',
        'loginpassword.min'  =>  '密码最少6个字符',
        'loginpassword.max'  =>  '密码最多30个字符',
        'mobile.require'  =>  '手机号码必须',
        'mobile.max'  =>  '手机号码最多11位',
        'mobile.regex' =>  '手机号码格式错误',
        'nickname.require'  =>  '真实姓名必须',
        'newpassword' =>  '新密码必须',
        //'seat_number' => '座席号必须为数字',
    ];

    //验证场景
    protected $scene = [
        'edit'  =>  ['username', 'mobile', 'nickname','seat_number'],
        'add'   => ['username', 'password', 'mobile', 'nickname','seat_number'],
        'ulogin'   => ['loginusername', 'loginpassword'], //用户名登陆
//        'mlogin'   => ['mobile', 'password'],//手机号码登陆
        'editpassword' => ['newpassword'], //修改密码
        //'import' => ['loginusername', 'password', 'mobile', 'nickname','seat_number'],
        'import' => ['loginusername', 'password', 'mobile', 'nickname'],
    ];

    // 自定义验证规则
    protected function checkEmpty($value,$rule,$data)
    {
        return trim($value) == '' ? '用户名不能为空' : true;
    }
}