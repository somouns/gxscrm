<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-11
 * Time: 10:34
 */
use app\admin\library\Auth;
use think\Session;
use think\db;

/*
 * 判断权限点
 */
function checkAuth($url)
{
    $admin = \think\Cookie::get('admin');
    if (!$admin) {
        return false;
    }
    $model = new Auth();
    $ids = $model->getIds($admin['id']);
    $where['name'] = $url;
    $where['status'] = 1;
    $id = Db::name('auth_rule')->field('id')->where($where)->find();
    if($admin['id'] == 1){
        return true;
    }
    if (@in_array($id['id'], $ids)) {
        return true;
    }else{
        return false;
    }
}

/*
 * 树形结构
 */
function makeTree($list, $pk = 'id', $pid = 'pid', $child = 'child', $root = 0)
{
    $tree = array();
    $packData = array();
    foreach ($list as $data) {
        $packData[$data[$pk]] = $data;
    }
    foreach ($packData as $key => $val) {
        if ($val[$pid] == $root) {//代表跟节点
            $tree[] =& $packData[$key];
        } else {
            //找到其父类
            $packData[$val[$pid]][$child][] =& $packData[$key];
        }
    }
    return $tree;
}