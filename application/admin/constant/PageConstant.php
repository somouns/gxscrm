<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-30 14:01
 * Author: SteveGao
 * Company: Mr.Stock CRM Project Team
 */

namespace app\admin\constant;

class PageConstant
{
    const PAGE_NORMAL = 1;
    const PAGE_DISABLED = 0;

    /**
     * ftp文件上传常量定义
     */
    const MAX_SIZE_ERROR_MESSAGE = "上传文件超出服务器上传限制大小";
    const EMPTY_FILE_ERROR_MESSAGE = "未上传文件或超出服务器上传限制";
    const MIMETYPE_ERROR_MESSAGE = "非法的文件类型";
    const FILE_CHECK_SURE_MESSAGE = "文件格式正确";
    const FTP_REMOTE_PATH = "/gxscrm";
    const EMPTY_FILE_ERROR_CODE = -1;
    const MAX_SIZE_ERROR_CODE = -2;
    const MIMETYPE_ERROR_CODE = -3;
    const FILE_CHECK_SURE_CODE = 1;

    const STATIC_FILE_URL = "http://static.guxiansheng.cn";

    const FTP_UPLOAD_SUCCESS_CODE = 1;
    const FTP_UPLOAD_SUCCESS_MSG = "文件上传成功";

    const FTP_UPLOAD_FAIL_CODE = -1;
    const FTP_UPLOAD_FAIL_MSG = "文件上传失败";

}
