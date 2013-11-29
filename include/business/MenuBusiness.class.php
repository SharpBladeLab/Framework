<?php if(!defined('IN_SYS')) exit();
/**
 * 菜单Business类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: MenuBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 class MenuBusiness extends Business
 {
	/**
	 * 数据表名称
	 */
	protected $tableName = 'apps_menu';

	/**
	 * 自动填充设置
	 */
	protected $_auto = array(
		array('CreateTime',  'time', '1', 'function'),
	);

	/**
	 * 数据验证
	 */
	protected $_validate = array(
		array('Name', 'require', '必须填写菜单名称'),
	);


	/* 可见性  */
	const VISIBLE_NONE  = 0;
	const VISIBLE_USER  = 1;
	const VISIBLE_SUPER = 2;
	public static $visibles = array(
		self::VISIBLE_NONE => '不显示',
		self::VISIBLE_USER => '显示',
		self::VISIBLE_SUPER => '超管显示',
	);

	/**
	 * 获取菜单地址
	 *
	 * @param array $menu  菜单
	 */
	public static function getUrl($menu) {
		if ( !empty($menu['Url']) )return $menu['Url'];

		if ( empty($menu['Symbol']) ) {
			$url = Helper::createLink($menu['App'].'/'.$menu['Controller'].'/'.$menu['Action']);
		} else {
			$url = Helper::createLink($menu['App'].'/'.$menu['Controller'].'/'.$menu['Action'], array('symbol'=>$menu['Symbol']));
		}
		return $url;
	}


	/**
	 * 根据用户组获取菜单
	 *
	 * @param integer $groupId
	 * @param string  $condition
	 */
	public function findByGroup($groupId, $condition = '1') {
		$menus = array();
		if(empty($menus)) {
			$Role = Helper::createModel('Role', 'platform');
			$group = $Role->field('MenuID')->find($groupId);
            $assigns = unserialize($group['MenuID']);
            $idArray = array();
            foreach($assigns as $menu) {
                $idArray = array_merge($idArray, $menu);
            }
            $ids = implode(',', $idArray);
			$condition .= " AND ID IN ({$ids})";
			$rows = $this->where($condition)->order(' Sort ASC , ID DESC ')->findAll();
			foreach($rows as $row) {
				$menus[$row['ID']] = $row;
			}
		}
		return $menus;
	}

	/**
	 * 根据应用获取菜单
	 *
	 * @param integer $typeid
	 * @param string $apps
	 * @param string $condition
	 */
	public function findByApp($typeid, $apps=null, $condition = '1') {
		$menus = array();
		if(empty($menus)) {
            $condition .= ' AND TypeID=' . $typeid;
			if( $apps !== null ) {
				$appIds = implode(',', $apps);
				$condition .= " AND AppID IN ({$appIds})";
			}
			$rows = $this->where($condition)->order(' Sort ASC , ID DESC ')->findAll();

			foreach($rows as $row) {
				$menus[$row['ID']] = $row;
			}
		}

		return $menus;
	}


    public function findMenu($app, $controller, $action, $condition = '1') {
        $app = strtolower($app);
        $controller = strtolower($controller);
        $action = strtolower($action);
        $condition = "App='{$app}' AND Controller='{$controller}' AND Action='{$action}' AND $condition";
        return $this->where($condition)->order('ID DESC')->find();
    }

	/**
	 * 数组转换为树形
	 *
	 * @param array $lists
	 *
	 * @return array
	 */
	public function getTree($lists) {
		$Tree = Helper::createPlugin('Tree');
		foreach( $lists as $key => $row ) {
			$Tree->setNode($row['ID'], $row['ParentID'], $row['Name'], $row);
		}
		$childs = $Tree->getChilds();
		$data = array();
		foreach($childs as $key => $row){
			$id = $childs[$key];
			$data[$key]['id'] = $id;
			$data[$key]['level'] = $Tree->getLayer($id);
			$data[$key]['arr'] = $Tree->getArr($id);
			$data[$key]['name'] = $Tree->getValue($id);
		}
		return $data;
	}

    public function getSimpleTree($lists, $checked = array()) {
        $tree = array();
        foreach($lists as $item) {
            $tree[] = array(
                'id' => $item['ID'],
                'pId' => $item['ParentID'],
                'name' => $item['Name'],
                'checked' => in_array($item['ID'], $checked),
            );
        }

        return $tree;
    }


	//---菜单类型-------------------------------------------------------------------------------------------

	/**
	 * 菜单类型列表
	 *
	 * @access public
	 */
	public function getTypeList() {
		$Model = Helper::createModel('apps_menu_type', '', true);
		return $Model->field('*')->order('ID ASC')->findAll();
	}
	/**
	 * 创建菜单类型
	 *
	 * @access public
	 */
	public function createType($app, $title) {
		if ( empty($app) || empty($title) ) return false;
		$Model = Helper::createModel('apps_menu_type', '', true);
		$data['Title'] =text($title);
		$data['App'] = text($app);
		return $Model->add($data);
	}

	/**
	 * 更新菜单类型
	 *
	 * @access public
	 */
	public function updateType($id, $app, $title) {
		if ( empty($id) ) return false;

		$Model = Helper::createModel('apps_menu_type', '', true);
		$data['ID'] = intval($id);
		$data['Title'] =text($title);
		$data['App'] = text($app);

		return $Model->save($data);
	}
	/**
	 * 删除菜单类型
	 *
	 * @access public
	 */
	public function deleteType($id) {
		$Model = Helper::createModel('apps_menu_type', '', true);
		$map['ID'] = intval($id);
		return $Model->where($map)->delete();
	}
	/**
	 * 删除菜单类型
	 *
	 * @access public
	 */
	public function deleteTypeByApp($app) {
		$Model = Helper::createModel('apps_menu_type', '', true);
		$map['App'] = text($app);
		return $Model->where($map)->delete();
	}
	/**
	 * 获取菜单中应用列表
	 *
	 * @access public
	 */
	public function getList($where) {
		return $this->where($where)->findAll();
	}

	/**
	 * 根据父ID获取子菜单
	 *
	 * @access public
	 */
	public function getsubmenu($parentid) {

		$condition = "parentid=".$parentid;
		$submenus = $this->where($condition)->order('Sort ASC,ID DESC')->findAll();
		return $submenus;
	}

}

