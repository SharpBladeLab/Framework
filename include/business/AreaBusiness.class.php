<?php if(!defined('IN_SYS')) exit();
/**
 * 地区Business
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: AreaBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 class AreaBusiness extends Business
 {

	protected $tableName = 'common_area';
	private $__depth = 3;

	/**
     * 当指定pid时，仅查询该父地区的所有子地区；否则查询所有地区
     *
	 * @param $pid 父地区ID
	 * @return array
	 */
	public function getAreaList($pid = -1) {

		if($pid!=-1){
			$das=$this->where("ParentID=".$pid)->findAll();
			return $das;
			//foreach($das as $kk=>$vv){
//				$ids.=$vv['ID'].",";
//			}
//			$ids=substr($ids,0,strlen($ids)-1);
//			$sql="ParentID in(".$ids.")";
//			$ds=$this->where($sql)->findAll();
//			foreach($ds as $k=>$v){
//				$bbs.=$v['ID'].",";
//			}
//			$bbs=substr($bbs,0,strlen($bbs)-1);
		}

		//$map = array();
		//$pid != -1 && $map = "ParentID=".$pid." and id not in(".$bbs.")";

		//return $this->where($map)->order('`ID` ASC')->findAll();
	}

		/**
     * 获取该ID的上级
     *
	 * @param $pid 父行业ID
	 * @return array
	 */
	public function getAreaListParent($id = -1) {
		$map = array();
		$id != -1 && $map['ID'] = $id;
		$data=$this->where($map)->order('`ID` ASC')->find();
		$omp['ParentID']=$data['ParentID'];
		$data2=$this->where($omp)->order('`ID` ASC')->findall();
		return $data2;
	}

	/**
	 * 目前简单处理，仅取前两级地区的结构树
	 *
     * @param  $pid 父地区ID
     * @return array
	 */
	public function getAreaTree($pid) {

		$output	= array();
		$list	= $this->getAreaList();

		/* 先获取省级 */
		foreach ($list as $k1 => $p) {
			if ($p['ParentID'] == 0) {

				/* 获取当前省的市 */
				$city  = array();
				foreach ($list as $k2 => $c) {
					if($c['ParentID'] == $p['ID']) {
						$city[] = array($c['ID'] => $c['Title']);
						unset($list[$k2]);
					}
				}
				$output['provinces'][] = array('id'=> $p['ID'],'name'	=> $p['Title'],'citys'=> $city,);
				unset($list[$k1], $city);
			}
		}
		unset($list);
		return $output;
	}

	/**
	 * 获取名称
	 *
	 * @param intger $id ID
	 */
	public function getAreaNameById($id) {
		$data = $this->getInfo($id);
		return $data['Title'];
	}

	/**
	 * 获取名称路径
	 *
	 * @param integer $id 地区ID
	 */
	public function getPathName($id) {
		$area = '';
		$data = $this->where(array("ID"=>$id))->find();
		if( empty($data['ParentID']) ) {
			return $data['Title'];
		} else {
			$area .=$this->getPathName($data['ParentID']);
		}
		$area.="_";
		$area.=$data['Title'];
		return $area;
	}

 	/**
	 * 获取ID路径
	 *
	 * @param integer $id 地区ID
	 */
	public function getPathID($id) {
		$area = '';
		$data = $this->where(array("ID"=>$id))->find();
		if( empty($data['ParentID']) ) {
			return $data['ID'];
		} else {
			$area = $area. ','.  $this->getPathID($data['ParentID']);
		}

		$area = $area. ','. $data['ID'];
		return $area;
	}

	/**
	 * 获取详情
	 *
	 * @param 内容ID $id
	 */
	public function getInfo($id) {

		/* 获取数据 */
		if( $id <=0 ) return false;
		$data = $this->where(array("ID"=>$id))->find();
		if ( empty($data['ID']) ) {
			return false;
		}

		return $data;
	}
 }


