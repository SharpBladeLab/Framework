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
 * @version     $Id: cookie.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * Cookie函数库
 */
 
/**
 *
 * Cookie 设置、获取、清除 (支持数组或对象直接设置) 2009-07-9
 *
 * 1 获取cookie: cookie('name')
 * 2 清空当前设置前缀的所有cookie: cookie(null)
 * 3 删除指定前缀所有cookie: cookie(null,'think_') | 注：前缀将不区分大小写
 * 4 设置cookie: cookie('name','value') | 指定保存时间: cookie('name','value',3600)
 * 5 删除cookie: cookie('name',null)
 *
 * $option 可用设置prefix,expire,path,domain
 * 支持数组形式:cookie('name','value',array('expire'=>1,'prefix'=>'think_'))
 * 支持query形式字符串:cookie('name','value','prefix=tp_&expire=10000')
 * 2010-1-17 去掉自动序列化操作，兼容其他语言程序。
 */
 function cookie($name,$value='',$option=null) {

 	/* 默认设置 */
    $config = array(
        'prefix' => config('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => config('COOKIE_EXPIRE'), // cookie 保存时间
        'path'   => config('COOKIE_PATH'),   // cookie 保存路径
        'domain' => config('COOKIE_DOMAIN'), // cookie 有效域名
    );

    /* 参数设置(会覆盖黙认设置) */
    if (!empty($option)) {
        if (is_numeric($option)) {
            $option = array('expire'=>$option);
        }else if( is_string($option) ) {
            parse_str($option,$option);
    	}
    	$config	=	array_merge($config,array_change_key_case($option));
    }

    /* 清除指定前缀的所有cookie */
    if( is_null($name) ) {
       if( empty($_COOKIE) ) return;
       /* 要删除的cookie前缀，不指定则删除config设置的指定前缀 */
       $prefix = empty($value)? $config['prefix'] : $value;
	   
	   /* 如果前缀为空字符串将不作处理直接返回 */
       if (!empty($prefix)) {
           foreach($_COOKIE as $key=>$val) {
               if (0 === stripos($key,$prefix)){
                    setcookie($_COOKIE[$key],'',time()-3600,$config['path'],$config['domain']);
                    unset($_COOKIE[$key]);
               }
           }
       }
       return;
    }
    $name = $config['prefix'].$name;

    if (''===$value) {
		/* 获取指定Cookie */
        return isset($_COOKIE[$name]) ? ($_COOKIE[$name]) : null;
		
    } else {
        if (is_null($value)) {
            setcookie($name,'',time()-3600,$config['path'],$config['domain']);
			/* 删除指定cookie */
            unset($_COOKIE[$name]);
			
        } else {
            /* 设置cookie */ 
            $expire = !empty($config['expire'])? time()+ intval($config['expire']):0;
            setcookie($name,($value),$expire,$config['path'],$config['domain']);
        }
    }
 }

 function dc_cookie($name,$value='',$option=null) {
 
    /* 默认设置 */
    $config = array(
        'prefix' => config('COOKIE_PREFIX'), // cookie 名称前缀
        'expire' => config('COOKIE_EXPIRE'), // cookie 保存时间
        'path'   => config('COOKIE_PATH'),   // cookie 保存路径
        'domain' => config('COOKIE_DOMAIN'), // cookie 有效域名
    );

    /* 参数设置(会覆盖黙认设置) */
    if (!empty($option)) {
        if (is_numeric($option)) {
            $option = array('expire'=>$option);
        }else if( is_string($option) ) {
            parse_str($option,$option);
    	}
    	$config	=	array_merge($config,array_change_key_case($option));
    }

    /* 清除指定前缀的所有cookie */
    if (is_null($name)) {
       if (empty($_COOKIE)) return;
	   
       /* 要删除的cookie前缀，不指定则删除config设置的指定前缀 */
       $prefix = empty($value)? $config['prefix'] : $value;
	   
	   /* 如果前缀为空字符串将不作处理直接返回 */
       if (!empty($prefix)) {
           foreach($_COOKIE as $key=>$val) {
               if (0 === stripos($key,$prefix)){
                    setcookie($_COOKIE[$key],'',time()-3600,$config['path'],$config['domain']);
                    unset($_COOKIE[$key]);
               }
           }
       }	   
       return;
    }
	
    $name = $config['prefix'].$name;

    if (''===$value){
		/* 获取指定Cookie */
        return isset($_COOKIE[$name]) ? ($_COOKIE[$name]) : null;
    } else {
        if (is_null($value)) {
            setcookie($name,'',time()-3600,$config['path'],$config['domain']);
			/* 删除指定cookie */
            unset($_COOKIE[$name]);
			
        } else {
            /* 设置cookie */
            $expire = !empty($config['expire'])? time()+ intval($config['expire']):0;
            setcookie($name,($value),$expire,$config['path'],$config['domain']);
        }
    }
 }
 