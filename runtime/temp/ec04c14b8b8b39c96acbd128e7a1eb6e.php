<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:89:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/index\view\index\index.html";i:1505111540;s:94:"F:\F-Project\0-Company\CRM\gxscrm\trunk\public/../application/index\view\layout\bootstrap.html";i:1505111540;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
        <title>FastAdmin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="基于ThinkPHP5和Bootstrap的极速后台开发框架">
        <meta name="renderer" content="webkit">
        <link rel="shortcut icon" href="__CDN__/assets/img/favicon.ico" />
        <!-- Loading Bootstrap -->
        <link href="__CDN__/assets/css/frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="__CDN__/assets/js/html5shiv.js"></script>
          <script src="__CDN__/assets/js/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript">
            var require = {
                config: <?php echo json_encode($config ); ?>
            };
        </script>
        <style>
            html{height:100%;overflow:auto;-webkit-overflow-scrolling: touch;}
            body{padding:70px 0 0 0;}
            footer {margin-top:20px;font-size:14px;padding:25px 0;background:#f4f6f6;}
            .footer-link ul li a{margin-top:10px;line-height: 25px;}
            #component-list .caption p {font-size:13px;line-height:20px;}
        </style>

        <script>
            var _hmt = _hmt || [];
            (function () {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?58347d769d009bcf6074e9a0ab7ba05e";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>
    </head>
    <body>
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-main">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo url('/'); ?>" class="navbar-brand">FastAdmin</a>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse-main">
                    <ul class="nav navbar-nav">
                        <li><a href="http://www.fastadmin.net">官网</a></li>
                        <li><a href="http://doc.fastadmin.net">文档</a></li>
                        <li><a href="http://forum.fastadmin.net">论坛</a></li>
                        <li class="divider"></li>
                        <li><a href="http://www.fastadmin.net#donate">打赏</a></li>
                        <li><a href="http://demo.fastadmin.net">演示</a></li>
                    </ul>
                    <form class="navbar-form navbar-left" action="http://forum.fastadmin.net">
                        <input name="q" type="text" class="form-control col-lg-8" placeholder="搜索">
                    </form>
                    <ul class="nav navbar-nav pull-right">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">你好!<?php echo $user->id?$user->nickname:'游客'; ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a id="admin" href="<?php echo url('admin/index/index'); ?>"><span class="text text-danger">后台演示</span></a></li>
                                <li><a id="home" href="http://www.fastadmin.net?ref=demo"><span class="text text-info">返回官网</span></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="container">
    <div class="row">
        <div class="col-xs-6">

            <a href="<?php echo url('admin/index/login'); ?>" class="btn btn-block btn-lg btn-success">FastAdmin后台演示</a>
        </div>
        <div class="col-xs-6">
            <a href="http://git.oschina.net/karson/fastadmin?ref=fastadmin" target="_blank" class="btn btn-block btn-lg btn-info">FastAdmin源码下载</a>
        </div>
    </div>
    <div class="page-header"><h3>组件</h3></div>

    <div class="row" id="component-list">
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <a href="<?php echo url('index/user/login'); ?>"><img style="height: 200px; width: 100%; display: block;" src="__CDN__/assets/img/third.jpg" data-holder-rendered="true"></a>
                <div class="caption">
                    <h3>第三方登录</h3>
                    <p>FastAdmin中自带第三方登录扩展组件,包括微博登录、QQ登录、微信登录,可极速进行第三方登录的融合,同时系统中已提供注册登录的代码</p>
                    <p><a href="<?php echo url('index/user/login'); ?>" class="btn btn-primary" role="button">预览</a> <a href="http://git.oschina.net/karson/fastadmin" class="btn btn-default" role="button">下载</a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <a href="<?php echo url('index/user/login'); ?>"><img style="height: 200px; width: 100%; display: block;" src="__CDN__/assets/img/ucenter.jpg" data-holder-rendered="true"></a>
                <div class="caption">
                    <h3>Ucenter整合登录</h3>
                    <p>FastAdmin中基于Ucenter融合一套账号同步登录注册退出的方案,同时提供修改版Ucenter和Discuz论坛代码下载,同时适应于PHP7,此版本改动较大,建议适用于新系统的开发</p>
                    <p><a href="<?php echo url('index/user/login'); ?>" class="btn btn-primary" role="button">预览</a> <a href="http://git.oschina.net/karson/fastadmin" class="btn btn-default" role="button">下载</a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <a href="<?php echo url('index/demo/qrcode'); ?>"><img style="height: 200px; width: 100%; display: block;" src="__CDN__/assets/img/qrcode.jpg" data-holder-rendered="true"></a>
                <div class="caption">
                    <h3>二维码生成</h3>
                    <p>基于第三方组件增加了一个二维码的生成示例,可快速的调整文字、标签、Logo的大小、颜色、字体等等。此外还可对标签位置、容错率等进行相关设置。</p>
                    <p><a href="<?php echo url('index/demo/qrcode'); ?>" class="btn btn-primary" role="button">预览</a> <a href="http://git.oschina.net/karson/fastadmin" class="btn btn-default" role="button">下载</a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <a href="<?php echo url('index/demo/bootstrap'); ?>"><img style="height: 200px; width: 100%; display: block;" src="__CDN__/assets/img/bootstrap.jpg" data-holder-rendered="true"></a>
                <div class="caption">
                    <h3>Bootstrap组件</h3>
                    <p>由于FastAdmin对Bootstrap进行了大量的二次开发,对Bootstrap的组件样式有许多更改,你可以直接在这里预览到Bootstrap的部分组件</p>
                    <p><a href="<?php echo url('index/demo/bootstrap'); ?>" class="btn btn-primary" role="button">预览</a> <a href="http://git.oschina.net/karson/fastadmin" class="btn btn-default" role="button">下载</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="footer-link">
                            <ul class="list-inline">
                                <li><a href="http://www.fastadmin.net">官方网站</a></li>
                                <li><a href="http://doc.fastadmin.net">帮助文档</a></li>
                                <li><a href="https://git.oschina.net/karson/fastadmin">Git@OSC</a></li>
                                <li><a href="http://www.fastadmin.net/#donate" class="">捐赠打赏</a></li>
                                <li><a href="http://forum.fastadmin.net" class="">交流社区</a></li>
                                <li><a href="http://demo.fastadmin.net/" class="">在线演示</a></li>
                            </ul>
                        </div>

                        <div class="pull-left">
                            <p>
                                <span class="text-muted"><a href="http://www.miitbeian.gov.cn/" target="_blank"><?php echo $site['beian']; ?></a></span>
                            </p>
                            <span class="small"></span>
                            <p>
                                Copyright © fastadmin.net 2017-2018 All rights reserved
                            </p>
                        </div>
                        <div class="pull-right hidden-xs hidden-sm">

                        </div>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        </footer>

        <script src="__CDN__/assets/js/require.js" data-main="__CDN__/assets/js/require-frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>