<?php if(!defined('IN_SYS')) exit();
/**
 * 行业Business
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: IndustryBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 class IndustryBusiness extends Business
 {

	protected $tableName = 'common_industry';

	/**
     * 当指定pid时，仅查询该父行业的所有子行业；否则查询所有行业
     *
	 * @param $pid 父行业ID
	 * @return array
	 */
	public function getIndustryList($pid = -1) {
		$map = array();
		$pid != -1 && $map['ParentID'] = $pid;
		return $this->where($map)->order('`ID` ASC')->findAll();
	}

	/**
     * 获取该ID的上级
     *
	 * @param $pid 父行业ID
	 * @return array
	 */
	public function getIndustryListParent($id = -1) {
		$map = array();
		$id != -1 && $map['ID'] = $id;
		$data=$this->where($map)->order('`ID` ASC')->find();
		$omp['ParentID']=$data['ParentID'];
		$data2=$this->where($omp)->order('`ID` ASC')->findall();
		return $data2;
	}

	/**
	 * 获得行业树形结构
	 *
     * @param  $pid 父行业ID
     * @return array
	 */
	public function getIndustryTree($pid) {
		$output	= array();
		$list	= $this->getIndustryList($pid);
		if($list){
			foreach ($list as $k1 => $p) {
				$p['next']=$res=$this->getIndustryTree($p['ID']);
			    $output[$k1]=$p;
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
	public function getIndustryNameById($id) {
		$data = $this->getInfo($id);
		return $data['Title'];
	}

	/**
	 * 获得行业最高的深度，即分几层
	 *
	 * @param intger $id ID
	 */
	public function getZindex() {
		$data=$this->getIndustryTree(0);
		return ceil((array_depth($data)-1)/2);
	}


	/**
	 * 获取名称路径
	 *
	 * @param integer $id 地区ID
	 */
	public function getIndustryName($id) {
		$area = '';
		$data = $this->where(array("ID"=>$id))->find();
		if( empty($data['ParentID']) ) {
			return $data['Title'];
		} else {
			$area .= $this->getIndustryName($data['ParentID']);
		}
		$area.="_";
		$area.=$data['Title'];
		return $area;
	}
 }


