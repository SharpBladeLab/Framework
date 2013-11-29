<?php if(!defined('IN_SYS')) exit();
/**
 * Key-value存储引擎 Business类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: ConfigBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 class ConfigBusiness extends Business
 {

	/* 数据库表名 */
	protected $tableName = 'common_config';
	/* 默认列表名 */
	protected $list_name = 'global';
	/* 数据表字段 */
	protected $fields = array ( 0 => 'id', 1 => 'uid', 2 => 'list', 3 => 'key', 4 => 'value', 5 => 'mtime', '_autoinc' => true, '_pk' => 'id' );


	/**
	 * 写入列表
	 *
	 * @param $listName  列表名称
	 * @param $listData  列表数据
	 *
	 * @return mixed
	 */
	public function putList($listName='', $listData=array() ) {


		/* 初始化list_name */
		$listName =	$this->_strip_key($listName);


		/* 格式化数据 */
		if(is_array($listData)) {
			$insert_sql .= "REPLACE INTO __TABLE__ (`list`,`key`,`value`) VALUES ";
			foreach($listData as $key=>$data){
				$insert_sql	.=	" ('$listName','$key','".serialize($data)."') ,";
			}
			$insert_sql	=	rtrim($insert_sql,',');


			/* 插入数据列表 */
			$result	= $this->execute($insert_sql);
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * 读取列表
	 *
	 * @param string $listName  列表名称
	 *
	 * @return mixed
	 */
	public function getList( $listName = '' ) {

		/* 初始化list_name */
		$listName = $this->_strip_key($listName);
		$map['`list`'] = $listName;
		$result	= $this->order('id ASC')->where($map)->findAll();


		if( !$result ) {
			return false;
		} else {
			/* 反序列化KEY值 */
			foreach($result as $v) {
				$datas[$v['key']] = unserialize($v['value']);
			}
		}
		return $datas;
	}


	/**
	 * 写入数据
	 *
	 * @param string $key 键
	 * @param string $value 值
	 * @param boolean $replace 是否是更新操作
	 *
	 * @return mixed 操作结果
	 */
	public function put($key, $value='', $replace=false) {
		$key = $this->_strip_key($key);
		$keys =	explode(':',$key);
		$data =	serialize($value);

		if( $replace ) {
			$insert_sql	=	"REPLACE INTO __TABLE__ ";
		} else {
			$insert_sql	=	"INSERT INTO __TABLE__ ";
		}


		/* 执行SQL操作 */
		$insert_sql	.=	"(`list`,`key`,`value`) VALUES ('$keys[0]','$keys[1]','$data') ";
		$result	= $this->execute($insert_sql);
		return $result;
	}

	/**
	 * 读取一条数据，todo：读取多条 list1:key1,list2:key2
	 */
	public function get($key) {

		$key = $this->_strip_key($key);
		$keys =	explode(':',$key);
		$map['`list`'] = $keys[0];
		$map['`key`'] = $keys[1];

		$result	= $this->where($map)->find();
		if( !$result ) {
			return false;
		} else {
			return unserialize($result['value']);
		}
	}


	/**
	 * 修改数据数据
	 *
	 * @param string $key   键
	 * @param string $value 值
	 *
	 * @return array
	 */
	public function save($key,$value='') {
		$result	= $this->put($key,$value,true);
		return $result;
	}

	/**
	 * 批量读取数据 非必要
	 *
	 * @param string $listName 列表名称
	 * @param string $keys    键值
	 *
	 * @return mixed
	 */
	public function getAll($listName, $keys) {

		/* 用于获取list下所有数据 Nonant */
		if( $key ) {
			$keysArray	=	$this->_parse_keys($keys);
			$map['`key`'] =	array('in', $keysArray);
		}


		$map['`list`'] = $listName;
		$result = $this->where($map)->findAll();
		if(!$result) {
			return false;
		} else {
			foreach($result as $v){
				$datas[$v['list']][$v['key']] = unserialize($v['value']);
			}
		}
		return $datas;
	}



	/**
	 * 解析过滤输入
	 *
	 * @param string $input 输入参数
	 *
	 * @access ptotected
	 *
	 * @return string 过滤后和参数
	 */
	protected function _parse_keys($input='') {
		$output	='';
		if(is_array($input) || is_object($input)) {
			foreach($input as $v){
				$output[] =	$this->_strip_key($v);
			}

		} elseif( is_string($input) ) {
			$output[]	=	$this->_strip_key($input);
		} else {
			/* 异常处理 */
		}
		return $output;
	}


	/**
	 * 过滤key,允许格式 数字字母下划线，list:key 不允许出现html代码 和这些符号 ' " & * % ^ $ ? ->
	 *
	 * @param  string $key 要过滤的字符串
	 *
	 * @return string 过滤后的字符串
	 */
	protected function _strip_key( $key = '' ) {

		/* 为空的情况 */
		if( $key == '' ) {
			return $this->list_name;

		} else {
			/* 字符替换 */
			$key = strip_tags($key);
			$key = str_replace(array('\'','"','&','*','%','^','$','?','->'),'',$key);
			return $key;
		}
	}

 }

