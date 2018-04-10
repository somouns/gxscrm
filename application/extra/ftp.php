<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-21
 * Time: 17:13
 */
return [
    'ftpconfig' => array(
        'server' => '192.168.10.231',//ftp服务器的主机地址
        'port' => 21,//ftp服务器的主机端口
        'username' => 'stockuser', //ftp服务器的用户名
        'password' => 'stockuser123', //ftp服务器的用户密码
        'passive' => false,
        'ssl' => false,
        'timeout' => 30,
        'max_size'=>20480000, //最大可上传大小2M
        'mimetype'  => ['jpg','jpeg','png','mp3','wav','gif', 'webp', 'xpm', 'xbm', 'wbmp', 'bmp'], //可上传的文件类型
        'videotype' => ['mp3','wav'], //可上传的音频
        'multiple'  => false, //是否支持批量上传
        'upload_dir'  => 'uploads/', //文件上传目录
    ),
];