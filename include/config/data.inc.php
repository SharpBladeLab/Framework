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
 * @version     $Id: data.inc.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 数据缓存配置 
 */
 return array( 
    'DATA_CACHE_TIME'		=> -1,			// 数据缓存有效期
    'DATA_CACHE_COMPRESS'   => false,		// 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'		=> false,		// 数据缓存是否校验缓存
    'DATA_CACHE_TYPE'		=> 'File',		// 数据缓存类型,支持:File|DataBase|Apc|Memcache|Shmop|Sqlite| Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       => TEMP_PATH,	// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'		=> false,		// 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       => 1,			// 子目录缓存级别
 );
 