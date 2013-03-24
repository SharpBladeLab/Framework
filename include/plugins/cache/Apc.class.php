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
 * @version     $Id: Apc.class.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * Alternative PHP Cache (apcCache)类
 */
 class Apc {

	/**
	 * 构造器
	 *
	 * @access public
	 */
	public function __construct() {
		$this->apc();
	}
	public function apc() {
		if( !extension_loaded('apc') ) {
			Helper::createException('Apc(Alternative PHP Cache)扩展没有开启!', false);		
		}
	}
	
	/**
	 * 设置一个缓存变量
	 *
	 * @param string $key      缓存Key
	 * @param mixed  $value    缓存内容
	 * @param int    $expire   缓存时间(秒)
	 * 
	 * @return boolean         是否缓存成功
	 * @access public 
	 */
	public function set($key, $value, $expire = 60) {
		return apc_store($key, $value, $expire);
	}

	/**
	 * 获取一个已经缓存的变量
	 *
	 * @param String $key  缓存Key
	 * @return mixed       缓存内容
	 * 
	 * @access public
	 */
	public function get($key) {
		$value = apc_fetch($key);
		return 	isset($value) ? $value : false;	
	}

	/**
	 * 删除一个已经缓存的变量
	 *
	 * @param  string $key
	 * @return boolean       是否删除成功
	 * 
	 * @access public
	 */
	public function del($key) {
		return apc_delete($key);
	}

	/**
	 * 删除全部缓存变量
	 *
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function delAll() {
		return apc_clear_cache();
	}

	/**
	 * 检测是否存在对应的缓存
	 *
	 * @param string $key   缓存Key
	 * @return boolean      是否存在key
	 * @access public
	 */
	public function has($key) {
		return( apc_fetch($key) === false ? false : true );
	}
 }
