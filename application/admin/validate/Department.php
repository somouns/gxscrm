<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-19
 * Time: 08:01
 */
namespace app\admin\validate;

use think\Validate;

class Department extends Validate
{
    //验证规则
    protected $rule = [
        'department_name'  =>  'require|max:30',
        'linkman'  =>  'require|max:10',
        'mobile' =>  'require|max:11|regex:^1[1-9][0-9]{9}$',

    ];

    //返回错误信息
    protected $message = [
        'department_name.require'  =>  '公司名称不能为空',
        'department_name.max'  =>  '公司名称最多30个字符',
        'linkman.require'  =>  '联系人不能为空',
        'linkman.max'  =>  '联系人最多10个字符',
        'mobile.require'  =>  '联系电话不能为空',
        'mobile.max'  =>  '联系电话最多11位数字',
        'mobile.regex' =>  '手机号码格式错误',
    ];

    //验证场景
    protected $scene = [
        'add'   => ['department_name','linkman','mobile'],
        'edit'   => ['department_name','linkman','mobile'],
        'editdepartment' =>['department_name'],
    ];
}