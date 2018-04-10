<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:99:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customer\customer\add.html";i:1507706882;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1507701392;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1507700164;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\bread_nav.html";i:1507706882;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1507700164;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1507700164;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/>
    <meta name="renderer" content="webkit"/>
    <title>巨景档案管理系统</title>
    <link rel="stylesheet" href="__ADMINJS__/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="__ADMINCSS__/common.css"/>
    <script type="text/javascript" src="__ADMINJS__/libs/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="__ADMINJS__/libs/vue/vue.js"></script>
    <script type="text/javascript">
    	window.ADMINJS =  "__ADMINJS__";
    </script>
<link rel="stylesheet" href="__ADMINCSS__/page/add-client.css"/>
</head>
<body>

<div class="container add-client-container">
    <!--面包屑导航-->
<div class="header" id="header" v-cloak>
    <div class="nav">
        <div class="arrow left-arrow" @click="moveLeft"><i class="iconfont icon-left-arrow"></i></div>
        <ul class="tabs">
            <li><a href="/">首页</a></li>
            <li v-for="(item, index) in tabs"
                :class="['tabs-item', item.active && 'tabs-item-active']"
            >
                <a :href="item.url" @click="switchTabs">{{item.name}}</a>
                <i class="iconfont icon-delete" @click.stop="closeCurTab(index)">&nbsp;</i>
            </li>
        </ul>
        <div class="arrow right-arrow">
            <a href="javascript:;" @click="moveRight"><i class="iconfont icon-right-arrow"></i></a>
            <a href="javascript:;" @click="closeOtherTab">关闭操作<i class="iconfont icon-down-arrow"></i></a>
            <a href="javascript:;"><i class="iconfont icon-back"></i>退出</a>
        </div>
    </div>
</div>
    <div class="main-wrap" id="app" v-cloak>
        <div class="main">
            <div class="layui-row">
                <div class="layui-row-title">客户信息</div>
                <!--客户信息表单-->
                <div class="layui-form">
                    <!--姓名-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">姓名</label>
                        <div class="layui-input-block ">
                            <div class="input-item layui-col-md3">
                                <input type="text" placeholder="请输入姓名" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <!--电话-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">电话</label>
                        <div class="layui-input-block phone-box">
                            <div class="input-item layui-col-md3">
                                <input type="text" placeholder="请输入电话号码" class="layui-input">
                                <span><i class="add-ico"></i></span>
                            </div>
                            <div class="err layui-col-md3">
                                <i class="err-prompt">手机号码已存在,请重新输入</i>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block phone-box">
                            <div class="input-item layui-col-md3">
                                <input type="text" placeholder="请输入电话号码" class="layui-input">
                                <span><i class="del-ico"></i><i class="add-ico"></i></span>
                            </div>
                            <div class="err layui-col-md3">
                                <i class="err-prompt">手机号码已存在,请重新输入</i>
                            </div>
                        </div>
                    </div>
                    <!--// 电话-->
                    <!--QQ-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">QQ</label>
                        <div class="layui-input-block ">
                            <div class="input-item layui-col-md3">
                                <input type="text" placeholder="请输入QQ" class="layui-input">
                            </div>
                            <div class="err layui-col-md3">
                                <i class="err-prompt">QQ号码已存在,请重新输入</i>
                            </div>
                        </div>
                    </div>
                    <!--微信号-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">微信号</label>
                        <div class="layui-input-block ">
                            <div class="input-item layui-col-md3">
                                <input type="text" placeholder="请输入微信号" class="layui-input">
                            </div>
                            <div class="err layui-col-md3">
                                <i class="err-prompt">微信号码已存在,请重新输入</i>
                            </div>
                        </div>
                    </div>
                    <!--来源-->
                    <div class="layui-form-item">
                        <label class="layui-form-label"><span>*&nbsp;</span>来源</label>
                        <div class="layui-input-block">
                            <div class="layui-col-xs3">
                                <input type="radio" name="sex" value="微信推广" title="微信推广" checked>
                                <input type="radio" name="sex" value="股先生" title="股先生">
                                <input type="radio" name="sex" value="其他" title="其他">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block phone-box">
                            <div class="input-item layui-col-md3">
                                <input type="text" placeholder="请输入其他来源" class="layui-input">

                            </div>
                        </div>
                    </div>
                    <!--// 来源-->
                </div>
                <!--// 客户信息表单-->
            </div>

            <div class="layui-row">
                <div class="layui-row-title">客户备注</div>
                <div class="remarks-list">
                    <ul>
                        <li class="list-item">新客户</li>
                        <li class="list-item active">老客户</li>
                        <li class="list-item">土豪客户</li>
                        <li class="list-item">屌丝客户</li>
                        <li class="add-remarks"><i></i>新增备注</li>
                        <div class="add-input">
                            <input maxlength="15" placeholder="请输入15个字符以内">
                            <span class="ok">确定</span>
                            <span>取消</span>
                            <p class="err-tip">备注名称已存在，请重新输入</p>
                        </div>
                    </ul>
                </div>
                <p class="row-cue">*新增备注后需要点击确定后生效</p>
            </div>
            <div class="layui-row">
                <div class="layui-row-title">客户分组</div>
                <div class="remarks-list">
                    <ul>
                        <li class="list-item">新客户</li>
                        <li class="list-item">老客户</li>
                        <li class="list-item">土豪客户</li>
                        <li class="list-item active">屌丝客户</li>
                        <li class="add-remarks disabled-add">
                            <i></i>新增分组
                        </li>
                    </ul>
                </div>
                <p class="row-cue">*新增分组后需要点击确定后生效</p>
            </div>
            <div class="btn-box">
                <button type="reset" class="layui-btn layui-btn-primary">取消</button>
                <button class="layui-btn" @click="addUsrTip">确定</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/addClient.js"></script>
</body>
</html>