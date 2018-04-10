<?php if (!defined('THINK_PATH')) exit(); /*a:12:{s:103:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customers\customer\update.html";i:1509790850;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1509896789;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\bread_nav.html";i:1509704257;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\side_menu.html";i:1510021729;s:99:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\global_message.html";i:1509704257;s:91:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\player.html";i:1509896789;s:107:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customers\customer\basic-info.html";i:1510205918;s:108:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customers\customer\follow-info.html";i:1510127682;s:113:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customers\customer\cooperation-info.html";i:1510127682;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1509704257;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1509704257;}*/ ?>
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
<link rel="stylesheet" href="__ADMINCSS__/page/client-info.css" /> </head>
<body>

<div class="container client-info-container">
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
    <div class="main-wrap" id="app" v-cloak>
        <!-- 档案头部 -->
        <div class="file-head">
            <div class="layui-row head-row">
                <div class="file-time pull-left">
                    <span>档案编号：{{headInfo.archives_num}}</span>
                    <span>创建时间：{{headInfo.create_time}}</span>
                </div>
                <div class="pull-right">
                    <?php if(checkAuth('/admin/customers/remarks/indexs') == 'true'): ?>
                    <a class="layui-btn" @click.stop="remarkEdit($event)" v-if="headInfo.is_cebind">编辑备注</a>
                    <?php endif; if(checkAuth('/admin/group/employeecustomgroup/updates') == 'true'): ?>
                    <a class="layui-btn" @click.stop="editGroup"  v-if="headInfo.is_cebind">编辑分组</a>
                    <?php endif; if(checkAuth('/admin/customers/customeroperationlog/index') == 'true'): ?>
                    <a class="layui-btn" data-type="tab" data-title="操作日志" :href='"/admin/customers/customer_operation_log/index?customer_id=" + customer_id '>操作日志</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="layui-row head-row">
                <div class="client-icon pull-left">
                    <img :src='"/assets/images/calluser" +headInfo.head_type +".png"' onerror="this.src='/assets/images/calluser0.png'">
                </div>
                <div class="basic-info layui-col-md11">
                    <div class="layui-col-md12 clearfix">
                        <div class="pull-left">
                            <span class="pull-left client-name">{{headInfo.real_name || '未命名'}} <i v-if="headInfo.sex">({{headInfo.sex | formatSex}})</i></span>
                        </div>
                        <ul class='label-list pull-left layui-col-md11'>
                            <li v-for="i in client_guest">{{i.tag_name}}</li>
                        </ul>
                    </div>
                    <div class="layui-col-md12 info-row">
                        <div class="layui-col-md2">
                            <label><i style="color: #f2493b">*</i> 来源 : </label>
                            <span v-if="customer_from_channel">{{customer_from_channel_text}}{{headInfo.mark ? '( ' + headInfo.mark +' )' : ''}}</span>
                        </div>
                        <div class="layui-col-md2 layui-elip">
                            <label>QQ : </label>
                            <span>{{headInfo.qq || '--'}}</span>
                        </div>
                        <div class="layui-col-md2 layui-elip">
                            <label> 微信号 : </label>
                            <span>{{headInfo.weixin || '--'}}</span>
                        </div>
                        <div class="layui-col-md3">
                            <label>身份证 : </label>
                            <span>{{headInfo.id_card || '--'}}</span>
                        </div>
                        <div class="layui-col-md2">
                            <label>职业 : </label>
                            <span>{{headInfo.career ||'--'}}</span>
                        </div>
                        <div class="layui-col-md5">
                            <label>地址 : </label>
                            <span>{{headInfo.province_name || '--'}}{{headInfo.city_name}}{{headInfo.area_name}}{{headInfo.address}}</span>
                        </div>
                    </div>
                    <div class="layui-col-md12 phone-row">
                        <label>电话 : </label>
                        <div class="phone-row-list">
                            <span v-for="mobile in headInfo.mobile">{{mobile.mobile || '--'}}
                                <?php if(checkAuth('/admin/call/call/call') == 'true'): ?>
                                <i v-if="mobile.mobile != '' && mobile.contact_id != ''" class='iconfont icon-phone' @click.stop="callTell(mobile.contact_id)"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 档案主题tab -->
        <div class="main">
            <div class="layui-tab layui-tab-card" lay-filter="client-tabs">
                <ul class="layui-tab-title">
                    <?php if(checkAuth('/admin/customers/customer/detail') == 'true'): ?>
                    <li lay-id="1" class="layui-this basic-li"> 
                        <a href="javascript:;">基本信息</a>
                    </li>
                    <?php endif; if(checkAuth('/admin/customers/followuprecord/index') == 'true'): ?>
                    <li lay-id="2" class="follow-li">
                        <a href="javascript:;">跟进信息</a>
                    </li>
                    <?php endif; if(checkAuth('/admin/cooper/customercoopersituation/index') == 'true'): ?>
                    <li lay-id="3" class="cooper-li">
                        <a href="javascript:;">合作情况</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="layui-tab-content tab-content-wrap">
                    <?php if(checkAuth('/admin/customers/customer/detail') == 'true'): ?>
                    <!-- 基本信息 -->
                    <div class="layui-tab-item basic-info">
                        <!--基本信息 -->

    <div class="eidt-box">
        <span>
            <i>*</i>新增合作情况必输</span>
        <span>
            <i>*</i>新增客户必输</span>
        <?php if(checkAuth('/admin/customers/customer/updates') == 'true'): ?>
        <a href="javascript:;" @click.stop="isEditBasic" v-if="!editBasic">编辑</a>
        <?php endif; ?>
    </div>
    <!-- 查看基本信息 -->
    <div class="layui-collapse examine-basic-info" :class="{'layui-hide':editBasic}">
        <div class="layui-colla-item basic-item">
            <h2 class="layui-colla-title basic-item-title">基本信息</h2>
            <div class="layui-colla-content basic-item-content layui-clear layui-show">
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-md2 layui-col-item">
                        <label>
                            <i>*</i>姓名</label>
                        <span>{{basicInfo.real_name || '未命名'}}</span>
                    </div>
                    <div class="layui-col-md3 layui-col-item">
                        <label>
                            <i>*</i> 身份证</label>
                        <span v-text="basicInfo.id_card || '--'"></span>
                    </div>
                    <div class="layui-col-md3 layui-col-item">
                        <label>性别</label>
                        <span>{{basicInfo.sex | formatSex}}</span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-md2 layui-col-item">
                        <label>年龄</label>
                        <span v-text="basicInfo.age || '--'"></span>
                    </div>
                    <div class="layui-col-md3 layui-col-item">
                        <label>职业</label>
                        <span v-text="basicInfo.career || '--'">--</span>
                    </div>
                    <div class="layui-col-md3 layui-col-item">
                        <label><i style="color: #f2493b">*</i>来源</label>
                        <!-- <span>{{global_set.customer_from_channel[basicInfo.from_channel]}}</span> -->
                        <span>{{customer_from_channel_text}}{{basicInfo.mark ? '(' + basicInfo.mark +')' : ''}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-colla-item layui-clear basic-item">
            <h2 class="layui-colla-title basic-item-title">联系方式</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-item layui-col-md12">
                        <label>
                            <i>*</i> 电话</label>
                        <span v-for='i in basicInfo.mobile'>{{i.mobile ? i.mobile : '--'}}</span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row layui-elip">
                    <div class="layui-col-item layui-col-md2 layui-elip">
                        <label>QQ</label>
                        <span v-text="basicInfo.qq || '--'"></span>
                    </div>
                    <div class="layui-col-item layui-col-md2 layui-elip">
                        <label>微信号</label>
                        <span v-text="basicInfo.weixin || '--'"></span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-md2 layui-col-item">
                        <label>省份</label>
                        <span v-text="basicInfo.province_name || '--'"></span>
                    </div>
                    <div class="layui-col-md2 layui-col-item">
                        <label>市</label>
                        <span v-text="basicInfo.city_name ||'--'"></span>
                    </div>
                    <div class="layui-col-md2 layui-col-item">
                        <label>区/县</label>
                        <span v-text="basicInfo.area_name || '--'"></span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-item">
                        <label>详细地址</label>
                        <span v-text="basicInfo.address || '--'"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-colla-item basic-item layui-clear">
            <h2 class="layui-colla-title basic-item-title">财务投资经验</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-md4 layui-col-item">
                        <label>收入来源</label>
                        <span v-for="i in finance_info.income_source" v-if="i.is_check">{{i.t_name}}<font v-if="i.t_id == 4">{{basicInfo.finance_info.income_source_mark ? '('+basicInfo.finance_info.income_source_mark +')' : ''}}</font></span>
                    </div>
                    <div class="layui-col-md4 layui-col-item layui-elip">
                        <label>最近三年平均收入</label>
                        <span>{{finance_info.lately_avg_income ? finance_info.lately_avg_income +' 元' : '--'}}</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item">
                        <label>有无债务</label>
                        <span>{{finance_info.has_debt == '2' ? '无' :  finance_info.has_debt == '' ? '--' : '有'}} {{finance_info.has_debt_mark ? finance_info.has_debt_mark + '元' : ''}}</span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-md4 layui-col-item">
                        <label>金融资产</label>
                        <span v-for="i in finance_info.financial_assets" v-if="i.is_check">{{i.t_name}}{{i.t_money ? '：'+i.t_money + '元' : ''}}{{i.t_name ? '；' : ''}}</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item">
                        <label>开通创业板</label>
                        <span>{{finance_info.is_open_gem == 1 ? '是': finance_info.is_open_gem == 2 ? '否' : '--'}}</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item">
                        <label>开通ST</label>
                        <span>{{finance_info.is_open_st == 1 ? '是': finance_info.is_open_st == 2 ? '否' : '--'}}</span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row border-bottom">
                    <div class="layui-col-item">
                        <label>当前仓位</label>
                        <span>{{finance_info.current_position != '' ?  finance_info.current_position + '成' : '--'}} </span>
                    </div>
                </div>
                <div class="layui-col-md12 content-row">
                    <div class="layui-col-md4 layui-col-item">
                        <label>证券投资</label>
                        <span v-for="i in finance_info.portfolio_investment" v-if="i.is_check">{{i.t_name}}{{i.t_money ? '：'+i.t_money + '年' : ''}}{{i.t_name ? '；' : ''}}</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item layui-elip">
                        <label>金融投资学习工作经历</label>
                        <span v-if="finance_info.financial_investment_experience && finance_info.financial_investment_experience_mark">{{finance_info.financial_investment_experience_mark + '年'}}</span>
                        <span v-if="finance_info.financial_investment_experience && !finance_info.financial_investment_experience_mark">{{finance_info.financial_investment_experience == 1 ? '有' : '无'}}</span>
                        <span v-if="!finance_info.financial_investment_experience">--</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item layui-elip">
                        <label>金融行业职业资格证书</label>
                        <span v-if="finance_info.financial_industry_certificate && finance_info.financial_industry_certificate_mark">{{'《' + finance_info.financial_industry_certificate_mark +'》'}}</span>
                        <span v-if="finance_info.financial_industry_certificate && !finance_info.financial_industry_certificate_mark">{{finance_info.financial_industry_certificate == 1 ? '有' : '无'}}</span>
                        <span v-if="!finance_info.financial_industry_certificate">--</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-box">
            <button class="layui-btn" @click="basicBack">返回</button>
        </div>
    </div>
    <!-- 编辑基本信息 -->
    <div class="layui-collapse layui-form edit-basic-info" :class="{'layui-hide':!editBasic}">
        <div class="layui-colla-item basic-item">
            <h2 class="layui-colla-title basic-item-title">基本信息</h2>
            <div class="layui-colla-content layui-form basic-item-content layui-clear layui-show">
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">
                        <span>*</span>姓名</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <input type="text" name="real_name" placeholder="请输入姓名" class="layui-input" :value="basicInfo.real_name" maxlength="6" :disabled="basicInfo.real_name == '******'"> <!--lay-verify="username"-->
                    </div>
                    <!-- <div class="layui-form-mid layui-word-aux">姓名不能为空</div> -->
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">
                        <span>*</span>身份证</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <input type="text" name='id_card' placeholder="请输入身份证" :disabled="basicInfo.id_card == '******'" class="layui-input" :value="basicInfo.id_card" maxlength="18">
                    </div>
                    <!-- @blur="numVerify(4, $event,'update')" -->
                    <!-- <div class="layui-form-mid layui-word-aux">请输入正确的身份证号码</div> -->
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">性别</label>
                    <div class="layui-input-block layui-col-md4 input-block-box" >
                        <input type="radio" name="sex" value="1" title="男" lay-filter='sex' :checked="basicInfo.sex == 1" :disabled="basicInfo.sex == '******'">
                        <input type="radio" name="sex" value="2" title="女" lay-filter='sex' :checked="basicInfo.sex == 2" :disabled="basicInfo.sex == '******'">
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">年龄</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <input type="text" name='age' placeholder="请输入年龄" :disabled="basicInfo.age == '******'" onkeyup="value=value.replace(/[^\d{2}]/,'')" class="layui-input" :value="basicInfo.age" maxlength="3">
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">职业</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <input type="text" name="career" placeholder="请输入职业" :disabled="basicInfo.career == '******'" class="layui-input" :value="basicInfo.career" maxlength="15">
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12 source-box" v-if="global_set.customer_from_channel && global_set.customer_from_channel">
                    <label class="layui-form-label row-label">
                        <span class="red-font">*</span>来源</label>
                    <div class="layui-input-block layui-col-md6 input-block-box">
                        <input type="radio" name="from_channel" v-for="(i,index) in global_set.customer_from_channel" :value="index" :title="i" :checked="basicInfo.from_channel == index" :disabled="basicInfo.from_channel == '******'" lay-filter="from_channel">
                        <div class="layui-input-inline input-block-box" :class="{'layui-hide': basicInfo.from_channel != 3}" data-show="from_channel">
                            <input type="text" name="mark" :disabled="basicInfo.mark == '******'" placeholder="请输入来源" maxlength="15" class="layui-input" :value="basicInfo.mark">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-colla-item basic-item">
            <h2 class="layui-colla-title basic-item-title">联系方式 </h2>
            <div class="layui-colla-content clearfix layui-show basic-item-content basic-first-content" style="overflow:visible">
                <div class="layui-form-item layui-col-md12" v-for="(mobile,index) in basicInfo.mobile">
                    <label class="layui-form-label row-label" v-html="index == 0 ? '<span>*</span>电话' : ''"></label>
                    <div class="layui-input-block layui-col-md4 input-block-box input-icon-box">
                        <input type="text" :name='"mobile[" + index + "]"' onkeyup="this.value = this.value.replace(/[^\d]/g,'')"  placeholder="请输入电话号码" maxlength="30" class="layui-input" v-model="editBasicMobile[index].mobile" :value="basicInfo.mobile[index].mobile" :disabled="basicInfo.mobile[index].is_relation == 1 || basicInfo.mobile[index].mobile == '******'" @blur="numVerify(1,editBasicMobile[index].mobile,'update',index)">
                        <span class="icon-box">
                            <i class="iconfont icon-shanchu" title="删除" v-show="basicInfo.mobile[index].is_relation != 1 && basicInfo.mobile[index].mobile != '******'" v-if="index != 0" @click.stop="delMobileItem(index,basicInfo.mobile)"></i>
                            <i class="iconfont icon-add" title="增加" v-if="index == 0" @click.stop="addMobile(basicInfo.mobile)"></i>
                        </span>
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">QQ</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <input type="text" name="qq" placeholder="请输入QQ号码" :disabled="basicInfo.qq == '******'" class="layui-input" maxlength="30" :value="basicInfo.qq" onkeyup="this.value = this.value.replace(/[^\d]/g,'')" @blur="numVerify(3,$event,'update')">
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">微信</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <input type="text" name='weixin' placeholder="请输入微信号码" :disabled="basicInfo.weixin == '******'" class="layui-input" maxlength="30" :value="basicInfo.weixin" @blur="numVerify(2,$event,'update')">
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">所在地区</label>
                    <div class="layui-input-block layui-col-md2 select-wrap input-block-box">
                        <select name="province" lay-filter="province" :disabled="basicInfo.province == '******'">
                            <option value=""></option>
                            <option :value="i.id" :selected="basicInfo.province == i.id" v-for="i in area.province">{{i.name}}</option>
                        </select>
                    </div>
                    <div class="layui-input-block layui-col-md2 select-wrap input-block-box">
                        <select name="city" lay-filter="city" :disabled="basicInfo.city == '******'">
                            <option value=""></option>
                            <option :value="i.id" :selected="basicInfo.city == i.id" v-for="i in area.city">{{i.name}}</option>
                        </select>
                    </div>
                    <div class="layui-input-block layui-col-md2 select-wrap input-block-box">
                        <select name="area" lay-filter="area" :disabled="basicInfo.area == '******'">
                            <option value="" ></option>
                            <option :value="i.id" :selected="basicInfo.area == i.id" v-for="i in area.county">{{i.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <label class="layui-form-label row-label">详细地址</label>
                    <div class="layui-input-block layui-col-md4 input-block-box">
                        <textarea name="address" placeholder="请输入详细地址" :disabled="basicInfo.address == '******'" class="layui-textarea" :value="basicInfo.address" maxlength="100"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <!-- 编辑财务信息 -->
        <div class="layui-colla-item finance basic-item">
            <h2 class="layui-colla-title basic-item-title">财务投资经验</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class="clearfix border-bottom">
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">收入来源</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">

                            <input type="checkbox" :value="item.t_id" v-for="(item,index) in finance_info.income_source" :name='"income_source[" +index+ "]"' lay-skin="primary" lay-filter='income_source' :checked="item.is_check" :title="item.t_name">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12" v-for="(item,index) in finance_info.income_source" v-if="index == 3" :class="{'layui-hide': item.t_id == 4 && item.is_check != 1}" data-show="showOther">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name='income_source_mark' :value="finance_info.income_source_mark" maxlength="15" placeholder="请输入其他来源" class="layui-input ">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12 unit-wrap">
                        <label class="layui-form-label row-label">最近三年平均收入</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" placeholder="请输入金额" name="lately_avg_income" class="layui-input" :value="finance_info.lately_avg_income">
                        </div>
                        <span class="unit">元</span>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">有无债券</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="has_debt" value="1" title="有" lay-filter='has_debt' :checked="finance_info.has_debt == 1">
                            <input type="radio" name="has_debt" value="2" title="无" lay-filter='has_debt' :checked="finance_info.has_debt == 2">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12 unit-wrap" data-show="has_debt" :class="{'layui-hide': finance_info.has_debt != 1}">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="has_debt_mark" :value="finance_info.has_debt_mark" placeholder="请输入债券金额" class="layui-input">
                        </div>
                        <span class="unit">元</span>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label row-label">金融资产</label>
                        <div class="layui-input-block layui-col-md3 assets input-block-box" v-for="(item,index) in finance_info.financial_assets" v-if="index <= 1">
                            <input type="checkbox" :name='"financial_assets["+index+"]"' lay-filter="financial_assets" :value="item.t_id" :checked="item.is_check" lay-skin="primary" :title="item.t_name">
                            <div class="layui-input-block layui-col-md6">
                                <input type="text" :name='"financial_assets_money["+index+"]"' :disabled="!item.is_check" :value="item.t_money" placeholder="请输入存款金额" class="layui-input">
                            </div>
                            <span class="unit">元</span>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md3 assets input-block-box" v-for="(item,index) in finance_info.financial_assets" v-if="index > 1">
                            <input type="checkbox" :name='"financial_assets["+index+"]"' lay-filter="financial_assets" :value="item.t_id" :checked="item.is_check" lay-skin="primary" :title="item.t_name">
                            <div class="layui-input-block layui-col-md6">
                                <input type="text" :name='"financial_assets_money["+index+"]"' :disabled="!item.is_check" :value="item.t_money" placeholder="请输入存款金额" class="layui-input">
                            </div>
                            <span class="unit">元</span>
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">是否开通创业板</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="is_open_gem" value="1" title="是" lay-filter='is_open_gem' v-model="finance_info.is_open_gem">
                            <input type="radio" name="is_open_gem" value="2" title="否" lay-filter='is_open_gem' v-model="finance_info.is_open_gem">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">开通ST</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="is_open_st" value="1" title="是" lay-filter='is_open_st' v-model="finance_info.is_open_st">
                            <input type="radio" name="is_open_st" value="2" title="否" lay-filter='is_open_st' v-model="finance_info.is_open_st">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12 unit-wrap">
                        <label class="layui-form-label row-label">当前仓位</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="current_position" onkeyup="value=value.replace(/[^\d{10}]/,'')" maxlength="2" placeholder="请输入当前仓位" class="layui-input" :value="finance_info.current_position">
                        </div>
                        <span class="unit">成</span>
                    </div>
                </div>
                <div class="clearfix form-padding">
                    <div class="layui-form-item">
                        <label class="layui-form-label row-label">证券投资</label>
                        <div class="layui-input-block layui-col-md3 assets" v-for="(item,index) in finance_info.portfolio_investment" v-if="index <= 1">
                            <input type="checkbox" :value="item.t_id" lay-filter='portfolio_investment' :name='"portfolio_investment["+index+"]"' lay-skin="primary" :title="item.t_name" :checked="item.is_check">
                            <div class="layui-input-block layui-col-md6 input-block-box">
                                <input type="text" :value="item.t_money" :disabled="!item.is_check" :name='"portfolio_investment_money["+index+"]"' placeholder="请输入年限" class="layui-input">
                            </div>
                            <span class="unit">年</span>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md3 assets" v-for="(item,index) in finance_info.portfolio_investment" v-if="index == 2 || index == 3">
                            <input type="checkbox" :value="item.t_id" lay-filter='portfolio_investment' :name='"portfolio_investment["+index+"]"' lay-skin="primary" :title="item.t_name" :checked="item.is_check">
                            <div class="layui-input-block layui-col-md6 input-block-box">
                                <input type="text" :value="item.t_money" :disabled="!item.is_check" :name='"portfolio_investment_money["+index+"]"' placeholder="请输入年限" class="layui-input">
                            </div>
                            <span class="unit">年</span>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md3 assets" v-for="(item,index) in finance_info.portfolio_investment" v-if="index > 3">
                            <input type="checkbox" :value="item.t_id" lay-filter='portfolio_investment' :name='"portfolio_investment["+index+"]"' lay-skin="primary" :title="item.t_name" :checked="item.is_check">
                            <div class="layui-input-block layui-col-md6 input-block-box">
                                <input type="text" :value="item.t_money" :disabled="!item.is_check" :name='"portfolio_investment_money["+index+"]"' placeholder="请输入年限" class="layui-input reset-input">
                            </div>
                            <span class="unit unit-ccc">年</span>
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">金融投资学习工作经历</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="financial_investment_experience" value="2" title="无" lay-filter='financial_investment_experience' :checked="finance_info.financial_investment_experience == 2">
                            <input type="radio" name="financial_investment_experience" value="1" title="有" lay-filter='financial_investment_experience' :checked="finance_info.financial_investment_experience == 1">
                        </div>
                    </div>
                    <!-- v-if="finance_info.financial_investment_experience == 1" -->
                    <div class="layui-form-item layui-col-md12 unit-wrap" :class="{'layui-hide': finance_info.financial_investment_experience != 1}" data-show="financial_investment_experience">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="financial_investment_experience_mark" placeholder="请输入年限" class="layui-input" :value="finance_info.financial_investment_experience_mark">
                        </div>
                        <span class="unit">年</span>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">金融行业职业资格证书</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="financial_industry_certificate" value="2" title="无" lay-filter='financial_industry_certificate' :checked="finance_info.financial_industry_certificate == 2">
                            <input type="radio" name="financial_industry_certificate" value="1" title="有" lay-filter='financial_industry_certificate' :checked="finance_info.financial_industry_certificate == 1">
                        </div>
                    </div>
                    <!-- v-if="finance_info.financial_industry_certificate == 1" -->
                    <div class="layui-form-item layui-col-md12 unit-wrap" data-show="financial_industry_certificate_mark" :class="{'layui-hide': finance_info.financial_industry_certificate != 1}" data-show="financial_industry_certificate">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="financial_industry_certificate_mark" placeholder="请输入证书名称" class="layui-input" :value="finance_info.financial_industry_certificate_mark">
                        </div>
                        <span class="unit">资格证</span>
                    </div>

                </div>

            </div>
        </div>
        <div class="layui-form-item btn-box">
            <button class="layui-btn layui-btn-primary" @click.stop="cancel(editBasic)">取消</button>
            <button class="layui-btn" lay-submit :disabled="basicBtnDisabled" lay-filter="ok" >确认</button><!-- @click.stop="basicBtn" -->
        </div>
    </div>

                    </div>
                    <?php endif; if(checkAuth('/admin/customers/followuprecord/index') == 'true'): ?>
                    <!-- 跟进信息 -->
                    <div class="layui-tab-item follow-info basic-info">
                        <!-- 查看跟进信息 -->
<div class="examine-follow" :class="{'layui-hide':editFollow}">
    <div class="follow-info-head">
        <div class="layui-row follow-tabs">
            <button v-for="(i,index) in global_set.follow_up_type" @click.stop="followTab(i.id)" :class="filterFollow.type == i.id ? 'active' : ''">{{i.name}}</button>
        </div>
        <div class="layui-row">
            <div class="layui-col-md12">
                <div class="layui-col-md3 layui-inline">
                    <label class='layui-form-label'>操作人</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" v-model="filterFollow.operate_real_name" placeholder="请输入操作人名称">
                    </div>
                </div>
                <div class="layui-col-md3 layui-inline">
                    <label class='layui-form-label'>产品名称</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" v-model="filterFollow.goods_name" placeholder="请输入产品名称">
                    </div>
                </div>
                <div class="layui-col-md5 layui-inline">
                    <label class="layui-form-label">跟进时间</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" id="follow_start" placeholder="yyyy-MM-dd HH:mm:ss">
                    </div>
                    <span>-</span>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" id="follow_end" placeholder="yyyy-MM-dd HH:mm:ss">
                    </div>
                </div>
                <?php if(checkAuth('/admin/customers/followuprecord/add') == 'true'): ?>
                <a href="javascript:;" class="layui-btn pull-right" @click="isEditFollow">新增跟进信息</a>
                <?php endif; ?>
            </div>
            <div class="layui-col-md12">
                <div class="follow-content">
                    <label class="layui-form-label">跟进内容</label>
                    <ul class="pull-left">
                        <li v-for="(i,index) in fContent" :class="i.active ? 'active' : ''" @click.stop='followContent(index)'>{{i.name}}</li>
                    </ul>
                    <button class="layui-btn" @click.stop="inquiryFollow">查询</button>
                    <button class="layui-btn layui-btn-primary" @click.stop="resetFollow">重置</button>
                </div>

            </div>
        </div>
    </div>
    <div class="follow-info-content clearfix" v-if="followInfo.list && followInfo.list.length">
        <ul>
            <li v-for="(item,index) in followInfo.list">
                <div class="info-content-item clearfix">
                    <div class="pull-left client-icon" style="margin-right:20px;">
                        <!-- 头像暂时没有 -->
                        <img :src="item.head_portrait" alt="头像">
                    </div>
                    <div class="layui-col-md11">
                        <div class="info-item info-item-title layui-col-md12">
                            <strong class="pull-left" v-text="item.employee_nickname">张三</strong>
                            <span class="department pull-left" v-text="item.employee_department">市场部一组</span>
                            <div class="label-box pull-right">
                                <a href="javascript:;" v-for="i in item.product_info">{{i.product_name}}</a>
                            </div>
                        </div>
                        <div class="info-item layui-col-md12" v-html="item.text_record"> </div>
                        <div class="info-item info-item-img layui-col-md12" :id='"follow_info_"+ item.followup_id' v-if="item.images">
                            <a :href="i.image" v-for="i in item.images" v-if="i.image && i.thumb_image">
                                <img :src="i.thumb_image" alt="">
                            </a>
                        </div>
                        <div class="info-item layui-table layui-col-md12" v-if="item.voice_records && item.voice_records.length">
                            <table class="layui-col-md12">
                                <tbody class="layui-col-md12">
                                    <tr class="layui-col-md12">
                                        <td class="layui-col-md2">姓名</td>
                                        <td class="layui-col-md2">电话</td>
                                        <td class="layui-col-md2">通话时长</td>
                                        <td class="layui-col-md2">通话时间</td>
                                        <td class="layui-col-md2">通话类型</td>
                                        <td class="layui-col-md2">操作</td>
                                    </tr>
                                    <tr class="layui-col-md12" v-for="i in item.voice_records ">
                                        <td class="layui-col-md2">{{i.customer_real_name ? i.customer_real_name : '未知用户'}}</td>
                                        <td class="layui-col-md2">{{i.customer_mobile || '--'}}</td>
                                        <td class="layui-col-md2">{{i.talk_time | VformatM}}</td>
                                        <td class="layui-col-md2">{{i.call_time}}</td>
                                        <td class="layui-col-md2">{{i.call_type == 1 ? '呼入' : '呼出'}}</td>
                                        <td class="layui-col-md2">
                                            <?php if(checkAuth('/admin/cooper/followuprecord/playvoice') == 'true'): ?>
                                            <a v-if="i.recfile" @click="playFollow(i.recfile,i.customer_real_name,i.call_time)" href="javascript:;">播放录音</a>
                                            <span v-else>暂无录音播放</span>
                                            <?php endif; if(checkAuth('/admin/cooper/followuprecord/downloadvoice') == 'true'): ?>
                                            <a v-if="i.recfile" :href="i.recfile ? i.recfile : 'javascript:;'" download="xxx">下载录音</a>
                                            <span v-else>暂无录音下载</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="info-item layui-item-time layui-col-md12">
                            <span v-text="item.create_time"></span>
                            <?php if(checkAuth('/admin/customers/followuprecord/update') == 'true'): ?>
                            <a href="javascript:;" @click="edit_item_Follow(item.followup_id)" class="follow-a">编辑</a>
                            <?php endif; if(checkAuth('/admin/cooper/followuprecord/delete') == 'true'): ?>
                            <a href="javascript:;" @click="del_item_Follow(item.followup_id,index)" class="follow-a">删除</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="follow-laypage" :class="followInfo.list && followInfo.list.length ? '' :'layui-hide'">
        <!-- <div class="site-page pull-left">
            <label>每页展示客户</label>
            <a href="javascript:;" :class="filterFollow.pagesize == 1000 ? 'active' : ''" @click.stop="pageSize(1000)">1000</a>
            <a href="javascript:;" :class="filterFollow.pagesize == 500 ? 'active' : ''" @click.stop="pageSize(200)">500</a>
            <a href="javascript:;" :class="filterFollow.pagesize == 300 ? 'active' : ''" @click.stop="pageSize(300)">300</a>
        </div> -->
        <div class="pull-right">
            <span class="pull-left recording">共有 {{followInfo.all_num}} 条记录</span>
            <div id="follow-page" class="pull-left"></div>
        </div>
    </div>
    <div class="follow-info-content" v-if="followInfo.list && !followInfo.list.length">
        <div class="no-result">
            <div class="no-result-icon"></div>
            <p>暂无数据</p>
        </div>
    </div>
</div>
<!-- 编辑跟进信息 -->
<div class="edit-follow layui-clear" :class="{'layui-hide':!editFollow}">
    <form class='layui-form layui-clear'>
        <div class="form-content">
            <!-- 线上产品 -->
            <div class="layui-form-item layui-col-md12 content-row">
                <label class="layui-form-label row-label two-font-color">跟进产品</label>
                <div class="" style="float: left; width: 80%;">
                    <div class="layui-input-block layui-col-md12 input-block-box" v-for="(i,indexs) in global_set.online_consulting_plan">
                        <div class="layui-input-block layui-col-md2 input-block-box" v-for="(item,index) in i.child">
                            <input type="checkbox" :name='"product_id[" + (index + indexs) + "]"' lay-skin="primary" :value="item.sid" :title="item.sname">
                        </div>
                    </div>
                </div>
                <!-- <div class="layui-input-block layui-col-md8 input-block-box" v-if="!global_set.online_consulting_plan && !global_set.line_investment_plan">
                            <p class="product-tip">暂无跟进产品可供选择，请联系管理员配置</p>
                        </div> -->
            </div>
            <!-- 线下产品 -->
            <div class="layui-form-item layui-col-md12 content-row">
                <label class="layui-form-label row-label two-font-color"></label>
                <div class="layui-input-block layui-col-md8 input-block-box">
                    <div class="layui-input-block layui-col-md2 input-block-box" v-for="(i,index) in global_set.line_investment_plan">
                        <input type="checkbox"  :name='"offline_product_id[" + index + "]"' lay-skin="primary" :value="i" :title="i">
                    </div>
                </div>
            </div>
            <div class="layui-form-item layui-col-md12">
                <label class="layui-form-label row-label two-font-color">
                    <span>*</span>跟进类型</label>
                <div class="layui-input-block layui-col-md4 input-block-box">
                    <!-- :checked="editFollowData.type == i.id" -->
                    <input type="radio" name="type" v-if="i.id == 1" checked v-for="(i,index) in global_set.follow_up_type"  :value="i.id" :title="i.name">
                    <input type="radio" name="type" v-if="i.id != 1" v-for="(i,index) in global_set.follow_up_type"  :value="i.id" :title="i.name">
                </div>
            </div>
            <div class="layui-form-item layui-col-md12">
                <label class="layui-form-label row-label two-font-color">说明</label>
                <div class="layui-input-block layui-col-md4 input-block-box textarea-box">
                    <textarea name="record" placeholder="请输入说明内容，不超过500字" class="layui-textarea" maxlength="500" v-model="editFollowData.record"></textarea>
                    <span>{{editFollowData.record && editFollowData.record.length ? editFollowData.record.length : 0}}/500</span>
                </div>
            </div>
            <div class="layui-form-item layui-col-md12">
                <label class="layui-form-label row-label two-font-color">上传图片
                    <br>
                    <i>(每张图片小于2M)</i>
                    <br>
                    <i>{{editFollowData.images.length}}/9</i>
                </label>
                <div class="layui-upload layui-col-md9 input-block-box">
                    <blockquote class="layui-elem-quote layui-quote-nm">
                        <div class="layui-upload-list pull-left" id="follow-upload-list">
                            <section class="pull-left">
                                <a :href="item.image" class="uploadImges" v-if="item.thumb_image" v-for="(item, index) in editFollowData.images" :key="index">
                                    <img :src="item.thumb_image" alt="">
                                    <i class="iconfont icon-delete" @click.stop.prevent="delFollowImage(index, item.image, item.thumb_image)"></i>
                                </a>
                            </section>
                            <button type="button" v-show="editFollowData.images.length < 9" class="layui-btn upload-btn pull-left" id="follow-upload">
                                <i class="iconfont icon-add"></i>
                            </button>
                        </div>
                    </blockquote>
                </div>
            </div>
            <div class="layui-form-item layui-col-md12">
                <label class="layui-form-label row-label two-font-color">录音记录</label>
                <div class="layui-input-block layui-col-md9 input-block-box">
                    <div class="layui-table layui-col-md12 " v-if="editFollowData.voice_record && editFollowData.voice_record.length">
                        <table class="layui-col-md12">
                            <tbody class="layui-col-md12">
                                <tr class="layui-col-md12">
                                    <td class="layui-col-md2">姓名</td>
                                    <td class="layui-col-md2">电话</td>
                                    <td class="layui-col-md2">通话时长</td>
                                    <td class="layui-col-md2">通话时间</td>
                                    <td class="layui-col-md3">通话类型</td>
                                    <td class="layui-col-md1">操作</td>
                                </tr>
                                <tr class="layui-col-md12" v-for="(i,index) in editFollowData.voice_record">
                                    <td class="layui-col-md2">{{i.toPhoneName ? i.toPhoneName : '未知用户'}}</td>
                                    <td class="layui-col-md2">{{i.toPhone || '--'}}</td>
                                    <td class="layui-col-md2">{{i.calltime | VformatM}}</td>
                                    <td class="layui-col-md2">{{i.starttime || '--'}}</td>
                                    <td class="layui-col-md3">{{i.CallType == 1 ? '呼入' : '呼出'}}</td>
                                    <td class="layui-col-md1">
                                        <a href="javascript:;" @click.stop='delRecordItem(index)'>删除</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a class="layui-btn" id='addRecord' @click.stop="addRecord">添加录音记录</a>
                </div>
            </div>
        </div>
        <div class="btn-box">
            <button type="reset" class="layui-btn layui-btn-primary" @click="cancelFollow">取消</button>
            <button class="layui-btn follow-info-btn" lay-submit lay-filter="followBtn">确认</button>
        </div>
    </form>
</div>



<script type="text/html" id="CallRecordAllLists">
    <li>
        <div class="checkbox-box">
            <input type="checkbox" name="checkAllCall" lay-filter="checkAllCall" lay-skin="primary">
        </div>
        <div class="name li-item">姓名</div>
        <div class="call li-item">电话</div>
        <div class="call-time-len li-item">通话时长</div>
        <div class="call-time li-item">通话时间</div>
    </li>
    {%each list as value i%}
    <li>
        <div class="checkbox-box">
            <input type="checkbox" lay-filter="checkCallList" {%value.ischecked ? 'checked' : ''%}  value="{%value.tel_id%}" name="tellCall[{%i%}]" lay-skin="primary">
        </div>
        <div class="name li-item">{%value.toPhoneName ? value.toPhoneName : value.telerName%}</div>
        <div class="call li-item">{%value.CallType == 1 ? value.telerNo : value.toPhone%}</div>
        <div class="call-time-len li-item">{%value.calltime | formatM%}</div>
        <div class="call-time li-item">{%value.starttime%}</div>
    </li>
    {%/each%}
</script>

                    </div>
                    <?php endif; if(checkAuth('/admin/cooper/customercoopersituation/index') == 'true'): ?>
                    <!-- 合作情况 -->
                    <div class="layui-tab-item cooperation-situation basic-info">
                        <!--  查看合作情况 -->
<div class="examine-cooperation" :class="{'layui-hide':editCooperation}">
    <div class="cooperation-title">
        <em v-if="cooperationTable.total_pay_num"> 购买数量:
            <span>{{cooperationTable.total_pay_num}}</span>
        </em>
        <em v-if="cooperationTable.total_pay_money">
            购买金额:
            <span>{{cooperationTable.total_pay_money}}元</span>
        </em>
        <?php if(checkAuth('/admin/cooper/customercoopersituation/add') == 'true'): ?>
        <a href="javascript:;" @click.stop="addCoop">新增合作情况</a>
        <?php endif; ?>
    </div>
    <div class="cooperation-table" v-if="cooperationTable.dataProduct && cooperationTable.dataProduct.length">
        <div class="layui-table layui-col-md12">
            <ul>
                <li>
                    <span>产品名称</span>
                    <span>购买电话</span>
                    <span>购买时间</span>
                    <span>购买数量</span>
                    <span>付费金额（元）</span>
                    <span>销售人员</span>
                    <span>服务开始时间</span>
                    <span>服务结束时间</span>
                    <span>操作</span>
                </li>
                <li v-for="i in cooperationTable.dataProduct">
                    <span v-text="i.product_name || '--'">产品名称</span>
                    <span v-text="i.pay_mobile || '--'">购买电话</span>
                    <span v-text="i.pay_time ||'--'">购买时间</span>
                    <span v-text="i.pay_num || '--'">购买数量</span>
                    <span v-text="i.pay_money || '--'">付费金额（元）</span>
                    <span>{{i.employee_name || '--'}}
                        <i v-if="i.employee_name && i.department">({{i.employee_name ? i.department :''}})</i>
                    </span>
                    <span v-text="i.service_start_time || '--'">服务开始时间</span>
                    <span v-text="i.service_end_time || '--'">服务结束时间</span>
                    <span v-if="i.product_type == 2">
                        <?php if(checkAuth('/admin/cooper/customercoopersituation/updates') == 'true'): ?>
                        <a href="javascript:;" @click.stop="alterServeTime(i)">更改服务期限</a>
                        <?php endif; ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>
    <div class="no-data" v-if="!cooperationTable.dataProduct || !cooperationTable.dataProduct.length">
        <div class="no-data-img">
            <p>暂无数据</p>
        </div>
    </div>
</div>
<!-- 新增编辑合作情况 -->
<form class="add-cooperation layui-form" :class="editCooperation && !noData ? 'layui-show' :'layui-hide'">
    <div class="layui-collapse examine-basic-info">
        <div class="layui-colla-item basic-item" :class="cooper_situation_jjtag ? 'layui-show' : 'layui-hide'">
            <h2 class="layui-colla-title basic-item-title">合作产品</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class=''>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>产品名称</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="product_type" value="1" title="线上投顾计划" v-model="cooper_situation_base.product_type" lay-filter="product_type">
                            <!-- <input type="radio" name="product_type" value="2" title="线下投顾计划" v-model="cooper_situation_base.product_type" lay-filter="product_type"> -->
                        </div>
                    </div>
                    <div class="product_online" :class='cooper_situation_base.product_type == 1 && global_set.online_consulting_plan.length ? "layui-show" : "layui-hide"'>
                        <div class="layui-form-item layui-col-md12 cooper-product-box">
                            <div class="layui-input-inline input-block-box child-box" v-for="i in global_set.online_consulting_plan" style="width:auto">
                                <input type="radio" name="product_id" v-for="item in i.child" :value="item.sid" :title="item.sname" v-model='cooper_situation_base.product_id'
                                    :data-code="item.scode" lay-filter="product_id">
                            </div>
                        </div>
                    </div>
                    <div class="product_online" :class='cooper_situation_base.product_type == 1 && global_set.online_consulting_plan.length == 0 ? "layui-show" : "layui-hide"'>
                        <div class="layui-form-item layui-col-md12 cooper-product-box">
                            <div class="layui-input-inline input-block-box" :class='cooper_situation_base.product_type == 1 && global_set.online_consulting_plan.length == 0 ? "layui-show" :"layui-hide"'>
                                暂无任何产品,请重新选择
                            </div>
                        </div>
                    </div>
                   <!--  <div class="product_line" :class="cooper_situation_base.product_type == 2 && global_set.line_investment_plan.length ? 'layui-show' :'layui-hide'">
                        <div class="layui-form-item layui-col-md12 cooper-product-box">
                            <div class="layui-input-inline input-block-box child-box" style="width:auto">
                                <input type="radio" name="product_id" lay-skin="primary" :value="i" :title="i" lay-filter="product_id" v-for="(i,index) in global_set.line_investment_plan"
                                    v-model="cooper_situation_base.product_name">
                            </div>
                        </div>
                    </div> -->
                   <!--  <div class="product_line" :class="cooper_situation_base.product_type == 2 && global_set.line_investment_plan.length == 0 ? 'layui-show' :'layui-hide'">
                        <div class="layui-form-item layui-col-md12 cooper-product-box">
                            <div class="layui-input-inline input-block-box" :class='cooper_situation_base.product_type == 2 && !global_set.line_investment_plan.length ?"layui-show":"layui-hide"'>
                                暂无任何产品,请重新选择
                            </div>
                        </div>
                    </div> -->
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>支付方式</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="pay_type" value="1" title="线上" v-model="cooper_situation_base.pay_type" lay-filter="pay_type" :disabled="cooper_situation_base.product_type == 2">
                            <input type="radio" name="pay_type" value="2" title="线下" v-model="cooper_situation_base.pay_type" lay-filter="pay_type">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12 pay-record-box" v-show="cooper_situation_base.product_type == 1">
                        <label class="layui-form-label row-label">
                            <span>*</span>购买电话</label>
                        <div class="layui-input-block layui-col-md8 input-block-box">
                            <input type="radio" v-for="(mobile,index) in mobile" name="pay_mobile" :value="mobile" :title="mobile" v-model="cooper_situation_base.pay_mobile"
                                lay-filter='pay_mobile'>

                        </div>
                        <a href="javascript:;" @click.stop="payRecord" v-if="cooper_situation_base.product_type == 1 && cooper_situation_base.product_id && cooper_situation_base.pay_type == 1 && cooper_situation_base.pay_mobile">关联购买记录</a>
                            <div class="layui-form-item layui-col-md12" v-show="cooper_situation_base.pay_type != 2">
                                <label class="layui-form-label row-label">购买数量</label>
                                <div class="layui-input-block layui-col-md4 input-block-box">
                                    <i v-text="cooper_situation_base.pay_num || '--'"></i>
                                </div>
                            </div>
                    </div>
                    <div class="layui-form-item layui-col-md12" v-show="cooper_situation_base.pay_type != 2">
                        <div class="layui-input-block layui-col-md12 input-block-box product-box">
                            <span>
                                <i>服务期限 : {{cooper_situation_base.days || '--'}}
                                    <em v-if="cooper_situation_base.days">{{cooper_situation_base.is_trade_day == 1 ? '交易日' : '自然日'}}</em>
                                </i>
                                <i>商品价格 : {{cooper_situation_base.goods_price ? cooper_situation_base.goods_price + '元' :'--'
                                    }}
                                </i>
                            </span>
                        </div>
                    </div>
                    <!-- 财务 -->
                    <div v-show="cooper_situation_base.pay_type == 2">
                        <div class="layui-form-item layui-col-md12">
                            <label class="layui-form-label row-label">
                                <span>*</span>购买数量</label>
                            <div class="layui-input-block layui-col-md4 input-block-box">
                                <input type="text" name="pay_num" class="layui-input" placeholder="请输入购买数量" v-model="cooper_situation_base.pay_num" :lay-verify="cooper_situation_base.pay_type == 2 && cooper_situation_base.pay_num != '******' ? 'pay_num|number' : ''" :disabled="cooper_situation_base.pay_num == '******'">
                            </div>
                        </div>
                        <div class="layui-form-item layui-col-md12">
                            <label class="layui-form-label row-label">
                                <span>*</span>服务期限</label>
                            <div class="layui-input-block layui-col-md4 input-block-box">
                                <input type="radio" name="is_trade_day" value="1" title="交易日" v-model="cooper_situation_base.is_trade_day" lay-filter="is_trade_day">
                                <input type="radio" name="is_trade_day" value="2" title="自然日" v-model="cooper_situation_base.is_trade_day" lay-filter="is_trade_day">
                            </div>
                        </div>
                        <div class="layui-form-item layui-col-md12">
                            <label class="layui-form-label row-label"></label>
                            <div class="layui-input-block layui-col-md4 input-block-box">
                                <input type="text" name="days" class="layui-input" placeholder="请输入服务期限" v-model="cooper_situation_base.days" :lay-verify="cooper_situation_base.pay_type == 2 ? 'days|number' : ''">
                            </div>
                        </div>
                        <div class="layui-form-item layui-col-md12 item-unit">
                            <label class="layui-form-label row-label">
                                <span>*</span>商品价格</label>
                            <div class="layui-input-block layui-col-md4 input-block-box">
                                <input type="text" name="goods_price" placeholder="请输入购买价格" class="layui-input" v-model="cooper_situation_base.goods_price"
                                    :lay-verify="cooper_situation_base.pay_type == 2 ? 'goods_price|number' : ''">
                            </div>
                            <span class="unit">元</span>
                        </div>
                        <!-- 财务end -->
                    </div>
                </div>
            </div>
        </div>
        <!-- 查看模式 合作产品-->
        <div class="layui-colla-item basic-item" v-if="!cooper_situation_jjtag">
            <h2 class="layui-colla-title basic-item-title">合作产品</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class="layui-form-item layui-col-md12 examine-method">
                    <div class="layui-col-md3 layui-col-item">
                        <label>购买产品</label>
                        <span v-if="cooper_situation_base.product_type">{{cooper_situation_base.product_type == 1 ? '线上投顾计划' :'线下投顾计划'}}-{{cooper_situation_base.product_name}}</span>
                        <!-- <span>{{cooper_situation_base.product_type == 1 '线上投顾计划' :'线下投顾计划'}}</span> -->
                    </div>
                    <div class="layui-col-md3 layui-col-item" v-if="cooper_situation_base.pay_type">
                        <label>支付方式</label>
                        <span v-if="cooper_situation_base.pay_type != '******'">{{cooper_situation_base.pay_type == 2 ? '线下' : '线上'}}</span>
                        <span v-else>******</span>
                    </div>
                    <div class="layui-col-md3 layui-col-item" v-if="cooper_situation_base.pay_mobile">
                        <label>购买电话</label>
                        <span> {{cooper_situation_base.pay_mobile}}</span>
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12 examine-method">
                    <div class="layui-col-md3 layui-col-item">
                        <label>购买数量</label>
                        <span>{{cooper_situation_base.pay_num || '--'}}</span>
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12">
                    <div class="layui-input-block layui-col-md12 input-block-box product-box">
                        <span>
                            <i>服务期限 : {{cooper_situation_base.days || '--'}}
                                <em v-if="cooper_situation_base.days">{{cooper_situation_base.is_trade_day == 1 ? '交易日' : '自然日'}}</em>
                            </i>
                            <i>商品价格 : {{cooper_situation_base.goods_price ? cooper_situation_base.goods_price + '元' :'--' }}
                            </i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- 财务信息 -->
        <div class="layui-colla-item basic-item" v-show="cooper_situation_jjtag">
            <h2 class="layui-colla-title basic-item-title">财务信息</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <!-- 默认情况 -->
                <div class="" v-if="cooper_situation_base.pay_type != 2">
                    <div class="layui-col-md12 content-row">
                        <div class="layui-col-md2 layui-col-item">
                            <label>付费金额</label>
                            <span>{{cooper_situation_base.pay_money ? cooper_situation_base.pay_money +'元' : '--'}}</span>
                        </div>
                        <div class="layui-col-md4 layui-col-item">
                            <label>付费日期</label>
                            <span v-text="cooper_situation_base.pay_time || '--'"></span>
                        </div>
                    </div>
                </div>
                <!-- 默认情况 end -->
                <!-- 财务 -->
                <div v-show="cooper_situation_base.pay_type == 2">
                    <div class="layui-form-item layui-col-md12 product-tip">
                        <label class="layui-form-label row-label">
                            <span>*</span>付费金额</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="pay_money" placeholder="请输入交费金额" autocomplete="off" class="layui-input" v-model="cooper_situation_base.pay_money"
                                :lay-verify="cooper_situation_base.pay_type == 2 ? 'pay_money|number' : ''">
                        </div>
                        <span class="unit">元</span>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>付费日期</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <!-- <input type="text" name="pay_time" placeholder="请输入交费日期" class="layui-input" v-model="cooper_situation_base.pay_time" :lay-verify="cooper_situation_base.pay_type == 2 ? 'pay_time' : ''"> -->
                            <input type="text" name="pay_time" class="layui-input" id="product_pay_time" placeholder="yyyy-MM-dd HH:mm:ss" v-model="cooper_situation_base.pay_time">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>付费人</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="payer" placeholder="请输入付费人" class="layui-input" v-model="cooper_situation_base.payer" :lay-verify="cooper_situation_base.pay_type == 2 ? 'payer' : ''">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>付费方式</label>
                        <div class="layui-input-block layui-col-md8 input-block-box">
                            <input type="radio" name="pay_method" lay-filter="pay_method" value="1" title="银行转账" v-model="cooper_situation_base.pay_method">
                            <input type="radio" name="pay_method" lay-filter="pay_method" value="2" title="支付宝" v-model="cooper_situation_base.pay_method">
                            <input type="radio" name="pay_method" lay-filter="pay_method" value="3" title="微信" v-model="cooper_situation_base.pay_method">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12" v-if="cooper_situation_base.pay_method == 1">
                        <label class="layui-form-label row-label">
                            <span>*</span>付费银行</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="pay_bank" placeholder="请输入付费银行" class="layui-input" v-model="cooper_situation_base.pay_bank" :lay-verify="cooper_situation_base.pay_type == 2 ? 'pay_bank' : ''"
                                maxlength='100'>
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>付费账号</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="text" name="payment_account" placeholder="请输入付费账号" class="layui-input" v-model="cooper_situation_base.payment_account"
                                :lay-verify="cooper_situation_base.pay_type == 2 ? 'payment_account' : ''" maxlength='100'>
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>收款账号</label>
                        <div class="layui-input-block layui-col-md9 input-block-box" :class="global_set.collection_account && global_set.collection_account.length ? 'layui-show' :'layui-hide'">
                            <input type="radio" name="receive_bank" lay-filter="receive_bank" v-for="(account,index) in global_set.collection_account"
                                :value="account.name" :title="account.name" v-model="cooper_situation_base.receive_bank">
                        </div>
                        <div class="layui-input-block layui-col-md9 input-block-box" :class="!global_set.collection_account || !global_set.collection_account.length ? 'layui-show' :'layui-hide'">
                            无收款方可供选择，请联系管理员配置
                        </div>
                        <div class="layui-col-md12" :class="receive_account && receive_account.length ? 'layui-show' : 'layui-hide'">
                            <label class="layui-form-label row-label"></label>
                            <div class="layui-input-block layui-col-md9 input-block-box" id="removeLayui">
                                <input type="radio" name="receive_account" v-for="i in receive_account" :value="i" :title="i" v-model="cooper_situation_base.receive_account"
                                    lay-filter="receive_account">
                            </div>
                        </div>
                        <!-- <div class="layui-col-md12" :class="!cooper_situation_base.receive_bank || !cooper_situation_base.receive_bank.length ? 'layui-show' : 'layui-hide'">
                            暂无任何收款方可供选择，请联系管理员配置
                        </div> -->
                    </div>
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">付费凭证
                            <br>
                            <i>(最多上传9张)</i>
                        </label>
                        <div class="layui-upload layui-col-md9 input-block-box">
                            <blockquote class="layui-elem-quote layui-quote-nm">
                                <div class="layui-upload-list">
                                    <section id="financeBoxs" class="pull-left">
                                        <a :href="item.image" v-for="(item,index) in cooper_situation_base.payment_certificate" :key="index" class="uploadImges">
                                            <img :src="item.thumb_image" alt="">
                                            <i class="iconfont icon-delete" @click.stop.prevent="delPaymentImage(index, item.image, item.thumb_image)"></i>
                                        </a>
                                    </section>
                                    <button type="button" v-show="cooper_situation_base.payment_certificate.length < 9" class="layui-btn upload-btn pull-left"
                                        id="uploadVoucher">
                                        <i class="iconfont icon-add"></i>
                                    </button>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                </div>
                <!-- 财务 end -->
                <!-- 法务，审核查看 -->
                <div class="finance-info layui-hide">
                    <div class="layui-col-md12 content-row">
                        <div class="layui-col-md2 layui-col-item">
                            <label>付费金额</label>
                            <span v-text="cooper_situation_base.pay_money || '--'"></span>
                        </div>
                        <div class="layui-col-md4 layui-col-item">
                            <label>付费日期</label>
                            <span v-text="cooper_situation_base.pay_time || '--'"></span>
                        </div>
                        <div class="layui-col-md3 layui-col-item">
                            <label>付费人</label>
                            <span v-text="cooper_situation_base.payer || '--'"></span>
                        </div>
                    </div>
                    <div class="layui-col-md12 content-row">
                        <div class="layui-col-md2 layui-col-item">
                            <label>付费方式</label>
                            <span v-text="cooper_situation_base.pay_method || '--'"></span>
                        </div>
                        <div class="layui-col-md4 layui-col-item">
                            <label>付费银行</label>
                            <span v-text="cooper_situation_base.pay_bank || '--'"></span>
                        </div>
                        <div class="layui-col-md3 layui-col-item">
                            <label>付费账号</label>
                            <span v-text="cooper_situation_base.payment_account || '--'"></span>
                        </div>
                    </div>

                    <div class="layui-col-md12 content-row layui-hide">
                        <div class="layui-col-md12 layui-col-item">
                            <label>收款账号</label>
                            <span v-text="cooper_situation_base.receive_bank || '--'"></span>
                            <span v-text="cooper_situation_base.receive_account || '--'"></span>
                        </div>
                    </div>
                    <div class="layui-col-md12 content-row certificate layui-hide">
                        <div class="layui-col-md12 layui-col-item">
                            <label class="pull-left">付费凭证</label>
                            <div class="layui-input-block layui-col-md10" id="justiceboxs">
                                <a :href="i.image" v-for="i in cooper_situation_base.payment_certificate">
                                    <img :src="i.thumb_image" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 法务，审核查看 end-->
            </div>
        </div>
        <!-- 查看模式 财务-->
        <div class="layui-colla-item basic-item" v-if="!cooper_situation_jjtag">
            <h2 class="layui-colla-title basic-item-title">财务信息</h2>
            <div class="layui-colla-content basic-item-content examine-method layui-show">
                <div class="layui-form-item layui-col-md12">
                    <div class="layui-col-md3 layui-col-item">
                        <label>付费金额</label>
                        <span>{{cooper_situation_base.pay_money ? cooper_situation_base.pay_money +'元' : '--'}}</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item">
                        <label>付费日期</label>
                        <span v-text="cooper_situation_base.pay_time || '--'"></span>
                    </div>
                    <div class="layui-col-md3 layui-col-item" v-if="cooper_situation_base.payer">
                        <label>付费人</label>
                        <span v-text="cooper_situation_base.payer || '--'"></span>
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12" v-if="cooper_situation_base.pay_method && cooper_situation_base.pay_method !=0 ">
                    <div class="layui-col-md3 layui-col-item">
                        <label>付费方式</label>
                        <span v-if="cooper_situation_base.pay_method == 1">银行卡</span>
                        <span v-if="cooper_situation_base.pay_method == 2">支付宝</span>
                        <span v-if="cooper_situation_base.pay_method == 3">微信</span>
                        <span v-if="cooper_situation_base.pay_method == '******'">******</span>
                    </div>
                    <div class="layui-col-md4 layui-col-item" v-if="cooper_situation_base.pay_bank">
                        <label>付费银行</label>
                        <span v-text="cooper_situation_base.pay_bank"></span>
                    </div>
                    <div class="layui-col-md3 layui-col-item" v-if="cooper_situation_base.payment_account">
                        <label>付费账号</label>
                        <span v-text="cooper_situation_base.payment_account"></span>
                    </div>
                </div>
                <div class="layui-form-item layui-col-md12" v-if="cooper_situation_base.receive_bank && cooper_situation_base.receive_account">
                    <div class="layui-col-md12 layui-col-item">
                        <label>收款账号</label>
                        <span>{{cooper_situation_base.receive_bank}} {{cooper_situation_base.receive_account}}</span>
                    </div>

                </div>
                <div class="layui-form-item layui-col-md12" :class="cooper_situation_base.payment_certificate && cooper_situation_base.payment_certificate.length ? 'layui-show' : 'layui-hide'">
                    <label class="pull-left">付费凭证</label>
                    <div class="layui-upload layui-col-md10 input-block-box" style="margin-left:20px" v-if='cooper_situation_base.payment_certificate !="******"'>
                        <blockquote class="layui-elem-quote layui-quote-nm">
                            <div class="layui-upload-list">
                                <section id="examineImages">
                                    <a :href="item.image" v-for="(item,index) in cooper_situation_base.payment_certificate" :key="index" class="uploadImges">
                                        <img :src="item.thumb_image" alt="">
                                    </a>
                                </section>
                            </div>
                        </blockquote>
                    </div>
                    <div class="layui-upload layui-col-md10 input-block-box" style="margin-left:20px" v-else>******</div>
                </div>
            </div>
        </div>
        <!-- 合规信息 -->
        <div class="layui-colla-item basic-item" v-if="!examine">
            <h2 class="layui-colla-title basic-item-title">合规信息</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class="layui-clear">
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>合规类型</label>
                        <div class="layui-input-block layui-col-md3 input-block-box">
                            <!-- cooper_situation_base.product_type -->
                            <input type="radio" name="compliance_type" value="1" title="线上合规" lay-filter="compliance_type" v-model="cooper_situation_base.compliance_type">
                            <input type="radio" name="compliance_type" value="2" title="线下合规" lay-filter="compliance_type" v-model="cooper_situation_base.compliance_type">
                        </div>
                        <div class="layui-input-block compliance layui-col-md6 input-block-box" v-if="cooper_situation_base.compliance_type == 1">
                            <p class="layui-col-md3">合规状态 &nbsp;&nbsp;
                                <i>{{cooper_situation_base.compliance_status == 1 ? '已合规' : ' 未合规' }}</i>
                            </p>
                            <p class="layui-col-md3">风险评测&nbsp;&nbsp;
                                <i>{{cooper_situation_base.risk}}</i>
                            </p>
                            <p class="layui-col-md5">适当性评测 &nbsp;&nbsp;
                                <i>{{cooper_situation_base.adapt}}</i>
                            </p>
                        </div>
                    </div>
                    <!-- 线下合规 -->
                    <div v-if="cooper_situation_base.compliance_type == 2">
                        <div class="layui-form-item layui-col-md12">
                            <label class="layui-form-label row-label">
                                <span>*</span>合规状态</label>
                            <div class="layui-input-block layui-col-md3 input-block-box">
                                <input type="radio" name="compliance_status" value="1" title="已合规" lay-filter="compliance_status" v-model="cooper_situation_base.compliance_status">
                                <input type="radio" name="compliance_status" value="0" title="未合规" lay-filter="compliance_status" v-model="cooper_situation_base.compliance_status">
                            </div>
                        </div>
                        <div class="layui-form-item layui-col-md12">
                            <label class="layui-form-label row-label">
                                <span>*</span>风险评测</label>
                            <div class="layui-input-block layui-col-md8 input-block-box">
                                <input type="radio" name="risk" value="保守型" title="保守型" lay-filter="risk" v-model="cooper_situation_base.risk">
                                <input type="radio" name="risk" value="相对保守型" title="相对保守型" lay-filter="risk" v-model="cooper_situation_base.risk">
                                <input type="radio" name="risk" value="稳健型" title="稳健型" lay-filter="risk" v-model="cooper_situation_base.risk">
                                <input type="radio" name="risk" value="相对积极型" title="相对积极型" lay-filter="risk" v-model="cooper_situation_base.risk">
                                <input type="radio" name="risk" value="积极型" title="积极型" lay-filter="risk" v-model="cooper_situation_base.risk">
                            </div>
                        </div>
                        <div class="layui-form-item layui-col-md12">
                            <label class="layui-form-label row-label">
                                <span>*</span>适当性评测</label>
                            <div class="layui-input-block layui-col-md3 input-block-box">
                                <input type="radio" name="adapt" value="普通投资者" title="普通投资者" lay-filter="adapt" v-model="cooper_situation_base.adapt">
                                <input type="radio" name="adapt" value="专业投资者" title="专业投资者" lay-filter="adapt" v-model="cooper_situation_base.adapt">
                            </div>
                        </div>
                    </div>
                    <!--  默认 -->
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">
                            <span>*</span>合同类型</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <input type="radio" name="contract_type" value="1" title="电子合同" v-model="cooper_situation_base.contract_type" lay-filter="contract_type"
                                :disabled="cooper_situation_base.pay_type == 2">
                            <input type="radio" name="contract_type" value="2" title="纸质合同" v-model="cooper_situation_base.contract_type" lay-filter="contract_type">
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12" v-show="cooper_situation_base.compliance_type == 2">
                        <label class="layui-form-label row-label">
                            <span>*</span>附件
                            <br>
                            <i>(最多上传9份)</i>
                        </label>
                        <div class="layui-upload layui-col-md9 input-block-box">
                            <blockquote class="layui-elem-quote layui-quote-nm">
                                <div class="layui-upload-list" id="annexBox">
                                    <section id="annexImages" class="pull-left">
                                        <a :href="item.image" v-for="(item,index) in cooper_situation_base.attachment.image" :key="index" class="uploadImges">
                                            <img :src="item.thumb_image" alt="">
                                            <i class="iconfont icon-delete" @click.stop.prevent="delAnnexImage(index, item.image, item.thumb_image)"></i>
                                        </a>
                                    </section>
                                    <button type="button" v-show="cooper_situation_base.attachment.image.length + cooper_situation_base.attachment.voice.length < 9"
                                        class="layui-btn upload-btn pull-left" id="uploadAnnex">
                                        <i class="iconfont icon-add"></i>
                                    </button>
                                </div>

                            </blockquote>
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12" v-show="cooper_situation_base.compliance_type == 2">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-upload  input-block-inline">
                            <blockquote class="layui-elem-quote layui-quote-nm">
                                <div class="layui-upload-list">
                                    <section id="voiceBox" class="pull-left">
                                        <p class="pull-left" v-for="(item,index) in cooper_situation_base.attachment.voice">
                                            <i @click="playFollow(item.video,item.title)"></i>
                                            <span class="voice" @click="playFollow(item.video,item.videoname)">{{item.videoname}}</span>
                                            <span class="del-vioce" @click="delVoice(index,item.video)">删除</span>
                                        </p>
                                    </section>
                                    <button type="button" v-show="cooper_situation_base.attachment.image.length + cooper_situation_base.attachment.voice.length < 9"
                                        class="layui-btn pull-left" id="voiceBtn">添加语音
                                    </button>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                    <!-- 默认 end-->
                    <!-- 财务 法务 审核查看-->
                    <div class="layui-form-item layui-col-md12 layui-hide">
                        <label class="layui-form-label row-label">合同类型</label>
                        <div class="layui-input-block compliance layui-col-md4 input-block-box">
                            <p class="layui-col-md4">
                                <i>纸质合同</i>
                            </p>
                        </div>
                    </div>
                    <!-- 财务end -->
                </div>
            </div>
        </div>
        <!-- 查看 合规-->
        <div class="layui-colla-item basic-item" v-if="examine">
            <h2 class="layui-colla-title basic-item-title">合规信息</h2>
            <div class="layui-colla-content basic-item-content layui-show">
                <div class="layui-clear">
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">合规类型</label>
                        <div class="layui-input-block layui-col-md2 input-block-box">
                            <span>{{cooper_situation_base.compliance_type == 1 ? '线上合规' : '线下合规'}}</span>
                        </div>
                        <div class="layui-input-block compliance layui-col-md6 input-block-box">
                            <p class="layui-col-md3">合规状态 &nbsp;&nbsp;
                                <i>{{cooper_situation_base.compliance_status == 1 ? '已合规' : '未合规'}}</i>
                            </p>
                            <p class="layui-col-md3" v-if="cooper_situation_base.risk">风险评测&nbsp;&nbsp;
                                <i>{{cooper_situation_base.risk || '--'}}</i>

                            </p>
                            <p class="layui-col-md3" v-if="cooper_situation_base.adapt">适当性评测 &nbsp;&nbsp;
                                <i>{{cooper_situation_base.adapt || '--'}}</i>
                            </p>
                        </div>
                    </div>
                    <!-- 默认 end-->
                    <!-- 财务 法务 审核查看-->
                    <div class="layui-form-item layui-col-md12">
                        <label class="layui-form-label row-label">合同类型</label>
                        <div class="layui-input-block layui-col-md4 input-block-box">
                            <p class="layui-col-md4">
                                <i>{{cooper_situation_base.contract_type == 1 ? '电子合同' : '纸质合同'}}</i>
                            </p>
                        </div>
                    </div>
                    <!-- 财务end -->
                    <div class="layui-form-item layui-col-md12" v-show="cooper_situation_base.compliance_type == 2">
                        <label class="layui-form-label row-label">
                            <span>*</span>附件
                            <br>
                            <i>(最多上传9份)</i>
                        </label>
                        <div class="layui-upload layui-col-md9 input-block-box">
                            <blockquote class="layui-elem-quote layui-quote-nm">
                                <div class="layui-upload-list" id="annexBox">
                                    <section id="annexImages">
                                        <a :href="item.image" v-for="(item,index) in cooper_situation_base.attachment.image" :key="index" class="uploadImges">
                                            <img :src="item.thumb_image" alt="">
                                        </a>
                                    </section>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                    <div class="layui-form-item layui-col-md12" v-show="cooper_situation_base.compliance_type == 2">
                        <label class="layui-form-label row-label"></label>
                        <div class="layui-upload  input-block-inline">
                            <blockquote class="layui-elem-quote layui-quote-nm">
                                <div class="layui-upload-list">
                                    <section id="voiceBox">
                                        <p class="pull-left" v-for="(item,index) in cooper_situation_base.attachment.voice">
                                            <i @click="playFollow(item.video,item.title)"></i>
                                            <span class="voice" @click="playFollow(item.video,item.videoname)">{{item.videoname}}</span>
                                        </p>
                                    </section>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 销售过程 -->
        <div class="layui-colla-item basic-item">
            <h2 class="layui-colla-title basic-item-title">销售过程</h2>
            <div class="layui-colla-content sales-process layui-clear layui-show">
                <div class="layui-row layui-col-md12 sales-process-item" v-for="(item,index) in sp_saleprocess" v-show="item.title">
                    <h2>{{item.title}}
                        <div>
                            <a href="javascript:;" @click.stop="addSaleprocess(index)" v-if="!examine">新增销售过程</a>
                            <a href="javascript:;" @click.stop="associateTrack(index)" v-if="!examine">关联跟进信息</a>
                        </div>
                    </h2>
                    <!-- 临时展示数据 start -->
                    <div class="layui-row-content sales-item-content layui-clear" v-for="(product,i) in item.info">
                        <div class="content-item content-title layui-col-md12" v-if="product.employee_nickname || product.employee_department || product.product_info && product.product_info.length">
                            <strong v-if="product.employee_nickname">{{product.employee_nickname}}</strong>
                            <span>{{product.employee_department}}</span>
                            <a href="javascript:;" class="pull-right" v-if="product.product_info &&　product.product_info.length" v-for="product_name in product.product_info">{{product_name.product_name}}</a>
                        </div>
                        <div class="layui-col-md12 content-item">
                            {{product.text_record}}
                        </div>
                        <div class="layui-col-md12 content-item product-iamges-box" v-if=" product.images &&　 product.images.length" :id='"productIamges_"+index+i'>
                            <a class="img-box" :href="img.image" v-for="img in product.images">
                                <img :src="img.thumb_image" alt="" v-if="img.thumb_image">
                            </a>
                        </div>
                        <div class="info-item layui-table layui-col-md12" :class="product.voice_records && product.voice_records.length ? 'layui-show' :'layui-hide'">
                            <table class="layui-col-md12">
                                <tbody class="layui-col-md12">
                                    <tr class="layui-col-md12">
                                        <td class="layui-col-md2">姓名</td>
                                        <td class="layui-col-md2">电话</td>
                                        <td class="layui-col-md2">通话时长</td>
                                        <td class="layui-col-md2">通话时间</td>
                                        <td class="layui-col-md2">通话类型</td>
                                        <td class="layui-col-md2">操作</td>
                                    </tr>
                                    <tr class="layui-col-md12" v-for="voice in product.voice_records">
                                        <td class="layui-col-md2">{{voice.customer_real_name || '未知用户'}}</td>
                                        <td class="layui-col-md2">{{voice.customer_mobile || '--'}}</td>
                                        <td class="layui-col-md2">{{voice.talk_time | VformatM}}</td>
                                        <td class="layui-col-md2">{{voice.call_time || '--'}}</td>
                                        <td class="layui-col-md2">{{voice.call_type == 1 ? '呼入' :'呼出'}}</td>
                                        <td class="layui-col-md2">
                                            <a v-if="voice.recfile" @click="playFollow(voice.recfile,voice.customer_real_name,voice.call_time)" href="javascript:;">播放录音</a>
                                            <span v-else>暂无录音播放</span>
                                            <a v-if="voice.recfile" :href="voice.recfile ? voice.recfile : 'javascript:;'" download="xxx">下载录音</a>
                                            <span v-else>暂无录音下载</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="info-item layui-item-time layui-col-md12 del-box" v-if="product.text_record && product.text_record.length || product.voice_records && product.voice_records.length || product.images && product.images.length">
                            <span>{{product.create_time}}</span>
                            <a class="del" href="javascript:;" @click.stop='delProductItem(index,i)' v-if="!examine">删除</a>
                        </div>
                    </div>
                    <!-- // 临时展示数据 end -->

                    <!-- 编辑 start -->
                    <div class="layui-row-content layui-clear sales-item-content new-item-content layui-hide">
                        <div class="layui-form-item layui-col-md12">
                            <div class="layui-input-block layui-col-md12 textarea-box">
                                <textarea name="desc" v-model="item.userdesc" placeholder="请输入说明内容，不超过500字" class="layui-textarea" maxlength="500"></textarea>
                                <span>{{item.userdesc ? item.userdesc.length : '0'}}/500</span>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-form-item layui-col-md12">
                                <div class="layui-upload layui-col-md12">
                                    <blockquote class="layui-elem-quote layui-quote-nm">
                                        <div class="layui-upload-list">
                                            <section :id='"saleimgboxs_"+index' class="pull-left">
                                                <a :href="imgs.image" class="uploadImges" v-for="(imgs,imgsi) in item.userimages">
                                                    <img :src="imgs.thumb_image ? imgs.thumb_image : ''" alt="">
                                                    <i class="iconfont icon-delete" @click.stop.prevent="delSalesImage(index, imgsi, imgs.image, imgs.thumb_image)"></i>
                                                </a>
                                            </section>
                                            <button type="button" class="layui-btn upload-btn pull-left" :id='"salesImgUpload_"+index' v-show="item.userimages.length < 9">
                                                <i class="iconfont icon-add"></i>
                                            </button>
                                        </div>
                                    </blockquote>
                                </div>
                                <div class="content-item layui-col-md12">
                                    <span>{{item.userimages.length}}/9</span> 每张图片请小于2M
                                </div>
                            </div>
                            <div class="content-item layui-col-md12 content-btn-box">
                                <a class="layui-btn layui-btn-primary" @click.stop="cancelProductItem(index)">取消</a>
                                <a class="layui-btn" @click.stop="addProductItem(index)">确认</a>
                            </div>
                        </div>
                    </div>
                    <!-- //编辑 end -->
                </div>
                <div class="layui-row layui-col-md12 sales-process-item">
                    <h2>备注
                    </h2>
                    <div class="layui-row-content layui-clear sales-item-content new-item-content" v-if="!examine || cooper_situation_base.remark">
                        <div class="layui-col-md12 content-item" v-if="cooper_situation_base.remark">
                            {{cooper_situation_base.remark}}
                        </div>
                        <div v-if="!examine">
                            <div class="layui-form-item layui-col-md12">
                                <div class="layui-input-block layui-col-md12 textarea-box">
                                    <textarea name="remark" placeholder="请输入说明内容，不超过500字" class="layui-textarea remark" maxlength="500" v-model="remarkWord"></textarea>
                                    <span class="remark-numlen">{{remarkWord.length || 0}}/500</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="layui-form-item btn-box" v-if='!examine'>
        <a class="layui-btn layui-btn-primary" @click="cooprationCancel">取消</a>
        <button class="layui-btn" id="cooperation-btn" lay-submit lay-filter="cooprationBtn">提交</button>
        <!-- " -->
    </div>
    <div class="layui-form-item btn-box" v-if='examine'>
        <a class="layui-btn back-btn" @click="cooprationCancel">返回</a>
    </div>

</form>
<div class="no-data" v-if="noData">
    <div class="no-data-img">
        <p>暂无数据</p>
    </div>
</div>
<script id="track" type="text/html">
    <li>
        <div class="checkbox-box">
            <input type="checkbox" name="checkAllCall" lay-filter="checkAllCall" lay-skin="primary">
        </div>
        <div class="li-item follow-name">跟进产品</div>
        <div class="li-item follow-time">跟进时间</div>
        <div class="li-item word">文字</div>
        <div class="li-item record">包含语音</div>
        <div class="li-item img">包含图片</div>
        <div class="li-item operator">操作人</div>
    </li>
    {% each list as value i %}
    <li>
        <div class="checkbox-box">
            {%if value.ischecked%}
            <input type="checkbox" checked name="checkItem" lay-filter="checkItem" lay-skin="primary" data-id="{% value.followup_id%}"> {%else%}
            <input type="checkbox" name="checkItem" lay-filter="checkItem" lay-skin="primary" data-id="{% value.followup_id%}"> {%/if%}
        </div>
        <div class="li-item layui-elip follow-name">
            {% if value.product_info && value.product_info.length %} {% each value.product_info as name %}
            <a href="javascript:;">{% name.product_name || '--' %}</a>
            {% /each %} {% else %}
            <a href="javascript:;">--</a>
            {% /if %}
        </div>
        <div class="li-item layui-elip follow-time">{% value.create_time || '--' %}</div>
        <div class="li-item layui-elip word">{% value.text_record || '--' %}</div>
        <div class="li-item layui-elip record">{% value.voice_records && value.voice_records.length ? '是':'否' %}</div>
        <div class="li-item layui-elip img">{% value.images && value.images .length ? '是':'否' %}</div>
        <div class="li-item layui-elip operator">{% value.employee_nickname || '--' %}</div>
    </li>
    {% /each %}
</script>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/clientInfo.js"></script>
</body>
</html>