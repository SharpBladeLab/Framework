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
 * @version     $Id: kernel.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 核心函数库
 */ 

/**
 * 获取和设置配置定义
 *
 * @param string $name   配置名称
 * @param mixed  $value  值
 *
 * @return mixed
 */ 
 function config($name=null, $value=null) {
 
    static $_config = array();
    
	/* 无参数时获取所有 */
    if( empty($name) ) return $_config;
   
    /* 优先执行设置获取或赋值 */ 
    if ( is_string($name) ) {
	
		/* 名称过滤 */
		if ( !strpos($name, '.') ) {
            $name = strtolower($name);
			
			/* 值过滤 */
            if (is_null($value))
                return isset($_config[$name])? $_config[$name] : null;
            
			/* 设置配置定义 */
			$_config[$name] = $value;
            return;
        }
		
        /* 二维数组设置和获取支持 */ 
        $name = explode('.',$name);
        $name[0] = strtolower($name[0]);
        
		if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        
		$_config[$name[0]][$name[1]] = $value;
        return;
    }
	
    /* 批量设置 */ 
    if(is_array($name))
        return $_config = array_merge($_config,array_change_key_case($name));
	
	/* 避免非法参数 */ 
    return null;
 }
 
/**
 * 处理标签
 *
 * @param string $name  名称
 * @param array  $params 参数
 *
 * @return boolean 
 */
 function tag($name, $params=array()) {
    $tags   =  config('_tags_.'.$name);	
    if( $tags ) {
        foreach ($tags as $key=>$call) {	
			/*  检测参数是否为合法的可调用结构  */
            if( is_callable($call) ) {
                $result = call_user_func_array($call,$params);
			}
        }
        return $result;
    }
    return false;
 }

/**
 * 根据PHP各种类型变量生成唯一标识号
 *
 * @param mixed $mix 类型变量
 *
 * @return string 唯一标识号
 */
 function to_guid_string( $mix ) {
	
    if(is_object($mix) && function_exists('spl_object_hash')) {
        /* 对象 */
		return spl_object_hash($mix);
		
    } elseif(is_resource($mix)){
        /* 资源文件 */
		$mix = get_resource_type($mix).strval($mix);
		
    } else {
	
		/* 其他类型时序列化数据 */
        $mix = serialize($mix);
    }
	
	/* MD5 */
    return md5($mix);
 }

 if (!function_exists('property_exists')) {	
	/**
	 * 判断对象的属性是否存在 PHP5.1.0以上已经定义
	 *
	 * @param  object $class     对象实例
	 * @param  string $property 属性名称
	 *
	 * @return boolen
	 */
	function property_exists($class, $property) {
		if (is_object($class))
			$class = get_class($class);
		return array_key_exists($property, get_class_vars($class));
	}
 }
