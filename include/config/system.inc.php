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
 * @version     $Id: system.inc.php 589 2013-08-26 02:22:14Z wgw $
 *
 * 系统配置文件
 */
 return array(

	/* 信息 */
	'SYS_AUTHOR_DESIGN'   => 'System Design:WuGuowem(QQ:84571242,E-mail:wgw8299@163.com);',
	'SYS_AUTHOR_ART'      => 'Art Design:ZhangYutao(QQ:4518620,E-Mail:4518620@QQ.com);',
	'SYS_AUTHOR_PAGE'     => 'Page Design:WuGuowem(QQ:84571242,E-mail:wgw8299@163.com);',
	'SYS_AUTHOR_PRO'      => 'Programme:WuGuowem(QQ:84571242,E-mail:wgw8299@163.com);',

	/* 版本相关 */
	'SYS_HEADER_EDITION'  => 'CMS Bate 0.1(Bosom Testing Edition)',
	'SYS_HEADER_WORK'     => 'Tiwer Developer Work Studio',

    /* 默认设定 */
    'DEFAULT_APP'         => 'manage',   // 默认应用
	'DEFAULT_MODULE'      => 'index',	 // 默认模块名称
    'DEFAULT_ACTION'      => 'index',	 // 默认操作名称
    'DEFAULT_CHARSET'     => 'utf-8',	 // 默认输出编码
    'DEFAULT_TIMEZONE'    => 'PRC',		 // 默认时区
    'DEFAULT_AJAX_RETURN' => 'JSON',	 // 默认AJAX数据返回格式,可选JSON XML ...
    'DEFAULT_THEME'		  => 'default',	 // 默认模板主题名称
    'DEFAULT_LANG'        => 'zh-cn',	 // 默认语言

    /* 系统变量名称设置 */
	'VAR_APP'			  => 'app',      // 默认应用获取变量
    'VAR_MODULE'          => 'model',    // 默认模块获取变量
    'VAR_ACTION'          => 'action',   // 默认操作获取变量
    'VAR_ROUTER'          => 'r',    	 // 默认路由获取变量
   	'VAR_PAGE'            => 'p',		 // 默认分页跳转变量
    'VAR_TEMPLATE'        => 't',		 // 默认模板切换变量
	'VAR_LANGUAGE'        => 'l',		 // 默认语言切换变量
    'VAR_AJAX_SUBMIT'     => 'ajax',     // 默认的AJAX提交变量
    'VAR_PATHINFO'        => 's',		 // PATHINFO兼容模式获取变量
 );
