<?php

/**
 * GRedis 操作类
 *
 */
class Gredisclient extends Gredis {
	
	private $group_pre = 'redis_connection_';
	private $redis_connections_config;
	private $redis_connections;
	private $cluster_groups;
	
	public $need_readonly = 0; //是否需要发送readonly
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $parameters 
	 * 
	 * array(array('host'=>'192.168.10.243','port'=>63791,'pconnect'=>0),array('host'=>'192.168.10.243','port'=>7379,'pconnect'=>0)),
	   array(array('host'=>'192.168.10.243','port'=>63791,'pconnect'=>0),array('host'=>'192.168.10.243','port'=>7379,'pconnect'=>0)),
	   array(array('host'=>'192.168.10.243','port'=>63791,'pconnect'=>0),array('host'=>'192.168.10.243','port'=>7379,'pconnect'=>0))
	 */
	public function __construct($parameters) {
		$this->set_child_class_name ( __CLASS__ );
		$this->cluster_groups = array ();
		foreach ( $parameters as $k => $v ) {
			$this->cluster_groups [] = $k;
			$items = $v;
			$seed_indx = $this->randweight ( $items ); //随机选取一个
			$item = $items [$seed_indx];
			
			$ip = $item ['host'];
			$port = $item ['port'];
			$func = $item ['pconnect'];
			$weight = isset ( $item ['weight'] ) ? $item ['weight'] : 1;
			$this->preidstoredis ( $ip, $port, $func, $weight, $k );
		}
	}
	
	/**
	 * 按照权重获取
	 * Enter description here ...
	 * @param unknown_type $parameters
	 */
	private function randweight($items) {
		
		$weights = array ();
		foreach ( $items as $v ) {
			if (isset ( $v ['weight'] )) {
				$weights [] = $v ['weight'];
			} else {
				$weights [] = 1; //如果没有配置权重 则权重为1
			}
		}
		
		$roll = rand ( 1, array_sum ( $weights ) );
		
		$_tmpW = 0;
		$rollnum = 0;
		foreach ( $weights as $k => $v ) {
			$min = $_tmpW;
			$_tmpW += $v;
			$max = $_tmpW;
			
			if ($roll > $min && $roll <= $max) {
				return $k;
			}
		}
	}
	
	/**
	 * 把集群配置 转为单redis配置
	 * Enter description here ...
	 * @param unknown_type $ip
	 * @param unknown_type $port
	 * @param unknown_type $num
	 */
	private function preidstoredis($ip, $port, $func, $weight, $group) {
		$item = array ();
		$item ['ip'] = $ip;
		$item ['port'] = $port;
		$item ['db'] = 0;
		$item ['pconnect'] = $func;
		$item ['weight'] = $weight;
		$connection_name = $this->group_pre . $group;
		
		if (empty ( $this->redis_connections_config ) || ! array_key_exists ( $connection_name, $this->redis_connections_config )) {
			$this->redis_connections_config [$connection_name] = $item;
		}
	}
	
	/**
	 * 单参数使用
	 * Enter description here ...
	 * @param unknown_type $name
	 * @param unknown_type $arguments
	 */
	public function __call($name, $arguments) {
		$argument = @$arguments [0];
		$this->set_client ( $argument );

		return parent::__call ( $name, $arguments );
	}
	
	/**
	 * predis 设置client
	 * Enter description here ...
	 * @param unknown_type $keyname
	 */
	private function set_client($keyname) {
		$group = $this->getgroupbykey ( $keyname );
		$this->set_client_bygroup ( $group );
	}
	
	private function set_client_bygroup($group) {
		$connection_name = $this->group_pre . $group;

		$this->client ['connection_name'] = $connection_name;;
		$this->client ['connections_config'] = $this->redis_connections_config;
		if (empty ( $this->redis_connections [$connection_name] )) {
			$this->setRedis ( $connection_name );
		}
		$this->client ['handle'] = $this->redis_connections [$connection_name];
	}
	
	/**
	 * 设置非集群redis
	 * Enter description here ...
	 * @param unknown_type $host
	 */
	private function setRedis($connection_name) {
		$this->s = microtime ( true );
		$config = $this->redis_connections_config [$connection_name];
		$func = $config ['pconnect'] ? 'pconnect' : 'connect';
		parent::__construct ( $config ['ip'], $config ['port'], $func, 0 );
		if ($this->need_readonly) {
			if (method_exists ( $this->client ['handle'], 'readonly' )) {
				$enble = parent::__call ( 'readonly', array () );
				if (! $enble) {
					$this->client ['handle'] = null;
				}
			}
		}
		
		$this->redis_connections [$connection_name] = $this->client ['handle'];
		$this->client ['connection'] = $this->redis_connections;
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
		$keys = array_keys ( $data );
		
		$return = array ();
		$groups = $this->groupkeys ( $keys );
		
		foreach ( $groups as $k => $group ) {
			$group_data = array ();
			foreach ( $group as $v ) {
				$group_data [$v] = $data [$v];
			}
			$this->set_client_bygroup ( $k );
			
			$result = parent::gmulti ( $group_data, $pipe );
			
			if (is_array ( $result )) {
				$return = array_merge ( $return, $result );
			}
		}
		
		return $return;
	}
	
	/**
	 * 
	 * 对key进行分组
	 * @param unknown_type $keys
	 */
	public function groupkeys($keys) {
		$list = array ();
		foreach ( $keys as $key ) {
			$slot = $this->getgroupbykey ( $key );
			$list [$slot] [] = $key;
		}
		return $list;
	}
	
	private function getgroupbykey($key) {
		$slot = $this->getslotbykey ( $key );
		foreach ( $this->cluster_groups as $v ) {
			if ($slot <= $v) {
				return $v;
			}
		}
	}
	
	/**
	 * 根据key 获取相应的桶
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	private function getslotbykey($key) {
		return abs ( crc16::hash ( $key ) ) % 16384;
	}
}