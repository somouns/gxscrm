<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:89:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\index\login.html";i:1509704257;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1509896789;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1509704257;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1509704257;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1509704257;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>
    <meta name="renderer" content="webkit"/>
    <title>证券金融三方智能办公系统</title>
    <link rel="stylesheet" href="__ADMINJS__/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="__ADMINCSS__/common.css"/>
    <script type="text/javascript" src="__ADMINJS__/libs/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="__ADMINJS__/libs/vue/vue.js"></script>
    <script type="text/javascript">
    	window.ADMINJS =  "__ADMINJS__";
    </script>
<link rel="stylesheet" href="__ADMINCSS__/login.css" />
</head>
<body>

<div class="container login-container" id="app">
    <div class="layui-row">
        <h2>欢迎使用证券金融三方智能办公系统</h2>
        <div class="layui-form">
            <form class="layui-form">
            <div class="layui-form-item">
                <input type="text" class="layui-input name" name="loginusername" required lay-verify="required|username" placeholder="用户名/手机号" maxlength="30" autocomplete="off">
            </div>
            <div class="layui-form-item">
                <input type="text" onfocus="this.type='password'" class="layui-input pass" name="loginpassword" required lay-verify="required|pass" placeholder="密码" maxlength="30" autocomplete="off">
            </div>
            <div class="layui-form-item">
                <button class="layui-btn" lay-submit lay-filter="formLogin">登录</button>
            </div>
            <!-- <div class="layui-form-item">
                <a href="javascript:;">忘记密码</a> | <a href="javascript:;">注册一个新账号</a>
            </div> -->
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/login.js"></script>
</body>
</html>