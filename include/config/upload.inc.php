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
 * @version     $Id: upload.inc.php 680 2013-01-07 08:59:59Z zzy $
 * @link        http://www.tiwer.cn
 *
 * 上传文件相关信息 
 */
 return array(
	'allowExts'  => "jpg,gif,png,swf,txt,xls,xlsx,csv",			                                            //上传文件后缀格式
	'allowTypes' => "image/pjpeg,image/jpeg,image/png,image/x-png,image/gif,application/x-shockwave-flash,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",		//上传文件MINI
	'maxSize'    => 32922000,                                                               //上传最大文件限制
 	'savePath'   => UPLOAD_PATH.SEP	                                                        //上传文件路径
 );
