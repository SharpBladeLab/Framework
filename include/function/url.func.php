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
 * @version     $Id: url.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * URL函数库
 */

/**
 * URL重定向
 *
 * @param string  $url  地址
 * @param integer $time 时间
 * @param string  $msg  跳转时的提示信息
 *
 * @return void
 */
 function redirect($url,$time=0, $msg='') {
 
    /* 多行URL地址支持 */
    $url = str_replace(array("\n", "\r"), '', $url);
	
	/* 提示信息 */
    if(empty($msg)) {
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	}
	
    if ( !headers_sent() ) {
        /* redirect   */ 
        if( 0===$time ) {
            header("Location: ".$url);
        } else {
            header("refresh:{$time};url={$url}");
            $str = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            $msg = $str . $msg;
            echo($msg);
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0) $str   .=   $msg;
        exit($str);
    }
 }

/**
 * 短地址 
 *
 * @return string
 */
 function getShortUrl($url) {
	return Helper::createService('ShortUrl')->getShort( htmlspecialchars_decode($url) );
 }
