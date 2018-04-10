<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-19
 * Time: 08:01
 */
namespace app\admin\validate;

use think\Validate;

class Grade extends Validate
{
    //验证规则
    protected $rule = [
        'grade_name'  =>  'require|max:30',

    ];

    //返回错误信息
    protected $message = [
        'grade_name.require'  =>  '职级名称不能为空',
        'grade_name.max'  =>  '职级最多30个字符',
    ];

    //验证场景
    protected $scene = [
        'edit'  =>  ['grade_name'],
        'add'   => ['grade_name'],
    ];
}