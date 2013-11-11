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
 * @version     $Id: config.inc.php 517 2013-07-30 09:03:18Z wgw $
 *
 * 系统文件配置信息
 */
 return array( 
	SYSINC_PATH. SEP . 'database.inc.php',   // 数据库配置
	SYSINC_PATH. SEP . 'access.inc.php',     // 游客访问控制黑/白名单
	SYSINC_PATH. SEP . 'alias.inc.php',      // 导入别名定义
	SYSINC_PATH. SEP . 'core.inc.php',       // 框架文件目录
	SYSINC_PATH. SEP . 'debug.inc.php',      // 调试模式配置文件  （如果项目有定义自己的调试模式配置文件，本文件无效）
	SYSINC_PATH. SEP . 'system.inc.php',	 // 系统配置文件
	SYSINC_PATH. SEP . 'app.inc.php',        // 应用配置信息  
	SYSINC_PATH. SEP . 'cookie.inc.php',     // Cookie配置信息  
	SYSINC_PATH. SEP . 'session.inc.php',    // SESSION配置信息  
	SYSINC_PATH. SEP . 'error.inc.php',      // 错误配置信息
	SYSINC_PATH. SEP . 'data.inc.php',       // 数据缓存配置
	SYSINC_PATH. SEP . 'tamplate.inc.php',	 // 模板引擎配置信息  
	SYSINC_PATH. SEP . 'page.inc.php',       // 分页配置信息 
	SYSINC_PATH. SEP . 'token.inc.php',      // 表单令牌验证配置信息  
	SYSINC_PATH. SEP . 'log.inc.php',        // 日志配置信息  
	SYSINC_PATH. SEP . 'html.inc.php',       // 静态缓存配置信息  
	SYSINC_PATH. SEP . 'url.inc.php',        // URL配置信息
	SYSINC_PATH. SEP . 'language.inc.php',   // 语言配置信息  
	SYSINC_PATH. SEP . 'upload.inc.php'      // 上传文件相关信息
 );
 