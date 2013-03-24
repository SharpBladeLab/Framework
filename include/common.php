<?php if(!defined('SITE_PATH')) exit();
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
 * @version     $Id: common.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 系统公共文件
 */

 /* 记录开始运行时间 */
 $GLOBALS['dc_beginTime'] = microtime(TRUE);

 /* 常用变量 */
 define('IN_SYS', true);
 define('SEP', DIRECTORY_SEPARATOR);

 /* 载入掘客类并初始化 */
 include_once SITE_PATH. SEP .'include'.SEP.'class'.SEP.'Route.class.php';
 Route::_init();

 /* 路径定义 */
 if( !defined('INCL_PATH') )       define('INCL_PATH',  SITE_PATH. SEP .'include');      // 网站核心
 if( !defined('DATAS_PATH') )      define('DATAS_PATH', SITE_PATH. SEP .'data');         // 数据文件
 if( !defined('APPS_PATH') )       define('APPS_PATH',  SITE_PATH. SEP .'application');  // 网站应用
 if( !defined('SKIN_PATH') )       define('SKIN_PATH',  SITE_PATH. SEP .'skin');         // 网站皮肤
 if( !defined('API_PATH') )        define('API_PATH',   SITE_PATH. SEP .'api');          // 网站接口
 if( !defined('TPLS_PATH') )       define('TPLS_PATH',  SITE_PATH. SEP .'tamplate');     // 模板文件

 /* 核心目录 */
 if( !defined('SYSINC_PATH') )     define('SYSINC_PATH',   INCL_PATH. SEP .'config');    // 配置文件
 if( !defined('FUNC_PATH') )       define('FUNC_PATH',     INCL_PATH. SEP .'function');  // 函 数 库
 if( !defined('CORE_PATH') )       define('CORE_PATH',     INCL_PATH. SEP .'class');     // 核心框架
 if( !defined('WIDGET_PATH') )     define('WIDGET_PATH',   INCL_PATH. SEP .'widgets');   // 网页部件
 if( !defined('PLUGIN_PATH') )     define('PLUGIN_PATH',   INCL_PATH. SEP .'plugins');   // 系统插件
 if( !defined('BLLS_PATH') )       define('BLLS_PATH',     INCL_PATH. SEP .'business');  // 商务模型
 if( !defined('SERVICES_PATH') )   define('SERVICES_PATH', INCL_PATH. SEP .'services');  // 系统服务

 /* 数据目录 */
 if( !defined('ATTACH_PATH') )     define('ATTACH_PATH',   DATAS_PATH. SEP .'attach');   // 附件目录
 if( !defined('BACKUP_PATH') )     define('BACKUP_PATH',   DATAS_PATH. SEP .'backup');   // 备份目录
 if( !defined('COMPILE_PATH') )    define('COMPILE_PATH',  DATAS_PATH. SEP .'compile');  // 编译目录
 if( !defined('LENGUAGE_PATH') )   define('LENGUAGE_PATH', DATAS_PATH. SEP .'language'); // 语言目录
 if( !defined('LOGS_PATH') )       define('LOGS_PATH',     DATAS_PATH. SEP .'log');      // 日志目录
 if( !defined('SESSION_PATH') )    define('SESSION_PATH',  DATAS_PATH. SEP .'session');  // 会话目录
 if( !defined('UPLOAD_PATH') )     define('UPLOAD_PATH',   DATAS_PATH. SEP .'uploads');  // 上传目录
 if( !defined('SYSDAT_PATH') )     define('SYSDAT_PATH',   DATAS_PATH. SEP .'dat');      // 数据目录
 if( !defined('TEMPS_PATH') )      define('TEMPS_PATH',    DATAS_PATH. SEP .'temp');     // 临时目录
 if( !defined('APP_PATH') )	       define('APP_PATH' ,     APPS_PATH.  SEP . APP_NAME);  // 应用目录

 /* 运行目录 */
 define('CACHE_DIR',	'template');  // 模板缓存
 define('HTML_DIR',		'html');      // 静态文件
 define('LOG_DIR',		'logs');      // 日志文件
 define('TEMP_DIR',		'temp');      // 临时文件

 /* MVC目录 */
 define('MODEL_DIR',	'model');      // 模型层 M
 define('VIEW_DIR',	    'view');       // 视图层 V
 define('CONTROL_DIR',	'controller'); // 控制器 C
 define('LANG_DIR',		'language');   // 语言层 L

 /* 应用目录 */
 define('MODEL_PATH',     APP_PATH. SEP . MODEL_DIR. SEP);    // 模型目录
 define('VIEW_PATH',	  APP_PATH. SEP . VIEW_DIR. SEP);     // 视图目录
 define('CONTROL_PATH',	  APP_PATH. SEP . CONTROL_DIR. SEP);  // 控制器目录
 define('LANG_PATH',      APP_PATH. SEP . LANG_DIR. SEP);     // 语言文件

  /* 配置文件 */
 define('COMMON_FILE',	  APP_PATH. SEP .'common.php');       // 公共目录
 define('CONFIG_FILE',	  APP_PATH. SEP .'config.php');       // 配置文件
 define('APPINFO_FILE',	  APP_PATH. SEP .'app.php');          // 应用信息

 /* 运行时路径 */
 define('LOG_PATH',		  LOGS_PATH. SEP .APP_NAME. SEP);     // 日志文件
 define('TEMP_PATH',	  TEMPS_PATH. SEP .APP_NAME. SEP);    // 临时文件
 define('CACHE_PATH',	  COMPILE_PATH. SEP .CACHE_DIR.SEP);  // 模板编译
 define('DATA_PATH',	  TEMP_PATH. SEP .'data' .  SEP);     // 数据文件
 define('HTML_PATH',	  DATAS_PATH. SEP .HTML_DIR. SEP);    // 访问路径

 /* 插件扩展路径 */
 define('EXPANSION_PATH', PLUGIN_PATH. SEP);

 /* 编译配置 */
 if( !defined('COMPILE_NONE')) define('COMPILE_NONE', true);
 if( !defined('RUNTIME_PATH')) define('RUNTIME_PATH', COMPILE_PATH.SEP.APP_NAME);

 /* 创建运行时目录 */
 if( !is_dir(RUNTIME_PATH) ) mkdir(RUNTIME_PATH, 0777, true);

 /* 域名编译文件 */
 if( COMPILE_NONE ) {
	//empty($_SERVER['HTTP_HOST']) || $DomainMD5 = md5($_SERVER['HTTP_HOST'] . $_SERVER['SERVER_SOFTWARE'] . $_SERVER['SERVER_ADDR']);
	empty($_SERVER['HTTP_HOST']) || $DomainMD5 = md5($_SERVER['HTTP_HOST'] . $_SERVER['SERVER_SOFTWARE'] );
 }

 /* 检查编译文件 */
 if( COMPILE_NONE && is_file(RUNTIME_PATH . SEP . $DomainMD5 .'_build.php') ) {
    $result =  require RUNTIME_PATH . SEP . $DomainMD5 .'_build.php';
    config($result);

    /* 自动设置为运行模式 */
    define('RUNTIME_MODEL',true);

 } else {

	/* 加载框架核心编译缓存,不存在则重新编译 */
    if( is_file(RUNTIME_PATH . SEP . 'compile.build.php')) {
        require RUNTIME_PATH . SEP . 'compile.build.php';
    } else {
        require FUNC_PATH . SEP . 'compile.func.php';
        build_runtime();
    }
 }

 /* 记录加载文件时间 */
 $GLOBALS['dc_loadTime'] = microtime(TRUE);
