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
 * @version     $Id: format.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 格式化内容函数库
 */
 function matchReplaceImages($content = '') {
	$image = preg_replace_callback('/<img.*src=(.*)[>|\\s]/iU',"matchReplaceImagesOnce",$content);
	return $image;
 }
 
 function matchImages($content='') { 
	$src = array();	
	preg_match_all('/<img.*src=(.*)[>|\\s]/iU',$content, $src);	
	if ( count($src[1]) > 0) {
		foreach($src[1] as $v) {
			/* 删除首尾的引号 ' " */
			$images[] =	trim($v,"\"'");	
		}
		return $images;
	} else {
		return false;
	}
 } 
 function matchReplaceImagesOnce($matches) { 
	$matches[1] = str_replace('"','', $matches[1]);	
	return sprintf("<a class='thickbox'  href='%s'>%s</a>", $matches[1], $matches[0]);
 }
 
 /**
  * 替换CSS样式中的图片
  *
  * @return string;
  */
 function replaceCssImages($data) {
	if($data) {
		return 'url('.__THEME__ .'/images/'.$data[1].')';
	} else {
		return $data[0];
	}
 }
 
 
/**
 * 字节格式化 (把字节数格式为BKMGT描述的大小)
 *
 * @return string
 */
 function byte_format($size, $dec = 2) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		$size /= 1024;
		$pos++;
	}
	return round($size, $dec)." ".$a[$pos];
 }
 
/**
 * 人民币转大写
 * 
 * @param  int $Arabic_numbers 小写RMB
 * 
 * @return string 大写RMB
 */
 function num2rmb($Arabic_numbers) {
 
	/* 中文大写数字 */
	$Chinese_numbers = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖'); 
	
	/*  中文大写单位 */
	$Chinese_unit = array(1=>'分',2=>'角',3=>'元',4=>'拾',5=>'佰',6=>'仟',7=>'万',8=>'拾',9=>'佰',10=>'仟',11=>'亿',12=>'拾',13=>'佰',14=>'仟');
	$Arabic_numbers = str_replace(",","",$Arabic_numbers);
	
	/*  将数字$Arabic_numbers格式化，精度为小数点后2位 */
	$Arabic_numbers  =number_format($Arabic_numbers,2); 
	$Arabic_numbers = str_replace(".","",$Arabic_numbers);
	$Arabic_numbers = str_replace(",","",$Arabic_numbers);
	$original_num = $Arabic_numbers;
	
	/* 取绝对值 */
	$Arabic_numbers = abs($Arabic_numbers); 
	
	/* 如果原始值与绝对值不相等，说明$Arabic_numbers为负数 */
	if($original_num != $Arabic_numbers) {
		$m="负";
	} else {
		$m='';
	}
	
	for ($i=1;$i <= strlen($Arabic_numbers);$i++) {
		$mynum = substr($Arabic_numbers,$i-1,1);
		$m = $m.$mynum.$Chinese_unit[strlen($Arabic_numbers)+1-$i];
	}
	
	/* 字符替换，将阿拉伯数字0123456789对应的替换成"零壹贰叁肆伍陆柒捌玖" */
	foreach($Chinese_numbers as $key=>$Arabic_numbers) {
		$m = str_replace($key,$Arabic_numbers,$m); 
	}	
	return $m; 
 }

