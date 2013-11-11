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
 * @version     $Id: json.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 编码函数库
 */ 
 
 /* php 5.2 以下版本的兼容函数 */
 if (!function_exists('json_encode')) {	
	
 	function format_json_value(&$value) {
		if(is_bool($value)) {
			$value = $value?'true':'false';
		}elseif(is_int($value)) {
			$value = intval($value);
		}elseif(is_float($value)) {
			$value = floatval($value);
		}elseif(defined($value) && $value === null) {
			$value = strval(constant($value));
		}elseif(is_string($value)) {
			$value = '"'.addslashes($value).'"';
		}
		return $value;
	}
	
	function json_encode($data) {
		if(is_object($data)) {
			/* 对象转换成数组 */
			$data = get_object_vars($data);
			
		}else if(!is_array($data)) {
			/* 普通格式直接输出 */ 
			return format_json_value($data);
				
		}
		
		/* 判断是否关联数组 */
		if(empty($data) || is_numeric(implode('',array_keys($data)))) {
			$assoc  =  false;
		} else {
			$assoc  =  true;
		}
		
		/* 组装 Json字符串 */
		$json = $assoc ? '{' : '[' ;
		foreach($data as $key=>$val) {
			if(!is_null($val)) {
				if($assoc) {
					$json .= "\"$key\":".json_encode($val).",";
				}else {
					$json .= json_encode($val).",";
				}
			}
		}
		
		if(strlen($json)>1) {
			
			/* 加上判断 防止空数组 */
			$json  = substr($json,0,-1);
		}
		
		$json .= $assoc ? '}' : ']' ;
		return $json;
	}
 }
 
 if (!function_exists('json_decode')) {
	
	/**
	 * 对 JSON 格式的字符串进行编码
	 *
	 * @param string  $json  待解码的 json string 格式的字符串。
	 * @param boolean $assoc 当该参数为 TRUE 时，将返回 array 而非 object 。 
	 *
	 * @return mixed  返回一个对象或boolean真的、数组
	 */
	function json_decode($json, $assoc=false) {
	
		/* 目前不支持二维数组或对象 */
		$begin  =  substr($json,0,1) ;
		
		if(!in_array($begin,array('{','['))) {
			/* 不是对象或者数组直接返回 */ 
			return $json;
		}
		
		$parse = substr($json,1,-1);
		$data  = explode(',',$parse);
		
		if($flag = $begin =='{' ) {
			/* 转换成PHP对象 */
			$result   = new stdClass();
			foreach($data as $val) {
				$item    = explode(':',$val);
				$key =  substr($item[0],1,-1);
				$result->$key = json_decode($item[1],$assoc);
			}
			if($assoc)
				$result   = get_object_vars($result);
		} else {
			/* 转换成PHP数组 */
			$result   = array();
			foreach($data as $val)
				$result[]  =  json_decode($val,$assoc);
		}
		return $result;
	}
 }
