<?php
namespace thumb;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-26
 * Time: 09:58
 */
//header('content-type:text/html;charset=utf-8');
//date_default_timezone_set('PRC');

class ImageThumb
{

    private $info;
    private $img;

    /**
     * 打开需要操作的原图，获取图片信息
     */
    public function __construct($path, $type = ['gif', 'jpg', 'png', 'webp', 'xpm', 'xbm', 'wbmp', 'jpeg', 'bmp'])
    {
        if (in_array(pathinfo($path, PATHINFO_EXTENSION), $type) == false) {
            die(json_encode(['code' => -1, 'msg' => '请上传正确格式的图片']));
        }
        $info = getimagesize($path);
        $this->info = array(
            'width' => $info[0],
            'height' => $info[1],
            'type' => image_type_to_extension($info[2], false),
            'mime' => $info['mime'] //image_type_to_mime_type($info[2])
        );
        $create = "imagecreatefrom{$this->info['type']}";
        $this->img = $create($path);

    }

    /**
     * [thumb 图片操作，等比例压缩]
     * @param  [number] $width   [想要压缩的宽度]
     * @param  [number] $height  [想要压缩的高度]
     *                           （因为是都能比例压缩高宽只能满足一个）
     * @return [resources]         ]
     */
    public function thumb($width, $height)
    {
        $pw = $width / $this->info['width'];
        $ph = $height / $this->info['height'];
        if ($pw > $ph) {
            $ratio = $ph;
        } else {
            $ratio = $pw;
        }
        $new_w = $this->info['width'] * $ratio;
        $new_h = $this->info['height'] * $ratio;
        $p_img = imagecreatetruecolor($new_w, $new_h);
        imagecopyresampled($p_img, $this->img, 0, 0, 0, 0, $new_w, $new_h, $this->info['width'], $this->info['height']);
        imagedestroy($this->img);
        $this->img = $p_img;
    }


    /**
     * save 存储处理过后的图片到指定位置
     * @param  string $newPath 保存文件的目录
     * @param  string $newName 文件的唯一的新名字
     * @return
     */
    public function save($newPath, $newName)
    {
        $newPath = 'ThumbImage/' . date("Ymd") . '/';
        if (!file_exists($newPath)) {
            mkdir($newPath, 0777, true);
        }
        $newName = md5(uniqid(microtime(true), true));
        $address = $newPath . $newName . '.' . $this->info['type'];
        $op = "image{$this->info['type']}";
        if ($op($this->img, $address)) {
            return $address;
        }
        $this->destroy();
    }

    /**
     * 销毁内存中的图片
     */
    public function destroy()
    {
        imagedestroy($this->img);
    }


}
















