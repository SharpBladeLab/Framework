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
 * @version     $Id: string.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 字符串函数库
 */
/**
 * 自动转换字符集 支持数组转换
 *
 * @param string $fContents 
 * @param string $from 
 * @param string $to 
 *
 * @return string 
 */
 function auto_charset($fContents, $from, $to) { 
    $from =  strtoupper($from)=='UTF8' ? 'utf-8' : $from;
    $to   =  strtoupper($to)=='UTF8'   ? 'utf-8' : $to;
	
    
	/* 如果编码相同或者非字符串标量则不转换 */
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ) {
        return $fContents;
    }
    	
    if(is_string($fContents) ) {		
        if(function_exists('mb_convert_encoding')) {
            return mb_convert_encoding ($fContents, $to, $from);			
        } elseif(function_exists('iconv')) {
            return iconv($from,$to,$fContents);			
        } else {
            return $fContents;
        }		
    } elseif(is_array($fContents)) {		
        foreach ( $fContents as $key => $val ) {
            $_key = auto_charset($key,$from,$to);
            $fContents[$_key] = auto_charset($val,$from,$to);
            if($key != $_key ) unset($fContents[$key]);			
        }		
        return $fContents;		
    } else {
        return $fContents;		
    }
 }
 
 
/**
 * 计算中文字符串长度
 * @param unknown_type $string
 */
 function utf8_strlen($string = null) {
	/* 将字符串分解为单元 */
	preg_match_all("/./us", $string, $match);
	/*  返回单元个数  */
	return count($match[0]);
 }
 
 
/**
 * 解析JavaScript中的escape()
 *
 * @param string $str 字符串
 *
 * @retrun josn
 */
 function unescape($str) {
 
	/* 对已编码的URL字符串进行解码 */
    $str = rawurldecode($str);
	
    preg_match_all("/(?:%u.{4})|.+/", $str, $r);	
    $ar = $r[0];
	
    foreach($ar as $k=>$v) {
        if(substr($v,0,2) == "%u" && strlen($v) == 6)
            $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4)));
    }	
    return join("",$ar);
 }
/**
 * 字符串截取，支持中文和其他编码
 *
 * @static
 * @access public
 *
 * @param  string  $str     需要转换的字符串
 * @param  string  $start   开始位置
 * @param  string  $length  截取长度
 * @param  string  $charset 编码格式
 * @param  string  $suffix  截断显示字符
 *
 * @return string
 */
 function msubstr($str, $start=0, $length=10, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr")){ 
		$slice = mb_substr($str, $start, $length, $charset);
		
	} elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
		
	} else {
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	if($suffix && $str != $slice) return $slice."...";
	return $slice;
 }

 /**
  * UTF-8切割字符串
  * 
  * @param string  $string
  * @param integer $length
  * @param string  $etc
  */
 function cn_substr($string, $length, $etc = '...'){
	
	$result = '';
	$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
	$strlen = strlen($string);
	
	for($i = 0; (($i < $strlen) && ($length > 0)); $i++){
		if($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')){
			if($length < 1.0){
			break;
		}
		$result .= substr($string, $i, $number);
		$length -= 1.0;
		$i += $number - 1;

		}else{
			$result .= substr($string, $i, 1);
			$length -= 0.5;
		}
	}
	$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
	if($i < $strlen){
		$result .= $etc;
	}
	return $result;
}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @static
 * @access public
 *
 * @param  string  $str     需要转换的字符串
 * @param  string  $length  截取长度
 * @param  string  $charset 编码格式
 * @param  string  $suffix  截断显示字符
 *
 * @return string
 */
 function mStr($str, $length, $charset="utf-8", $suffix=true) {
	return msubstr($str, 0, $length, $charset, $suffix);
 }
