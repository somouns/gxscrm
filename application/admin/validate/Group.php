<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-18
 * Time: 18:05
 */
namespace app\admin\validate;

use think\Validate;

class Group extends Validate
{
    //验证规则
    protected $rule = [
        'name'  =>  'require|checkEmpty|max:15',

    ];

    //返回错误信息
    protected $message = [
        'name.require'  =>  '权限组名称不能为空',
        'name.checkEmpty'  =>  '权限组名称不能为空',
        'name.max'  =>  '权限组名称最多15个字符',
    ];

    //验证场景
    protected $scene = [
        'edit'  =>  ['name'],
        'add'   => ['name'],
    ];

    // 自定义验证规则
    protected function checkEmpty($value,$rule,$data)
    {
        return trim($value) == '' ? '权限组名称不能为空' : true;
    }
}