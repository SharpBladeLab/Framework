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
 * @version     $Id: file.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 文件目录函数库
 */ 

/**
 * 区分大小写的文件存在判断
 *
 * @param string $filename
 */
 function file_exists_case($filename) {
    if(is_file($filename)) {
        if(IS_WIN && config('APP_FILE_CASE')) {
            if(basename(realpath($filename)) != basename($filename)) {
                return false;
			}
        }
        return true;
    }
    return false;
 }
 
/**
 * 循环创建目录
 *
 * @param  string  $dir   目录 
 * @param  integer $mode  权限
 */
 function mk_dir($dir, $mode = 0755) {  	
	if ( is_dir($dir) || @mkdir($dir,$mode)) 
  		return true;
  	
  	if ( !mk_dir(dirname($dir),$mode) ) 
  		return false;
  	
  	return @mkdir($dir,$mode);
 }

/**
 * 删除文件
 *
 * @param  string  $dirname   目录名称 
 * @return boolean true/false 是否删除成功
 */
 function rmdirr($dirname) {
	
	/* 文件是否存在 */
	if (!file_exists($dirname)) {
		return false;
	}
	
	/* 删除文件 */
	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}
	
	/* 递归方式删除目录  */
	$dir = dir($dirname);
	while( false !== $entry = $dir->read()) {
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
	}
	$dir->close();
	return rmdir($dirname);
 }
 
function file2dir($sourcefile, $dir)
{
     if( is_dir($sourcefile) ){ // 如果你希望同样移动目录里的文件夹
         return file2dir($sourcefile, $dir);
     }
     if( ! file_exists($sourcefile)){
         return false;
     }
     $filename = basename($sourcefile);
     return copy($sourcefile, $dir .'/'. $filename);
}