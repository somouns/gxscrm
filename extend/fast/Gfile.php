<?php
namespace fast;
if (stripos(' '. PHP_OS, 'win')) {
    $cacheFile = $_SERVER['DOCUMENT_ROOT'] . '/data/cache';
} else {
    $cacheFile = "/data/cache";
}
define('FILECACHEDIR', $cacheFile);

/**
 *
 * 变量缓存类
 *
 * @author Administrator
 *        
 */
class Gfile
{

    private static $files;

    // 静态文件内容缓存数组
    private static $expire = 300;

    // 变量文件缓存有效期秒
    private static $filedir = FILECACHEDIR;

    private static $bufferSize = 1024 * 1024 * 100;

    /**
     *
     * 变量写入文件缓存
     *
     * for ($i=0;$i<1000;$i++)
     * {
     * $list[$i] = $i.'试文件锁测试文件锁测试文件锁';
     * }
     * $list[count($list)-1] = microtime(true);
     * var_dump(Gfile::set('testfile',$list));
     *
     * @param unknown_type $key            
     * @param unknown_type $data
     *            可以为数组或字符串 int 等等
     */
    public static function set($key, $data, $dir = null)
    {
        if (! is_null($dir)) {
            self::$filedir = $dir;
        }
        
        $key = str_replace('-', '_', $key);
        $key = str_replace(':', '_', $key);
        $key = str_replace('.', '_', $key);
        
        $value = serialize($data);
        
        $filename = self::_path($key);
        
        // 判断目录是否存在(自定义目录存储)
        if (! is_dir(dirname($filename))) {
            @mkdir(iconv("UTF-8", "GBK", dirname($filename)), 0777, true);
        }
        
        $try_num = 1; // 重试次数
        $re = self::write_file_lock($filename, $key, $value);
        while (! $re && $try_num < 3) {
            usleep(10);
            $re = self::write_file_lock($filename, $key, $value);
            $try_num ++;
        }
        return $re;
    }

    /**
     * 获取缓存变量内容
     *
     * var_dump(Gfile::get("testfile"));
     *
     * Enter description here ...
     *
     * @param unknown_type $key            
     * @param unknown_type $exp            
     */
    public static function get($key, $exp = -1, $dir = null, $isdaemon = FALSE)
    {
        if (! is_null($dir)) {
            self::$filedir = $dir;
        }
        
        $key = str_replace('-', '_', $key);
        $key = str_replace(':', '_', $key);
        $key = str_replace('.', '_', $key);
        if (! isset(self::$files[$key]) || $exp == 0) {
            $filename = self::_path($key);
            if (is_file($filename)) {
                
                $mtime = filemtime($filename);
                $now_time = time();
                $exp = intval($exp);
                if ($exp == - 1) {
                    $exp = self::$expire;
                }
                
                if (($mtime + intval($exp)) > $now_time || $exp == 0) {
                    $content = self::read_file_lock($filename);
                    // $content =file_get_contents($filename);
                    $try_num = 1;
                    while (empty($content) && $try_num < 3) {
                        $content = self::read_file_lock($filename);
                        // $content =file_get_contents($filename);
                        $try_num ++;
                    }
                    $content = unserialize($content);
                    if ($content) {
                        self::$files[$key] = $content;
                    }
                    $result = null;
                } else {
                    @unlink($filename);
                    return '';
                }
            } else {
                return '';
            }
        }
        
        return self::$files[$key];
    }

    private static function _path(&$key)
    {
        $_key = strtolower($key);
        $_key = self::distributionfile($_key);
        
        $path = self::$filedir . '/' . $_key;
        
        return $path . '/' . $key . '.php';
    }

    private static function distributionfile($key)
    {
        if (strlen($key) < 2) {
            return $key;
        }
        
        $_key_dir = str_replace('_', '/', $key);
        
        return $_key_dir;
    }

    private static function read_file_lock($filename)
    {
        $result = false;
        $fo = @fopen($filename, 'r');
        if (flock($fo, LOCK_SH)) {
            $result = fread($fo, self::$bufferSize);
            flock($fo, LOCK_UN);
            fclose($fo);
        }
        return $result;
    }

    private static function write_file_lock($filename, $key, $data)
    {
        $content = $data;
        $fo = fopen($filename, 'cb');
        if ($fo) {
            if (flock($fo, LOCK_EX)) {
                rewind($fo);
                fwrite($fo, $content);
                fflush($fo);
                ftruncate($fo, ftell($fo));
                flock($fo, LOCK_UN);
                fclose($fo);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}