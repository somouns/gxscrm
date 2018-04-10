<?php
include 'Crc16.php';
include 'Gredis.php';
include 'Gredisclient.php';
include 'Grediscluster.php';
/**
 * CacheRedis 操作类
 *
 */
class CachePredis {
	public $connected;
	public $type;
	public $prefix;
	public $host;
	public $servers;
	public $alias;
	public $redis_type = 'redis';
	
	public static $config;
	public static $handle;
	public $init_redis;
	public $need_readonly = 0;
	
	public function __construct() {
		//获取redis集群配置文件
		if (empty ( self::$config )) {
			self::$config = C ( 'redis_config' );
		}

	}
	
	/**
	 * 设置集群主库
	 * Enter description here ...
	 */
	public function setClusterMaster() {
		
		$parameters = self::$config [$this->host] ['seeds_nodes'];
		$clients = new Grediscluster ( $parameters );
		$clients::$hashsharding = C ( 'redis_hashsharding' );
		
		self::$handle [$this->host] ['master'] = $clients;
	}
	
	/**
	 * 哨兵设置主库
	 * Enter description here ...
	 */
	public function setSentinelMaster() {
		
		//目前composer 只有哨兵
    	if (!@require(APP_ROOT_PATH.'/composer/vendor/autoload.php')) exit(APP_ROOT_PATH.'/composer/verdor/autoload.php isn\'t exists!');
    	$servers =self::$config[$this->host]['sentinel_servers'];//获取哨兵列表
    	$aliass = self::$config[$this->host]['cluster_alias'];//获取集群列表

    	$clusters = array();
    	foreach ($aliass as $k=>$alias)
    	{
    		$masterDiscovery = new PSRedis\MasterDiscovery($alias);
	    	foreach ($servers as $v)
	    	{
	    		$sentinel = new PSRedis\Client($v[0],$v[1]);
	    		$masterDiscovery->addSentinel($sentinel);
	    	}
	    	$master = $masterDiscovery->getMaster();

	    	$cluster['host'] = $master->getIpAddress();
	    	$cluster['port'] = $master->getPort();
	    	$cluster['pconnect'] = 1;
	    	$cluster['db'] = 0;
	    	$clusters[$k][] = $cluster;
    	}

		$clients = new Gredisclient ( $clusters);
		$clients::$hashsharding = C ( 'redis_hashsharding' );
		
		self::$handle [$this->host] ['master'] = $clients;
	}
	
	/**
	 * 集群客户端自动识别主库
	 * Enter description here ...
	 */
	public function setClusterclientMaster() {
		
		$seeds_nodes = self::$config [$this->host] ['seeds_nodes'];
		$master_groups = self::$config [$this->host] ['master_groups'];
		
		$clusters = array ();

		$rediscluster = new Grediscluster($seeds_nodes);
		$_masters = $rediscluster->masters ();
		if(is_null($_masters))
		{
			//如果失败则取第一个节点	
			foreach ( $master_groups as $k => $vs ) {
				foreach ( $vs as $v ) {
					$item = $v ['host'] . ':' . $v ['port'];
					$clusters [$k] [] = $v;
					break;
				}
			}
		}
		else {	
			$masters = array ();
			foreach ( $_masters as $v ) {
				$masters [] = $v [0] . ':' . $v [1];
			}

			foreach ( $master_groups as $k => $vs ) {
				foreach ( $vs as $v ) {
					$item = $v ['host'] . ':' . $v ['port'];
					if (in_array ( $item, $masters )) {
						$clusters [$k] [] = $v;
					}
				}
			}
		}
		
		$clients = new Gredisclient ( $clusters);
		$clients::$hashsharding = C ( 'redis_hashsharding' );
		self::$handle [$this->host] ['master'] = $clients;
	}
	
	/**
	 * 设置从库
	 * Enter description here ...
	 */
	public function setClusterSlave() {
		$parameters = self::$config [$this->host] ['slaves'];

		$clients = new Gredisclient ( $parameters );
		$clients::$hashsharding = C ( 'redis_hashsharding' );
		$clients->need_readonly = $this->need_readonly;
		self::$handle [$this->host] ['slave'] = $clients;
		//echo "最终属性：" . var_dump(self::$handle [$this->host] ['slave']) . "<br>";
	}
	
	/**
	 * 设置非集群redis
	 * Enter description here ...
	 * @param unknown_type $host
	 */
	public function setRedis($host, $db,$mors) {
		
		$items = self::$config [$host][$mors];
		$seed_indx = rand(0, count($items)-1);//随机选取一个
		$item  = $items[$seed_indx];
		$func = $item ['pconnect'] ? 'pconnect' : 'connect';

		$clients = new GRedis ( $item ['host'], $item ['port'], $func, $db );
		$clients::$hashsharding = C ( 'redis_hashsharding' );
		
		self::$handle [$this->host] [$mors] = $clients;
	}
	
	public function init($host) {
		if (empty ( $host ) || $host == 'master' || $host == 'slave') {
			$host = "appcluster";
		}
		$this->host = $host;
		$this->prefix = self::$config [$this->host] ['prefix'] ? self::$config [$this->host] ['prefix'] : substr ( md5 ( $_SERVER ['HTTP_HOST'] ), 0, 6 ) . '_';
		if ($this->prefix == "none")
			$this->prefix = '';
	
	}
	/**
	 * 检查主库
	 * Enter description here ...
	 * @param unknown_type $host
	 * @param unknown_type $db
	 */
	public function init_master($host, $db = '') {
		$this->init ( $host );
		$this->check_time('master');
		$this->redis_type = self::$config [$this->host] ['type'];
		//redis 和cluster del zDeleteRangeByScore 方法名字不同
		if($this->redis_type!='cluster')
		{
			$this->redis_type = 'redis';
		}
		if (empty ( self::$handle [$this->host] ['master'] )) {
			$type = self::$config [$this->host] ['type'];

			if ($type == "redis") {
				$this->setRedis ( $this->host, $db,'master' );
			} else {
				$name = 'set'.ucwords($type).'Master';
				call_user_func_array(array($this,$name),array());
			}
		}
	}
	
	/**
	 * 检查从库
	 * Enter description here ...
	 * @param unknown_type $host
	 * @param unknown_type $db
	 */
	public function init_slave($host, $db = '') {
		$this->init ( $host );
		$this->check_time('slave');
		$this->redis_type = 'redis';
		if (empty ( self::$handle [$this->host] ['slave'] )) {
			$type = self::$config [$this->host] ['type'];

			if ($type == "redis") {
				$this->setRedis ( $this->host, $db, 'slave' );
			} else {
				//集群配置中的丛库 需要readonly
				if ($type == "clusterclient" || $type == "cluster") {
					$this->need_readonly = 1;
				}
				$this->setClusterSlave ();
			}
		}
	}
	
	/**
	 * 检查连接持续时间
	 * @param unknown $mors
	 */
	private function check_time($mors)
	{
	    //连接创建时间
	    $createTime = intval ( @self::$handle['createtime'] [$this->host] [$mors]);
	    //如果大于链接 默认持续时间 则重新建立链接
	    if ((time () - $createTime) < 30) {
	        return ;
	    }
	    //检查链接
	    self::$handle[$this->host] [$mors]= null;
	    unset(self::$handle[$this->host] [$mors]);

	    self::$handle['createtime'] [$this->host] [$mors]= time();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $key
	 * @param unknown_type $type
	 * @param unknown_type $host
	 * @param unknown_type $db
	 * @param unknown_type $needserialize 默认序列化 0不序列化
	 * @param unknown_type $usemaster 默认从库 0查询使用主库 
	 */
	public function get($key, $type = '', $host = '', $db = '', $needserialize = 1) {
		$this->init_slave ( $host, $db );
		
		$this->type = $type;
		$value = self::$handle [$this->host] ['slave']->get ( $this->_key ( $key ) );
		return $needserialize == 1 ? unserialize ( $value ) : $value;
	}
	
	public function set($key, $value, $prefix = '', $expire = null, $host = '', $db = '', $needserialize = 1) {
		$this->init_master ( $host, $db );
		$this->type = $prefix;

		if ($needserialize) {
			$value = serialize ( $value );
		}

		if (is_int ( $expire )) {
			$result = self::$handle [$this->host] ['master']->setex ( $this->_key ( $key ), $expire, $value );
		} else {
			$result = self::$handle [$this->host] ['master']->set ( $this->_key ( $key ), $value );
		}
		return $result;
	}
	
	public function sset($key, $value, $prefix = '', $expire = null, $host = '', $db = '') {
		return $this->set ( $key, $value, $prefix, $expire, $host, $db, 0 );
	}
	
	public function set_noserialize($key, $value, $prefix = '', $expire = null, $host = '', $db = '') {
		return $this->set ( $key, $value, $prefix, $expire, $host, $db, 0 );
	}
	
	public function hset($name, $prefix, $data, $host = '', $db = '') {
		$this->init_master ( $host, $db );
		if (! is_array ( $data ) || empty ( $data ))
			return false;
		$this->type = $prefix;
		
		if (count ( $data ) == 1) {
			
			$r = self::$handle [$this->host] ['master']->ghset ( $this->_key ( $name ), key ( $data ), current ( $data ) );
		
		} elseif (count ( $data ) > 1) {
			
			$r = self::$handle [$this->host] ['master']->ghmset ( $this->_key ( $name ), $data );
		
		}
		return $r;
	}
	
	public function hget($name, $prefix, $key = null, $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		$this->type = $prefix;

		if ($key == '*' || is_null ( $key )) {
			return self::$handle [$this->host] ['slave']->hGetAll ( $this->_key ( $name ) );
		} elseif ($key == 'ALLKeys') {
			return self::$handle [$this->host] ['slave']->HKEYS ( $this->_key ( $name ) );
		} elseif (strpos ( $key, ',' ) != false) {
			return self::$handle [$this->host] ['slave']->ghmget ( $this->_key ( $name ), explode ( ',', $key ) );
		} else {
			return self::$handle [$this->host] ['slave']->ghget ( $this->_key ( $name ), $key );
		
		}
	}
	
	public function hdel($name, $prefix, $key = null, $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		$this->type = $prefix;
		if ($key == '*' || is_null ( $key )) {
			if (is_array ( $name )) {
				if ($this->redis_type == "reids") {
					return self::$handle [$this->host] ['master']->delete ( array_walk ( $array, array (self, '_key' ) ) );
				} else {
					return self::$handle [$this->host] ['master']->del ( array_walk ( $array, array (self, '_key' ) ) );
				}
			} else {
				if ($this->redis_type == "reids") {
					return self::$handle [$this->host] ['master']->delete ( $this->_key ( $name ) );
				} else {
					return self::$handle [$this->host] ['master']->del ( $this->_key ( $name ) );
				}
			}
		} else {
			if (is_array ( $name )) {
				foreach ( $name as $key => $value ) {
					return self::$handle [$this->host] ['master']->ghdel ( $this->_key ( $name ), $key );
				}
			} else {
				return self::$handle [$this->host] ['master']->ghdel ( $this->_key ( $name ), $key );
			}
		}
	}
	
	public function rm($key, $type = '', $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		$this->type = $type;
		if ($this->redis_type == "reids") {
			return self::$handle [$this->host] ['master']->delete ( $this->_key ( $key ) );
		} else {
			return self::$handle [$this->host] ['master']->del ( $this->_key ( $key ) );
		}
	
	}
	public function zDeleteByScore($key, $score1, $score2, $type = '', $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		$this->type = $type;
		if ($this->redis_type == "reids") {
			return self::$handle [$this->host] ['master']->zDeleteRangeByScore ( $this->_key ( $key ), $score1, $score2 );
		}
		else{
			return self::$handle [$this->host] ['master']->zremrangebyscore ( $this->_key ( $key ), $score1, $score2 );
		}
	}
	
    public function zsetAll($name, $prefix, $arr,$host='',$db=''){
        $this->init_master($host,$db);

        $this->type='';

        self::$handle [$this->host] ['master']->multi();
		$replies = array();
        if(is_array($arr) && count($arr) >0)
        {
            foreach($arr as $k => $v){
                self::$handle [$this->host] ['master']->zAdd($this->_key($name), $k, $v);
            }
        }

        $replies = self::$handle [$this->host] ['master']->exec();

        return $replies;
    }
	
	//批量删除指定的有序集合的值
	public function zDeleteByScoreALL($key, $arr, $type = '', $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		$this->type = $type;
		
		self::$handle [$this->host] ['master']->multi ();
		$replies = array();
		if (is_array ( $arr ) && count ( $arr ) > 0) {
			foreach ( $arr as $k => $v ) {
				if ($this->redis_type == "reids") {
					$replies[] = self::$handle [$this->host] ['master']->zDeleteRangeByScore ( $this->_key ( $key ), $v, $v );
				}
				else{
					$replies[] = self::$handle [$this->host] ['master']->zremrangebyscore ( $this->_key ( $key ), $v, $v );
				}
			}
		}
		
		$replies = self::$handle [$this->host] ['master']->exec ();
		
		return $replies;
	
	}
	public function zremrangebyrank($key, $start, $end, $db = '',$host = '' ) {
        $this->init_master ( $host, $db );

        return self::$handle [$this->host] ['master']->zremrangebyrank ( $this->_key ( $key ), $start, $end );
    }

    public function clear($host = '', $db = '') {
        $this->init_master ( $host, $db );

        return self::$handle [$this->host] ['master']->flushDB ();
    }

    public function zset($name, $prefix, $sorted, $data, $host = '', $db = '') {
        $this->init_master ( $host, $db );

        $this->type = '';
        return self::$handle [$this->host] ['master']->zAdd ( $this->_key ( $name ), $sorted, $data );
    }

    public function zrevrank($key, $value, $db = '', $host = '') {
        $this->init_master ( $host, $db );

        $this->type = '';
        return self::$handle [$this->host] ['master']->zrevrank ( $this->_key ( $key ), $value );
    }

	
	public function zget($name, $prefix, $sorted, $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		
		//清除不需要的前缀
		if ($prefix)
			$this->type;
		else
			$this->type = $prefix;
		
		return self::$handle [$this->host] ['slave']->ZRANGEBYSCORE ( $this->_key ( $name ), $sorted, $sorted );
	}
	
	/**
	 * 通过索引获取sortedset 值
	 * Enter description here ...
	 * @param unknown_type $name
	 * @param unknown_type $s
	 * @param unknown_type $e
	 * @param unknown_type $is_desc
	 * @param unknown_type $db
	 * @param unknown_type $host
	 */
	public function zgetbyse($name, $s, $e, $is_desc = true, $db = '', $host = '',$withscores=false) {

		$this->init_slave ( $host, $db );

		if ($is_desc) {

			$data = self::$handle [$this->host] ['slave']->ZREVRANGE ( $this->_key ( $name ), $s, $e ,$withscores);
		} else {
			$data = self::$handle [$this->host] ['slave']->ZRANGE ( $this->_key ( $name ), $s, $e ,$withscores);
		}
		return $data;
	}
	
	/**
	 * 通过score区间获取sortedset 值
	 * Enter description here ...
	 * @param unknown_type $name
	 * @param unknown_type $s
	 * @param unknown_type $e
	 * @param unknown_type $is_desc
	 * @param unknown_type $db
	 * @param unknown_type $host
	 */
	public function zgetbyscore($name, $s, $e, $is_desc = true, $db = '', $host = '',$withscores=[]) {
		$this->init_slave ( $host, $db );

		if($is_desc){
			$data = self::$handle [$this->host] ['slave']->ZREVRANGEBYSCORE( $this->_key ( $name ), $e,$s,$withscores);
		}else{
			$data = self::$handle [$this->host] ['slave']->ZRANGEBYSCORE ( $this->_key ( $name ), $s, $e,$withscores);
		}
		return $data;
	}
	
	//加入排序方式
	public function zzget($name, $prefix, $sorted, $is_desc = true, $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		
		//清除不需要的前缀
		if ($prefix)
			$this->type;
		else
			$this->type = $prefix;
		
		if ($is_desc) {
			return self::$handle [$this->host] ['slave']->ZRANGEBYSCORE ( $this->_key ( $name ), $sorted, $sorted );
		} else {
			return self::$handle [$this->host] ['slave']->ZRANGE ( $this->_key ( $name ), $sorted, $sorted );
		}
	}
	
	public function getMaxScore($source, $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		$this->type = '';
		
		$rssName = $this->_key ( $source );
		
		$data = self::$handle [$this->host] ['slave']->ZREVRANGE ( $rssName, 0, 0, true );
		
		if (is_array ( $data )) {
			foreach ( $data as $v ) {
				$r = $v;
				break;
			}
			return $r;
		} else {
			return false;
		}
	}
	
	public function ggetMaxScore($source, $prefix, $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		
		$this->type = $prefix;
		$rssName = $this->_key ( $source );
		
		$data = self::$handle [$this->host] ['slave']->ZREVRANGE ( $rssName, 0, 0, true );
		
		if (is_array ( $data )) {
			return $data;
		} else {
			return false;
		}
	}
	
	public function zpage($source, $rss, $pagesize = 5, $is_desc = true, $host = '', $db = '', $getchild = 1, $curpage = '') {
		$this->init_slave ( $host, $db );
		
		$this->type = '';
		$rssName = $this->_key ( $rss );
		
		$count = self::$handle [$this->host] ['slave']->ZCARD ( $rssName );

		//当前页码
		if ($curpage > 0) {
			$page = $curpage;
		} else {
			$page = ! empty ( $_REQUEST ['curpage'] ) ? $_REQUEST ['curpage'] : 1;
		}
		
		$page = intval ( $page );
		//页总数
		$pagesize = intval ( $pagesize );
		$pageCount = ceil ( $count / $pagesize );
		
		$hasmore = $pageCount - $page;
		
		if ($is_desc) {
			$data = self::$handle [$this->host] ['slave']->ZREVRANGE ( $rssName, (($page - 1) * $pagesize), (($page - 1) * $pagesize + ($pagesize - 1)) );
		} else {
			$data = self::$handle [$this->host] ['slave']->ZRANGE ( $rssName, (($page - 1) * $pagesize), (($page - 1) * $pagesize + ($pagesize - 1)) );
		}
		
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$redis_value = $this->hget ( $source, '', $value );
				if ($redis_value == "nil" || empty($redis_value) ) {
					//删除有序集对应字段
				//self::$handle[$this->host]['master']->ZREM($rssName, $value);
				} else {
					$temparray = array ();
					$temparray = unserialize ( $redis_value );
					$temparray ['hashfield'] = $value;
					
					$list [] = $temparray;
				}
			}
			$result ['hasmore'] = $hasmore > 0 ? true : false;
			$result ['data'] = $list;
		}
        $result ['total'] = $count;
		return $result;
	}
	
	// public function getchild($source ,$rss, $prefix, $pagesize=5 ,$is_desc=true,$db=''){
	// }
	//TODO tangjun 翻页有部分差异
	public function zzpage($source, $rss, $prefix, $pagesize = 5, $is_desc = true, $host = '', $db = '', $is_read = false) {
		
		$this->init_slave ( $host, $db );
		
		$this->type = '';
		
		//清除不需要的前缀
		if ($prefix)
			$this->type;
		else
			$this->type = $prefix;
		
		$rssName = $this->_key ( $rss );
		
		$count = self::$handle [$this->host] ['slave']->ZCARD ( $rssName );
		//当前页码
		$page = ! empty ( $_REQUEST ['curpage'] ) ? $_REQUEST ['curpage'] : 1;
		$page = intval ( $page );
		//页总数
		$pagesize = intval ( $pagesize );
		$pageCount = ceil ( $count / $pagesize );
		
		$hasmore = $pageCount - $page;
		
		if ($is_desc) {
			$data = self::$handle [$this->host] ['slave']->ZREVRANGE ( $rssName, (($page - 1) * $pagesize), (($page - 1) * $pagesize + ($pagesize - 1)) );
		} else {
			$data = self::$handle [$this->host] ['slave']->ZRANGE ( $rssName, (($page - 1) * $pagesize), (($page - 1) * $pagesize + ($pagesize - 1)) );
		}
		$result = array ();
		$list = array ();

		if (is_array ( $data )) {
			
			$id_arr = array ();
			
			//处理序列号的值
			foreach ( $data as $k => $vel ) {
				if($vel>0){

					$id_ar['id'] = $vel;
					$id_arr[] = $id_ar;

				}else{
					$arr = unserialize ( $vel );
					
					if ($is_read) {
						$id_a['id'] = $arr ['id'];
						$id_a['is_read'] = $arr ['is_read'];
						$id_arr [] = $id_a;
					} else {
						$id_['id'] = $arr ['id'];
						$id_arr[] = $id_;

					}

				}
			}

			foreach ( $id_arr as $key => $value ) {
				if ($is_read) {
					if ($value > 0) {
						if(!empty($value ['id']))
						{
							$redis_value = $this->hget ( $source, '', $value ['id'], $host, $db );
							if ($redis_value == "nil" || empty($redis_value)) {
								//删除有序集对应字段
							//self::$handle[$this->host]['master']->ZREM($rssName, $value['id']);
							} else {
								$source_list = unserialize($redis_value);
								//把hash的field值给予前端， 方便做单条删除
								$source_list ['message_id'] = $value ['id'];
								$source_list ['is_read'] = $value ['is_read'];
								$list [] = $source_list;
							}
						}
					}
				} else {
					if ($value > 0) {
						$redis_value = $this->hget ( $source, '', $value['id'], $host, $db );
						if ($redis_value == "nil" || empty($redis_value) ) {
							//删除有序集对应字段
						    //self::$handle[$this->host]['slave']->ZREM($rssName, $value);
						} else {
							$source_list = unserialize ( $redis_value);
							//把hash的field值给予前端， 方便做单条删除
							$source_list ['message_id'] = $value['id'];
							$list [] = $source_list;
						}
					}
				}
			}
			
			$result ['hasmore'] = $hasmore > 0 ? true : false;
			$result ['data'] = $list;
		}
		
		return $result;
	}
	
	public function zdel($name, $prefix, $member, $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		if (self::$handle [$this->host] ['master']->EXISTS ( $this->_key ( $name ) )) {
			return self::$handle [$this->host] ['master']->ZREM ( $this->_key ( $name ), $member );
		}
		else{
			return 1;
		}
	}
	
	public function exist($name, $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		return self::$handle [$this->host] ['master']->EXISTS ( $this->_key ( $name ) );
	}
	
	/**
	 * string 类型自增长
	 * Enter description here ...
	 * @param unknown_type $name
	 * @param unknown_type $db
	 */
	public function incr($name, $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		return self::$handle [$this->host] ['master']->INCRBY ( $this->_key ( $name ), 1 );
	}
	
	public function keys($key, $type = '', $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		
		$this->type = $type;
		$value = self::$handle [$this->host] ['slave']->keys ( $this->_key($key) );
		
		return $value;
	}
	
	public function rename($key1, $key2, $host = '', $db = '') {
		$this->init_master ( $host, $db );
		
		$value = self::$handle [$this->host] ['master']->rename ( $key1, $key2 );
		
		return $value;
	}
	
	public function _key($str) {
		//初始化redis key 不适用前缀
		if ($this->init_redis) {
			$this->prefix = '';
		}
		$__key = $this->prefix . $this->type . $str;
		$__key = str_replace ( '_', ':', $__key );
		return $__key;
	}
	//只取有序集合排序后数据
	public function jzpage($rss, $pagesize = 5, $is_desc = true, $host = '', $db = '', $getchild = 1, $curpage = '') {
		$this->init_slave ( $host, $db );
		
		$this->type = '';
		$rssName = $this->_key ( $rss );
		
		$count = self::$handle [$this->host] ['slave']->ZCARD ( $rssName );
		//当前页码
		if ($curpage > 0) {
			$page = $curpage;
		} else {
			$page = ! empty ( $_REQUEST ['curpage'] ) ? $_REQUEST ['curpage'] : 1;
		}
		
		$page = intval ( $page );
		//页总数
		$pagesize = intval ( $pagesize );
		$pageCount = ceil ( $count / $pagesize );
		
		$hasmore = $pageCount - $page;
		
		if ($is_desc) {
			$data = self::$handle [$this->host] ['slave']->ZREVRANGE ( $rssName, (($page - 1) * $pagesize), (($page - 1) * $pagesize + ($pagesize - 1)) );
		} else {
			$data = self::$handle [$this->host] ['slave']->ZRANGE ( $rssName, (($page - 1) * $pagesize), (($page - 1) * $pagesize + ($pagesize - 1)) );
		}
		
		return $data;
	}
	
	/*获取有序集合score*/
	public function zscore($rss, $value, $host = '', $db = '') {
		$this->init_slave ( $host, $db );
		$data = self::$handle [$this->host] ['slave']->zScore ( $this->_key ( $rss ), $value );
		return $data;
	}

	//获取有序集合的个数
	public function zcard($rss, $host = '', $db = ''){
		$this->init_slave ( $host, $db );

		return self::$handle [$this->host] ['slave']->ZCARD ( $this->_key ( $rss ) );
	}
	
	/**
	 * 
	 * 多keys 多值批量操作
	 * @param unknown_type $data
	 *[
	 *'testmhset1'=>['call'=>'hset','args'=>[1,1]],
	 *'testmhset2'=>['call'=>'set','args'=>[1]],
	 *'testmhset3'=>['call'=>'zadd','args'=>[1,1]],
	 *'testmhset4'=>['call'=>'hget','args'=>[1]],
	 *'testmhset5'=>['call'=>'hgetall','args'=>[]]
	 *]
	 * @param unknown_type $host
	 * @param unknown_type $master
	 */
	public function gpipe($data, $host = '', $master = true) {
		return $this->adapterkey ( $data, $host, $master, true );
	
	}
	
	/**
	 * 多key事务
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $host
	 * @param unknown_type $master
	 */
	public function gtran($data, $host = '', $master = true) {
		return $this->adapterkey ( $data, $host, $master, false );
	}
	
	private function adapterkey($data, $host = '', $master = true, $pipe = true) {
		if (! is_array ( $data ) || count ( $data ) == 0) {
			return  array();
		}
		if ($master) {
			$host_son = 'master';
			$this->init_master ( $host, 0 );
		} else {
			$host_son = 'slave';
			$this->init_slave ( $host, 0 );
		}
		$tmp = array ();
		foreach ( $data as $k => $v ) {
			$nk = $this->_key ( $k );
			$tmp [$nk] = $v;
		}
		
		if ($pipe) {
			return self::$handle [$this->host] [$host_son]->gmulti ( $tmp, true );
		} else {
			return self::$handle [$this->host] [$host_son]->gmulti ( $tmp );
		}
	}
    
	
	public static  function Dispose()
	{
		if(!empty(self::$config))
		{
			foreach (array_keys(self::$config) as $h)
			{
				foreach (array('master','slave') as $ms)
				{
					if(!is_null(self::$handle [$h] [$ms]))
					{
						self::$handle [$h] [$ms] = null;
					}
				}
			}
		}
	}

    //有序集合 分数自增
    public function zIncrBy($name, $sorted, $value,$host = '', $db = '') {

        $this->init_master ( $host, $db );

        if (self::$handle [$this->host] ['master']->EXISTS ( $this->_key ( $name ) )) {
            $r = self::$handle [$this->host] ['master']->zIncrBy($this->_key ( $name ),$sorted,$value);
        }else{
            $r = self::$handle [$this->host] ['master']->zAdd ( $this->_key ( $name ), $sorted, $value );
        }

        return $r;
    }

    //根据有序集合的value 删除对应的有序集合记录
    public function zDelete($name,$value,$host = '', $db = ''){
        $this->init_master ( $host, $db );

        $r = self::$handle [$this->host] ['master']->zDelete ( $this->_key ( $name ),$value );

        return $r;
    }

}