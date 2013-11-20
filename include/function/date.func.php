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
 * @version     $Id: date.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 时间函数库
 */

/**
 * 友好时间显示
 * 
 * @param integer $sTime
 * @param string  $type
 * @param string  $alt
 */
 function friendlyDate($sTime, $type='normal', $alt='false') {

	/* sTime=源时间，cTime=当前时间，dTime=时间差 */
	$cTime = time();
	$dTime = $cTime - $sTime;
	$dDay  = intval(date("Ymd",$cTime)) - intval(date("Ymd",$sTime));
	$dYear = intval(date("Y",$cTime))   - intval(date("Y",$sTime));

	
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

	} elseif( $type == 'mohu' ) {
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


/**
 * 获取每月第一天与最后一天
 *
 * @param int $date 月份的时间戳
 */
 function getthemonth($date) {
	$firstday = date('Y-m-01', strtotime($date));
	$lastday  = date('Y-m-d',  strtotime("$firstday +1 month -1 day"));
	return array($firstday,$lastday);
 }



/**
 * 求两个日期之间每个月的开始和结束时间
 *
 * @param integer $start  开始时间<unix 时间戳>
 * @param integer $end    结束时间<unix 时间戳>
 *
 * @return array
 */
 function timeMonthBetween($start, $end){

	$d_s = strtotime(date('Y-m', $start));
	$d_e = strtotime(date('Y-m', $end));

	$num = 1;
	$dates = array();

	while ($start<=$end){
		if($d_s == $d_e){
			$dates[] = array(
	 			'time_start' => date('Y-m-d',$start)." 00:00:00",
	 			'time_end' => date('Y-m-d',$end)." 23:59:59",
			);
			break;


		} else {
			if($num == 1){
				$dates[] = array(
 					'time_start' => date('Y-m-d',$start)." 00:00:00",
 					'time_end' => date('Y-m-t',$start)." 23:59:59",
				);

			}else{
				$dates[] = array(
 					'time_start' => date('Y-m' . '-01',$start)." 00:00:00",
 					'time_end' => date('Y-m-t',$start)." 23:59:59",
				);
			}
			$start = strtotime('+1 month',$start);
		}
		$num++;
	}
	$pop_ele = array_pop($dates);
	$pop_ele['time_end'] = date('Y-m-d',$end)." 23:59:59";

	array_push($dates, $pop_ele);
	return $dates;
}

/**
 * 获取一度中的四个享度时间区间
 *
 * @param integer $year
 */
 function timeQuarterBetween($year) {

 	/* 返回数据 */
 	$array = array();


 	$now = time();
 	$temp = date('Y', $now);
 	$temp < $year && $year = $temp;


 	/* 第一季度时间区间 */
 	if( $now > strtotime("$year-01-01") ) {
 		$array[0]['time_start'] = "$year-01-01 00:00:00";
 		$array[0]['time_end']   = "$year-03-".date('t',strtotime("$year-03"))." 23:59:59";
 	}

 	/* 第二季度时间区间 */
 	if( $now > strtotime("$year-04-01") ) {
 		$array[1]['time_start'] = "$year-04-01 00:00:00";
 		$array[1]['time_end']   = "$year-06-".date('t',strtotime("$year-06"))." 23:59:59";
 	}

 	/* 第三季度时间区间 */
 	if( $now > strtotime("$year-07-01") ) {
 		$array[2]['time_start'] = "$year-07-01 00:00:00";
 		$array[2]['time_end']   = "$year-09-".date('t',strtotime("$year-09"))." 23:59:59";
 	}

 	/* 第四季度时间区间 */
 	if( $now > strtotime("$year-10-01") ) {
 		$array[3]['time_start'] = "$year-10-01 00:00:00";
 		$array[3]['time_end']   = "$year-12-".date('t',strtotime("$year-12"))." 23:59:59";
 	}

 	return count($array)>0 ? $array : false;
}


/**
 * 求两个日期之间每个年的开始和结束时间
 *
 * @param integer $start  开始时间<unix 时间戳>
 * @param integer $end    结束时间<unix 时间戳>
 *
 * @return array
 */
function timeYearBetween($start, $end){

	$d_s = intval(date('Y', $start));
	$d_e = intval(date('Y', $end));

	$dates = array();
	$num =  $d_e - $d_s;
	if( $num <= 0  ) {
		$dates[] = array(
 				'time_start' => "$d_s-01-01 00:00:00",
 				'time_end' => "$d_s-12-31 23:59:59",
		);
	} else  {
		$year = $d_s;
		for($i=0; $i<=$num; $i++) {
			$dates[] = array(
 				'time_start' => "$year-01-01 00:00:00",
 				'time_end' => date('Y-m-d', strtotime("$year-12-01 +1 month -1 day"))." 23:59:59",
			);
			$year++;
		}
	}
	return $dates;
}


/**
 * 求两个日期之间每个天的开始和结束时间
 *
 * @param integer $start  开始时间<unix 时间戳>
 * @param integer $end    结束时间<unix 时间戳>
 *
 * @return array
 */
function timeDayBetween($start, $end) {
    
    /* 参数过滤 */
    if( $start>$end ) return false;
    
    
    /* 返回数据 */
 	$dates = array();
    
    
	/* 转换年、月、日  */
	$year  = date('Y', strtotime(date("Y-m-d", $start)));
	$month = date('m', strtotime(date("Y-m-d", $start)));
	$day   = date('d', strtotime(date("Y-m-d", $start)));	
    
	
	/* 生成时间区间  */
	for( $i=$start; $i<=$end; $i+=86400 ) {
		$y = mktime(0,0,0, $month, $day, $year);
		$temp = date("Y-m-d", $y+$j*24*3600);
		$dates[] = array(
 				'time_start' => $temp." 00:00:00",
 				'time_end'   => $temp." 23:59:59",
		);
		$j++;
	}
	return count($dates)>0 ? $dates : false;
}