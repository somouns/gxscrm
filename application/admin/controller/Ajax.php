<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use EasyWeChat\Support\Log;
use fast\Random;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use think\Cache;
use think\Config;
use think\Db;
use think\Lang;
use app\admin\constant\PageConstant;
use ftp\Ftp;
use think\Request;
use thumb\ImageThumb;
use OSS\OssClient;
use OSS\Core\OssException;

/**
 * Ajax异步请求接口
 * @internal
 */
class Ajax extends Backend
{

    protected $noNeedLogin = ['lang'];
    protected $noNeedRight = ['*'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();

        //设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
    }

    /**
     * 加载语言包
     */
    public function lang()
    {
        header('Content-Type: application/javascript');
        $callback = $this->request->get('callback');
        $controllername = input("controllername");
        $this->loadlang($controllername);
        //强制输出JSON Object
        $result = 'define(' . json_encode(Lang::get(), JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE) . ');';
        return $result;
    }

    /**
     * 测试上传
     * Created by: Xerxes Sultan
     */
    public function uploadview()
    {
        $upload_dir = $_REQUEST['upload_dir'];
        $this->assign('upload_dir', $upload_dir);
        return $this->view->fetch();
    }


    /**
     * 使用ftp上传图片资源
     */
    public function ftp_upload()
    {
        $upload = Config::get('ftp.ftpconfig');
        $file = $_FILES['fileUpload'];

        if (empty($file)) {
            $this->code = 0;
            $this->msg = "未上传文件或超出服务器上传限制";
            return;
        }
        $suffix = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        /**
         * 验证文件
         */
        $check = $this->check_file($file, $upload);
        /**
         * 验证文件通过，上传文件到本地服务器
         */
        $random = rand(1, 100000);
        $uploadDir = $upload['upload_dir'];
        $fileName = md5(time() . $random);
        $path = $uploadDir . $fileName . '.' . $suffix;

        if ($check['code'] == 1) {
            /**
             * 把文件先上传到本地服务器
             */
            if (file_exists('uploads/' . $fileName)) {
                echo $_FILES["file"]["name"] . " already exists. ";
                die;
            }
            $localBoolean = move_uploaded_file($file['tmp_name'], $path);
            //压缩图片
            if (!in_array($suffix, $upload['videotype'])) {
                $image_thumb = $this->thumbImage($path);
            } else {
                $image_thumb = $path;
            }
            if ($localBoolean && $check) {
                //使用阿里云Oss上传文件
                $videoname = $_FILES['fileUpload']['name'];
                $oss = $this->connectOssAndUpload($path, $image_thumb, $_REQUEST['upload_dir'], $suffix, $videoname);
                if (!empty($oss)) {
                    @unlink($path);
                    @unlink($image_thumb);
                }
                //上传到我们的静态服务器上
//                $image = $this->ftpUploadFile($upload, $path, $_REQUEST['upload_dir'], $suffix);
//                $imageThumb = $this->ftpUploadFile($upload, $image_thumb, $_REQUEST['upload_dir'], $suffix);
//                $uploadInfo['data'] = array(
//                    "image" => PageConstant::STATIC_FILE_URL . $image['data'],
//                    "imageThumb" => PageConstant::STATIC_FILE_URL . $imageThumb['data']
//                );

                $imageFinal = array("code" => 1, "message" => "上传成功", "data" => $oss);
                echo json_encode($imageFinal);
                die;
            }
        } else {
            $this->code = $check['code'];
            $this->msg = $check['msg'];
            trace(date("Y-m-d") . ':上传文件到ftp服务器失败', "error");
            echo json_encode(array("code" => -1, "message" => "上传失败，文件验证不通过", "data" => ''));
            die;
        }
    }

    /**
     * 上传语音文件
     * @param $path
     * @param $upload_dir
     * @return array
     * Created by: Xerxes Sultan
     */
    private function connectOssAndUploadAudio($path, $upload_dir)
    {
        $config = Config::get('oss.oss_config');
        $accessKeyId = $config['OSS_TEST_ACCESS_KEY_ID'];
        $accessKeySecret = $config['OSS_TEST_ACCESS_KEY_SECRET'];
        $endpoint = $config['OSS_TEST_ENDPOINT'];
        $bucket = $config['OSS_TEST_BUCKET'];
        $image_path = [];
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->setTimeout(3600);
            $ossClient->setConnectTimeout(10);
            $object_original = date("Y-m-d", time()) . '/' . $upload_dir . '/' . $path;
            $image = $ossClient->uploadFile($bucket, $object_original, $path);
            $image_path['image'] = $image['info']['url'];
        } catch (OssException $e) {
            print $e->getMessage();
        }

        return $image_path;
    }


    /**
     * 链接oss
     * Created by: Xerxes Sultan
     */
    private function connectOssAndUpload($path, $image_thumb, $upload_dir, $suffix, $videoname)
    {
        $config = Config::get('oss.oss_config');
        $accessKeyId = $config['OSS_TEST_ACCESS_KEY_ID'];
        $accessKeySecret = $config['OSS_TEST_ACCESS_KEY_SECRET'];
        $endpoint = $config['OSS_TEST_ENDPOINT'];
        $bucket = $config['OSS_TEST_BUCKET'];
        $image_path = [];
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->setTimeout(3600);
            $ossClient->setConnectTimeout(10);
            $upload = Config::get('ftp.ftpconfig');
            if (in_array($suffix, $upload['videotype'])) {
                $object_original = date("Y-m-d", time()) . '/' . $upload_dir . '/' . $path;
                $image = $ossClient->uploadFile($bucket, $object_original, $path);
                $image_path['video'] = self::replaceInternal($image['info']['url']);
                $image_path['videoname'] = $videoname;
            } else {
                $object_original = date("Y-m-d", time()) . '/' . $upload_dir . '/' . $path;
                $object_thumb = date("Y-m-d", time()) . '/' . $upload_dir . '/' . $image_thumb;
                $image = $ossClient->uploadFile($bucket, $object_original, $path);
                $thumb = $ossClient->uploadFile($bucket, $object_thumb, $image_thumb);
                $image_path['image'] = self::replaceInternal($image['info']['url']);
                $image_path['thumb_image'] = self::replaceInternal($thumb['info']['url']);
            }

        } catch (OssException $e) {
            print $e->getMessage();
        }

        return $image_path;
    }

    private static function replaceInternal($url)
    {
        return str_replace('-internal', '', $url);
    }


    /**
     * 压缩图片
     * @param $path
     * Created by: Xerxes Sultan
     * 时间：2017年10月17日13:40:04
     */
    private function thumbImage($path)
    {
        $thumb = new ImageThumb($path);
        $thumb->thumb(200, 300);
        $savePath = $thumb->save("", "");
        return $savePath;
    }


    /**
     * 链接ftp上传
     * Created by: Xerxes Sultan
     * 时间：2017年10月17日09:35:17
     */
    private function ftpUploadFile($upload, $path, $remote_dir, $suffix)
    {
        /**
         * 链接ftp服务器，并将本地图片/文件上传到ftp服务器上
         */
        $ftp = new Ftp();
        $connection = $ftp->start($upload);
        $fileName = md5(time() . $remote_dir) . rand(1000, 9999);

        $ftp_upload_path = PageConstant::FTP_REMOTE_PATH . '/' . date("Ymd") . '/' . $remote_dir . '/' . $fileName . '.' . $suffix;
        if ($connection) {
            /**
             * ftp服务器链接成功后将本地文件上传至ftp服务器，并删除本地的文件
             */
            $ftpUploadBoolean = $ftp->put($ftp_upload_path, $path);
            if ($ftpUploadBoolean) {
                $result['code'] = PageConstant::FTP_UPLOAD_SUCCESS_CODE;
                $result['msg'] = PageConstant::FTP_UPLOAD_SUCCESS_MSG;
                $result['data'] = $ftp_upload_path;
                return $result;
            } else {
                $result['code'] = PageConstant::FTP_UPLOAD_FAIL_CODE;
                $result['msg'] = PageConstant::FTP_UPLOAD_FAIL_MSG;
                return $result;
            }
        }
    }


    /**
     * 验证文件的合法性
     * @param $file
     * @param $upload
     * 时间：2017年10月16日21:02:21
     */
    private function check_file($file, $upload)
    {
        $result = [];
        /**
         * 文件上传为空的异常
         */
        if (empty($file)) {
            $result['code'] = PageConstant::EMPTY_FILE_ERROR_CODE;
            $result['msg'] = PageConstant::EMPTY_FILE_ERROR_MESSAGE;
            return $result;
        }
        $file_size = $file['size'];
        /**
         * 文件上传超过限制大小的异常
         */
        if ($file_size > $upload['max_size']) {
            $result['code'] = PageConstant::MAX_SIZE_ERROR_CODE;
            $result['msg'] = PageConstant::MAX_SIZE_ERROR_MESSAGE;
            return $result;
        }
        /**
         * 文件上传错误类型的异常
         */
        $suffix = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($suffix, $upload['mimetype'])) {
            $result['code'] = PageConstant::MIMETYPE_ERROR_CODE;
            $result['msg'] = PageConstant::MIMETYPE_ERROR_MESSAGE;
            return $result;
        }
        $result['code'] = PageConstant::FILE_CHECK_SURE_CODE;
        $result['msg'] = PageConstant::FILE_CHECK_SURE_MESSAGE;
        return $result;
    }


    /**
     * 上传文件
     */
    public function upload()
    {
        $this->code = -1;
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->msg = "未上传文件或超出服务器上传限制";
            return;
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();
        $uploaded = model("attachment")->where('sha1', $sha1)->find();
        if ($uploaded) {
            $this->code = 1;
            $this->data = [
                'url' => $uploaded['url']
            ];
            return;
        }

        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';
        $replaceArr = [
            '{year}' => date("Y"),
            '{mon}' => date("m"),
            '{day}' => date("d"),
            '{hour}' => date("H"),
            '{min}' => date("i"),
            '{sec}' => date("s"),
            '{random}' => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}' => $suffix,
            '{.suffix}' => $suffix ? '.' . $suffix : '',
            '{filemd5}' => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . '/public' . $uploadDir, $fileName);
        if ($splInfo) {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'filesize' => $fileInfo['size'],
                'imagewidth' => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype' => $suffix,
                'imageframes' => 0,
                'mimetype' => $fileInfo['type'],
                'url' => $uploadDir . $splInfo->getSaveName(),
                'uploadtime' => time(),
                'storage' => 'local',
                'sha1' => $sha1,
            );
            model("attachment")->create(array_filter($params));
            $this->code = 1;
            $this->data = [
                'url' => $uploadDir . $splInfo->getSaveName()
            ];
        } else {
            // 上传失败获取错误信息
            $this->data = $file->getError();
        }
    }

    /**
     * 通用排序
     */
    public function weigh()
    {
        //排序的数组
        $ids = $this->request->post("ids");
        //拖动的记录ID
        $changeid = $this->request->post("changeid");
        //操作字段
        $field = $this->request->post("field");
        //操作的数据表
        $table = $this->request->post("table");
        //排序的方式
        $orderway = $this->request->post("orderway", 'strtolower');
        $orderway = $orderway == 'asc' ? 'ASC' : 'DESC';
        $sour = $weighdata = [];
        $ids = explode(',', $ids);
        $prikey = 'id';
        $pid = $this->request->post("pid");
        //限制更新的字段
        $field = in_array($field, ['weigh']) ? $field : 'weigh';

        // 如果设定了pid的值,此时只匹配满足条件的ID,其它忽略
        if ($pid !== '') {
            $hasids = [];
            $list = Db::name($table)->where($prikey, 'in', $ids)->where('pid', 'in', $pid)->field('id,pid')->select();
            foreach ($list as $k => $v) {
                $hasids[] = $v['id'];
            }
            $ids = array_values(array_intersect($ids, $hasids));
        }

        //直接修复排序
        $one = Db::name($table)->field("{$field},COUNT(*) AS nums")->group($field)->having('nums > 1')->find();
        if ($one) {
            $list = Db::name($table)->field("$prikey,$field")->order($field, $orderway)->select();
            foreach ($list as $k => $v) {
                Db::name($table)->where($prikey, $v[$prikey])->update([$field => $k + 1]);
            }
            $this->code = 1;
        } else {
            $list = Db::name($table)->field("$prikey,$field")->where($prikey, 'in', $ids)->order($field, $orderway)->select();
            foreach ($list as $k => $v) {
                $sour[] = $v[$prikey];
                $weighdata[$v[$prikey]] = $v[$field];
            }
            $position = array_search($changeid, $ids);
            $desc_id = $sour[$position];    //移动到目标的ID值,取出所处改变前位置的值
            $sour_id = $changeid;
            $desc_value = $weighdata[$desc_id];
            $sour_value = $weighdata[$sour_id];
            //echo "移动的ID:{$sour_id}\n";
            //echo "替换的ID:{$desc_id}\n";
            $weighids = array();
            $temp = array_values(array_diff_assoc($ids, $sour));
            foreach ($temp as $m => $n) {
                if ($n == $sour_id) {
                    $offset = $desc_id;
                } else {
                    if ($sour_id == $temp[0]) {
                        $offset = isset($temp[$m + 1]) ? $temp[$m + 1] : $sour_id;
                    } else {
                        $offset = isset($temp[$m - 1]) ? $temp[$m - 1] : $sour_id;
                    }
                }
                $weighids[$n] = $weighdata[$offset];
                Db::name($table)->where($prikey, $n)->update([$field => $weighdata[$offset]]);
            }
            $this->code = 1;
        }
    }

    /**
     * 清空系统缓存
     */
    public function wipecache()
    {
        $wipe_cache_type = ['TEMP_PATH', 'CACHE_PATH'];
        foreach ($wipe_cache_type as $item) {
            $dir = constant($item);
            if (!is_dir($dir))
                continue;
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            //rmdir($dir);
        }
        Cache::clear();
        $this->code = 1;
    }

    /**
     * 读取分类数据,联动列表
     */
    public function category()
    {
        $type = $this->request->get('type');
        $pid = $this->request->get('pid');
        $where = ['status' => 'normal'];
        $categorylist = null;
        if ($pid !== '') {
            if ($type) {
                $where['type'] = $type;
            }
            if ($pid) {
                $where['pid'] = $pid;
            }

            $categorylist = Db::name('category')->where($where)->field('id as value,name')->order('weigh desc,id desc')->select();
        }
        $this->code = 1;
        $this->data = $categorylist;
        return;
    }

    /**
     * 读取省市区数据,联动列表
     */
    public function area()
    {
        $province = $this->request->get('province');
        $city = $this->request->get('city');
        $where = ['pid' => 0, 'level' => 1];
        $provincelist = null;
        if ($province !== '') {
            if ($province) {
                $where['pid'] = $province;
                $where['level'] = 2;
            }
            if ($city !== '') {
                if ($city) {
                    $where['pid'] = $city;
                    $where['level'] = 3;
                }
                $provincelist = Db::name('area')->where($where)->field('id as value,name')->select();
            }
        }
        $this->code = 1;
        $this->data = $provincelist;
        return;
    }

    /**
     * 将本地文件上传到ftp服务器，并删除本地文件
     * @param $check
     * @param $ftp_upload_path
     * @param $path
     */
    private function local_to_ftp($check, $ftp_upload_path, $path)
    {
        $upload_bool = Ftp_for_crm::upload($ftp_upload_path, $path);
        Ftp_for_crm::close();
        if ($upload_bool) {
            /**
             * 上传ftp服务器成功后，向前台返回图片的地址并且删除本地服务器上的图片
             */
            $output_url = ['url' => PageConstant::STATIC_FILE_URL . $ftp_upload_path];
            $this->code = 1;
            $this->data = $output_url;
            $this->msg = $check['msg'];
            unlink($path);
            trace(date("Y-m-d") . ":上传文件到ftp服务器成功，图片路径为：" . $output_url['url'], "info");
//            file_put_contents(date("Y-m-d") . 'ftp.log', "成功上传图片到ftp服务器上,返回信息：" . $this->msg . "当前时间是：" . date("Y-m-d", time()));
        }
    }


    /**
     * 删除Oss文件
     * Created by: Xerxes Sultan
     */
    public function deleteOssFile()
    {
        $request = Request::instance();
        $file_name = $request->param("delete_file_path");
        //接口接受参数以“，”隔开
        if (!empty($file_name)) {
            $file_names = explode(",", $file_name);
            $this->deleteFile($file_names);
        } else {
            echo json_encode(array("code" => -3, "message" => "必须传一个文件地址", "data" => ""));
            die;
        }
    }

    /**
     * 删除oss上的文件
     * @param $file_name
     * Created by: Xerxes Sultan
     */
    private function deleteFile($file_name)
    {
        $config = Config::get('oss.oss_config');
        $accessKeyId = $config['OSS_TEST_ACCESS_KEY_ID'];
        $accessKeySecret = $config['OSS_TEST_ACCESS_KEY_SECRET'];
        $endpoint = $config['OSS_TEST_ENDPOINT'];
        $bucket = $config['OSS_TEST_BUCKET'];

//        $file_name = str_replace("http://" . $bucket . '.' . $endpoint . '/', '', $file_name);
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->setTimeout(3600);
            $ossClient->setConnectTimeout(10);
            //判断文件在oss上是否存在
            $exist = true;
            foreach ($file_name as $key => $item) {
                $file_pathInfo = parse_url($file_name[$key]);
                $path = substr($file_pathInfo['path'], 1);
                $exist = $ossClient->doesObjectExist($bucket, $path);
            }
            if ($exist) {
                $boolean = $ossClient->deleteObjects($bucket, $file_name);
                if ($boolean) {
                    echo json_encode(array("code" => 1, "message" => "删除文件成功", "data" => ""));
                    die;
                } else {
                    echo json_encode(array("code" => -1, "message" => "删除文件失败", "data" => ""));
                    die;
                }
            } else {
                echo json_encode(array("code" => -2, "message" => "多个或一个文件在oss上不存在", "data" => ""));
                die;
            }
        } catch (OssException $e) {
            print $e->getMessage();
        }

    }
}
