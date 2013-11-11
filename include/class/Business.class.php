<?php
/**
 * The model class file of Tiwer Developer Framework.
 *
 * Tiwer Developer Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
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
 * @version     $Id: Business.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * 商务逻辑父类
 */
 abstract class Business extends Model 
 {
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}
	
	
	/**
	 * 根据条件获取内容
	 *
	 * @param mixed   $map    条件
	 * @param string  $field  字段
	 * @param integer $limit  显示条数
	 * @param string  $order  排序
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getModelByMap($map=false, $field = '*', $limit = 20, $order = 'ID ASC') {
	
		$data = array();
	
		/* 获取 */
		if( empty($map) ) {
			$data = $this->field($field)->order($order)->findPage($limit);
		} else {
			$data = $this->where($map)->field($field)->order($order)->findPage($limit);
		}
		return $data;
	}
	
	
	/**
	 * 获详细信息
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getInfo($id, $field = '*') {
		$data = array();
		
		if( empty($id) ) {
			$data = $this->field($field)->find();
		} else {
			$map[$this->getPk()] = $id;
			$data = $this->where($map)->field($field)->find();
		}
		return $data;
	}	
 }
 