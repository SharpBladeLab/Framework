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
 * @version     $Id: db.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 数据库函数库
 */

 /**
  * 格式化mySQL字符串安全mySQL件正确无论如果魔法报价是在)
  *
  * @param string $str 字符串
  *
  * @return string
  */
 function escape( $str ) {
	return mysql_escape_string(stripslashes($str));
 }
 
 /**
 * 生成SQL查询中的IN(a,b,c)部分代码
 * 
 * @param misc $ids   id列表，可以是数组，也可以是使用逗号隔开的字符串。 
 *
 * @return  string
 */
 function makeDBIN($ids) {
	if(is_array($ids)) return "IN ('" . join("','", $ids) . "')";
	return "IN ('" . str_replace(',', "','", $ids) . "')";
 }