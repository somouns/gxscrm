<?php if (!defined('THINK_PATH')) exit(); /*a:9:{s:102:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customers\customer\index.html";i:1510249089;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1509896789;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\bread_nav.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\side_menu.html";i:1510021729;s:99:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\global_message.html";i:1509704257;s:91:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\player.html";i:1509896789;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1509704257;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1509704257;}*/ ?>
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
<link rel="stylesheet" href="__ADMINCSS__/page/my-client.css"/>
</head>
<body>

<div class="container my-client-container">
    <!--面包屑导航-->
<div class="header" id="header" v-cloak>
    <div class="nav">
        <div class="arrow left-arrow" @click="moveLeft"><i class="iconfont icon-left-arrow"></i></div>
        <ul class="tabs">
            <li><a href="/admin/index/index">首页</a></li>
            <li v-for="(item, index) in tabs"
                :class="['tabs-item', item.active && 'tabs-item-active']"
                v-bind="{'data-index': index}"
            >
                <a :href="item.url" @click="switchTabs">{{item.name}}</a>
                <i class="iconfont icon-delete" @click.stop="closeCurTab(index)">&nbsp;</i>
            </li>
        </ul>
        <div class="arrow right-arrow">
            <a href="javascript:;" @click="moveRight"><i class="iconfont icon-right-arrow"></i></a>
            <div :class="['btn-group', closeBarShow && 'open']" @click="openCloseBar">
                <button>关闭操作<i class="iconfont icon-down-arrow"></i></button>
                <ul>
                    <li><a href="JavaScript:;" @click="delAllTab">关闭全部选项卡</a></li>
                    <li><a href="JavaScript:;" @click="closeOtherTab">关闭其它选项卡</a></li>
                </ul>
            </div>
            <a href="javascript:;" @click="logout"><i class="iconfont icon-back"></i>退出</a>
        </div>
    </div>
    <!--全局侧栏导航-->
<div :class="['side-menu-fixed', sideBarPhoneShow && 'open']">
    <div class="side-nav">
        <ul>
            <li class="nav-letter"><a href="/admin/sms/sms/index" data-title="消息管理" data-type="tab"><span v-if="globalNum !=0">{{globalNum}}</span></a></li>
            <?php if(checkAuth('/admin/call/call/call') == 'true'): ?>
            <li class="nav-tel"><a href="javascript:void(0)" @click="openSideBarPhone" data-title="电话助手"></a></li>
            <?php endif; ?>
            <!--<li class="nav-edit"><a href="/admin/customers/remarks/index" title="备注管理" data-type="tab"></a></li>
            <li class="nav-people"><a href="/admin/customers/customer" title="客户管理" data-type="tab"></a></li>-->
        </ul>
    </div>
    <!-- 电话窗口 -->
    <div class="tel-window">
        <div class="tel-window-nav">
            <p>快速拨号<a href="/admin/call/call/lists" data-type="tab" title="所有记录" class="all-record">所有记录</a></p>
            <a href="javascript:void(0)" class="tel-close" @click="openSideBarPhone"></a>
        </div>
        <div class="layui-tab" lay-filter="tellWindow">
            <ul class="layui-tab-title">
                <li class="layui-this lately-contact" lay-id="1"><i></i><span>最近联系</span></li>
                <li class="call" lay-id="2"><i></i>通话</li>
                <li class="custom-dial" lay-id="3"><i></i>自定义拨号</li>
                <li class="unknown-customer" lay-id="4"><i></i>未知客户</li>
            </ul>
            <div class="layui-tab-content">
                <!-- 最近联系人 -->
                <div class="layui-tab-item layui-show tab-item0">
                    <div class="item-lists" v-if="callDatahas.length != 0">
                        <ul>
                            <li v-for="(item,index) in callDatahas">
                                <div class="contact-head">
                                    <img :src="item.image" width="34" height="34">
                                    <h2>{{item.toPhoneName}}</h2>
                                    <p>{{item.starttime}}</p>
                                </div>
                                <div class="contact-icon">
                                    <a href="javascript:;" @click="listCallTell(item.customer_contact_id,index)" title="拨号" class="icon-call"></a>
                                    <a href="javascript:;" @click="player(item.recfile, item.toPhoneName, item.starttime)" title="查看录音" class="icon-record"></a>
                                    <a :href='"/admin/customers/customer/update?type=2&customer_id="+item.customer_id' v-if="item.is_cebind == 1" data-type="tab" title="添加记录" class="icon-addrecord"></a>
                                    <a href="javascript:;" v-else title="添加记录" class="icon-addrecord"></a>
                                    <a href="javascript:;" @click="removeCallRecord('has',index, item.tel_id)" title="移除" class="icon-remove"></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="no-data" v-if="callDatahas.length == 0"></div>
                    <!-- 包含多个电话 start -->
                    <div class="more-tells-boxs" :class="{'layui-show':moreTellsShow}">
                        <header>
                            <span>请选择联系电话</span>
                            <a href="javascript:;" title="关闭" @click="moreTellsShow = !moreTellsShow" class="layui-icon">&#x1006;</a>
                        </header>
                        <section>
                            <ul>
                                <li v-for="(list,i) in moreTells" :key="i">
                                    <span>{{list.contact_way}}</span>
                                    <a href="javascript:;" @click="listCallTell(list.contact_id, i, true)"></a>
                                </li>
                            </ul>
                        </section>
                    </div>
                    <!-- // 包含多个电话 end -->
                </div>
                <!-- 通话 -->
                <div class="layui-tab-item tab-item1">
                    <div class="dial-connter" v-if="connect">
                        <div class="contact-nav clearfix">
                            <img :src="callInfo.image" width="81" height="81">
                            <div class="breath-line active">
                                <p class="line1item"></p>
                                <p class="line2item"></p>
                                <p class="line3item"></p>
                                <p class="line4item"></p>
                            </div>
                        </div>
                        <div class="contact-text">
                            <p class="contact-name">{{callInfo.real_name}}</p>
                            <p class="contact-tel" v-html="callInfo.phone_num"></p>
                            <p class="contact-warn">设备正常</p>
                        </div>
                        <!-- 挂断状态 active -->
                        <!-- <div class="contact-way">
                            <p>正在呼叫...</p>
                            <button class="contact-bth active"></button>
                        </div> -->
                        <div class="wave-box"></div>
                    </div>
                    <div class="no-tell" v-if="!connect"></div>
                </div>
                <!-- 自定义拨号 -->
                <div class="layui-tab-item tab-item2">
                    <div class="input-box"><input type="text" name="tell" v-model="tellVal" maxlength="11"><span v-show="clearTell" @click="tellVal = ''"></span></div>
                    <dl>
                        <dd v-for="(item,index) in tellkey" @click="dialfn(item)" :key="index">{{item}}</dd>
                    </dl>
                    <button @click="callTell" title="拨打电话"></button>
                </div>
                <!-- 未知用户 -->
                <div class="layui-tab-item tab-item0">
                    <div class="item-lists" v-if="callDatanot.length != 0">
                        <ul>
                            <li v-for="(item,index) in callDatanot">
                                <div class="contact-head">
                                    <img :src="item.image" width="34" height="34">
                                    <h2>{{item.toPhoneName ? item.toPhoneName : '未知用户'}}</h2>
                                    <p>{{item.starttime}}</p>
                                </div>
                                <div class="contact-icon">
                                    <a href="javascript:;" @click="listCallTell(item.toPhone)" title="拨号" class="icon-call"></a>
                                    <a href="javascript:;" @click="player(item.recfile, '未知用户', item.starttime, true, item.toPhone)" title="查看录音" class="icon-record"></a>
                                    <a :href='"/admin/customers/customer/add?usertell="+item.toPhone' data-type="tab" title="新建客户" class="icon-addrecord"></a>
                                    <a href="javascript:;" @click="removeCallRecord('not',index, item.tel_id)" title="移除" class="icon-remove"></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="no-customer" v-if="callDatanot.length == 0"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- 客户备注窗口 -->
    <div class="remarks-window remarks-group" style="display:none">
        <div class="window-nav">
            <p>客户备注</p>
            <a href="javascript:void(0)" class="window-close"></a>
        </div>
        <div class="action-box">
            <ul class="choose-finish clearfix">
                <li>老客户<a href=""></a></li>
                <li>有意向<a href=""></a></li>
                <li>可以关单<a href=""></a></li>
                <li>老客户<a href=""></a></li>
                <li>有意向<a href=""></a></li>
                <li>可以关单<a href=""></a></li>
            </ul>
            <ul class="choose-wait clearfix">
                <li>新客户</li>
                <li>重点关注</li>
                <li>慎重开发客户</li>
                <li>新客户</li>
                <li>新客户</li>
            </ul>
        </div>
    </div>
    <!-- 客户分组窗口 -->
    <div class="group-window remarks-group" style="display:none">
        <div class="window-nav">
            <p>客户分组</p>
            <a href="javascript:void(0)" class="window-close"></a>
        </div>
        <div class="action-box">
            <ul class="choose-finish clearfix">
                <li>老客户<a href=""></a></li>
                <li>有意向<a href=""></a></li>
                <li>可以关单<a href=""></a></li>
                <li>老客户<a href=""></a></li>
            </ul>
            <ul class="choose-wait clearfix">
                <li>新客户</li>
                <li>重点关注</li>
                <li>慎重开发客户</li>
                <li>新客户</li>
                <li>新客户</li>
            </ul>
            <p class="group-warning"><i>*</i>&nbsp; 选择分组后，客户将从原有自 定义分组一处</p>
        </div>
    </div>
</div>
    <!--全局消息框-->
<div :class="['fixed-wrap', closeGlobalMsgShow && 'open']">
    <h2 class="fix-tit" >重要提醒<span class="fix-num">{{globalNewsCount}}</span><a class="iconfont icon-cha" @click="closeGlobalMsgShow = false"></a></h2>
    <div class="fix-con" v-for = "item in globalNews">
        <div class="fix-time">{{item.createtime}}</div>
        <p class="con-text">{{item.content}}</p>
    </div>
    <div class="fix-bot example-handle">
        <div id="GlobalMsgPage"></div>
        <span v-for = "item in globalNews">
            <a class="examine-operate" data-type="tab" title="消息管理" id="global" :href="'/admin/sms/sms?type='+item.type+'&sontype='+item.sontype">立即查看</a>
        </span>
    </div>
</div>
    <!-- 音频播放组件 start -->
<div class="jplayer-container-box" :class="{'layui-hide': !jplayerShow}" id="jquery_jplayer_control">
    <div class="jplayer-container-wapper">
        <!-- player ui start -->
        <div class="jplayer-container-audio">
            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
            <div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
                <div class="jp-type-single">
                    <div class="jp-gui jp-interface">
                        <div class="jp-play-head">
                            <!-- 标题 -->
                            <div class="jp-title-holder">{{jplayer.title}}&nbsp;&nbsp;{{jplayer.time}}</div>
                            <!-- time start -->
                            <div class="jp-time-holder">
                                <div class="jp-current-time" role="timer" aria-label="time">00:00</div>
                                <div class="jp-cut-off">/</div>
                                <div class="jp-duration" role="timer" aria-label="duration">00:00</div>
                            </div>
                            <!-- // time end -->
                        </div>
                        <!-- 播放 暂停按钮 start -->
                        <div class="jp-controls">
                            <button class="jp-play" role="button" tabindex="0">play</button>
                        </div>
                        <!-- // 播放 暂停按钮 end -->
                        <!-- 进度条 start -->
                        <div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>
                        <!-- // 进度条 end -->
                    </div>
                </div>
            </div>
        </div>
        <!-- // player ui end -->
        <div class="jplayer-container-down">
            <a :href="jplayer.url" title="下载录音" download="xxx">下载录音</a>
            <a :href='"/admin/customers/customer/add?usertell=" + jplayer.phone' v-show="jplayer.unknown" data-type="tab" title="创建客户">创建客户</a>
        </div>
    </div>
    <a href="javascript:;" class="closeJplay" @click="jplayerShow = !jplayerShow" title="关闭"><i class="layui-icon">&#x1006;</i></a>
</div>
<!-- // 音频播放组件 end -->
</div>
    <div id="app" v-cloak>
        <!--侧栏-->
        <div class="sidebar-collapse">
            <h3 :class="addGroupShow && 'show-add'">我的客户
                <?php if(checkAuth('/admin/group/employeecustomgroup/add') == 'true'): ?>
                <i :class="['iconfont','icon-add', addGroupShow && 'disable']" @click="showAddGroup" title="新增自定义分组"></i>
                <?php endif; ?>
            </h3>
            <div class="add-group-wrap">
                <input v-model="addedGroupName" class="pull-left" placeholder="请输入15个字符以内" maxlength="15" id="addGroupName">
                <div class="btn-wrap pull-right">
                    <span class="ok" @click="addGroup">确定</span>
                    <span class="cancel" @click="showAddGroup">取消</span>
                </div>
                <p class="err clearfix" v-if="addedGroupNameExist">名称已存在，请重新输入</p>
                <p class="err clearfix" v-if="addedGroupNameEmpty">请输入群组名称</p>
            </div>
            <div class="line"></div>

            <?php if(checkAuth('/admin/group/employeecustomgroup/index') == 'true'): ?>
            <!--自定义群组 groupType:0-->
            <ul class="nav side-menu">
                <li class="choice">
                    <a href="javascript:;" @click="getSideMenu($event, all_num.filename)">
                         <span class="nav-label">全部{{all_num.total_num >= 0 ? '(':''}}{{all_num.total_num}}{{all_num.total_num >= 0 ? ')':''}}</span>
                    </a>
                </li>
                <li v-for="(i,index) in generalList">
                    <a href="javascript:;" @click="getSideMenu($event, i.filename, 0)">
                        <span class="nav-label">{{i.name}}({{i.total_num}})</span>
                        <span class="iconfont icon-right" v-if="i.child"></span>
                    </a>
                    <div class="edit-del-wrap">
                        <?php if(checkAuth('/admin/group/employeecustomgroup/update') == 'true'): ?>
                        <i class="edit" @click="editGroup($event, i.type, i.name)" title="重命名分组"></i>
                        <?php endif; if(checkAuth('/admin/group/employeecustomgroup/delete') == 'true'): ?>
                        <i class="del" @click="delGroupTip(i.type, i.total_num, i.name)" title="删除分组"></i>
                        <?php endif; ?>
                    </div>
                    <div class="add-group-wrap clearfix">
                        <input v-model.trim="editingGroupName" class="pull-left" maxlength="15">
                        <div class="btn-wrap pull-right">
                            <span class="ok" @click="renameGroupName(i.type)">确定</span>
                            <span class="cancel" @click="closeEditGroup">取消</span>
                        </div>
                        <p class="err clearfix" v-if="addedGroupNameExist">名称已存在，请重新输入</p>
                        <p class="err clearfix" v-if="addedGroupNameEmpty">请输入群组名称</p>
                    </div>
                    <ul class="nav nav-second-level" v-if="i.child">
                        <li v-for="item in i.child">
                            <a href="javascript:;" @click="getSideMenu($event, item.filename, 0)">
                                <span class="nav-label last-nav-label" v-cloak>{{item.name}}({{item.total_num}})</span>
                                <span class="iconfont icon-right" v-if="item.child"></span>
                            </a>
                            <div class="edit-del-wrap">
                                <i class="edit" @click="editGroup"></i>
                                <i class="del" @click="delGroupTip"></i>
                            </div>
                            <div class="add-group-wrap clearfix">
                                <input type="text" class="pull-left" maxlength="15">
                                <div class="btn-wrap pull-right">
                                    <span class="ok">确定</span>
                                    <span class="cancel" @click="editGroup">取消</span>
                                </div>
                                <p class="err clearfix">名称已存在，请重新输入</p>
                            </div>
                            <ul class="nav nav-third-level" v-if="item.child">
                                <li v-for="j in item.child">
                                    <a href="javascript:;">
                                        <span class="nav-label" v-cloak>{{j.name}}({{j.total_num}})</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="line"></div>
            <?php endif; ?>
            <!--固定群组 groupType:1-->
            <ul class="nav side-menu">
                <li v-for="(i,index) in operaList">
                    <a href="javascript:;" @click="getSideMenu($event, i.filename, 1)">
                        <span class="nav-label">{{i.name}}({{i.total_num}})</span>
                        <span class="iconfont icon-right" v-if="i.child"></span>
                    </a>
                    <ul class="nav nav-second-level" v-if="i.child">
                        <li v-for="item in i.child">
                            <a href="javascript:;" @click="getSideMenu($event, item.filename, 1)">
                                <span class="nav-label last-nav-label" v-cloak>{{item.name}}({{item.total_num}})</span>
                                <span class="iconfont icon-right" v-if="item.child"></span>
                            </a>
                            <ul class="nav nav-third-level" v-if="item.child">
                                <li v-for="j in item.child">
                                    <a href="javascript:;">
                                        <span class="nav-label" v-cloak>{{j.name}}({{j.total_num}})</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="line"></div>

            <!--特殊群组 groupType:2-->
            <ul class="nav side-menu">
                <li v-for="(i,index) in productList">
                    <a href="javascript:;" @click="getSideMenu($event, i.filename, 2)">
                        <span class="nav-label">{{i.name}}({{i.total_num}})</span>
                        <span class="iconfont icon-right" v-if="i.child"></span>
                    </a>
                    <ul class="nav nav-second-level" v-if="i.child">
                        <li v-for="item in i.child">
                            <a href="javascript:;" @click="getSideMenu($event, item.filename, 2)">
                                <span class="nav-label last-nav-label" v-cloak>{{item.name}}({{item.total_num}})</span>
                                <span class="iconfont icon-right" v-if="item.child"></span>
                            </a>
                            <ul class="nav nav-third-level" v-if="item.child">
                                <li v-for="j in item.child">
                                    <a href="javascript:;">
                                        <span class="nav-label" v-cloak>{{j.name}}({{j.total_num}})</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!--// 侧栏-->

        <!--正文-->
        <div class="main-wrap">
            <!--搜索/按钮组-->
            <div class="search-btn-container">
                <div class="main clearfix">
                    <div class="examine-search pull-left">
                        <form class="layui-form" action="">
                            <div class="layui-input-inline search-parent">
                                <input class="layui-input search-inp"
                                       name="username"
                                       autocomplete="off"
                                       maxlength="15"
                                       placeholder="请输入姓名"
                                       v-model="username"
                                >
                                <input class="layui-input search-inp"
                                       name="phone"
                                       autocomplete="off"
                                       maxlength="15"
                                       placeholder="请输入手机号"
                                       v-model="phone"
                                >
                            </div>
                            <button :class="['iconfont icon-search search-icon', (username || phone) && 'available']"
                                    lay-submit
                                    lay-filter="formSearch"
                                    @click="searchName"
                            >
                            </button>
                        </form>
                    </div>
                    <div class="example-handle pull-right">
                        <?php if(checkAuth('/admin/customers/customer/add') == 'true'): ?>
                        <a class="examine-operate" href='/admin/customers/customer/add' data-type="tab" title="新增客户">新增客户</a>
                        <?php endif; if(checkAuth('/admin/customers/customer/import') == 'true'): ?>
                        <a class="examine-operate" @click="importUsr">导入客户</a>
                        <?php endif; if(checkAuth('/admin/customers/customer/export') == 'true'): ?>
                        <a href="javascript:;" @click="exportUsr" class="examine-operate">导出客户</a>
                        <?php endif; if(checkAuth('/admin/customers/remarks/index') == 'true'): ?>
                        <a class="examine-operate" href="/admin/customers/remarks/index" data-type="tab" title="备注管理">备注管理</a>
                        <?php endif; if(checkAuth('/admin/customers/tag/index') == 'true'): ?>
                        <a class="examine-operate" href="/admin/customers/tag/index" data-type="tab"
                           title="标签管理">标签管理</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!--// 搜索/按钮组-->

            <div class="main">
                <div class="layui-row condition">
                    <!--筛选条件-->
                    <div class="examine-tag" v-show="!showPool">
                        <span class="tag-title">备注：</span>
                        <ul>
                            <li><a href="javascript:;"
                                   :class="['tag-a', ajaxCondition.remarkId.length === 0 && 'tag-active']"
                                   @click="cancelAllRemarks($event)"
                            >不限</a></li>
                            <li v-for="(item, index) in remarkList">
                                <a href="javascript:;"
                                   class="tag-a"
                                   :data-id="item.id"
                                   @click="addRemarks($event,index,item.id)"
                                >
                                    {{item.mark_name}}
                                </a>
                            </li>
                            <li><a href="javascript:;"
                                   :class="['tag-a', 'no-tag', !ajaxCondition.remarkId.length === 0 && 'tag-active']"
                                   @click="addRemarks($event,'',-1)"
                            >无备注</a></li>
                        </ul>
                    </div>
                    <div class="examine-tag">
                        <span class="tag-title">标签：</span>
                        <ul>
                            <li><a href="javascript:;"
                                   :class="['tag-a', ajaxCondition.tagId.length === 0 && 'tag-active']"
                                   @click="cancelAllTag($event)">不限</a></li>
                            <li v-for="(item, index) in tagList">
                                <a href="javascript:;"
                                   class="tag-a"
                                   :data-id="item.id"
                                   @click="addTags($event,index,item.tag_code)"
                                >{{item.tag_name}}</a>
                            </li>
                            <li><a href="javascript:;"
                                   :class="['tag-a','no-tag', !ajaxCondition.tagId.length === 0 && 'tag-active']"
                                   @click="addTags($event,'',-1)">无标签</a></li>
                        </ul>
                    </div>
                    <div class="examine-select clearfix">
                        <span class="tag-title">筛选：</span>
                        <div class="examine-filter" v-for="(item, index) in condition">
                            <a :class="['filter-a', condition[index].show && 'open', condition[index].active && 'active']"
                               href="javascript:;"
                               @click="setCondition(index)"
                            ><span>{{item.name }}</span><i class="iconfont icon-down-arrow"></i></a>
                            <!--时间-->
                            <div class="filter-time" v-if="index < (condition.length -1)">
                                <div class="choose-icon">
                                    <a href="javascript:;" @click="cancelCondition($event,index)">不限</a>
                                    <a href="javascript:;" @click="choiceTime($event,index)">今天</a>
                                    <a href="javascript:;" @click="choiceTime($event,index)">昨天</a>
                                    <a href="javascript:;" @click="choiceTime($event,index)">最近7天</a>
                                    <a href="javascript:;" @click="choiceTime($event,index)">最近30天</a>
                                    <a href="javascript:;" @click="choiceTime($event,index,1)">自定义</a>
                                    <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                        <input :class="['layui-input', 'lay-date-a-' + index]" readonly>
                                        -
                                        <input :class="['layui-input', 'lay-date-b-' + index]" readonly>
                                        <button @click="addConditons($event, index, item.type)">确定</button>
                                    </div>
                                </div>
                            </div>
                            <!--组织架构-->
                            <div class="organize-framework" v-if="index == (condition.length -1)">
                                <h2 class="organize-nav">组织架构<a href="JavaScript:;" @click="closeOrg(index,1)"></a></h2>
                                <div class="organize-content">
                                    <div class="customer-group">
                                        <form class="layui-form" action="">
                                            <div class="layui-inline">
                                                <div class="layui-input-inline">
                                                    <select name="modules" lay-filter="search-org" lay-search="">
                                                        <option value="">请输入用户姓名、账号</option>
                                                        <option :value="item.id + ','+item.name" v-for="item in OrgSearchArr">{{item.name}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                        <nav class="sidebar-nav">
                                            <ul class="metismenu" id="org-framework">
                                                <li v-for="(item, index) in usrOrgList">
                                                    <a :class="!item.type && 'has-arrow'" href="javascript:;"
                                                       aria-expanded="false" :data-id="item.id" :data-text="item.name"
                                                       @click="orgSelectItem">
                                                        {{ item.name }}
                                                        <span @click.stop="orgSelectAll($event,item.id)"
                                                              v-if="!item.type">全选</span>
                                                    </a>
                                                    <ul aria-expanded="false" v-if="item.child">
                                                        <li v-for="(item2, index) in item.child">
                                                            <a :class="!item2.type && 'has-arrow'" href="javascript:;"
                                                               aria-expanded="false" :data-id="item2.id"
                                                               :data-text="item2.name" @click="orgSelectItem">
                                                                {{ item2.name }}
                                                                <span @click.stop="orgSelectAll"
                                                                      v-if="!item2.type">全选</span>
                                                            </a>
                                                            <ul aria-expanded="false" v-if="item2.child">
                                                                <li v-for="(item3, index) in item2.child">
                                                                    <a :class="!item3.type && 'has-arrow'"
                                                                       href="javascript:;" aria-expanded="false"
                                                                       :data-id="item3.id" :data-text="item3.name"
                                                                       @click="orgSelectItem">
                                                                        {{ item3.name }}
                                                                        <span @click.stop="orgSelectAll"
                                                                              v-if="!item3.type">全选</span>
                                                                    </a>
                                                                    <ul aria-expanded="false" v-if="item3.child">
                                                                        <li v-for="(item4, index) in item3.child">
                                                                            <a :class="!item4.type && 'has-arrow'"
                                                                               href="javascript:;" aria-expanded="false"
                                                                               :data-id="item4.id"
                                                                               :data-text="item4.name"
                                                                               @click="orgSelectItem">
                                                                                {{ item4.name }}
                                                                                <span @click.stop="orgSelectAll"
                                                                                      v-if="!item4.type">全选</span>
                                                                            </a>
                                                                            <ul aria-expanded="false"
                                                                                v-if="item4.child">
                                                                                <li v-for="(item5, index) in item4.child">
                                                                                    <a :class="!item5.type && 'has-arrow'"
                                                                                       href="javascript:;"
                                                                                       aria-expanded="false"
                                                                                       :data-id="item5.id"
                                                                                       :data-text="item5.name"
                                                                                       @click="orgSelectItem">
                                                                                        {{ item5.name }}
                                                                                        <span @click.stop="orgSelectAll"
                                                                                              v-if="!item5.type">全选</span>
                                                                                    </a>
                                                                                    <ul aria-expanded="false"
                                                                                        v-if="item5.child">
                                                                                        <li v-for="(item6, index) in item5.child">
                                                                                            <a :class="!item6.type && 'has-arrow'"
                                                                                               href="javascript:;"
                                                                                               aria-expanded="false"
                                                                                               :data-id="item6.id"
                                                                                               :data-text="item6.name"
                                                                                               @click="orgSelectItem">
                                                                                                {{ item6.name }}
                                                                                                <span @click.stop="orgSelectAll"
                                                                                                      v-if="!item6.type">全选</span>
                                                                                            </a>
                                                                                            <ul aria-expanded="false"
                                                                                                v-if="item6.child">
                                                                                                <!--暂时支持7层-->
                                                                                            </ul>
                                                                                        </li>
                                                                                    </ul>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                    <div class="choose-people">
                                        <h2>已选客户：{{ selectedOrgUsr.length }}</h2>
                                        <ul>
                                            <li v-for="(item, index) in selectedOrgUsr">{{ item.name }} <i
                                                  @click="delChoose(index)"></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="organize-bth">
                                    <button class="cancel" @click="closeOrg(index)">取消</button>
                                    <button class="ensure" @click="addConditonsOrg($event, index, item.type)">确定
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--// 筛选条件-->

                    <!--表格-->
                    <div class="table-box">
                        <div class="t-header">
                            <ul class="clearfix">
                                <li class="check-all">
                                    <div class="squaredFour">
                                        <input type="checkbox" id="_check_all" v-model="allChecked"
                                               @click="checkAllUsr"/>
                                        <label for="_check_all"></label>
                                    </div>
                                    <span>全选</span>
                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customers/remarks/add') == 'true'): ?>
                                    <a href="javascript:;" @click="addRemark"
                                       :class="!checkedIdArr.length && 'disable'">添加备注</a>
                                    <?php endif; ?>
                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customers/remarks/del') == 'true'): ?>
                                    <a href="javascript:;" @click="delRemark"
                                       :class="!checkedIdArr.length && 'disable'">删除备注</a>
                                    <?php endif; ?>
                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customers/customer/movecustomergroup') == 'true'): ?>
                                    <a href="javascript:;" @click="moveGroup"
                                       :class="!checkedIdArr.length && 'disable'">移动分组</a>
                                    <?php endif; ?>
                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customer/customer/sharecustomer') == 'true'): ?>
                                    <a href="javascript:;" @click="shareUsr" :class="!checkedIdArr.length && 'disable'">共享客户</a>
                                    <?php endif; ?>

                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customer/customer/movecustomertopool') == 'true'): ?>
                                    <a href="javascript:;" @click="removePublic"
                                       :class="!checkedIdArr.length && 'disable'">移入公海</a>
                                    <?php endif; ?>
                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customer/customer/transfercustomer') == 'true'): ?>
                                    <a href="javascript:;" @click="moveUsr" :class="!checkedIdArr.length && 'disable'" >转移客户</a>
                                    <?php endif; ?>
                                </li>
                                <li v-if="!showPool">
                                    <?php if(checkAuth('/admin/customer/customer/deletecustomer') == 'true'): ?>
                                    <a href="javascript:;" @click="delUser" :class="!checkedIdArr.length && 'disable'" v-show="!showPool">删除客户</a>
                                    <?php endif; ?>
                                </li>
                                <li v-if="showPool">
                                    <?php if(checkAuth('/admin/customers/customer/movecustomertopool') == 'true'): ?>
                                    <a href="javascript:;" @click="allotUsr" :class="!checkedIdArr.length && 'disable'">客户分配</a>
                                    <?php endif; ?>
                                </li>
                                <li class="phone-helper" style="display: none">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" id="_phone_helper" name="check" checked/>
                                        <label for="_phone_helper"></label>
                                    </div>
                                    <span>电话助手</span>
                                </li>
                                <li class="clear-custom" v-show="!showPool">
                                    <?php if(checkAuth('/admin/customer/customer/batchdeletecustomerpool') == 'true'): ?>
                                    <a href="javascript:;" @click="clearUsr">客户清理</a>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                        <div class="table-container">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="m-table">
                                <thead>
                                <tr>
                                    <th class="usr-name"><a href="javascript:;">客户({{usrDataTotalNum}})</a></th>
                                    <th><a href="javascript:;">来源</a></th>
                                    <th class="recent-contact"><a href="javascript:;" data-type="0" @click="recentContactFilter">最近联系<i></i></a></th>
                                    <th class="recent-news"><a href="javascript:;" data-type="0" @click="recentNewsFilter">最近动态<i></i></a></th>
                                    <th><a href="javascript:;">最近成交</a></th>
                                    <th><a href="javascript:;">所买商品</a></th>
                                    <th><a href="javascript:;">拨号</a></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in usrDataList">
                                    <td class="usr-name">
                                        <!--客户姓名-->
                                        <div class="squaredFour">
                                            <input type="checkbox"
                                                   :id="'_check_item_' + index"
                                                   name="check"
                                                   @click="checkUsr(item.id)"
                                                   v-model="item.checked"
                                            >
                                            <label :for="'_check_item_' + index"></label>
                                        </div>
                                        <a :href="'/admin/customers/customer/update?type=1&customer_id=' + item.id"
                                           data-type="tab"
                                           data-title="基本信息"
                                        >
                                            <dl class="clearfix">
                                                <dd><img
                                                      src="/assets/images/calluser2.png"
                                                      :src="'/assets/images/calluser' + item.head_type + '.png'"
                                                ></dd>
                                                <dd class="name">{{ item.real_name ? item.real_name : '未命名' }}({{ item.sex === '**' ? item.sex : (item.sex === '0' ? '--' : (item.sex === '1' ? '男':'女')) }})
                                                </dd>
                                            </dl>
                                        </a>
                                    </td>
                                    <!--来源渠道-->
                                    <td class="from-channel">
                                        <a :href="'/admin/customers/customer/update?type=1&customer_id=' + item.id"
                                           data-type="tab"
                                           data-title="基本信息"
                                        >
                                            {{ item.from_channel ? item.from_channel : '无' }}
                                        </a>
                                    </td>
                                    <!--最近联系-->
                                    <td class="contact">
                                        <a :href="'/admin/customers/customer/update?type=1&customer_id=' + item.id"
                                           data-type="tab"
                                           data-title="基本信息"
                                           :class="!item.lately_contact_employee_name && 'empty'"
                                        >
                                            <dl class="clearfix" v-show="item.lately_contact_employee_name">
                                                <dd><span>{{ item.lately_contact_time }}</span></dd>
                                                <dd><span>{{ item.lately_contact_employee_name }}{{ item.department_name ? '(' : '' }}{{item.department_name}}{{ item.department_name ? ')' : '' }}</span></dd>
                                            </dl>
                                            <span v-show="!item.lately_contact_employee_name">无</span>
                                        </a>
                                    </td>
                                    <!--最近动态-->
                                    <td>
                                        <a :href="'/admin/customers/customer/update?type=1&customer_id=' + item.id"
                                           data-type="tab"
                                           data-title="基本信息"
                                           :class="!item.lately_dynamic && 'empty'"
                                        >
                                            <dl class="clearfix" v-show="item.lately_dynamic">
                                                <dd><span>{{ item.lately_dynamic_time }}</span></dd>
                                                <dd><span>{{ item.lately_dynamic }}</span></dd>
                                            </dl>
                                            <span v-show="!item.lately_dynamic">无</span>
                                        </a>
                                    </td>
                                    <!--最近成交-->
                                    <td class="recent-buy">
                                        <a :href="'/admin/customers/customer/update?type=1&customer_id=' + item.id"
                                           data-type="tab"
                                           data-title="基本信息"
                                           :class="!item.cooper_situation.length && 'empty'"
                                        >
                                            <dl class="clearfix" v-show="item.cooper_situation.length">
                                                <dd><span>{{ item.cooper_situation[0] }}</span></dd>
                                                <dd>
                                                    <span>
                                                        {{ item.cooper_situation[2] === '--' ? '' : item.cooper_situation[2] }}
                                                        {{ item.cooper_situation[1] === '--' ? '' : '(' }}{{ item.cooper_situation[1] === '--' ? '(--)' : item.cooper_situation[1] }}{{ item.cooper_situation[1] === '--' ? '' : ')' }}
                                                    </span>
                                                </dd>
                                            </dl>
                                            <span v-show="!item.cooper_situation.length">无</span>
                                        </a>
                                    </td>
                                    <!--所买商品-->
                                    <td class="product">
                                        <a :href="'/admin/customers/customer/update?type=1&customer_id=' + item.id"
                                           data-type="tab"
                                           data-title="基本信息"
                                           :class="!item.product_name.length && 'empty'"
                                        >
                                            <dl class="clearfix" v-show="item.product_name.length">
                                                <dd><span>{{ item.product_name[0] }}</span></dd>
                                                <dd><span>{{ item.product_name[1] }}</span></dd>
                                            </dl>
                                            <span v-show="!item.product_name.length">无</span>
                                        </a>
                                    </td>
                                    <td class="call-it">
                                        <?php if(checkAuth('/admin/call/call/call') == 'true'): ?>
                                        <a href="javascript:;"
                                           :class="item.mobile ? (item.mobile.length && 'has-phone' ) : ''"
                                           @click="getCallIt($event, item.mobile)"></a>
                                        <?php endif; ?>
                                        <!-- 包含多个电话 start -->
                                        <div class="more-tells-boxs" v-if=" item.mobile && item.mobile.length > 1">
                                            <header>
                                                <span>请选择联系电话</span>
                                                <a href="javascript:;" title="关闭" @click="getCallIt($event)" class="layui-icon">&#x1006;</a>
                                            </header>
                                            <section>
                                                <ul>
                                                    <li v-for="phoneItem in item.mobile">
                                                        <span>{{ phoneItem.contact_way }}</span>
                                                        <a href="javascript:;" @click="moreTelCallIt(phoneItem.contact_id)"></a>
                                                    </li>
                                                </ul>
                                            </section>
                                        </div>
                                        <!-- // 包含多个电话 end -->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--暂无数据-->
                        <div class="no-result" v-show="usrDataList.length === 0">
                            <p>暂无数据</p>
                        </div>
                        <!--// 暂无数据-->

                        <!--分页-->
                        <div class="t-footer clearfix">
                            <ul class="show-num pull-left clearfix">
                                <li>每页展示用户</li>
                                <li><a class="cur" @click="setShowNum">1000</a></li>
                                <li><a @click="setShowNum">500</a></li>
                                <li><a @click="setShowNum">300</a></li>
                            </ul>
                            <!--分页-->
                            <div class="page-container pull-right ">
                                <div class="pull-left page-total">共有 {{usrDataTotalNum}} 条记录</div>
                                <div id="page" class="pull-right"></div>
                            </div>
                        </div>
                        <!--// 分页-->

                    </div>
                    <!--// 表格-->
                </div>
            </div>
            <!--// 正文-->
        </div>
    </div>
</div>

<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/myClient.js"></script>
</body>
</html>