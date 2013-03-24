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
 * @version     $Id: Xml.class.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 解析XML类
 */
 class Xml extends Plugin  
 {
 	/**
 	 * xml字符内容
 	 * 
 	 * @param string $xml
 	 */
	public function decode($xml) {		
		$values = array();
		$index  = array();
		$array  = array();		
		$parser = xml_parser_create('utf-8');
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $values, $index);
		xml_parser_free($parser);
		
		$i = 0;		
		$name = $values[$i]['tag'];
		$array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
		$array[$name] = $this->_struct_to_array($values, $i);
		return $array;
	}	 
	/**
	 * xml编码
	 *
	 * @param array $data      数据
	 * @param strin $encoding  编码
	 * @param string $root     根结点
	 *
	 * @return string 生成的Xml
	 */
	public function encode($data, $encoding='utf-8', $root=NULL) {	
	    $xml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
	    if ( isset($root) ) $xml.= '<'.$root.'>';
	    $xml.= $this->_data_to_xml($data);
	    if ( isset($root) ) $xml.= '</'.$root.'>';		
	    return $xml;
	}
	
	
	
	
	private function _struct_to_array($values, &$i) {
		$child = array();
		if (isset($values[$i]['value'])) 
		array_push($child, $values[$i]['value']);
		
		while ($i++ < count($values)) {
			switch ($values[$i]['type']) {
				case 'cdata':
					array_push($child, $values[$i]['value']);
					break;
				
				case 'complete':
					$name = $values[$i]['tag'];
					if(!empty($name)) {
						$child[$name]= ($values[$i]['value'])?($values[$i]['value']):'';
						if(isset($values[$i]['attributes']))  {                   
							$child[$name] = $values[$i]['attributes'];
						}
					}   
				break;
				
				case 'open':
					$name = $values[$i]['tag'];
					$size = isset($child[$name]) ? sizeof($child[$name]) : 0;
					$child[$name][$size] = $this->_struct_to_array($values, $i);
					break;
				
				case 'close':
					return $child;
					break;
			}
		}
		return $child;
	}	
	/*  数据转换成XML */
	private function _data_to_xml($data) {
		
		/* 对象则转换成变量 */
	    if(is_object($data)) $data = get_object_vars($data);	
	   	
		/* 循环生成 */
	     $xml = '';	
	    foreach( $data as $key=>$val ) {
		
	        is_numeric($key) && $key="item id=\"$key\"";
	        $xml.="<$key>";		
	        $xml.=(is_array($val)||is_object($val)) ? $this->_data_to_xml($val):$val;
	        list($key,)=explode(' ',$key);
	        $xml.="</$key>";
	    }		
	    return $xml;
	} 	
}