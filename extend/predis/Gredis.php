<?php

/**
 * Gredis 操作类
 *
 */
class Gredis {
    
	private $index_pr = 'i';//hash分区索引前缀
    private $index_factor = 5;//hash分区 右取长度因子
    private $child_name = 'Gredis';
    public $error_msg = '';
    
    public $client = array();
    public static $hashsharding;
    public $s;
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $parameters array(array('127.0.0.1:80','127.0.0.1:81'))
	 */
	public function __construct($ip, $port, $func, $db = 0) {
		$this->s = microtime ( true );
		$re = $this->install ( $ip, $port, $func, $db );
		$try_num = 1; //重试次数
		while ( ! $re && $try_num < 2 ) {
			$re = $this->install ( $ip, $port, $func, $db );
			$try_num ++;
		}

		$this->client ['handle'] = $re;
	}
	
	public function install($ip, $port, $func, $db = 0){
		$re = new Redis();
		try {
			$enble = $re->$func ( $ip, $port, 1 );
		} catch ( Exception $ex ) {
			$this->error_msg = $func . ':' . $ex;
			$enble = false;
		}
		if ($enble) {
			try {
				$enble = $re->select ( intval ( $db ) );
			} catch ( Exception $ex ) {
				$enble = false;
				$this->error_msg = 'select:' . $ex;
			}
			if (empty ( $enble )) {
				$re = null;
			}
		} else {
			$this->error_msg = $func . ':error';
			$re = null;
		}
		return $re;
	}
    
    public function set_child_class_name($name)
    {
    	$this->child_name = $name;
    }
    

    /**
     * 单参数使用
     * Enter description here ...
     * @param unknown_type $name
     * @param unknown_type $arguments
     */
    public function __call($name, $arguments) 
    {
    	if(!is_null(@$this->client['handle']))
    	{
    		try {
    			$this->s = microtime(true);
	    		$re = call_user_func_array(array(@$this->client['handle'],$name),$arguments);
    		}
    		catch (Exception $ex)
    		{
    			$this->error_msg = $ex;
    			$re = null;
    		}
	    	
	    	$this->checkresult($re,__METHOD__,func_get_args());
	    	return $re;
    	}
    	else{
    		return null;
    	}
    }
    
    public function ghset($keyname,$field,$value)
    {
    	list($keyname,$field) = $this->gethashkey($keyname, $field);
		return $this->hset($keyname,$field,$value);
    }
    
	public function ghget($keyname,$field)
    {
    	list($keyname,$field) = $this->gethashkey($keyname, $field);
		return $this->hget($keyname,$field);
    }
    
    public function ghdel($keyname,$field)
    {
    	list($keyname,$field) = $this->gethashkey($keyname, $field);
		return $this->hdel($keyname,$field);
    }
    
    public function ghmset($keyname,$list)
    {
    	if (!in_array ( $keyname, self::$hashsharding )) //是否分片
		{
			return $this->hmset($keyname,$list);
		}
    	$result = array();
    	foreach ($list as $k=>$v)
    	{
    		list($keyname,$field) = $this->gethashkey($keyname, $k);
			$result[] = $this->hset($keyname,$field,$v);
    	}
    	return $result;
    }
    
    public function ghmget($keyname,$fields)
    {
    	if (!in_array ( $keyname, self::$hashsharding )) //是否分片
		{
			return $this->hmget($keyname,$fields);
		}
    	$result = array();
    	foreach ($fields as $v)
    	{
    		list($keyname,$field) = $this->gethashkey($keyname, $v);
			$result[] = $this->hget($keyname,$field);
    	}
    	return $result;
    }
	
	/**
	 * 
	 * 多keys 多值批量操作
	 * @param unknown_type $data
	 * @param unknown_type $pipe true 没有事务 false 事务
	 * 事务 
	 * 开始事务。
		命令入队。
		执行事务
	 * [
	 * 'testmhset1'=>['call'=>'hset','args'=>[1,1],
	 * 'testmhset2'=>['call'=>'set','args'=>[1]],
	 * 'testmhset3'=>['call'=>'zadd','args'=>[1,2]],
	 * ]
	 */
	public function gmulti($data, $pipe = false) {
		$this->s = microtime ( true );
		if (is_null ( $this->client ['handle'] ) || ! is_array ( $data ) || count ( $data ) == 0) {
			return array();
		}
		
		if ($pipe) {
			$pipe = $this->client ['handle']->multi ( Redis::PIPELINE );
		} else {
			$pipe = $this->client ['handle']->multi ();
		}

		foreach ( $data as $k => $item ) {
			$name = $item ['call'];
			$arguments = $item ['args'];

			array_unshift ( $arguments, $k);
			call_user_func_array ( array ($pipe, $name ), $arguments );
		}
		$result = $pipe->exec ();
		$newRew = [];
		$prefix = !empty(C('redis_config.clusterclient.prefix'))?C('redis_config.clusterclient.prefix'):'stocksir:';
		$prefix = str_replace('_',':',$prefix);
		
		$i = 0;
		foreach ($data as $k => $item){
			$k = str_replace($prefix,'',$k);
			$newRew[$k] = $result[$i];
			$i++;
		}

		//@file_put_contents ( '/tmp/Gredis_pipe_log.txt', 'dateline:'.date('Y-m-d H:i:s').' runtime:'.(round(microtime(true)-$this->s,6)*1000)."\r\n".var_export($data,true)."\r\n".var_export($result,true), FILE_APPEND );
		return $newRew;
	}
	
	public function checkresult($re, $method, $args) {
		$class_export = var_export ( $this->child_name, true );
		$method_export = var_export ( $method, true );
		$args_export = var_export ( $args, true );
		$result = gettype ( $re ) . '---' . var_export ( $re, true );
		$client = print_r ( $this->client, true );
		$err = '';
		if (! empty ( $this->error_msg )) {
			$err = 'error:' . $this->error_msg;
			$this->error_msg = '';
		}
		
		$runtime = round(microtime(true)-$this->s,6)*1000; 
		$dataline = date('Y-m-d H:i:s') ;
		$content = "dataline: {$dataline} runtime:{$runtime}  class:{$class_export} method:{$method_export} args:{$args_export}:\r\n  result:{$result}\r\n client:{$client} {$err} \r\n";
		
		if (is_null ( $re )) {
			Log::record ( $content, Log::REDISERR );
		}
	}
    
	/**
	 * hash中field分区
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	private  function getfieldindex($field) {
		if(is_numeric($field))
		{
			$field_len = strlen ( $field );
			if ($field_len > $this->index_factor) {
				$index = substr ( $field, 0, $field_len - $this->index_factor );
				$field = substr ( $field, - $this->index_factor );
			} else {
				$index = 0;
			}
			return array($index,$field);
		}
		else{
			return null;
		}
	}
	
	/**
	 * 获取hashkey 及field 组成key的slot
	 * Enter description here ...
	 * @param unknown_type $key
	 * @param unknown_type $field
	 */
	private function gethashkey($keyname, $field) {
		if (in_array ( $keyname, self::$hashsharding )) //是否分片
		{
			$item = $this->getfieldindex ( $field );
			if(!is_null($item))
			{
				$keyname = $keyname . ':'.$this->index_pr . $item[0];
				$field = $item[1];
			}
		}
		return array($keyname ,$field);
	}
}