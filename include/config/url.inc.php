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
 * @version     $Id: url.inc.php 70 2012-12-05 05:31:39Z zzy $
 * @link        http://www.tiwer.cn
 *
 * URL配置信息
 */
 return array (
	'URL_CASE_INSENSITIVE'  => true,      // URL地址是否不区分大小写
    'URL_ROUTER_ON'         => true,      // 是否开启URL路由
	'URL_REWRITERULE'       => false,     // 是否开启RewriteRule
	'URL_DIGCITY'           => true,      // 是否开启Route路由
	'URL_AUTO_FILTER'       => true,      // 是否进行自动对POST.GET.COOKIE进行过滤
	'URL_HTML'              => true,      // 是否在网址后加扩展名,必须开启Route路由
	'URL_DIGCITY_SAFE'      => false,      // 是否开启url地址安全编码，必须开启Route路由(开始安全过滤之后不需要扩展名)
	'URL_HTML_EXTENDED'     => '/default.shtml',   // 扩展名,必须URL_HTML为真才有用
 );
 