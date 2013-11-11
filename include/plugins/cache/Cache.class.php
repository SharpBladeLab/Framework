<?php if(!defined('IN_SYS')) exit();
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
 * @version     $Id: Cache.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * 缓存插件
 */
 class Cache extends Plugin {
	
	/* 插件版本 */
 	protected  $version = '0.1';
 	
 	/**
     * 构造函数
     *
     * @access public
     */
 	public function __construct($param = null) {
 		
 		/* 什么类型的缓存类 */
 		$type = $param['type'];
 		if (empty($type)) $type = 'Xcache';
 		unset($param['type']);
 		
 		static $instance = null;
		if( empty($instance) ) {
			
			/* 载入文件 */
			$file = PLUGIN_PATH. SEP. 'cache'. SEP. $type.'.class.php';			
			include_once($file);
			
			/* 新建对象 */
			if( isset($param) ) {
				$instance = new $type($param);
			} else {
				$instance = new $type();
			}
		}
		return $instance;
 	} 	
 	
 }
