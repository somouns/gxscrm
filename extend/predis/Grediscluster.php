<?php

/**
 * Grediscluster 操作类
 *
 */
class Grediscluster extends Gredis {

    /**
     * 
     * Enter description here ...
     * @param unknown_type $parameters array(array('127.0.0.1:80','127.0.0.1:81'))
     */
	public function __construct($parameters) {
		$this->s = microtime(true);
		$this->set_child_class_name(__CLASS__);
		
		//获取需要进行条目分片的hash key
		if (empty ( self::$hashsharding )) {
			self::$hashsharding = C ( 'redis_hashsharding' );
		}

		$select_seed_indx =rand(0, count($parameters)-1);
		$seed[] = $parameters[$select_seed_indx];
		unset($parameters[$select_seed_indx]);
		$parameters = array_merge_recursive($seed,$parameters);
		try{
			$re = new RedisCluster(NUll,$parameters,1,1);
		}
		catch (Exception $ex){
			$re = null;
		}
		$this->checkresult($re,__METHOD__,func_get_args());
		$this->client['handle'] = $re;

    }

    public function masters()
    {
    	$re = null;
    	if(is_null($this->client ['handle']))
    	{
    		return $re;
    	}
    	
    	if (method_exists ( $this->client ['handle'], '_masters' )) {
    		try {
				$re =$this->client ['handle']->_masters();
    		}
    		catch (Exception $ex)
    		{
    			$re = null;
    		}
    		parent::checkresult($re,__METHOD__,func_get_args());
		}
		return $re;
    }
}