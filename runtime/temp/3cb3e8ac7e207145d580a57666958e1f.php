<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:101:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\customer\customer\index.html";i:1507797199;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_top.html";i:1507701392;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\header_bottom.html";i:1507700164;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\bread_nav.html";i:1507706882;s:95:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_top.html";i:1507700164;s:98:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/admin\view\common\footer_bottom.html";i:1507700164;}*/ ?>
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
<link rel="stylesheet" href="__ADMINCSS__/page/my-client.css"/>
</head>
<body>

<div class="container my-client-container">
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
    <div id="app" v-cloak>
        <!--侧栏-->
        <div class="sidebar-collapse">
            <h3>我的客户 <i class="iconfont icon-add"></i></h3>
            <ul class="nav" id="side-menu">
                <li v-for="(i,index) in list" v-cloak>
                    <a href="javascript:;" :class="i.child ? '':'J_menuItem'">
                        <span class="nav-label" v-cloak>{{i.name}}({{i.num}})</span>
                        <span class="iconfont icon-right" v-if="i.child"></span>
                    </a>
                    <ul class="nav nav-second-level" v-if="i.child">
                        <li v-for="item in i.child">
                            <a href="javascript:;" :class="item.child ? '':'J_menuItem'">
                                <span class="nav-label last-nav-label" v-cloak>{{item.name}}({{item.num}})</span>
                                <span class="iconfont icon-right" v-if="item.child"></span>
                            </a>
                            <ul class="nav nav-third-level" v-if="item.child">
                                <li v-for="j in item.child">
                                    <a href="javascript:;" class="J_menuItem">
                                        <span class="nav-label" v-cloak>{{j.name}}({{j.num}})</span>
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
            <div class="layui-row search-btn-container">
                <div class="main">
                    <div class="examine-search layui-col-md4">
                        <div class="search-parent">
                            <input class="search-inp" placeholder="请输姓名或手机号"/>
                            <i class="iconfont icon-search search-icon"></i>
                        </div>
                    </div>
                    <div class="example-handle layui-col-md8">
                        <a class="examine-operate" href='/admin/customer/customer/add' data-type="tab">新增客户</a>
                        <a class="examine-operate" @click="importUsr">导入客户</a>
                        <a class="examine-operate">导出客户</a>
                        <a class="examine-operate" href="/page/client-manage/remark-manage.html"
                           data-type="tab">备注管理</a>
                        <a class="examine-operate" href="/page/client-manage/tags-manage.html" data-type="tab">标签管理</a>
                    </div>
                </div>
            </div>
            <!--// 搜索/按钮组-->

            <div class="main">
                <!--筛选条件-->
                <div class="layui-row">
                    <div class="examine-tag">
                        <span class="tag-title">备注：</span>
                        <ul>
                            <li><a href="javascript:;" class="tag-a">不限</a></li>
                            <li><a href="javascript:;" class="tag-a tag-active">老客户</a></li>
                            <li><a href="javascript:;" class="tag-a">有意向</a></li>
                            <li><a href="javascript:;" class="tag-a">可以关单</a></li>
                            <li><a href="javascript:;" class="tag-a">重点关注</a></li>
                            <li><a href="javascript:;" class="tag-a">今日待联系</a></li>
                            <li><a href="javascript:;" class="tag-a">信息错误</a></li>
                        </ul>
                    </div>
                    <div class="examine-tag">
                        <span class="tag-title">标签：</span>
                        <ul>
                            <li><a href="javascript:;" class="tag-a">不限</a></li>
                            <li><a href="javascript:;" class="tag-a tag-active">暂未联系时间</a></li>
                            <li><a href="javascript:;" class="tag-a">签单中</a></li>
                            <li><a href="javascript:;" class="tag-a">投诉中</a></li>
                            <li><a href="javascript:;" class="tag-a">7天未联系</a></li>
                            <li><a href="javascript:;" class="tag-a">退款中</a></li>
                            <li><a href="javascript:;" class="tag-a">30天未联系</a></li>
                            <li><a href="javascript:;" class="tag-a">签单中</a></li>
                        </ul>
                    </div>
                    <div class="examine-select">
                        <span class="tag-title">筛选：</span>
                        <div class="examine-filter">
                            <a class="filter-a">创建时间<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display:none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input">
                                    -
                                    <input type="text" class="layui-input">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter">
                            <a class="filter-a">联系时间<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display:none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input">
                                    -
                                    <input type="text" class="layui-input">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter">
                            <a class="filter-a">成交时间<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display:none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input">
                                    -
                                    <input type="text" class="layui-input">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter active">
                            <a class="filter-a">加入时间2017-09-26到2017-10-10<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display: none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input" id="test1">
                                    -
                                    <input type="text" class="layui-input" id="test1">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter">
                            <a class="filter-a">未联系时间<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display:none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input">
                                    -
                                    <input type="text" class="layui-input">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter">
                            <a class="filter-a">更新动态时间<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display:none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input">
                                    -
                                    <input type="text" class="layui-input">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter">
                            <a class="filter-a">服务到期时间<i class="iconfont icon-down-arrow"></i></a>
                            <div class="filter-time" style="display:none">
                                <div class="choose-icon">
                                    <a href="">不限</a>
                                    <a href="">今天</a>
                                    <a href="">昨天</a>
                                    <a href="">最近七天</a>
                                    <a href="">最近30天</a>
                                    <a href="" class="active">自定义</a>
                                </div>

                                <div class="layui-inline"> <!-- 注意：这一层元素并不是必须的 -->
                                    <input type="text" class="layui-input">
                                    -
                                    <input type="text" class="layui-input">
                                </div>
                                <button>确定</button>
                            </div>
                        </div>
                        <div class="examine-filter active">
                            <a class="filter-a">组织架构<i class="iconfont icon-down-arrow"></i></a>
                            <div class="organize-framework" style="display: none;">
                                <h2 class="organize-nav">组织架构<a href=""></a></h2>
                                <div class="organize-content">
                                    <div class="customer-group">
                                        <input type="" name="" placeholder="请输入用户姓名、账号">
                                        <ul class="layui-nav layui-nav-tree" lay-filter="test">
                                            <!-- 侧边导航: <ul class="layui-nav layui-nav-tree layui-nav-side"> -->
                                            <li class="layui-nav-item layui-nav-itemed">
                                                <a href="javascript:;"><span
                                                      class="layui-nav-more active"></span>默认展开</a>
                                                <dl class="layui-nav-child">
                                                    <dd class="active-child">
                                                        <a href="javascript:;"><span
                                                              class="layui-nav-more active"></span>默认展开</a>
                                                        <dl class="layui-nav-child">
                                                            <dd><a href="javascript:;">选项1</a></dd>
                                                            <dd><a href="javascript:;">选项2</a></dd>
                                                        </dl>
                                                    </dd>
                                                    <dd><a href="javascript:;">选项2</a></dd>
                                                </dl>
                                            </li>
                                            <li class="layui-nav-item">
                                                <a href="javascript:;"><span class="layui-nav-more"></span>解决方案</a>
                                                <dl class="layui-nav-child">
                                                    <dd><a href="">移动模块</a></dd>
                                                    <dd><a href="">后台模版</a></dd>
                                                    <dd><a href="">电商平台</a></dd>
                                                </dl>
                                            </li>
                                            <li class="layui-nav-item"><a href=""><span class="layui-nav-more"></span>产品</a>
                                            </li>
                                            <li class="layui-nav-item"><a href=""><span class="layui-nav-more"></span>大数据</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="choose-people">
                                        <h2>已选客户：4</h2>
                                        <ul>
                                            <li>张三 <i></i></li>
                                            <li>李四 <i></i></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="organize-bth">
                                    <button class="cancel">取消</button>
                                    <button class="ensure">确定</button>
                                </div>
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
                                    <input type="checkbox" value="None" id="_check_all" name="check"/>
                                    <label for="_check_all"></label>
                                </div>
                                <span>全选</span>
                            </li>
                            <li><a href="javascript:;" @click="addRemark">添加备注</a></li>
                            <li><a href="javascript:;" @click="delRemark">删除备注</a></li>
                            <li><a href="javascript:;" @click="moveGroup">移动分组</a></li>
                            <li><a href="javascript:;">共享客户</a></li>
                            <li><a href="javascript:;" @click="removePublic">移入公海</a></li>
                            <li><a href="javascript:;">转移客户</a></li>
                            <li><a href="javascript:;" @click="delUser">删除客户</a></li>
                            <li class="phone-helper">
                                <div class="squaredFour">
                                    <input type="checkbox" value="None" id="_phone_helper" name="check" checked/>
                                    <label for="_phone_helper"></label>
                                </div>
                                <span>电话助手</span>
                            </li>
                            <li class="clear-custom"><a href="javascript:;" @click="clearUsr">客户清理</a></li>
                        </ul>
                    </div>
                    <div class="table-container">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="m-table">
                            <thead>
                            <tr>
                                <th><a href="javascript:;">客户(465)</a></th>
                                <th><a href="javascript:;">来源</a></th>
                                <th class="recent-contact"><a href="javascript:;">最近联系 <i></i></a></th>
                                <th class="recent-news"><a href="javascript:;" class="asc">最近动态 <i></i></a></th>
                                <th><a href="javascript:;">最近成交</a></th>
                                <th><a href="javascript:;">所买商品</a></th>
                                <th><a href="javascript:;">拨号</a></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="usr-name">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" id="_check_item_0" name="check"/>
                                        <label for="_check_item_0"></label>
                                    </div>
                                    <a href="javscript:;">
                                        <img src="/assets/images/avatar.png">股客张三
                                    </a>
                                </td>
                                <td>微信推广</td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三 添加了销售记录</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>主升传奇..</span>
                                </td>
                                <td class="call-it">
                                    <a href="javascript:;"></a>
                                </td>
                            </tr>
                            <tr>
                                <td class="usr-name">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" id="_check_item_0" name="check"/>
                                        <label for="_check_item_0"></label>
                                    </div>
                                    <a href="javscript:;">
                                        <img src="/assets/images/avatar.png">股客张三
                                    </a>
                                </td>
                                <td>微信推广</td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三 添加了销售记录</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>主升传奇..</span>
                                </td>
                                <td class="call-it">
                                    <a href="javascript:;"></a>
                                </td>
                            </tr>
                            <tr>
                                <td class="usr-name">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" id="_check_item_0" name="check"/>
                                        <label for="_check_item_0"></label>
                                    </div>
                                    <a href="javscript:;">
                                        <img src="/assets/images/avatar.png">股客张三
                                    </a>
                                </td>
                                <td>微信推广</td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三 添加了销售记录</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>主升传奇..</span>
                                </td>
                                <td class="call-it">
                                    <a href="javascript:;"></a>
                                </td>
                            </tr>
                            <tr>
                                <td class="usr-name">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" id="_check_item_0" name="check"/>
                                        <label for="_check_item_0"></label>
                                    </div>
                                    <a href="javscript:;">
                                        <img src="/assets/images/avatar.png">股客张三
                                    </a>
                                </td>
                                <td>微信推广</td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三 添加了销售记录</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>主升传奇..</span>
                                </td>
                                <td class="call-it">
                                    <a href="javascript:;"></a>
                                </td>
                            </tr>
                            <tr>
                                <td class="usr-name">
                                    <div class="squaredFour">
                                        <input type="checkbox" value="None" id="_check_item_0" name="check"/>
                                        <label for="_check_item_0"></label>
                                    </div>
                                    <a href="javscript:;">
                                        <img src="/assets/images/avatar.png">股客张三
                                    </a>
                                </td>
                                <td>微信推广</td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>2017-03-01 15：16</span><br>
                                    <span>股客张三 添加了销售记录</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>股客张三（市场五部一组）</span>
                                </td>
                                <td>
                                    <span>涨停风云</span><br>
                                    <span>主升传奇..</span>
                                </td>
                                <td class="call-it">
                                    <a href="javascript:;"></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="t-footer clearfix">
                        <ul class="show-num pull-left clearfix">
                            <li>每页展示用户</li>
                            <li class="cur"><a>1000</a></li>
                            <li><a>500</a></li>
                            <li><a>300</a></li>
                        </ul>
                        <!--分页-->
                        <div class="page-container pull-right ">共有 99 条记录</div>
                    </div>

                    <!--暂无数据-->
                    <div class="no-result" style="display: none">
                        <p>暂无数据</p>
                    </div>
                    <!--// 暂无数据-->
                </div>
                <!--// 表格-->
            </div>
        </div>
        <!--// 正文-->
    </div>
</div>

<script type="text/javascript" src="__ADMINJS__/libs/require/require.min.js"></script>
<script type="text/javascript" src="__ADMINJS__/require.config.js"></script>
<script src="__ADMINJS__/app/myClient.js"></script>
</body>
</html>