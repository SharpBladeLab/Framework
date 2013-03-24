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
 * @version     $Id: log.inc.php 259 2012-12-13 12:34:18Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 日志配置信息  
 */
 return array(
    'LOG_RECORD'        => true,                                                                     // 默认不记录日志
    'LOG_FILE_SIZE'     => 2097152,	                                                             // 日志文件大小限制
    'LOG_RECORD_LEVEL'  => array('EMERG','ALERT','CRIT','ERR','WARN','NOTIC','INFO','DEBUG','SQL'),  // 允许记录的日志级别
 );
 
