<?php if(!defined('IN_SYS')) exit();
/**
 * 登录记录Business类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: LoginBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 class LoginBusiness extends Business
 {
	/* 内容数据表 */
	protected $tableName = 'common_login';

	/**
	 * 获取所以数据列表
	 *
	 * @param array   $map    条件
	 * @param string  $field  字段
	 * @param string  $order  排序
	 * @param integer $limit  分页限制
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getModelByMap($map, $field = '*', $order = 'ID DESC', $limit=25) {
		$data = array();

		/* 获取 */
		if( empty($map) ) {
			if ($limit) {
				$data = $this->field($field)->order($order)->findPage($limit);
			} else {
				$data = $this->field($field)->order($order)->findAll();
			}
		} else {
			if ($limit) {
				$data = $this->where($map)->field($field)->order($order)->findPage($limit);
			} else {
				$data = $this->where($map)->field($field)->order($order)->findAll();
			}
		}
		return $data;
	}


	/**
	 * 根据ID详细信息
	 *
	 * @param mixed   $id  应用ID
	 * @param string  $field   字段
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getloginInfo($id) {
		$map['id'] = $id;
		return $this->where($map)->field($field)->find();
	}


	/**
	 * 登录记录
	 *
	 * @param intger  $uid  用户ID
	 * @param integer $role 角色
	 *
	 * @return void
	 */
	public function record($uid, $role=-1) {

		global $dc;

		/* 登录信息  */
		$form['UserID']	    = $uid;
		$form['Role']	    = $role;
		$form['IP']		    = get_client_ip();
		$form['Place']	    = convert_ip($form['IP']);
		$form['CreateTime']	= time();


		/* 具体方法   */
		$form['App']        = $dc['_app'];
		$form['Controller'] = $dc['_mod'];
		$form['Action']     = $dc['_act'];


		/* 请求内容  */
		$data = array();
		if(!empty($_POST))$data[] = serialize($_POST);
		if(!empty($_GET)) $data[] = serialize($_GET);
		$form['Data']= serialize($data);


		/* 参数过滤 */
		if( empty( $form['App']) && empty($form['Controller']) && empty($form['Action']) ) return false;


		/* 添加内容 */
		$res = $this->add($form);
		if( $res ) {
			return true;
		} else {
			return false;
		}
	}
 }
