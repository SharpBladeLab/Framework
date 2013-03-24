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
 * @version     $Id: Application.class.php 229 2012-12-11 14:29:02Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 应用程序类 - (执行应用过程管理)
 */
 class Application
 {
    /**
     * 应用程序初始化
     *
     * @access public
     *
     * @return void
     */
    static public function init() {

		/* 系统全局变量 */
        global $dc;

        /* 设定错误处理 */
        set_error_handler(array('Application', 'appError'));

		/* 设定异常处理 */
		set_exception_handler(array('Application','appException'));
		
		
        //[TIWER]
        /* 检查项目是否编译过 */
        if( defined('RUNTIME_MODEL') ) {
        } elseif( is_file(RUNTIME_PATH.SEP.'application.build.php') && (!is_file(CONFIG_FILE) || filemtime(RUNTIME_PATH. SEP .'application.build.php') > filemtime( CONFIG_FILE )) ) {
			config( include RUNTIME_PATH.SEP.'application.build.php' );
		} else {
			/* 预编译项目 */
			Application::build();
		}
        //[/TIWER]

		
		
		/* 站点设置 */
		Application::checkSiteOption();

		
        /* 项目开始标签;是否开启插件机制,如果开发则处理标签 */
        if( config('APP_PLUGIN_ON') ) tag('app_begin');

		
        /* 设置系统时区PHP5支持 */
        if( function_exists('date_default_timezone_set') ) {
            date_default_timezone_set(config('DEFAULT_TIMEZONE'));
		}
		

        /* 允许注册AUTOLOAD方法, 注册__autoload()函数 */
        if( config('APP_AUTOLOAD_REG') && function_exists('spl_autoload_register') ) {
            spl_autoload_register( array('Framework', 'autoload') );
		}

		/* Session初始化 */
        if( config('SESSION_AUTO_START') ) {
			session_save_path(SESSION_PATH);
			session_start();
		}
		
		
		/* PHP_FILE 由内置的Dispacher定义.如果不使用该插件，需要重新定义 */
        if(!defined('PHP_FILE')) {           
            define('PHP_FILE',_PHP_FILE_);
		}
		

        /* 取得模块和操作名称,可以在Dispatcher中定义获取规则.检查应用是否安装 */
        if ( !in_array(APP_NAME, config('DEFAULT_APPS') ) && !Helper::createBusiness('App')->isAppNameActive(APP_NAME) ) {
			/* 应用不存在的情况下，抛出异常 */
			Helper::createException(Helper::createLanguage('_APP_INACTIVE_').APP_NAME);
        }
		

		
		/* 应用（APP）、模型（Module）、控件器(Controller) */
		$dc['_app']	= APP_NAME;
		$dc['_mod']	= CONTROLLER_NAME;
        $dc['_act']	= ACTION_NAME;

		
		
		/* 加载应用模块配置文件 */
        if( is_file(CONFIG_FILE) ) config(include CONFIG_FILE);

		/* 语言检查 */
		Application::checkLanguage();

		/* 模板检查 */
		Application::checkTemplate();

		 /* 开启静态缓存 */
        if( config('HTML_CACHE_ON') ) HtmlCache::readHTMLCache();

        /* 项目初始化标签 */
        if( config('APP_PLUGIN_ON') ) tag('app_init');
        return;
    }

    //[TIWER]	
	
	/**
     * 读取配置信息 编译项目
     *
     * @access private
     *
     * @return void
     */
    static private function build() {

		global $DomainMD5;

		/* 加载配置文件列表 */
		$config = include SYSINC_PATH. SEP .'config.inc.php';

		foreach ($config as $value) {
			if( is_file($value) ) {
				config(include $value);
			}
		}
        $common  = '';

		/* 是否调试模式 ALL_IN_ONE 模式下面调试模式无效  */
        $debug  =  config('APP_DEBUG') && (!$COMPILE_NONE);

		/* 加载项目公共文件 */
        if( is_file(COMMON_FILE) ) {

            include COMMON_FILE;

			/* 编译文件 */
			if( !$debug ) $common .= compile(COMMON_FILE);
        }

		/* 加载项目编译文件列表 */
		if( is_file( APP_PATH .'app.php' ) ) {
			$list   =  include APP_PATH.'app.php';

			/* 加载并编译文件 */
			foreach ($list as $file) {
				require $file;
				if(!$debug) $common .= compile($file);
			}
		}

        /* 如果是调试模式加载调试模式配置文件 */
        if( $debug ) {

            /* 加载系统默认的开发模式配置文件 */
			config(include SYSINC_PATH.'/debug.inc.php');

			/* 允许项目增加开发模式配置定义 */
			if( is_file(APP_PATH.'debug.php') ) {
				config(include APP_PATH.'debug.php');
			}

        } else {

            /* 部署模式下面生成编译文件.下次直接加载项目编译文件 */
            if( COMPILE_NONE ) {

				$sysinfo = "<?php\n/**\n * 系统编译文件(请不要任何形式的修改)\n *\n * Project: Tiwer Developer Framework\n * This is NOT a freeware, use is subject to license terms! \n * \n * Site: http://www.tiwer.cn  \n *\n * author: compile.temp.php ".date('Y-m-d- H:i:s')." wgw8299<wgw8299@163.com> \n *\n * Copyright (C) 2007-2011 Tiwer.NET Developer Team. All Rights Reserved.\n */\n ";

				/* 获取用户自定义变量 */
                $defs = get_defined_constants(TRUE);

				/* 修改核心，删除几个编译后被重复定义的常量  */
				unset(  $defs['user']['SEP'],
				        $defs['user']['IN_SYS'],
				        $defs['user']['INTAKE'],
						$defs['user']['SITE_PATH'],
						$defs['user']['DATAS_PATH'],
						$defs['user']['APPS_PATH'],
						$defs['user']['SKIN_PATH'],
						$defs['user']['API_PATH'],
						$defs['user']['TPLS_PATH'],
						$defs['user']['BLLS_PATH'],
						$defs['user']['SERVICES_PATH'],
						$defs['user']['SYSINC_PATH'],
						$defs['user']['FUNC_PATH'],
						$defs['user']['CORE_PATH'],
						$defs['user']['WIDGET_PATH'],
						$defs['user']['PLUGIN_PATH'],
						$defs['user']['ATTACH_PATH'],
						$defs['user']['BACKUP_PATH'],
						$defs['user']['COMPILE_PATH'],
				        $defs['user']['LENGUAGE_PATH'],
						$defs['user']['LOGS_PATH'],
						$defs['user']['SESSION_PATH'],
						$defs['user']['UPLOAD_PATH'],
						$defs['user']['SYSDAT_PATH'],
						$defs['user']['TEMPS_PATH'],
						$defs['user']['INCL_PATH'],
						$defs['user']['CONTROL_PATH'],
						$defs['user']['ACTION_NAME'],
						$defs['user']['CACHE_DIR'],
						$defs['user']['LOG_DIR'],
						$defs['user']['TEMP_DIR'],
						$defs['user']['MODEL_DIR'],
						$defs['user']['VIEW_DIR'],
						$defs['user']['HTML_DIR'],
						$defs['user']['EXPANSION_PATH'],
						$defs['user']['HTML_PATH'],
						$defs['user']['DATA_PATH'],
						$defs['user']['CACHE_PATH'],
						$defs['user']['CONTROLLER_NAME'],
						$defs['user']['CONTROL_DIR'],
						$defs['user']['LANG_DIR'],
						$defs['user']['MODEL_PATH'],
						$defs['user']['VIEW_PATH'],
						$defs['user']['LANG_PATH'],
						$defs['user']['COMMON_FILE'],
						$defs['user']['CONFIG_FILE'],
						$defs['user']['APPINFO_FILE'],
						$defs['user']['LOG_PATH'],
						$defs['user']['TEMP_PATH'],
						$defs['user']['HAS_ONE'],
						$defs['user']['BELONGS_TO'],
						$defs['user']['HAS_MANY'],
						$defs['user']['MANY_TO_MANY'],
						$defs['user']['CLIENT_MULTI_RESULTS']);

				/* 根据数组生成常量定义 */
                $content .= array_define($defs['user']);

				/* 加载系统编译文件,同时删该文件 */
                $content .= substr(file_get_contents(RUNTIME_PATH. SEP .'compile.build.php'),5);
				rmdirr(RUNTIME_PATH. SEP .'compile.build.php');

				/* 返回变量的表示 */
                $content .= $common."\nreturn ".var_export(config(),true).';';
				$content = $sysinfo . substr(strip_whitespace('<?php' . $content), 5). "\n ";

				/* 存储编译文件 */
                file_put_contents( RUNTIME_PATH . SEP . $DomainMD5 .'_build.php', $content);

            } else {

				$sysinfo = "<?php\n/**\n * 应用编译文件(请不要任何形式的修改)\n *\n * Project: Tiwer Developer Framework\n * This is NOT a freeware, use is subject to license terms! \n * \n * Site: http://www.tiwer.cn  \n *\n * author: application.build.php ".date('Y-m-d- H:i:s')." wgw8299<wgw8299@163.com> \n *\n * Copyright (C) 2007-2011 Tiwer.NET Developer Team. All Rights Reserved.\n */\n ";

				/* 加载配置文件 */
                $content  = $common."\nreturn ".var_export(config(),true).";\n";
				$content = $sysinfo . substr(strip_whitespace('<?php' . $content), 5). "\n ";
                file_put_contents(RUNTIME_PATH. SEP .'application.build.php', $content);
            }
        }

        return ;
    }

    //[/TIWER]

    /**
     * 语言检查（检查浏览器支持语言，并自动加载语言包）
     *
     * @access private
     *
     * @return void
     */
    static private function checkLanguage() {

		/* 默认系统语言包 */
        $langSet = config('DEFAULT_LANG');

	    /* 不开启语言包功能，仅仅加载全局语言文件包直接返回 */
        if (!config('LANG_SWITCH_ON')) {
            Helper::createLanguage(include LENGUAGE_PATH. SEP .$langSet. SEP .'global.php');
            return;
        }

        /* 启用了语言包功能，根据是否启用自动侦测设置获取语言选择 */
        if ( config('LANG_AUTO_DETECT') ){

		    /* 检测浏览器支持语言 */
            if( isset($_GET[config('VAR_LANGUAGE')]) ) {

				/* url中设置了语言变量 */
                $langSet = $_GET[config('VAR_LANGUAGE')];
                cookie('digcity_language_var', $langSet, 3600);

			} elseif( cookie('digcity_language_var') ) {

				/* 获取上次用户的选择 */
                $langSet = cookie('digcity_language_var');

            } elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

				/* 自动侦测浏览器语言 */
                preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                $langSet = $matches[1];
                cookie('digcity_language_var',$langSet,3600);
            }
        }

		$langSet = strtolower($langSet);

		
        /* 定义当前语言 */
        define('LANG_SET', $langSet);

		
        /* 加载全局语言包 */
        if(is_file( LENGUAGE_PATH. SEP .$langSet. SEP .'global.php')) {
			Helper::createLanguage(include LENGUAGE_PATH. SEP .$langSet. SEP .'global.php');
		}
		

        /* 加载错误语言包 */
        if (is_file(LANG_PATH.$langSet. SEP .'error.php')) {
            Helper::createLanguage(include LANG_PATH.$langSet. SEP .'error.php');
		}
		

        /* 读取项目公共语言包 */
        if (is_file(LANG_PATH. $langSet. SEP .'common.php')) {
            Helper::createLanguage(include LANG_PATH. $langSet. SEP .'common.php');
		}
		

        /* 读取当前模块语言包 */
        if (is_file(LANG_PATH.$langSet. SEP .$group.strtolower(CONTROLLER_NAME).'.php')) {
            Helper::createLanguage(include LANG_PATH.$langSet. SEP .$group.strtolower(CONTROLLER_NAME).'.php');
		}
    }

    /**
     * 模板检查，如果不存在使用默认
     *
     * @access private
     *
     * @return void
     */
    static private function checkTemplate() {

		global $dc;
		
		
		/* 公共模板  */
		define('COMMON_TPL_PATH', TPLS_PATH. SEP ."common");
		define('PUBLIC_TPL_PATH', TPLS_PATH. SEP ."public");
		
		
		
        /* 当前地址 */
		define('__SELF__', HTTPHOST.$_SERVER['PHP_SELF']);
        define('__APP__',  HTTPHOST.PHP_FILE.'?app='.APP_NAME);
		define('__URL__',  HTTPHOST.PHP_FILE.'?app='.APP_NAME.'&'. config('VAR_MODULE') .'='.CONTROLLER_NAME);
        
		
        
        /* 模板文件   */
        config('TMPL_FILE_NAME', VIEW_PATH . strtolower(CONTROLLER_NAME). SEP . ACTION_NAME . config('TMPL_TEMPLATE_SUFFIX'));
        config('CACHE_PATH',CACHE_PATH);
        
        
        
        /* 网站公共文件目录 */
        define('WEB_SKIN_PATH',   SITE_URL.'/skin');
		define('WEB_JS_PATH',     WEB_SKIN_PATH.'/js');
		define('WEB_IMAGE_PATH',  WEB_SKIN_PATH.'/images');		
		 
        
		
        /* 应用模板目录 */
        define('APP_VIEW_PATH',   VIEW_PATH);
		define('APP_VIEW_PUBLIC', APP_VIEW_PATH.'public'.SEP );
		
		
		
		/* 应用模板文件、公共文件  */
		define('APP_VIEW_URL',    SITE_URL. '/application/' . APP_NAME .'/'. VIEW_DIR);		
        define('APP_SKIN_PATH',   SITE_URL. '/application/' . APP_NAME .'/'. VIEW_DIR .'/skin');
        
        
        
        /* 网站主题  */
        $themes = ($dc['site']['site_theme']) ? $dc['site']['site_theme']: 'default';	
		define('__THEME__', WEB_SKIN_PATH."/themes/{$themes}");
		define('__ADMIN__', WEB_SKIN_PATH."/admin");
		
        return;
    }

    /**
     * 模板站点配置
     *
     * @access private
     *
     * @return void
     */
	static private function checkSiteOption() {
		global $dc;

		/* 初始化站点配置信息，在站点配置中：表情，网站头信息，网站的应用列表，应用权限等 */
		$f_path	= TEMPS_PATH.SEP.'sysinfo'.SEP;

		
		/* 站点配置文件是否存在 */
		if(file_exists($f_path.'sys_config.php')) {
			$dc['site']	= Helper::createTempFile('sys_config');

		} else {

			/* 获取站点配置并写入文件 */
			$dc['site']	= Helper::createBusiness('Config')->getList('siteopt');
			$dc['site']['site_header_author'] = config('SYS_AUTHOR_DESIGN').config('SYS_AUTHOR_ART').config('SYS_AUTHOR_PAGE').config('SYS_AUTHOR_PRO');
			$dc['site']['site_header_work']   = config('SYS_HEADER_WORK');
			$dc['site']['site_header_edition']= config('SYS_HEADER_EDITION');
			Helper::createTempFile('sys_config', $dc['site']);
		}

		
		/* 检测网站关闭 */
        if ( 1 == $dc['site']['site_closed'] && APP_NAME != 'manage' && !Helper::createBusiness('App')->isAppAdmin(APP_NAME, CONTROLLER_NAME) ) {
        	$reason   = $dc['site']['site_closed_reason'];
        	$template = $dc['site']['site_theme'] ? $dc['site']['site_theme'] : 'classic';
        	include SKIN_PATH. SEP .'themes'. SEP .$template. SEP  .'close.html';
            exit;
        }
		return;
	}

    /**
     * 执行应用程序
     *
     * @access public
     *
     * @return void
     *
     * @throws TiwerException
     */
    static public function exec() {

		/* 是否开启标签扩展 */
        $tagOn = config('APP_PLUGIN_ON');
		/* 项目运行标签 */
        if($tagOn) tag('app_run');

		
		/* 创建控制器实例 */
		$contrller = Helper::createController(CONTROLLER_NAME);
        if( !$contrller ) {

		
            /* 是否存在扩展控制器 */
            $_contrller = config('_modules_.'.CONTROLLER_NAME);
            if( $_contrller ) {
                import($_contrller[0]);
                $class = isset($_contrller[1]) ? $_contrller[1]: CONTROLLER_NAME . 'Controller';
				$contrller = new $class;

            } else {
                /* 是否定义Empty模块 */
				$contrller = Helper::createController("Empty");
            }
			

			/* 模块不存在 抛出异常 */
			if(!$contrller) {
				Helper::createException(Helper::createLanguage('_MODULE_NOT_EXIST_') .' ：'. CONTROLLER_NAME);
			}
        }

        /* 获取当前操作名 */
        $action = ACTION_NAME;

        /* 执行当前操作 */
		call_user_func(array(&$contrller, $action));

		/* 项目结束标签 */
        if($tagOn)  tag('app_end');
		return ;
    }

	
	
	/**
	 * 记录当前状态
	 */
    static private function __remeberReferUrl() {
    	/* 记录当前状态 */
		if ( !(APP_NAME == 'kernel' && CONTROLLER_NAME == 'Public') ) {
			$_SESSION['refer_url'] = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		}
    }

    /**
     * 运行应用实例 入口文件使用的快捷方法
     *
     * @access public
     *
     * @return void
     */
    static public function run() {

		/* 应用类初始化 */
        Application::init();

		/* 记录应用初始化时间 */
        if( config('SHOW_RUN_TIME') )  $GLOBALS['dc_initTime'] = microtime(TRUE);

	    /* 加载模块 */
        Application::exec();

		/* 保存日志记录 */
		if(config('LOG_RECORD')) Log::save();
        return ;
    }

    /**
     * 自定义异常处理
     *
     * @access public
     *
     * @param mixed $e 异常对象
     */
	static public function appException($exception) {
		halt( $exception->__toString() );
    }

    /**
     * 自定义错误处理
     *
     * @access public
     *
     * @param int     $errno    错误类型
     * @param string  $errstr   错误信息
     * @param string  $errfile  错误文件
     * @param int     $errline  错误行数
     *
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
		    
			/* 系统错误 */
		    case E_ERROR:
		    case E_USER_ERROR:
		 	    $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
			    if(config('LOG_RECORD')) Log::write($errorStr,Log::ERR);
			    halt($errorStr);
			    break;
			
			
		    /* 一般错误 */
		    case E_STRICT:
		    case E_USER_WARNING:
		    case E_USER_NOTICE:
		    default:
			    $errorStr = "[$errno] $errstr ".basename($errfile)." 第 $errline 行.";
			    Log::record($errorStr,Log::NOTICE);
			    break;
		}
    }
	
	
 }
