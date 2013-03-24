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
 * @version     $Id: Cookie.class.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * Cookie管理类
 */
 class Cookie extends Framework
 {
    /**
	 * 判断Cookie是否存在
	 *
	 * @prarm string  $name  缓存名称
	 *
	 * @return boolean
  	 */
    static function is_set($name) {
        return isset($_COOKIE[config('COOKIE_PREFIX').$name]);
    }

    /**
	 * 获取某个Cookie值
	 * 
	 * @prarm string  $name 缓存名称
	 *
	 * @retrun string 值
	 */
    static function get($name) {
	
        $value = $_COOKIE[config('COOKIE_PREFIX').$name];
        $value = unserialize(base64_decode($value));
		
        return $value;
    }

    /**
	 * 设置某个Cookie值
	 *
	 * @prarm string $name    名称
	 * @prarm string $value   值
	 * @prarm string $expire  过期时间
	 * @prarm string $path    路径
	 * @prarm string $domain  域名
	 */
    static function set($name, $value, $expire='', $path='', $domain='') {
        
		/* 过期时间 */
		if($expire=='') {
            $expire =   config('COOKIE_EXPIRE');
        }
		
		/* 路径 */
        if(empty($path)) {
            $path = config('COOKIE_PATH');
        }
		
		/* 域名 */
        if(empty($domain)) {
            $domain =   config('COOKIE_DOMAIN');
        }
		
        $expire =   !empty($expire)?    time()+$expire   :  0;
        $value   =  base64_encode(serialize($value));
		
		/* 设置 */
        setcookie(config('COOKIE_PREFIX').$name, $value, $expire, $path, $domain);
        $_COOKIE[config('COOKIE_PREFIX').$name]  =   $value;
    }

    /**
	 * 删除某个Cookie值
	 * 
	 * @param String $name 名称
	 *
	 * @return void
 	 */
    static function delete($name) {
        Cookie::set($name, '', time()-3600);
        unset($_COOKIE[config('COOKIE_PREFIX').$name]);
    }

    /**
	 * 清空Cookie值
	 * 
	 * @return void
	 */
    static function clear() {
        unset($_COOKIE);
    }
 }
