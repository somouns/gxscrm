<?php

/**
 * 文档控制器
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-10-09
 * Time: 14:46
 */
namespace app\admin\controller\material;

use app\common\controller\Backend;

class MaterialDocumentArticle extends Backend
{
    /**
     * 新建文档
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日14:57:13
     */
    public function add()
    {
        return $this->view->fetch();
    }

    /**
     * 编辑文档
     * @return string
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日14:59:13
     */
    public function edit()
    {
        return $this->view->fetch();
    }

    /**
     * 查看文档详情
     * @return string
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日14:59:32
     */
    public function detail()
    {
        return $this->view->fetch();
    }

    /**
     * 文档分类列表
     * @return string
     * Created by: Xerxes Sultan
     */
    public function categoryArticleIndex()
    {
        return $this->view->fetch();
    }

    /**
     * 新增文档分类
     * Created by: Xerxes Sultan
     * 时间：2017-10-9 16:36:29
     */
    public function categoryAdd()
    {

    }


    /**
     * 编辑文档分类的权限节点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日16:37:07
     */
    public function categoryEdit()
    {

    }

    /**
     * 删除文档分类的权限节点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日16:38:12
     */
    public function categoryDelete()
    {

    }

    /**
     * 文档所属附件列表的访问控制权限点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:43:58
     */
    public function articleAttachment()
    {

    }

    /**
     * 附件下载功能的访问控制权限点
     * Created by: Xerxes Sultan
     */
    public function attachmentDownload()
    {
    }

    /**
     * 删除文档的访问控制权限点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:46:11
     */
    public function delete()
    {
    }

    /**
     * 文档系列接口转发接口站点方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日13:54:14
     */
    public function crmAdmin()
    {

    }
}