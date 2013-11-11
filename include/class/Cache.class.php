<?php
/**
 * The model class file of Tiwer Developer Framework.
 *
 * Tiwer Developer Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Tiwer Developer Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with Tiwer Developer Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright (C) 2007-2011 Tiwer Studio. All Rights Reserved.
 * @author      wgw8299 <wgw8299@gmail.com>
 * @package     Tiwer Developer Framework
 * @version     $Id: Cache.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * 缓存管理类
 */
 class Cache extends Framework
 {
    /**
	 * 是否连接
     *
     * @var string
	 *
     * @access protected
     */
    protected $connected;

    /**
     * 操作句柄
     *
     * @var string
	 *
     * @access protected
     */
    protected $handler;

    /**
     *
     * 缓存存储前缀
     *
     * @var string
	 *
     * @access protected
     */
    protected $prefix = '~@';

    /**
     * 缓存连接参数
     *
     * @var integer
	 *
     * @access protected
     */
    protected $options = array();

    /**
     * 缓存类型
     *
     * @var integer
	 *
     * @access protected
     */
    protected $type;

    /**
     * 缓存过期时间
     *
     * @var integer
	 *
     * @access protected
     */
    protected $expire;

    /**
     * 连接缓存
     *
     * @access public
     *
     * @param string $type 缓存类型
     * @param array  $options  配置数组
     *
     * @return object
     *
     * @throws TiwerException
     */
    public function connect( $type = '', $options = array()) {
	
        if(empty($type)) $type = config('DATA_CACHE_TYPE');
		
        $cachePath = PLUGIN_PATH . SEP . 'cache' . SEP;
		$cacheClass = ucwords(strtolower(trim($type))).'Cache';		
		
		/* 载入对应的缓存类 */
        require_cache($cachePath.$cacheClass.'.class.php');
		
		/* 是否存在该类缓存类 */
        if(class_exists($cacheClass)) {
            $cache = new $cacheClass($options);
        } else {
            Helper::createException(Helper::createLanguage('_CACHE_TYPE_INVALID_').' : '. $type );
		}
        return $cache;
    }
	
	/**
	 * Get 
	 */
    public function __get($name) {
        return $this->get($name);
    }
	
	/**
	 * Set
	 */
    public function __set($name,$value) {
        return $this->set($name,$value);
    }
	
	/**
	 * unset
	 */
    public function __unset($name) {
        $this->rm($name);
    }
	
	/**
	 * setOptions
	 */
    public function setOptions($name,$value) {
        $this->options[$name] = $value;
    }
	
	/**
	 * setOptions
	 */
    public function getOptions($name) {
        return $this->options[$name];
    }
	
    /**
     * 取得缓存类实例
     *
     * @static
     * @access public
     *
     * @return mixed
     */
    static function getInstance() {
	
       $param = func_get_args();	   
        return get_instance_of(__CLASS__, 'connect', $param);
    }

    /**
	 * 读取缓存次数
	 */
    public function Q($times = '' ) {
	
        static $_times = 0;		
        if( empty($times) ) {
            return $_times;
        } else {
            $_times++;
		}
    }

    /**
	 * 写入缓存次数
	 */
    public  function W($times='') {
        
		static $_times = 0;
		
        if( empty($times) ) {
            return $_times;
        } else {
            $_times++;
		}
    }
}
