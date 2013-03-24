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
 * @version     $Id: date.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 时间函数库
 */

 /**
  * 友好时间显示
  *
  * @param 
  */
 function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
 
	/* sTime=源时间，cTime=当前时间，dTime=时间差 */
	$cTime = time();
	$dTime = $cTime - $sTime;
	$dDay  = intval(date("Ymd",$cTime)) - intval(date("Ymd",$sTime));
	$dYear = intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	
	/* normal：n秒前，n分钟前，n小时前，日期 */
	if( $type=='normal' ) {
	
		if( $dTime < 60 ) {
			return $dTime."秒前";
		} elseif( $dTime < 3600 ) {
			return intval($dTime/60)."分钟前";
		} elseif( $dTime >= 3600 && $dDay == 0  ) {			
			return '今天'.date('H:i',$sTime);
		} elseif($dYear==0){
			return date("m月d日 H:i",$sTime);
		}else{
			return date("Y-m-d H:i",$sTime);
		}
		
	}elseif( $type == 'mohu' ) {		
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif( $dDay > 0 && $dDay<3 ){
			return intval($dDay)."天前";
		}elseif( $dDay >= 3 ){
			return "n天前";
		}elseif( $dDay >= 30 ){
			return "n个月前";
		}
		
	} elseif($type=='full') {
	
		return date("Y-m-d , H:i:s",$sTime);
		
	} elseif($type=='ymd') {
	
		return date("Y-m-d",$sTime);
		
	} else {
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif($dYear==0){
			return date("Y-m-d H:i:s",$sTime);
		}else{
			return date("Y-m-d H:i:s",$sTime);
		}
	}
 }
 