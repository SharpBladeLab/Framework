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
 * @version     $Id: core.inc.php 517 2013-07-30 09:03:18Z wgw $
 *
 * 框架文件目录
 */
 return array (
    CORE_PATH. SEP   . 'TiwerException.class.php',
    CORE_PATH. SEP   . 'Log.class.php',
	CORE_PATH. SEP   . 'DataBase.class.php',
    CORE_PATH. SEP   . 'Application.class.php',
    CORE_PATH. SEP   . 'Controller.class.php',
    CORE_PATH. SEP   . 'Model.class.php',
    CORE_PATH. SEP   . 'View.class.php',
	CORE_PATH. SEP   . 'Plugin.class.php',
	CORE_PATH. SEP   . 'Helper.class.php',
	PLUGIN_PATH. SEP . 'database'.SEP.'Mysql.class.php',
	SYSINC_PATH. SEP . 'alias.inc.php',
 );
