<?php if (!defined('IN_SYS')) exit();
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
 * @version     $Id: compile.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 文件编译函数库
 */

 /**
  * 系统编译
  *
  * @return void
  */
 function build_runtime() {

	/* 加载常量定义文件 */
    require FUNC_PATH. SEP .'tiwer.func.php';

	/* 函数库文件 */
    $runtime  = include SYSINC_PATH. SEP .'function.inc.php';

	/* 核心基类必须加载 */
    $runtime[] = CORE_PATH. SEP .'Framework.class.php';

    /* 加载核心编译文件 */
    $list = include SYSINC_PATH. SEP .'core.inc.php';


	/* 合并文件 */
    $runtime = array_merge($runtime, $list);

    /* 加载核心编译文件列表 */
    foreach ( $runtime as $key => $file ) {
        if( is_file($file) )  require $file;
    }

    /* 检查项目目录结构 如果不存在则自动创建 */
    if( !is_dir(RUNTIME_PATH) ) {

        /* 创建项目目录结构 */
        build_app_dir();

    } else {

        /* 检查缓存目录 */
        check_runtime();
    }

    /* 生成核心编译缓存 去掉文件空白以减少大小 */
    if( !defined('NO_CACHE_RUNTIME') ) {

		$sysinfo = "<?php\n/**\n * 系统编译文件(请不要任何形式的修改)\n *\n * Project: Tiwer Developer Framework\n * This is NOT a freeware, use is subject to license terms! \n * \n * Site: http://www.tiwer.cn  \n *\n * author: compile.build.php ".date('Y-m-d- H:i:s')." wgw8299<wgw8299@163.com> \n *\n * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.\n */\n ";

		/* 添加 */
        $content = compile(FUNC_PATH.SEP.'tiwer.func.php');
        foreach ($runtime as $file) {
            $content .= compile($file);
        }

        if ( defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE == false ) {
			/* 不用除空白 */
            file_put_contents(RUNTIME_PATH. SEP .'compile.build.php', '<?php'.$sysinfo.$content);

        } else {
			/* 用除空白 */
            file_put_contents(RUNTIME_PATH. SEP .'compile.build.php', $sysinfo.substr(strip_whitespace('<?php'.$content)."\n ", 5));
        }
        unset($content);
    }
 }


/**
 * 批量创建目录
 *
 * @param array $dirs 目录
 * @param int   $mode 权限
 */
 function mkdirs($dirs, $mode=0777) {
    foreach ($dirs as $dir) {
        if(!is_dir($dir)) mkdir($dir,$mode,true);
    }
 }


/**
 * 创建项目目录结构
 *
 * @return void
 */
 function build_app_dir() {

    /* 没有创建项目目录的话自动创建 */
    if( !is_dir(APP_PATH) ) mkdir(APP_PATH,0777,true);

    if( is_writeable(APP_PATH) ) {

		/* 创建应用文件目录 */
        $dirs  = array(

			/* MVC目录 */
            MODEL_PATH,
			VIEW_PATH,
            CONTROL_PATH,

			/* 语言文件目录 */
			LANG_PATH,

            /* 运行时路径 */
			RUNTIME_PATH,
            LOG_PATH,
            TEMP_PATH,
            DATA_PATH,
			CACHE_PATH,
        );
		mkdirs($dirs);

        /* 目录安全写入 */
        if(!defined('BUILD_DIR_SECURE')) define('BUILD_DIR_SECURE',false);

        if( BUILD_DIR_SECURE ) {
            if( !defined('DIR_SECURE_FILENAME') ) define('DIR_SECURE_FILENAME', 'index.html');
            if( !defined('DIR_SECURE_CONTENT')  ) define('DIR_SECURE_CONTENT',' ');

			/* 自动写入目录安全文件 */
            $content = DIR_SECURE_CONTENT;
            $a = explode(',', DIR_SECURE_FILENAME);
            foreach ($a as $filename){
                foreach ($dirs as $dir) {
                    file_put_contents($dir.$filename,$content);
				}
            }
        }

        /* 写入配置文件 */
        if(!is_file(CONFIG_PATH.'config.php')) {
            file_put_contents(CONFIG_PATH.'config.php',"<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
		}

		/* 写入测试Action */
        if( !is_file( CONTROL_PATH . 'IndexController.class.php') ) {
            $content =
'<?php
class IndexAction extends Controller{
    public function index(){
        header("Content-Type:text/html; charset=utf-8");
        echo "<div style=\'font-weight:normal;color:blue;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;font-size:14px;font-family:Tahoma\'>Hello,欢迎使用<span style=\'font-weight:bold;color:red\'>Tiwer Framework<br /> Copyright (C) 2007-2012 Tiwer Developer Team. All Rights Reserved</span></div>";
    }
}
?>';
            file_put_contents( CONTROL_PATH . 'IndexController.class.php', $content);
        }
    } else {
        header("Content-Type:text/html; charset=utf-8");
        exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~</div>');
    }
 }


/**
 * 检查缓存目录(Runtime) 如果不存在则自动创建
 *
 * @return boolean
 */
 function check_runtime() {

    /* 判断给定的文件名是否可写 */
	if( !is_writeable(RUNTIME_PATH) ) {
		header("Content-Type:text/html; charset=utf-8");
		exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>目录 [ '.RUNTIME_PATH.' ] 不可写！</div>');
	}

	/* 模板缓存目录 */
	if( !is_dir(CACHE_PATH) ) {
		mkdir(CACHE_PATH, 0777, true);
	}

	/* 日志目录 */
	if( !is_dir(LOG_PATH) ) {
		mkdir(LOG_PATH, 0777, true);
	}

	/* 数据缓存目录 */
	if( !is_dir(TEMP_PATH) ) {
		mkdir(TEMP_PATH, 0777, true);
	}

	/* 数据文件目录 */
	if( !is_dir(DATA_PATH) ) {
		mkdir(DATA_PATH, 0777, true);
	}

	/* 系统会话目录 */
	if( !is_dir(SESSION_PATH) ) {
		mkdir(SESSION_PATH, 0777, true);
	}

	return true;
 }


/**
 * 编译文件
 *
 * @param string  $filename 文件名称
 * @param boolean $runtime  是否替换掉编译内容
 *
 * @return string
 */
 function compile($filename) {

	$content = file_get_contents($filename);

	/* 替换预编译指令 */
	if ( true === COMPILE_NONE ) {
        $content = preg_replace('/\/\/\[TIWER\](.*?)\/\/\[\/TIWER\]/s','',$content);
	}

	/* 替换php文件标签 */
    $content = substr(trim($content),5);
    if('?>' == substr($content,-2)) {
        $content = substr($content,0,-2);
	}
    return $content;
 }


/**
 * 去除代码中的空白和注释
 *
 * @param string $content 内容
 *
 * @return string 去除空白与注释后的代码
 */
 function strip_whitespace($content) {

    $stripStr = '';

    /* 分析php源码 */
    $tokens = token_get_all($content);

    $last_space = false;
    for ($i = 0, $j = count ($tokens); $i < $j; $i++) {
        if (is_string ($tokens[$i])) {
            $last_space = false;
            $stripStr .= $tokens[$i];
        } else {
            switch ($tokens[$i][0])
            {
                /* 过滤各种PHP注释 */
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;

                /* 过滤空格 */
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr .= ' ';
                        $last_space = true;
                    }
                    break;

                default:
                    $last_space = false;
                    $stripStr .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
 }


/**
 * 根据数组生成常量定义
 *
 * @param array $array
 *
 * @return string  常量定义的PHP代码
 */
 function array_define($array) {
    $content = '';
    foreach($array as $key=>$val) {
        $key =  strtoupper($key);
        if(in_array($key, array('APP_NAME','APP_PATH','RUNTIME_PATH','COMPILE_NONE','THINK_MODE')))
            $content .= 'if(!defined(\''.$key.'\')) ';

        if(is_int($val) || is_float($val)) {
            $content .= "define('".$key."',".$val.");";
        }elseif(is_bool($val)) {
            $val = ($val)?'true':'false';
            $content .= "define('".$key."',".$val.");";
        }elseif(is_string($val)) {
            $content .= "define('".$key."','".addslashes($val)."');";
        }
    }
    return $content;
 }

