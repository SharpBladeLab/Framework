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
 * @version     $Id: tamplate.inc.php 517 2013-07-30 09:03:18Z wgw $
 *
 * 模板引擎配置信息  
 */
 return array( 
    'TMPL_ENGINE_TYPE'		=> 'Think',           // 默认模板引擎 以下设置仅对使用内置模板引擎有效
    'TMPL_DETECT_THEME'     => false,             // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX'  => '.html',           // 默认模板文件后缀
    'TMPL_CACHFILE_SUFFIX'  => '.php',            // 默认模板缓存后缀
    'TMPL_DENY_FUNC_LIST'	=> 'echo,exit',       // 模板引擎禁用函数
    'TMPL_PARSE_STRING'     => '',                // 模板引擎要自动替换的字符串，必须是数组形式。
    'TMPL_L_DELIM'          => '{',			      // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          => '}',			      // 模板引擎普通标签结束标记
    'TMPL_VAR_IDENTIFY'     => 'array',           // 模板变量识别。留空自动判断,参数为'obj'则表示对象
    'TMPL_STRIP_SPACE'      => false,             // 是否去除模板文件里面的html空格与换行
    'TMPL_CACHE_ON'			=> true,              // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_CACHE_TIME'		=>	-1,               // 模板缓存有效期 -1 为永久，(以数字为值，单位:秒)
    'TMPL_ACTION_ERROR'     => 'Public:success',  // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success',  // 默认成功跳转对应的模板文件	

	/* 模板引擎标签库相关设定 */
    'TAGLIB_BEGIN'          => '<',               // 标签库标签开始标记
    'TAGLIB_END'            => '>',               // 标签库标签结束标记
    'TAGLIB_LOAD'           => true,              // 是否使用内置标签库之外的其它标签库，默认自动检测
    'TAGLIB_BUILD_IN'       => 'input',           // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔
    'TAGLIB_PRE_LOAD'       => 'html',            // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
    'TAG_NESTED_LEVEL'		=> 3,                 // 标签嵌套级别
    'TAG_EXTEND_PARSE'      => '',                // 指定对普通标签进行扩展定义和解析的函数名称。
	
	'TMPL_TRACE_FILE'       => TPLS_PATH. SEP .'framework'. SEP .'pagetrace.tpl.php', // 页面Trace的模板文件
    'TMPL_EXCEPTION_FILE'   => TPLS_PATH. SEP .'framework'. SEP .'exception.tpl.php',  // 异常页面的模板文件
    'TMPL_FILE_DEPR'		=>'/',                                                     // 模板文件MODULE_NAME与ACTION_NAME之间的分割符
 );
 