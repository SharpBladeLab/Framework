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
 * @version     $Id: using.func.php 24 2012-11-28 03:59:21Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 加载使用函数库(USING)
 */
  
/**
 * 基于命名空间方式导入函数库 load('@.Util.Array')
 *
 *
 * @param string $name    函数库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext     导入的文件扩展名
 *
 * @return void
 */
 function load($name,$baseUrl='',$ext='.php') {
	
	/* 路径替换 */
    $name = str_replace(array('.','#'), array('/','.'), $name);
	
    if( empty($baseUrl) ) {	
        if(0 === strpos($name,'@/')) {
            /* 加载当前项目函数库 */
            $baseUrl   =  APP_PATH. SEP .'common'. SEP;
            $name =  substr($name,2);
			
        } else {
            /* 系统函数库 */
            $baseUrl =  FUNC_PATH . SEP;
        }
    }
	if(substr($baseUrl, -1) != SEP) $baseUrl .= SEP;
    include $baseUrl . $name . $ext;
 }

/**
 * 快速导入第三方框架类库,所有第三方框架的类库文件统一放到,并且默认都是以.php后缀导入
 * 
 * @param string $class   类名
 * @param string $baseUrl 基地址
 * @param string $ext     扩展名
 *
 * @return object 
 */
 function vendor($class, $baseUrl = '',$ext='.php') {
    if(empty($baseUrl)) $baseUrl = EXPANSION_PATH;	
    return import($class, $baseUrl,$ext);
 }

/**
 * 快速定义和导入别名
 *
 * @parma string $alias     别名
 * @param string $classfile 类文件
 * 
 * @return mixed
 */
 function alias_import($alias, $classfile = '' ) {

    static $_alias   =  array();
	
    if( '' !== $classfile ) {
        /* 定义别名导入 */
        $_alias[$alias]  = $classfile;
        return;
    }
	
    if(is_string($alias)) {
		
		/* 是否存在别名 */
        if( isset($_alias[$alias]) )
            return require_cache($_alias[$alias]);
			
    } elseif( is_array($alias) ){
	
        foreach ($alias as $key=>$val)
            $_alias[$key]  =  $val;
			
        return ;
    }
    return false;
 }

/**
 * 基类库或者应用类库导入 (本函数有缓存功能)
 *
 * @param string $class    表示要导入的类库,采用命名空间的方式( 加载插件plugins.bups.XXXXXX)
 * @param string $baseUrl  表示导入的基础路径,留空的话系统
 * @param string $ext      ext 表示类库后缀，默认是 .class.php 
 *
 * @return boolen
 */
 function import($class, $baseUrl = '', $ext='.class.php') {
    
	/* 静态变量定义 */
	static $_file = array();
    static $_class = array();	
    
    /* 导入插件 */
    $temp =explode('.', $class);
	if ( $temp[0]=='plugins' ) {
		$class = str_replace(array('.', '#'), array(SEP, '.'), $class);
		$classfile = INCL_PATH.SEP.$class.$ext;
		return require_cache($classfile);
	}
	
    $class = str_replace(array('.', '#'), array(SEP, '.'), $class);		
    
	/* 检查别名导入 */
    if( '' === $baseUrl && false === strpos($class, SEP)) return alias_import($class);
    	
    if( isset($_file[$class.$baseUrl]) ) {
        return true;
    } else {
        $_file[$class.$baseUrl] = true;
	}
    $class_strut = explode(SEP, $class);

    if( empty($baseUrl) ) {      
		if('@'==$class_strut[0] || APP_NAME == $class_strut[0] ) {
			/* 加载当前项目应用类库 */
            $baseUrl = dirname(APP_PATH);
            $class = str_replace(array(APP_NAME.SEP, '@'.SEP), APP_PATH.SEP, $class);
			
        } elseif ( in_array(strtolower($class_strut[0]), array('think','org','com')) ) {		
            /* 加载框架基类库或者公共类库 */
            $baseUrl = CORE_PATH. SEP;
			
        } else {
			$class = APPS_PATH. SEP . $class;
        }
    }
	
	if(substr($baseUrl, -1) !=  SEP) $baseUrl .= SEP;	
	$classfile = is_file($class.$ext) ? $class.$ext : $baseUrl . $class . $ext;

	if( $ext == '.class.php' && is_file($classfile)) {
        /* 冲突检测 */
        $class = basename($classfile, $ext);
        if(isset($_class[$class])) {
            Helper::createException(Helper::createLanguage('_CLASS_CONFLICT_').':'.$_class[$class].' '.$classfile);
		}
		
        $_class[$class] = $classfile;
    }
    /* 导入目录下的指定类库文件 */
    return require_cache($classfile);
 }
 
 
 /**
  * 载入标签库
  * 
  * @param string $class
  * @param string $ext
  */
 function importTag($class, $ext='.class.php') {
 	return require_cache(EXPANSION_PATH .'tamplate'. SEP.'framework'. SEP. 'taglib'. SEP.$class.$ext); 	
 }
 
/**
 * 自动加载当前项目的Model和Controller对象（并且支持配置自动加载路径）
 *
 * @param string $name 对象类名
 *
 * @return void
 */
function __autoload($name) {

    /* 检查是否存在别名定义 */ 
    if(alias_import($name)) return;
	
    /* 自动加载当前项目的Actioon类和Model类 */
    if(substr($name,-5) == "Model" ) {
        require_cache(LIB_PATH.'Model/'.$name.'.class.php');
		
    } elseif(substr($name,-6)=="Controller") {
        require_cache(LIB_PATH.'Controller/'.$name.'.class.php');
		
    } else {
        /* 根据自动加载路径设置进行尝试搜索 */
        if(config('APP_AUTOLOAD_PATH')) {
            $paths  =   explode(',',config('APP_AUTOLOAD_PATH'));
            foreach ($paths as $path) {
			
                if(import($path.$name)) {
                    /* 如果加载类成功则返回 */ 
                    return ;
                }				
            }
        }
    }
    return ;
 }

/**
 * 优化的require_once
 */
 function require_cache($filename) {
    static $_importFiles = array();
	
	/*  返回规范化的绝对路径名 */
    $filename  =  realpath($filename);	
    if (!isset($_importFiles[$filename])) {
        if(file_exists_case($filename)){
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
 }
 