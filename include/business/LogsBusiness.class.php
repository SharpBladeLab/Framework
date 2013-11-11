<?php if(!defined('IN_SYS')) exit();
/**
 * 日志模型Business类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: LogsBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 class LogsBusiness extends Business
 {

	/**
	 * 内容数据表
	 */
	protected $tableName = 'common_logs';


	/**
	 * 添加系统后台日志
	 *
	 * @param string $message 操作信息
	 *
	 * @access public
	 *
	 * @return boolean  false/ture
	 */
	public function addSysLog($message, $userid) {
		$this->addLogs($message, 0 ,$userid);
	}


 	/**
	 * 添加前台日志
	 *
	 * @param string $message 操作信息
	 *
	 * @access public
	 *
	 * @return boolean  false/ture
	 */
	public function addPlatformLog($message, $userid) {
		$this->addLogs($message, 1 ,$userid);
	}


	/**
	 * 添加客户端请求日志
	 *
	 * @param string $message 操作信息
	 *
	 * @access public
	 *
	 * @return boolean  false/ture
	 */
	public function addClientLog($message){
		$this->addLogs($message, 1 ,0);
	}


	/**
	 * 添加日志
	 *
	 * @param string $message 信息
	 * @param string $type    类型
	 * @param string $userid  用户（0：匿名）
	 *
	 * @access public
	 *
	 * @return boolean  false/ture
	 */
	private function addLogs($message, $type=1, $userid) {
		global $dc;

		$form['App']        = $dc['_app'];
		$form['Controller'] = $dc['_mod'];
		$form['Action']     = $dc['_act'];
		$form['Createtime'] = time();
		$form['IP']         = get_client_ip();
		$form['Type']       = intval($type);
		$form['UserID']     = intval($userid);
		$form['Message']    = text($message);


		/* 请求内容  */
		$data = array();
		if(!empty($_POST))$data[] = serialize($_POST);
		if(!empty($_GET)) $data[] = serialize($_GET);
		$form['Data']= serialize($data);


		/* 参数过滤 */
		if( empty( $form['App']) && empty($form['Controller']) && empty($form['Action']) ) {
			return false;
		}

		/* 添加内容 */
		$res = $this->add($form);
		if( $res ) {
			return true;
		} else {
			return false;
		}
	}
 }
