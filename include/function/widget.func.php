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
 * @version     $Id: widget.func.php 24 2012-11-28 03:59:21Z wgw $
 * @link        http://www.tiwer.cn
 *
 * widget函数库
 */

/**
 * 渲染输出小部件
 *
 * @param string  $name   名称
 * @param array   $data   数据
 * @praam boolean $return 是否返回值
 *
 * @return mixed
 */
 function widget($name, $data=array(), $return=false) {
 
	/* 类名 */
    $class = $name.'Widget';
	
	/* 是否存在类 */
	if(file_exists( WIDGET_PATH. SEP .$class.'.class.php')){
		require_cache( WIDGET_PATH. SEP .$class.'.class.php');
	} else {
		require_cache( WIDGET_PATH. SEP .$class.'.class.php');
	}
	
	/* 不存在 */
    if(!class_exists($class)) {
        Helper::createException(Helper::createLanguage('_CLASS_NOT_EXIST_').':'.$class);
	}
	
	/* 新建一个对象 */
    $widget	= new $class();
    $content = $widget->render($data);
	
    if($return) {
        return $content;
    } else {
        echo $content;
	}
 }
