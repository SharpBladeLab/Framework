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
 * @version     $Id: html.inc.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 静态缓存配置信息  
 */
 return array( 
    'HTML_CACHE_ON'			=> false,     // 默认关闭静态缓存
    'HTML_CACHE_TIME'		=> 60,        // 静态缓存有效期
    'HTML_READ_TYPE'        => 0,         // 静态缓存读取方式  < 0 readfile 1 redirect >
    'HTML_FILE_SUFFIX'      => '.shtml',  // 默认静态文件后缀
 );
 