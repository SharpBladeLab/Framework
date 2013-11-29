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
 * @version     $Id: tiwer.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 系统定义文件
 */

//[TIWER]

 if (!defined('IN_SYS')) exit();

 /* 系统信息 */
 if( version_compare(PHP_VERSION,'6.0.0','<') ) {
    @set_magic_quotes_runtime(0);
    define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
 }
 
 /* PHP运行环境 */
 define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
 define('IS_CGI',          substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
 define('IS_WIN',          strstr(PHP_OS, 'WIN') ? 1 : 0 );
 define('IS_CLI',          PHP_SAPI=='cli'? 1   :   0);
 
 /* 支持的URL模式 */
 define('URL_COMMON',   0);   // 普通模式
 define('URL_PATHINFO', 1);   // PATHINFO模式
 define('URL_REWRITE',  2);   // REWRITE模式
 define('URL_COMPAT',   3);   // 兼容模式
 
 /* 非CLI（Command Line Interface）模式下运行 */
 if( !IS_CLI ) {
 
    /* 当前文件名 */ 
    if( !defined('_PHP_FILE_') ) {	
        if( IS_CGI ) {
            /* CGI/FASTCGI模式下 */
            $_temp  = explode('.php', $_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"], '', $_temp[0].'.php'), '/'));			
        } else {
            define('_PHP_FILE_',  rtrim($_SERVER["SCRIPT_NAME"], '/'));
        }
    }   
	
	
    if( !defined('__ROOT__') ) {	
        /* 网站URL根目录 */
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        } else {
            $_root = dirname(_PHP_FILE_);
        }		
		define('__ROOT__',  (($_root=='/' || $_root=='\\') ? '': rtrim($_root,'/')));
    }  
	
	
    if( !defined('HTTPHOST') )   define('HTTPHOST',   'http://'.$_SERVER['HTTP_HOST']);
	if( !defined('SITE_URL') )   define('SITE_URL',   HTTPHOST.__ROOT__);
	if( !defined('__UPLOAD__') ) define('__UPLOAD__', SITE_URL.'/data/uploads');	
 }

 /* 版本信息 */
 define('Tiwer', '0.1');

//[/TIWER]

 /* 记录内存初始使用 */
 if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();

 /* 为了方便导入第三方插件类库 设置目录到include_path */
 set_include_path( get_include_path() . PATH_SEPARATOR . EXPANSION_PATH . PATH_SEPARATOR . CORE_PATH .SEP);
