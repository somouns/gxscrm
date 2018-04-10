<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-09
 * Time: 20:14
 */

namespace fast\third;


use fast\Curl;

const CALLURL = 'http://192.168.2.239:8000';

class CallFunc
{
    public static function task_call($mobile,$act='call')
    {
        $job_numbers = 717;
        $task_id = 119;
        $task_start_time = time()-30*60;
        $task_start_time = date('Y-m-d H:i:s',$task_start_time);
        $task_end_time = time()+30*60;
        $task_end_time = date('Y-m-d H:i:s',$task_end_time);
        $data = [
            'act'=>$act,
            'phones'=>$mobile,
            'job_numbers'=>$job_numbers,
            'task_id'=>$task_id,
            'task_start_time'=>$task_start_time,
            'task_end_time'=>$task_end_time,
        ];
        $result_str = Curl::httpCurlPost(CALLURL,$data);
        if($result_str === false)
        {
            $return="";// 网络失败
        }
        else
        {
            $return=$result_str;
        }
        return $return;
    }
}