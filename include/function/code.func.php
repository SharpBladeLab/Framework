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
 * @version     $Id: code.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 代码处理函数库
 */

/**
 * 代码加亮
 *
 * @param String  $str   要高亮显示的字符串 或者 文件名
 * @param Boolean $show  是否输出
 *
 * @return String
 */
 function highlight_code($str, $show=false) {
 
    if(file_exists($str)) {
        $str =  file_get_contents($str);
    }	
    $str =  stripslashes(trim($str));
    $str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);
    $str = str_replace(array('&lt;?php', '?&gt;',  '\\'), array('phptagopen', 'phptagclose', 'backslashtmp'), $str);
    $str = '<?php //tempstart'."\n".$str.'//tempend ?>';

    $str = highlight_string($str, TRUE);

    if (abs(phpversion()) < 5)
    {
        $str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
        $str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
    }

    $str = preg_replace("#\<code\>.+?//tempstart\<br />\</span\>#is", "<code>\n", $str);
    $str = preg_replace("#\<code\>.+?//tempstart\<br />#is", "<code>\n", $str);
    $str = preg_replace("#//tempend.+#is", "</span>\n</code>", $str);

    $str = str_replace(array('phptagopen', 'phptagclose', 'backslashtmp'), array('&lt;?php', '?&gt;', '\\'), $str); //<?
    $line   =   explode("<br />", rtrim(ltrim($str,'<code>'),'</code>'));
    $result =   '<div class="code"><ol>';
    foreach($line as $key=>$val) {
        $result .=  '<li>'.$val.'</li>';
    }
    $result .=  '</ol></div>';
    $result = str_replace("\n", "", $result);
    if( $show!== false) {
        echo($result);
    }else {
        return $result;
    }
 }
 
/**
 * 解析UBB
 *
 * @param string  $Text 文本
 *
 * @return void  
 */
 function ubb($Text) {
 
	$Text=trim($Text);  
	//$Text=htmlspecialchars($Text);
  
	$Text = preg_replace("/\\t/is","  ",$Text);  
	$Text = preg_replace("/\[h1\](.+?)\[\/h1\]/is", "<h1>\\1</h1>",$Text);
	$Text = preg_replace("/\[h2\](.+?)\[\/h2\]/is", "<h2>\\1</h2>",$Text);
	$Text = preg_replace("/\[h3\](.+?)\[\/h3\]/is", "<h3>\\1</h3>",$Text);
	$Text = preg_replace("/\[h4\](.+?)\[\/h4\]/is", "<h4>\\1</h4>",$Text);
	$Text = preg_replace("/\[h5\](.+?)\[\/h5\]/is", "<h5>\\1</h5>",$Text);
	$Text = preg_replace("/\[h6\](.+?)\[\/h6\]/is", "<h6>\\1</h6>",$Text);  
	$Text = preg_replace("/\[separator\]/is","",$Text);
	$Text = preg_replace("/\[center\](.+?)\[\/center\]/is",             "<center>\\1</center>",$Text);
	$Text = preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is", "<a href=\"http://\\1\" target=_blank>\\2</a>",$Text);
	$Text = preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is",          "<a href=\"http://\\1\" target=_blank>\\2</a>",$Text);
	$Text = preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is",       "<a href=\"http://\\1\" target=_blank>\\1</a>",$Text);
	$Text = preg_replace("/\[url\]([^\[]*)\[\/url\]/is",                "<a href=\"\\1\" target=_blank>\\1</a>",$Text);
	$Text = preg_replace("/\[img\](.+?)\[\/img\]/is",                   "<img src=\\1>",$Text);
	$Text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is",         "<font color=\\1>\\2</font>",$Text);
	$Text = preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is",           "<font size=\\1>\\2</font>",$Text);
	$Text = preg_replace("/\[sup\](.+?)\[\/sup\]/is",                   "<sup>\\1</sup>",$Text);
	$Text = preg_replace("/\[sub\](.+?)\[\/sub\]/is",                   "<sub>\\1</sub>",$Text);
	$Text = preg_replace("/\[pre\](.+?)\[\/pre\]/is",                   "<pre>\\1</pre>",$Text);
	$Text = preg_replace("/\[email\](.+?)\[\/email\]/is",               "<a href='mailto:\\1'>\\1</a>",$Text);
	$Text = preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis",        "color_txt('\\1')",$Text);
	$Text = preg_replace("/\[emot\](.+?)\[\/emot\]/eis",                "emot('\\1')",$Text);
	$Text = preg_replace("/\[i\](.+?)\[\/i\]/is",                       "<i>\\1</i>",$Text);
	$Text = preg_replace("/\[u\](.+?)\[\/u\]/is",                       "<u>\\1</u>",$Text);
	$Text = preg_replace("/\[b\](.+?)\[\/b\]/is",                       "<b>\\1</b>",$Text);
	$Text = preg_replace("/\[quote\](.+?)\[\/quote\]/is",               "<div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $Text);
	$Text = preg_replace("/\[code\](.+?)\[\/code\]/eis",                "highlight_code('\\1')", $Text);
	$Text = preg_replace("/\[php\](.+?)\[\/php\]/eis",                  "highlight_code('\\1')", $Text);
	$Text = preg_replace("/\[sig\](.+?)\[\/sig\]/is",                   "<div class='sign'>\\1</div>", $Text);
	$Text = preg_replace("/\\n/is",                                     "<br/>",$Text);
   	return $Text;
 }
 