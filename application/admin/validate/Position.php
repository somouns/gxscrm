<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-19
 * Time: 08:01
 */
namespace app\admin\validate;

use think\Validate;

class Position extends Validate
{
    //验证规则
    protected $rule = [
        'position_name'  =>  'require|max:30',

    ];

    //返回错误信息
    protected $message = [
        'position_name.require'  =>  '职位名称不能为空',
        'position_name.max'  =>  '职位最多30个字符',
    ];

    //验证场景
    protected $scene = [
        'edit'  =>  ['position_name'],
        'add'   => ['position_name'],
    ];
}