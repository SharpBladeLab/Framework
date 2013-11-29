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
 * @version     $Id: instance.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 实例化函数库
 */
 
/**
 * 实例化系统层（有缓存功能）
 *
 * @param string $name   名称
 * @param array  $params 参数
 * @param string $domain 服务类型(Service/Business)
 *
 * @return object
 */
 function Instantiation($name, $params = array(), $domain='Service') {

	/* 静态变量,缓存 */
    static $_service = array();
        

	/* 是否已经实体化过 */
    if(isset($_service[$domain.'_'.APP_NAME.'_'.$name])) {
        return $_service[$domain.'_'.APP_NAME.'_'.$name];
	}
	$class = $name.$domain;

    
	
	/* 文件是否存在 */
	if( file_exists( APP_PATH.strtolower($domain).SEP.$class.'.class.php')) {
		require_cache( APP_PATH.strtolower($domain).SEP.$class.'.class.php');		
	} else {
		if($domain == 'Business' ) {
			require_cache( BLLS_PATH . SEP . $class.'.class.php' );
		}
		if($domain == 'Plugin') {
			require_cache( PLUGIN_PATH.SEP.strtolower($name).SEP.$name.'.class.php' );
		}
	}
    
    
		
	/* 请求的类不存在，记录日志或抛出异常 */
	if( class_exists($class) ) {		
		$obj =  new $class($params);
		$_service[$domain.'_'.APP_NAME.'_'.$name] =  $obj;		
		return $obj;
	
	} elseif (class_exists($name)) {
		$obj =  new $name($params);
		$_service[$domain.'_Plugin_'.APP_NAME.'_'.$name] =  $obj;
		return $obj;
		
	} else {
		Helper::createException(Helper::createLanguage('_CLASS_NOT_EXIST_').':'.$class);
	}
 }
 
 /**
  * 取得对象实例(支持调用类的静态方法)
  *
  * @param string $name    类名
  * @param string $method  方法
  * @param array  $args    参数
  *
  * @return object
  */
 function get_instance_of($name, $method = '', $args = array() ) {
 
    static $_instance = array();
	
	/* 生成唯一标实 */
    $identify = empty($args) ? $name.$method : $name.$method.to_guid_string($args);	
    if (!isset($_instance[$identify])) {		
        if(class_exists($name)){		
            $o = new $name();			
            if(method_exists($o, $method)){
                if(!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                } else {
                    $_instance[$identify] = $o->$method();
                }
            } else {
                $_instance[$identify] = $o;
			}
        } else {		
			/* 不存在该类 */
            halt(Helper::createLanguage('_CLASS_NOT_EXIST_').':'.$name);
		}
    }
    return $_instance[$identify];
 }
