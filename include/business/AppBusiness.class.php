<?php if(!defined('IN_SYS')) exit();
/**
 * 应用Business类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: AppBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
class AppBusiness extends Business
{
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
		$this->buildDomain();
	}


	/**
	 * 数据表名称
	 */
	protected $tableName = 'apps';

	/**
	 * 自动填充设置
	 */
	protected $_auto = array(
		array('CreateTime',  'time', '1', 'function'),
	);

	/**
	 * 自动验证设置
	 */
	protected $_validate = array(
		array('Title',  'require',  '应用名称必须填写！'),
		array('Name',   'require',  '唯一标实必须填写！'),
		array('Logo',   'require',  '应用图片必须填写！'),
	);



	/**
	 * 根据应用名称判断该应用是否存在
	 *
	 * @param string  $app_name 应用名称
	 * @param integer $app_id   应用ID
	 *
	 * @accsee public
	 *
	 * @return boolean  存在返回真，不存在返回假
	 */
	public function isAppNameExist($appname, $appid = 0) {
		$map['ID']	 = array('neq', $appid);
		$map['Name'] = $appname;
		return $this->where($map)->find() ? true : false;
	}


	/**
	 * 根据应用名判断该应用是否为活动
	 *
	 * @param string $app_name 应用名称
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function isAppNameActive($appname) {
		$map['Name']    = $appname;
		$map['Status']	= array('neq', 0);
		return $this->where($map)->find() ? true : false;
	}


	/**
	 * 根据应用ID判断该应用是否为活动
	 *
	 * @param string $app_name 应用名称
	 *
	 * @access public
	 *
	 * @return boolean
	 */
	public function isAppIdActive($appid) {
		$map['ID'] = $appid;
		$map['Status'] = array('neq', 0);
		return $this->where($map)->find() ? true : false;
	}


	/**
	 * 获取全部的应用
	 *
	 * @param string $field 字段
	 * @param string $order 排序条件
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getAllApp($field = '*', $order = 'Sort ASC,ID ASC') {
		return $this->field($field)->order($order)->findAll();
	}


	/**
	 * 获取全部的应用(代分页功能)
	 *
	 * @param integer $limit  显示多少条
	 * @param string  $field  字段
	 * @param string  $order  排序条件
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getAllAppByPage($limit = 20, $field = '*', $order = 'Sort ASC,ID ASC') {
		return $this->field($field)->order($order)->findPage($limit);
	}


	/**
	 * 获取开放的应用并代分页功能
	 *
	 * @param integer $limit 显示多少条
	 * @param string  $field 字段
	 * @param string  $order 排序条件
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getOpenAppByPage($limit = 20, $field = '*', $order = 'sort ASC,id ASC') {
		return $this->where('`Status`<>0')->field($field)->order($order)->findPage($limit);
	}


	/**
	 * 根据应用ID获取应用详细信息
	 *
	 * @param mixed   $app_id  应用ID
	 * @param string  $field   字段
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getAppDetailById($appid, $field = '*') {
		$app_id = is_array($appid) ? $appid : explode(',', $appid);
		$map['ID'] = array('in', $appid);
		if (count($app_id) <= 1) {
			return $this->where($map)->field($field)->find();
		} else {
			return $this->where($map)->field($field)->findAll();
		}
	}


	/**
	 * 根据应用名称获取应用详细信息
	 *
	 * @param string  $app_id  应用ID
	 * @param string  $field   字段
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function getAppDetailByName($appname) {
		$map['Name'] = $appname;
		return $this->where($map)->find();
	}


	/**
	 * 删除应用
	 *
	 * @param string  $app_id  应用ID
	 *
	 * @access public
	 *
	 * @return mixed
	 */
	public function deleteApp($appid) {
		$map['ID'] = intval($appid);
		return $this->where($map)->delete();
	}

	/**
	 * 创建相关文件夹
	 *
	 * @param String $appname 应用名称
	 *
	 * @return string  应用别名
	 */
	public function createAppPath($name) {
		$path = APPS_PATH.SEP.$name.SEP;

		/* 没有创建项目目录的话自动创建 */
		if(!is_dir($path)) mkdir($path, "0777", true);
		if( is_writeable(APP_PATH) ) {

			/* 创建应用文件目录 */
			$dirs = array ($path."controller",  $path."language",  $path."model",  $path."view",);
			foreach ($dirs as $dir) if(!is_dir($dir)) mkdir($dir,"0777",true);

			/* 自动写入目录安全文件 */
			foreach ($dirs as $dir) file_put_contents($dir.SEP.'index.html', '');

			/* 写入系统相关 */
			if(!is_file($path.'config.php')) {
				file_put_contents($path.'config.php',  "");
			}
			if(!is_file($path.'app.php')) {
				file_put_contents($path.'app.php',  "");
			}
			if(!is_file($path.'common.php')) {
				file_put_contents($path.'common.php',  "");
			}
		}
	}

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
	public function getAll($map, $field = '*', $order = 'ID ASC', $limit=false) {
		$data = array();
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
	 * 获取满足条件的应用
	 *
	 * @param string $where
	 *
	 * @return array
	 */
	public function getappformap($where){
		$data=$this->where($where)->findAll();
		return $data;
	}

	/**
	 * 获取授权的应用
	 *
	 * @param integer $companyid
	 * @param integer $userid
	 * @param integer $super
	 * @param integer $groupid
	 *
	 * @return array
	 */
	public function getmyapp($companyid,$userid,$super,$groupid){

		/* 商家超管调用购买的所有应用和系统自带应用 */
		$model=Helper::createModel('App','product');
		$rs=$model->getApps($companyid);

		$sql="";
		foreach($rs as $k=>$v){
			$sql.=$v['AppID'].",";
		}
		$sql="(".substr($sql,0,strlen($sql)-1).")";
		if($sql=="()"){
			$map="type=2 and `default`=1";
		}else{
			$map="(type=2 and `default`=1) or id in".$sql;
		}

		$data=$this->getappformap($map);

		if($super==1) return $data;



		/*  商家普通用户调用购买的所有应用（并且是被授权的应用）和系统自带应用  */
		$Role = Helper::createModel('Role','platform');
		$role = $Role->find($groupid);
		$assigns = unserialize($role['MenuID']);


		$newarr = array();
		foreach($data as $app) {
			if(isset($assigns[$app['ID']])) $newarr[] = $app;
		}
		return $newarr;




	}

	/**
	 * 获取公司的应用列表
	 *
	 * @param integer $companyId
	 */
	public function getCompanyApps($companyId) {
		$condition = '`Default`=1';
		$App = Helper::createModel('App', 'product');
		$companyApps = $App->getApps($companyId);
		if($companyApps) $condition .= " OR ID IN(".implode(', ', array_keys($companyApps)).")";

		$apps = array();
		$rows = $this->where($condition)->findAll();
		foreach($rows as $app) {
			$apps[$app['ID']] = $app;
		}
		return $apps;
	}


	/**
	 * 缓存域名列表
	 *
	 * @access public
	 */
	public function cacheDomain() {
		$data = array();
		$path = TEMPS_PATH.SEP.'sysinfo'.SEP;
		$name = 'domain';

		/* 是否过期  */
		if( file_exists( $path.$name.'.php' )  ) {
			$data = include $path.$name.'.php';
		} else {
			$data = $this->buildDomain($path, $name);
		}
		return $data;
	}

	/**
	 * 重新生成缓存
	 *
	 * @param string $path   路径
	 * @param string $name   名称
	 *
	 * @return array
	 */
	public function buildDomain() {
		$data = array();
		$path = TEMPS_PATH.SEP.'sysinfo'.SEP;
		$name = 'domain';


		/* 是否存在 */
		if(file_exists( $path.$name.'.php' )) return true;


		/* 生成域名缓存 */
		$arr = $this->field('Name, Domain')->where(" Status=1 AND Domain IS NOT NULL ")->order($order)->findAll();
		if ( count($arr)>0 )
		foreach ($arr as $key=>$value) {
			$temp = explode(',', $value['Domain']);
			foreach ( $temp as $val ) {
				$data[$val]=$value['Name'];
			}
		}
		Helper::createTempFile($name, $data, $path);
	}


}

