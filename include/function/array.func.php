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
 * @version     $Id: array.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 数组函数库
 */ 
 
/**
 * 去一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 *
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * 
 * @return 返回新的一维数组
 */
 function getSubByKey($pArray, $pKey="", $pCondition=""){
    $result = array();
    foreach($pArray as $temp_array){
    	if(is_object($temp_array)){
    		$temp_array = (array) $temp_array;
    	}
        if((""!=$pCondition && $temp_array[$pCondition[0]]==$pCondition[1]) || ""==$pCondition) {
            $result[] = (""==$pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
        }
    }
    return $result;
 }
 
 /**
  * 获取多维数组子数组的键值
  *
  */
 function getMultiArraySubByKey($pArray, $pKey = "") {
 
	/* 返回数组 */
    $result = array();
	$result = getSubByKey($temp_array,$pKey);
	
    foreach($pArray as $temp_array) {
    	if(is_object($temp_array)) {
    		$temp_array = (array) $temp_array;
    	}
		
    	foreach ( $temp_array as $value){
    		if(is_array($value)){
    			$result = array_merge(getSubByKey($value,$pKey),$result);
    		}
    	}
    }
	return $result;
 }

/**
 * 将两个二维数组根据指定的字段来连接起来，连接的方式类似sql查询中的连接
 *
 * @param $pArray1  一个二维数组
 * @param $pArray2  一个二维数组
 * @param $pFields  作为连接依据的字段
 * @param $pType    连接的方式，默认为左联，即在右面的数组中没有找到匹配的则对应左面的行，否则不对应
 *
 * @return 连接好的数组
 */
 function arrayJoin($pArray1, $pArray2, $pFields, $pType="left") {
    
	$result = array();
    
	foreach($pArray1 as $row1) {
        $is_join = false;
        foreach($pArray2 as $row2) {
            if(canJoin($row1, $row2, $pFields)) {
                $result[] = array_merge($row2, $row1);
                $is_join = true;
                break;
            }
        }

        /* 如果是左连接并且没有找到匹配的连接 */
        if($is_join==false && $pType=="left") {
            $result[] = $row1;
        }
    }
    return $result;
 }
 
 
 
 
/**
 * 判断两个行是否满足连接条件
 *
 * @param $pRow1   数组的一行
 * @param $pRow2   数组的一行
 * @param $pFields 作为连接依据的字段
 *
 * @return 是否可以连接
 */
 function canJoin($pRow1, $pRow2, $pFields) {
    $field_array = explode(",", $pFields);
    foreach($field_array as $key) {
        if(strtolower($pRow1[$key])!=strtolower($pRow2[$key])) {
            return false;
		}
    }
    return true;
 }


/**
 * 根据指定的键对数组排序
 *
 * 用法：
 * @code php
 * $rows = array(
 *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
 *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
 *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
 *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
 *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
 *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
 * );
 *
 * $rows = sortByCol($rows, 'id', SORT_DESC);
 * dump($rows);
 *
 *  输出结果为：
 *  array(
 *      array('id' => 6, 'value' => '6-1', 'parent' => 3),
 *      array('id' => 5, 'value' => '5-1', 'parent' => 2),
 *      array('id' => 4, 'value' => '4-1', 'parent' => 2),
 *      array('id' => 3, 'value' => '3-1', 'parent' => 1),
 *      array('id' => 2, 'value' => '2-1', 'parent' => 1),
 *      array('id' => 1, 'value' => '1-1', 'parent' => 1),
 *  )
 * @endcode
 *
 * @param array $array 要排序的数组
 * @param string $keyname 排序的键
 * @param int $dir 排序方向
 *
 * @return array 排序后的数组
 */
 function sortByCol($array, $keyname, $dir = SORT_ASC) {
    return sortByMultiCols($array, array($keyname => $dir));
 }

 
/**
 * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * 用法：
 * @code php
 * $rows = sortByMultiCols($rows, array(
 *     'parent' => SORT_ASC,
 *     'name' => SORT_DESC,
 * ));
 * @endcode
 *
 * @param array $rowset 要排序的数组
 * @param array $args 排序的键
 *
 * @return array 排序后的数组
 */
 function sortByMultiCols($rowset, $args) {
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir)
    {
        foreach ($rowset as $offset => $row)
        {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    
    if (empty($sortArray) || empty($sortRule)) { return $rowset; }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
 }
 
/**
 * 二维数组按照指定的键值进行排序
 * 
 * @param array  $arr    待排序的数组
 * @param string $keys   键值
 * @param string $type   排序方式 asc、desc
 */
 function array_sort($arr,$keys,$type='asc'){ 
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
        $new_array[$k] = $arr[$k];
    }
    return $new_array; 
 }
 
 
/**
 * 把返回的数据集转换成Tree
 *
 * @access public
 *
 * @param  array   $list   要转换的数据集
 * @param  string  $pid    parent标记字段
 * @param  string  $level  level标记字段
 *
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
	/* 创建Tree */
    $tree = array();
	
    if( is_array($list) ) {
		
		/* 创建基于主键的数组引用 */
        $refer = array();
		
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
		
        foreach ($list as $key => $data) {
			
			/* 判断是否存在parent */
            $parentId = $data[$pid];
			
            if ($root == $parentId) {
                $tree[] =& $list[$key];
				
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 对查询结果集进行排序
 *
 * @access public
 *
 * @param  array   $list   查询结果
 * @param  string  $field  排序的字段名
 * @param  array   $sortby 排序类型(asc:正向排序 desc:逆向排序 nat:自然排序)
 *
 * @return array
 */
 function list_sort_by($list,$field, $sortby='asc')  {
	
   if( is_array($list) ) {
	
       $refer = $resultSet = array();	
       foreach ($list as $i => $data)  $refer[$i] = &$data[$field];
	
       switch ($sortby) {
			case 'asc': 
				/* 正向排序 */
                asort($refer);
                break;
				
			case 'desc':
				/* 逆向排序 */
                arsort($refer);
                break;
				
			case 'nat': 
				/* 自然排序 */
                natcasesort($refer);
                break;
       }
	
       foreach ( $refer as $key=> $val) $resultSet[] = &$list[$key];
	
       return $resultSet;
   }
   return false;
 }

/**
 * 在数据列表中搜索
 *
 * @access public
 *
 * @param array $list 数据列表
 * @param mixed $condition 查询条件   支持 array('name'=>$value) 或者 name=$value
 *
 * @return array
 */
 function list_search($list,$condition) {

    if(is_string($condition)) {
        parse_str($condition,$condition);
	}
	
    /* 返回的结果集合 */
    $resultSet = array();
	
    foreach ($list as $key=>$data) {
		
        $find = false;
		
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find  = preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)  $resultSet[] = &$list[$key];
    }
    return $resultSet;
 }
 
 
 /**
  * 数组转换为json格式
  * 
  * @param $rarray 被转换的数组
  * 
  * @return string
  */  
 function arrayToJson( $array ) { 	
 	
 	if( !is_array( $array ) ) return false;
 	
 	$associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
 	if( $associative ) {
 
 		$construct = array();
 		foreach( $array as $key => $value ){
 
 			// We first copy each key/value pair into a staging array,
 			// formatting each key and value properly as we go.
 
 			// Format the key:
 			if( is_numeric($key) ){
 				$key = "key_$key";
 			}
 			$key = "\"".addslashes($key)."\"";
 
 			// Format the value:
 			if( is_array( $value )){
 				$value = arrayToJson( $value );
 			} else if( !is_numeric( $value ) || is_string( $value ) ){
 				$value = "\"".addslashes($value)."\"";
 			}
 
 			// Add to staging array:
 			$construct[] = "$key: $value";
 		}
 
 		// Then we collapse the staging array into the JSON form:
 		$result = "{ " . implode( ", ", $construct ) . " }";
 
 	} else { // If the array is a vector (not associative):
 
 		$construct = array();
 		foreach( $array as $value ){
 
 			// Format the value:
 			if( is_array( $value )){
 				$value = arrayToJson( $value );
 			} else if( !is_numeric( $value ) || is_string( $value ) ){
 				$value = "\"".addslashes($value)."\"";
 			}
 
 			// Add to staging array:
 			$construct[] = $value;
 		}
 
 		// Then we collapse the staging array into the JSON form:
 		$result = "[ " . implode( ", ", $construct ) . " ]";
 	} 
 	return $result;
 }
 
 /**
  * 将字符串转换成数组 
  * 
  * @return array
  */
 function string2array($info) {
        if($info == '') return array();
        $info=stripcslashes($info);
        eval("\$r = $info;");
        return $r;
 }
 
 /**
  * 将数组转换成 字符串
  * 
  * @return string
  */
 function array2string($info) {
	if($info == '') return '';
	if(!is_array($info)) $string = stripslashes($info);
	foreach($info as $key => $val) $string[$key] = stripslashes($val);
	return addslashes(var_export($string, TRUE));
 }
 
  /**
  * 获取数组维数深度
  * 
  * @return interger
  */
 function array_depth($array) { 
    static $offset = 0; 
    $arr_str = serialize($array); 
    $num = substr_count($arr_str,'{'); 
    $result = array(); 
    for($i=0;$i<$num;$i++){ 
        $l_pos = strpos($arr_str, '{', $offset); 
        $temp_str = substr($arr_str,0,$l_pos); 
        $offset = $l_pos + 1; 
       $result[] = substr_count($temp_str,'{')-substr_count($temp_str,'}'); 
    } 
   array_multisort($result,SORT_DESC); 
   return ++$result[0];   

} 

 /**
  * 对数组插入元素
  * 
  * @param array   $array      数组
  * @param mexid   $value      插入的值
  * @param integer $position   位置
  * 
  * @return array 
  */
 function array_insert($array, $value, $position=0){ 
	$fore = ($position==0) ? array(): array_splice($array, 0, $position);  
	$fore[] = $value;
	$ret = array_merge($fore,$array);
	return $ret;
 }
