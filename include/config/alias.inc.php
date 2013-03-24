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
 * @version     $Id: alias.inc.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 导入别名定义
 */
 alias_import(array(	
	
	/* 插件库 */
	'Mysql'		    =>	PLUGIN_PATH. SEP . 'database' . SEP . 'Mysql.class.php',
    'AdvModel'		=>	PLUGIN_PATH. SEP . 'model' .    SEP . 'AdvModel.class.php',
	
	/* 模板库 */
	'ThinkTemplate'	=>	PLUGIN_PATH. SEP . 'tamplate'.  SEP . 'framework' . SEP . 'ThinkTemplate.class.php',
    'TemplateThink' =>	PLUGIN_PATH. SEP . 'tamplate'.  SEP . 'framework' . SEP . 'TemplateThink.class.php',
    'TagLib'		=>	PLUGIN_PATH. SEP . 'tamplate'.  SEP . 'framework' . SEP . 'TagLib.class.php',
    'TagLibCx'		=>	PLUGIN_PATH. SEP . 'tamplate'.  SEP . 'framework' . SEP . 'taglib'. SEP .'TagLibCx.class.php',
	
	/* 核心类库 */
	'DataBase'	    =>	CORE_PATH. SEP .'DataBase.class.php',
	'Cache'			=>	CORE_PATH. SEP .'Cache.class.php',
	'HtmlCache'		=>	CORE_PATH. SEP .'HtmlCache.class.php',
    'Cookie'		=>	CORE_PATH. SEP .'Cookie.class.php',
    'Session'		=>	CORE_PATH. SEP .'Session.class.php',
	'Service'		=>	CORE_PATH. SEP .'Service.class.php',
	'Business'		=>	CORE_PATH. SEP .'Business.class.php',
	'Model'			=>	CORE_PATH. SEP .'Model.class.php',
	'Controller'	=>	CORE_PATH. SEP .'Controller.class.php',
	'View'			=>	CORE_PATH. SEP .'View.class.php',
	'Widget'		=>	CORE_PATH. SEP .'Widget.class.php',
	'Api'		    =>	CORE_PATH. SEP .'Api.class.php',
 ));
