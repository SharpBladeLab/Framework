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
 * @version     $Id: debug.inc.php 517 2013-07-30 09:03:18Z wgw $
 *
 * 调试模式配置文件  （如果项目有定义自己的调试模式配置文件，本文件无效）
 */
 return array(
    'SHOW_RUN_TIME'    => false,  // 运行时间显示
    'SHOW_ADV_TIME'    => false,  // 显示详细的运行时间
    'SHOW_DB_TIMES'    => false,  // 显示数据库查询和写入次数
    'SHOW_CACHE_TIMES' => false,  // 显示缓存操作次数
    'SHOW_USE_MEM'     => false,  // 显示内存开销
    'SHOW_PAGE_TRACE'  => false,  // 显示页面Trace信息 由Trace文件定义和Action操作赋值
	'SHOW_ERROR_MSG'   => true,   // 显示错误信息
    'APP_FILE_CASE'    => true,   // 是否检查文件的大小写 对Windows平台有效  	
 );
