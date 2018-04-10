<?php if (!defined('THINK_PATH')) exit(); /*a:9:{s:107:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\examination\my_audit\all_list.html";i:1510208331;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1509896789;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\bread_nav.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\side_menu.html";i:1510021729;s:99:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\global_message.html";i:1509704257;s:91:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\player.html";i:1509896789;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1509704257;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1509704257;}*/ ?>
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
<link rel="stylesheet" href="__ADMINCSS__/page/financeManage.css" />
</head>
<body>

<!-- 客户审批 -->
<div class="container">
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
        <!-- 权限组列表主内容区域 -->
        <div class="layui-container">
            <div class="layui-tab" lay-filter="tabProject">
                <ul class="layui-tab-title init-tab">
                    <?php if(checkAuth('/admin/examination/my_audit/my_submit') == 'true'): ?>
                    <li class="" data-id="1">我提交的审批</li>
                    <?php endif; if(checkAuth('/admin/examination/my_audit/all_lists') == 'true'): ?>
                    <li data-id="2">全部审批</li>
                    <?php endif; if(checkAuth('/admin/examination/my_audit/my_modify') == 'true'): ?>
                    <li data-id="3">待我修改的审批</li>
                    <?php endif; ?>
                </ul>
                <div class="layui-tab-content init-content">
                    <?php if(checkAuth('/admin/examination/my_audit/my_submit') == 'true'): ?>
                    <!-- 我提交的审批 S-->
                    <div class="layui-tab-item  content-list" data-id="1">
                        <!-- 头部 -->
                        <div class="layui-form-item finance-search">
                            <div class="layui-input-block">
                                <form action="" class="layui-form">
                                    <input type="text" name="title" lay-verify="" @blur="searchBlur" autocomplete="off" placeholder="请输入客户姓名、产品名称" class="layui-input light-in">
                                    <button lay-submit lay-filter="formSearchMe" class="layui-btn iconfont icon-search search-icon"></button>
                                </form>
                            </div>
                        </div>
                        <table class="common-table" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
                            <thead>
                            <tr align="center">
                                <th>客户姓名</th>
                                <th>购买产品</th>
                                <th>产品类型</th>
                                <th>购买数量</th>
                                <th>付费金额（元）</th>
                                <th>支付方式</th>
                                <th>移交时间</th>
                                <th class="left-status">审核状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr align="center" v-for="item in tableDataMe">
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.customer_name}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.goods_name}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.product_type == 1 ? '线上产品' : (item.product_type == 2 ? '线下产品' : '******') }}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_num}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_money}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_type == 1 ? '线上支付' : (item.pay_type == 2 ? '线下支付' : '******') }}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.create_time}}</a></td>
                                <td class="audit-td left-status">
                                    <a class="open-show" data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.new_status}}
                                        <div class="audit-state">
                                            <p>上一步状态：{{item.up_status}}</p>
                                            <p>审核备注：{{item.audit_remark}}</p>
                                            <p>审核时间：{{item.audit_time}}</p>
                                            <p>最新状态：{{item.new_status}}</p>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="tableDataMe.length == 0" class="no-result">
                            <p>暂无数据</p>
                        </div>
                        <!-- 分页 S-->
                        <div class="page" v-show="tableDataMe.length != 0">
                            <div class="page-select">
                                <span class="page-title">每页展示客户</span>
                                <ul>
                                    <li><a href="javascript:;" @click="pageNumMe($event ,index)">1000</a></li>
                                    <li><a href="javascript:;" @click="pageNumMe($event)">500</a></li>
                                    <li><a href="javascript:;" @click="pageNumMe($event)">300</a></li>
                                </ul>
                            </div>
                            <div class="page-choice">
                                <div id="test1"></div>
                            </div>
                            <p class="page-total">共有<span>{{ getMeListTotal }}</span>条记录</p>
                        </div>
                        <!-- 分页 E-->
                    </div>
                    <!-- 我提交的审批 E-->
                    <?php endif; if(checkAuth('/admin/examination/my_audit/all_lists') == 'true'): ?>
                    <!-- 全部审批 S-->
                    <div class="layui-tab-item content-list" data-id="2">
                        <!-- 审核流程 -->
                        <div class="finance-flow">
                            <span class="flow-title">审核流程</span>
                            <ul>
                                <li><a class="flow-first" href="javascript:;"><span>①</span>财务</a></li>
                                <li><a class="flow-middle" href="javascript:;"><span>②</span>质检</a></li>
                                <li><a class="flow-middle" href="javascript:;"><span>③</span>合同</a></li>
                                <li><a class="flow-last" href="javascript:;"><span>④</span>回访</a></li>
                            </ul>
                        </div>
                        <!-- 搜索 -->
                        <div class="layui-form-item finance-search financial">
                            <div class="layui-input-block">
                                <form action="" class="layui-form">
                                    <input type="text" name="title" lay-verify="keywords" @blur="searchBlur" autocomplete="off" placeholder="请输入客户姓名、产品名称或所属销售" class="layui-input">
                                    <button lay-submit="" lay-filter="formSearchAll" class="layui-btn iconfont icon-search search-icon"></button>
                                </form>
                            </div>
                        </div>
                        <!-- 标签 -->
                        <div class="finance-tag">
                            <div class="examine-tag">
                                <span class="tag-title">筛选：</span>
                                <ul class="filter">
                                    <li v-for="(item, index) in condition">
                                        <a href="javascript:;"
                                           :class="['tag-a', item.show && 'show', item.active && 'tag-active']"
                                           @click="showCondition(index)">
                                            {{item.name}}<i class="iconfont icon-down-arrow"></i>
                                        </a>
                                        <div class="filter-time" v-if="index == 0">
                                            <div class="choose-icon">
                                                <div>
                                                    <a href="javascript:;" @click="noCondition($event, index)">全部</a>
                                                </div>
                                                <div>
                                                    <span class="choose-title">财务：</span>
                                                    <a href="javascript:;" @click="setCondition($event, index)">待财务审核</a>

                                                </div>
                                                <div>
                                                    <span class="choose-title">质检：</span>
                                                    <a href="javascript:;" @click="setCondition($event, index)">待质检审核</a>
                                                    <a href="javascript:;" @click="setCondition($event, index)">质检转投诉</a>
                                                </div>

                                                <div>
                                                    <span class="choose-title">合同：</span>
                                                    <a href="javascript:;" @click="setCondition($event, index)">待合同审核</a>
                                                    <a href="javascript:;" @click="setCondition($event, index)">合同待签订</a>
                                                    <a href="javascript:;" @click="setCondition($event, index)" class="choose-wait">合同待审核</a>
                                                    <a href="javascript:;" @click="setCondition($event, index)">合同待修改</a>
                                                </div>
                                                <div>
                                                    <span class="choose-title">回访：</span>
                                                    <a href="javascript:;" @click="setCondition($event, index)">待回访审核</a>
                                                    <a href="javascript:;" @click="setCondition($event, index)">回访转投诉</a>
                                                </div>
                                                <div>
                                                    <span class="choose-title">销售：</span>
                                                    <a href="javascript:;" @click="setCondition($event, index)">签单审核待修改</a>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="filter-time" v-if="index == 1">
                                            <div class="choose-icon">
                                                <a href="javascript:;" @click="noCondition($event, index)">全部</a>
                                                <a href="javascript:;" @click="setCondition($event, index)">今天</a>
                                                <a href="javascript:;" @click="setCondition($event, index)">昨天</a>
                                                <a href="javascript:;" @click="setCondition($event, index)">最近7天</a>
                                                <a href="javascript:;" @click="setCondition($event, index)">最近30天</a>
                                                <a href="javascript:;" @click="setCondition($event, index, 1)">自定义</a>
                                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                                    <input :class="['layui-input', 'lay-date-a-' + index]" readonly="">
                                                    -
                                                    <input :class="['layui-input', 'lay-date-b-' + index]" readonly="">
                                                    <button @click="addConditons($event, index)">确定</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-time" v-if="index == 2">
                                            <div class="choose-icon">
                                                <a href="javascript:;" @click="noCondition($event, index)">全部</a>
                                                <a href="javascript:;" @click="setCondition($event, index)">线上支付</a>
                                                <a href="javascript:;" @click="setCondition($event, index)">线下支付</a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <table class="common-table" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
                            <thead>
                            <tr align="center">
                                <th>客户姓名</th>
                                <th>购买产品</th>
                                <th>产品类型</th>
                                <th>购买数量</th>
                                <th>付费金额（元）</th>
                                <th>支付方式</th>
                                <th>移交时间</th>
                                <th>销售人员</th>
                                <th class="left-status">审核状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr align="center" v-for="item in tableDataAll">
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.customer_name}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.goods_name}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.product_type == 1 ? '线上产品' : (item.product_type == 2 ? '线下产品' : '******') }}</td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_num}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_money}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_type == 1 ? '线上支付' : (item.pay_type == 2 ? '线下支付' : '******') }}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.create_time}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.salesman}}</a></td>
                                <td class="audit-td left-status">
                                    <a class="open-show" data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>
                                        {{item.new_status}}
                                        <div class="audit-state">
                                            <p>上一步状态：{{item.up_status}}</p>
                                            <p>审核备注：{{item.audit_remark}}</p>
                                            <p>审核时间：{{item.audit_time}}</p>
                                            <p>最新状态：{{item.new_status}}</p>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="tableDataAll.length == 0" class="no-result">
                            <p>暂无数据</p>
                        </div>
                        <!-- 分页 S-->
                        <div class="page" v-show="tableDataAll.length != 0">
                            <div class="page-select">
                                <span class="page-title">每页展示客户</span>
                                <ul>
                                    <li><a href="javascript:;" @click="pageNumAll($event)">1000</a></li>
                                    <li><a href="javascript:;" @click="pageNumAll($event)">500</a></li>
                                    <li><a href="javascript:;" @click="pageNumAll($event)">300</a></li>
                                </ul>
                            </div>
                            <div class="page-choice">
                                <div id="test2"></div>
                            </div>
                            <p class="page-total">共有<span>{{ getAllListTotal }}</span>条记录</p>
                        </div>
                        <!-- 分页 E-->
                    </div>
                    <!-- 全部审批 E-->
                    <?php endif; if(checkAuth('/admin/examination/my_audit/my_modify') == 'true'): ?>
                    <!-- 待我修改的审批 S-->
                    <div class="layui-tab-item content-list" data-id="3">
                        <!-- 审核流程 -->
                        <div class="finance-flow">
                            <span class="flow-title">审核流程</span>
                            <ul>
                                <li><a class="flow-first" href="javascript:;"><span>①</span>财务</a></li>
                                <li><a class="flow-middle" href="javascript:;"><span>②</span>质检</a></li>
                                <li><a class="flow-middle" href="javascript:;"><span>③</span>合同</a></li>
                                <li><a class="flow-last" href="javascript:;"><span>④</span>回访</a></li>
                            </ul>
                        </div>
                        <div class="finance-wait">
                            <div class="squaredFour">
                                <input type="checkbox" value="None" id="check-all" name="checks" class="checkAll"/>
                                <label  class="child-select" for="check-all"></label>
                            </div>
                            <span>全选</span>
                            <a @click="deleteName">删除合作情况</a>
                        </div>
                        <table class="common-table wait-table" cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
                            <thead>
                            <tr align="center">
                                <th></th>
                                <th>客户姓名</th>
                                <th>购买产品</th>
                                <th>产品类型</th>
                                <th>购买数量</th>
                                <th>付费金额（元）</th>
                                <th>支付方式</th>
                                <th>移交时间</th>
                                <th>拒绝环节</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr align="center" v-for="(item, index) in tableDataWait">
                                <td class="td-first">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" :data-id="item.cooper_id" :id='"check" + index' name="check" class="child-input"/>
                                        <label class="child-select" :for='"check" + index'></label>
                                    </div>
                                </td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.customer_name}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.goods_name}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.product_type == 1 ? '线上产品' : (item.product_type == 2 ? '线下产品' : '******') }}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_num}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_money}}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.pay_type == 1 ? '线上支付' : (item.pay_type == 2 ? '线下支付' : '******') }}</a></td>
                                <td><a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>{{item.create_time}}</a></td>
                                <td class="audit-td">
                                    <a class="open-show" data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=1&id=" + item.cooper_id'>
                                        {{item.refused_link}}
                                        <div class="audit-state audit-state-other">
                                            <p>拒绝环节：{{item.refused_link}}</p>
                                            <p>审核说明：{{item.refused_reason}}</p>
                                            <p>审核时间：{{item.refused_time}}</p>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a data-type="tab" data-title="合作情况" :href='"/admin/customers/customer/update?customer_id=" + item.customer_id + "&type=3&status=2&id=" + item.cooper_id' class="audit-skip">修改</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="tableDataWait.length == 0" class="no-result">
                            <p>暂无数据</p>
                        </div>
                        <!-- 分页 S-->
                        <div class="page" v-show="tableDataWait.length != 0">
                            <div class="page-select">
                                <span class="page-title">每页展示客户</span>
                                <ul>
                                    <li><a href="javascript:;" @click="pageNumWait($event)">1000</a></li>
                                    <li><a href="javascript:;" @click="pageNumWait($event)">500</a></li>
                                    <li><a href="javascript:;" @click="pageNumWait($event)">300</a></li>
                                </ul>
                            </div>
                            <div class="page-choice">
                                <div id="test3"></div>
                            </div>
                            <p class="page-total">共有<span>{{ getWaitListTotal }}</span>条记录</p>
                        </div>
                        <!-- 分页 E-->
                    </div>
                    <!-- 待我修改的审批 E-->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/myExamination.js"></script>
</body>
</html>