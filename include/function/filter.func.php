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
 * @version     $Id: filter.func.php 245 2012-12-13 05:29:29Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 过滤函数库
 */


 /**
  * 清除HTML代码、空格、回车换行符
  * 
  * @return string $str
  */
 function filterhtml($str) {	
	$str = trim($str);	
	$str = strip_tags($str,"");
	$str = ereg_replace("\t","",$str);
	$str = ereg_replace("\r\n","",$str);
	$str = ereg_replace("\r","",$str);
	$str = ereg_replace("\n","",$str);
	$str = ereg_replace(" "," ",$str);	
	return trim($str); 	
 }
 
 /**
  * 清除空白
  * 
  * @param string $str
  */
 function clearblank($str) {
 	str_replace(array(PHP_EOL, chr(32)), "", $str);
 	return trim($str); 
 }
 
/**
 * 输出纯文本
 * 
 * @param string  $text    文件
 * @param boolean $parseBr 是否清除内容中的转义字符
 *
 * @return string 
 */
 function text($text, $parseBr = false) {
	if( !$parseBr ) {
		$text =	str_replace(array("\r","\n","\t"),' ',$text);
	} else {
		$text = nl2br($text);
	}
	$text = stripslashes($text);
	$text = htmlspecialchars($text, ENT_NOQUOTES,'UTF-8 ');	
	return $text;
 }
 
 
/**
 * 过滤得到安全的html
 * 
 * @param string $text   待过滤的字符串
 * @param array  $tags   标签的过滤白名单
 *
 * @return string 过滤后的字符
 */
 function filter($text, $tags = null) {
 
	$text = trim($text);
	
	/* 完全过滤注释 */
	$text = preg_replace('/<!--?.*-->/','',$text);
	
	/* 完全过滤动态代码 */
	$text =	preg_replace('/<\?|\?'.'>/','',$text);
	
	/* 完全过滤js */
	$text =	preg_replace('/<script?.*\/script>/','',$text);
	$text =	str_replace('[','&#091;',$text);
	$text =	str_replace(']','&#093;',$text);
	$text =	str_replace('|','&#124;',$text);
	
	/* 过滤换行符 */
	$text =	preg_replace('/\r?\n/','',$text);
	
	/* br */
	$text =	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text =	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	
	/* 过滤危险的属性，如：过滤on事件lang js */
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	
	/* 允许的HTML标签 */
	$text = preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
	
	/* 过滤多余html */
	$text =	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
	
	/* 过滤合法的html标签 */
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	
	/* 转换引号 */
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
	}
	
	/* 过滤错误的单个引号 */
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
	}
	
	/* 转换其它所有不合法的 < > */
	$text =	str_replace('<','&lt;',  $text);
	$text =	str_replace('>','&gt;',  $text);
	$text =	str_replace('"','&quot;',$text);
	
	/* 反转换 */
	$text = str_replace('[','<',$text);
	$text = str_replace(']','>',$text);
	$text = str_replace('|','"',$text);
	
	/* 过滤多余空格 */
	$text = str_replace('  ',' ',$text);
	return $text;
 }
 
 /**
  * 数据信息XSS过滤后
  *
  * @param string $val 要过滤的数据信息
  *
  * @return string 过滤后的信息
  */ 
 function xss_filter($val) {

	/* 正则表达式替换 */
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    /* 搜索代码 */
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&amp;*()';
    $search .= '~`&quot;;:?+/={}[]-_|\'\\';

    /* 过滤所有的非打印字符如 : CR(0a),LF(0b),AB(9),\n,\r,\t,JS */
    for ($i = 0; $i < strlen($search); $i++) {
		$val = preg_replace( '/(&amp;#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);	  
		$val = preg_replace( '/(&amp;#0{0,8}'.ord($search[$i]).';?)/',              $search[$i], $val);
    }

	/*  脚本代码  */
	$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   
    /* 合并数组 */
    $ra = array_merge($ra1, $ra2);

    /* 保持取代只要上一轮取代了一些东西 */ 
    $found = true;

    while ($found == true) {
        $val_before = $val;
	  
        for ($i = 0; $i < sizeof($ra); $i++) {
		
			$pattern = '/';
		 
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
                    $pattern .= '(';
					$pattern .= '(&amp;#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(&amp;#0{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}		 
			$pattern .= '/i';
		 
			/* 添加 &lt;&gt 来替换代码中的HTML */
			$replacement = substr($ra[$i], 0, 2).'&lt;x&gt;'.substr($ra[$i], 2); 
		 
			/* 过滤掉的十六进制标签 */
			$val = preg_replace($pattern, $replacement, $val);        
			if ($val_before == $val) $found = false;
		}
	}
    return $val;   
 }
 