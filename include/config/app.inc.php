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
 * @version     $Id: app.inc.php 316 2012-12-18 05:30:18Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 应用配置信息
 */
 return array(
  
	'APP_DEBUG'		   => false,		      // 是否开启调试模式
    'APP_DOMAIN_DEPLOY'=> false,		      // 是否使用独立域名部署项目
    'APP_PLUGIN_ON'    => false,		      // 是否开启插件机制
    'APP_FILE_CASE'    => false,		      // 是否检查文件的大小写对Windows平台有效
    'APP_AUTOLOAD_REG' => true,		          // 是否开启SPL_AUTOLOAD_REGISTER
    'APP_AUTOLOAD_PATH'=> CORE_PATH.SEP,      //  __autoLoad机制额外检测路径设置,注意搜索顺序
	
        /* 
         * 项目额外需要加载的配置列表， 默认包括：taglibs(标签库定义), 
         * routes(路由定义),tags(标签定义),htmls(静态缓存定义), 
         * modules(扩展模块),actions(扩展操作) 
   		 */
    'APP_CONFIG_LIST'  => array('taglibs','routes','tags', 'htmls','modules','actions'),
	'DEFAULT_APPS'	   => array('manage'),        // 默认应用
 );
 