<?php if (!defined('THINK_PATH')) exit(); /*a:9:{s:99:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\group\file_group\edit.html";i:1510249598;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1509896789;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\bread_nav.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\side_menu.html";i:1510021729;s:99:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\global_message.html";i:1509704257;s:91:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\player.html";i:1509896789;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1509704257;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1509704257;}*/ ?>
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
<link rel="stylesheet" href="__ADMINCSS__/page/jurisdication.css" />
<link rel="stylesheet" href="__ADMINCSS__/page/add-group.css" />
</head>
<body>

<!-- 编辑群组 -->
<div class="container add-group-container">
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
    <div id="app" v-cloak class="main-wrap">
        <div class="layui-container">
            <!-- 权限组列表主内容区域 -->
            <div class="main-group group-title">
                <h2>编辑群组</h2>
                <div class="layui-form layui-col-md12 group-list">
                    <!-- 群组名称 -->
                    <div class="layui-form-item layui-col-md12 form-item group-first">
                        <label class="layui-form-label">
                            <span>* </span>群组名称：</label>
                        <div class="layui-input-block layui-col-md4">
                            <input type="text" v-model="groupName.group_name" placeholder="请输入群组名称" class="layui-input group-name">
                        </div>
                    </div>
                    <!-- 管理员 -->
                    <div class="layui-form-item layui-col-md12 form-item">
                        <label class="layui-form-label">
                            <span>* </span>管理员：</label>
                        <div class="layui-input-block layui-col-md4">
                            <button class="layui-btn" @click="addMan($event)" data-code="1"><i class="iconfont icon-add" data-code="1"></i>新增</button>
                        </div>
                    </div>
                    <!-- 管理员人员数量 -->
                    <div class="layui-form-item layui-col-md12 form-item">
                        <label class="layui-form-label">人员数量( <span class="group-nums">{{lastTags1.length}}</span>)</label>
                        <div class="layui-input-block layui-col-md8">
                            <ul class="group-ul ul-man" data-code="1">
                                <li v-for="(item, index) in lastTags1" :data-id="item.employee_id"><span>{{item.employee_name}}</span><i class="iconfont icon-delete" @click="deleteTag1(index)"></i></li>
                            </ul>
                        </div>
                    </div>
                    <!-- 群组成员 -->
                    <div class="layui-form-item layui-col-md12 form-item">
                        <label class="layui-form-label">
                            <span>*</span>群组成员：</label>
                        <div class="layui-input-block layui-col-md8">
                            <button class="layui-btn" @click="addMan($event)"  data-code="2"><i class="iconfont icon-add" data-code="2"></i>新增</button>
                            <button class="layui-btn" @click="nowGroupShow">增加现有群组</button>
                        </div>
                    </div>
                    <!-- 群组成员人员数量 -->
                    <div class="layui-form-item layui-col-md12 form-item">
                        <label class="layui-form-label">人员数量( <span class="group-nums">{{lastTags2.length}}</span>)</label>
                        <div class="layui-input-block layui-col-md8">
                            <ul class="group-ul ul-member" data-code="2">
                                <li v-for="(item, index) in lastTags2" :data-id="item.employee_id">{{item.employee_name}}<i class="iconfont icon-delete" @click="deleteTag2(index)"></i></li>
                            </ul>
                        </div>
                    </div>
                    <!-- 保存与取消 -->
                    <div class="examine">
                        <div class="example-handle">
                            <a class="examine-query" @click="editMan">保存</a><a class="examine-reset"  @click="cancel">取消</a>
                        </div>
                    </div>
                    <!--组织架构-->
                    <div class="organize-framework" v-show="groupShow">
                        <h2 class="organize-nav">组织架构<a href="JavaScript:;"></a></h2>
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
                                    <ul class="metismenu" id="menu1">
                                        <li v-for="(item, index) in groupList">
                                            <a :class="!item.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item.id" :data-text="item.name" @click="orgSelectItem">
                                                {{ item.name }}
                                                <span @click.stop="orgSelectAll($event,item.id)" v-if="!item.type">全选</span>
                                            </a>
                                            <ul aria-expanded="false" v-if="item.child">
                                                <li v-for="(item2, index) in item.child">
                                                    <a :class="!item2.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item2.id" :data-text="item2.name" @click="orgSelectItem">
                                                        {{ item2.name }}
                                                        <span @click.stop="orgSelectAll" v-if="!item2.type">全选</span>
                                                    </a>
                                                    <ul aria-expanded="false" v-if="item2.child">
                                                        <li v-for="(item3, index) in item2.child">
                                                            <a :class="!item3.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item3.id" :data-text="item3.name" @click="orgSelectItem">
                                                                {{ item3.name }}
                                                                <span @click.stop="orgSelectAll" v-if="!item3.type">全选</span>
                                                            </a>
                                                            <ul aria-expanded="false" v-if="item3.child">
                                                                <li v-for="(item4, index) in item3.child">
                                                                    <a :class="!item4.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item4.id" :data-text="item4.name" @click="orgSelectItem">
                                                                        {{ item4.name }}
                                                                        <span @click.stop="orgSelectAll" v-if="!item4.type">全选</span>
                                                                    </a>
                                                                    <ul aria-expanded="false" v-if="item4.child">
                                                                        <li v-for="(item5, index) in item4.child">
                                                                            <a :class="!item5.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item5.id" :data-text="item5.name" @click="orgSelectItem">
                                                                                {{ item5.name }}
                                                                                <span @click.stop="orgSelectAll" v-if="!item5.type">全选</span>
                                                                            </a>
                                                                            <ul aria-expanded="false" v-if="item5.child">
                                                                                <li v-for="(item6, index) in item5.child">
                                                                                    <a :class="!item6.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item6.id" :data-text="item6.name" @click="orgSelectItem">
                                                                                        {{ item6.name }}
                                                                                        <span @click.stop="orgSelectAll" v-if="!item6.type">全选</span>
                                                                                    </a>
                                                                                    <ul aria-expanded="false" v-if="item6.child">
                                                                                        <li v-for="(item7, index) in item6.child">
                                                                                            <a :class="!item7.type && 'has-arrow'" href="javascript:;" aria-expanded="false" :data-id="item7.id" :data-text="item7.name" @click="orgSelectItem">
                                                                                                {{ item7.name }}
                                                                                                <span @click.stop="orgSelectAll" v-if="!item7.type">全选</span>
                                                                                            </a>
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
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="choose-people">
                                <h2>已选客户：{{ selectedOrgUsr.length }}</h2>
                                <ul>
                                    <li v-for="(item, index) in selectedOrgUsr" :data-id="item.id">{{ item.name }} <i @click="delChoose(index)"></i></li>
                                </ul>
                            </div>
                        </div>
                        <div class="organize-bth">
                            <button class="cancel" @click="groupCancel">取消</button>
                            <button class="ensure" @click="groupSure($event)">确定</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-row common-row group-row" v-show="addGroupShow">
                <!-- 头部 -->
                <div class="layui-col-md12 row-list">
                    <h2 class="jur-head">现有群组</h2>
                </div>
                <!-- 搜索查询等 -->
                <div class="layui-col-md12 jur-top row-list fl">
                    <div class="layui-form juf-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">群组名：</label>
                            <div class="layui-input-block">
                                <input type="text" name="group_name"  lay-verify="" placeholder="请输入标题" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">管理员：</label>
                            <div class="layui-input-block">
                                <input type="text" name="administrator"  lay-verify="" placeholder="请输入标题" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="example-handle">
                            <button class="examine-query" lay-submit lay-filter="formSelect">查询</button>
                        </div>
                    </div>
                </div>
                <div class="jur-total clear">共<span class="jur-number">{{getAllListTotal}}</span>个群组</div>
                <!-- 表格部分 -->
                <table class="common-table wait-table" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
                    <thead>
                    <tr align="center">
                        <th>
                            <div class="squaredFour">
                                <input type="checkbox" value="None" id="check-all" name="checks" class="checkAll"/>
                                <label  class="child-select" for="check-all"></label>
                            </div>
                        </th>
                        <th>序号</th>
                        <th>群组名称</th>
                        <th>人员数量</th>
                        <th>管理员</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr align="center" v-for="(item, index) in groupNewList">
                        <td>
                            <div class="squaredFour">
                                <input type="checkbox" value="None" :data-id="item.id" :id='"check" + index' name='check' class="child-input"/>
                                <label class="child-select" :for='"check" + index'></label>
                            </div>
                        </td>
                        <td>{{index + 1}}</td>
                        <td>{{item.group_name}}</td>
                        <td>{{item.member_num}}</td>
                        <td class="group-admin">{{item.administrator}}</td>
                        <td><a class="audit-skip" href="javascript:;" :data-id="item.id" @click="newGroupSelect(item.id)">选择</a></td>
                    </tr>
                    </tbody>
                </table>
                <div class="page">
                    <div class="page-select" style="margin-left: 20px;">
                        <div class="examine">
                            <div class="example-handle pull-left">
                                <a class="examine-query" @click="batchAdd">批量添加</a>
                            </div>
                            <div class="example-handle pull-left">
                                <a class="examine-reset" @click="newAddCancel">取消</a>
                            </div>
                        </div>
                    </div>
                    <!-- 分页 S-->
                    <div id="test2" class="pull-right"></div>
                    <!-- 分页 E-->
                </div>
            </div>
            <!-- 选择成员 S-->
            <div class="group-pop" v-show="lastChooseShow">
                <div class="select-title">
                    <h3>选择成员</h3>
                </div>
                <ul class="choose-ul">
                    <li v-for="(item, index) in lastChoose">
                        <div class="squaredFour">
                            <input type="checkbox" value="None" :data-id="item.employee_id" :id='"checked" + index' name="checkOne" class="child-last"/>
                            <label class="child-select" :for='"checked" + index'></label>
                        </div>
                        <span >{{item.employee_name}}</span>
                    </li>
                </ul>
                <div class="example-handle">
                    <div class="squaredFour">
                        <input type="checkbox" value="None" id='checkLast' name="checkSelect" class="child-one"/>
                        <label class="child-select" for='checkLast'></label>
                    </div>
                    <span>全选</span>
                    <button class="examine-reset" @click="selectCancel">取消</button><button class="examine-query pull-right" @click="selectOneUser">确定</button>
                </div>
            </div>
            <!-- 选择成员 E-->
        </div>
    </div>
</div>

<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/groupManageEdit.js"></script>
</body>
</html>