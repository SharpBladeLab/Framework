<?php if(!defined('IN_SYS')) exit();
/**
 * 无限分类驱动类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * 调用设例
 * 
 * $Tree = new Tree();
 * 
 * $Tree->setNode(目录ID, 上级ID, '目录名字');
 * 
 * $Tree->setNode(1, 0, '目录1');
 * $Tree->setNode(5, 3, '目录5');
 * $Tree->setNode(2, 1, '目录2');
 * $Tree->setNode(4, 2, '目录4');
 * $Tree->setNode(9, 4, '目录9');
 * $Tree->setNode(6, 2, '目录6');
 * $Tree->setNode(7, 2, '目录7');
 * $Tree->setNode(8, 6, '目录8');
 * 
 * $category = $Tree->getChilds();
 * 
 * 遍历输出 
 * foreach ($category as $key=>$id) {
 *     echo $id.$Tree->getLayer($id, '|—').$Tree->getValue($id)."<br />";
 * }
 *
 * $Id: Tree.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class Tree extends Plugin {
 	
 	/**
 	 * 插件版本
 	 */
 	protected  $version = '0.1';
 	
	
	/* CMS */
	public $icon = array('│','├','└');
	public $nbsp = "&nbsp;";
	public $ret = '';
	public $level = 0;
 	
	public $data=array();
	public $cateArray=array();
	public $arr = array();
	
	
  	public function __construct($arr=null) {
  		if ($arr!=null) {
  			$this->arr = $arr;
	    	$this->ret = '';
	     	return is_array($arr);
  		}
    }	
	public function setNode($id,$parent,$value,$arr="") {		
		$parent = $parent ? $parent : 0;
		
		$this->data[$id]	 = $value;
		$this->cateArray[$id]= $parent;
		$this->arr[$id]		 = $arr;
    }	
	public function getChildsTree($id=0){
		$childs=array();
		foreach ($this->cateArray as $child=>$parent){
			if($parent==$id){				
				$childs[$child]=$this->getChildsTree($child);
				$childs[$child]['value'] = $this->data[$child];
			}
		}
		return $childs;
	}    
	public function getChilds($id=0){
		$childArray=array();
		$childs=$this->getChild($id);
		foreach ($childs as $child){
			$childArray[]=$child;
			$childArray=array_merge($childArray,$this->getChilds($child));
		}
		
		return $childArray;
	}
	public function getChild($id){
		$childs=array();
		foreach ($this->cateArray as $child=>$parent){
			if($parent==$id){
				$childs[$child]=$child;
			}
		}
		return $childs;
	}
	
	
	
	
	
	
	
	
	
	
	
	/******** zzy start********/		
	
	public function getChilds2($id=0){
		$childArray=array();
		$childs=$this->getChild($id);
		foreach ($childs as $child){
			$childArray[]=$child;
			$childArray=array_merge($childArray,$this->getChilds2($child));
		}
		return $childArray;
	}
	/******** zzy end********/

	/**
	 * 单线获取父节点
	 */
	public function getNodeLever($id){
		$parents=array();
		if (key_exists($this->cateArray[$id],$this->cateArray)){
			$parents[]=$this->cateArray[$id];
			$parents=array_merge($parents,$this->getNodeLever($this->cateArray[$id]));
		}
		return $parents;
	}
	
	public function getLayer($id,$preStr='|—　'){
		return str_repeat($preStr,count($this->getNodeLever($id)));
	}
    
	public function getValue ($id){
		return $this->data[$id];
	}	
	public function getArr($id){
		return $this->arr[$id];
	}
	
		
	
	/******** Content Managent System ********/
 	public function child($bid) {
		$a = $newarr = array();
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a['parentid'] == $bid) $newarr[$id] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}
	function getTree($bid, $str, $sid = 0, $adds = '', $strgroup = ''){
		$number=1;
		$child = $this->getchild($bid);
		if(is_array($child)){
		    $total = count($child);
			foreach($child as $id=>$a){
				$j=$k='';
				if($number==$total){
					$j .= $this->icon[2];
				} else {
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';

				@extract($a);
				if(empty($a['selected'])){$selected = $id==$sid ? 'selected' : '';}
				$parentid == 0 && $strgroup ? eval("\$newstr = \"$strgroup\";") : eval("\$newstr = \"$str\";");
				$this->ret .= $newstr;
				$nbsp = $this->nbsp;
				$this->get_tree($id, $str, $sid, $adds.$k.$nbsp,$strgroup);
				$number++;
			}
		}
		return $this->ret;
	}

	function get_nav($bid,$maxlevel,$effected_id='navlist',$style='filetree ' ,$homefont='',$recursion=FALSE ,$child='',$enhomefont='',$lang='') {	
		if($enhomefont) $indexen =  '<em>'.$enhomefont.'</em>';
		if($homefont) $homefont='<li id="nav_0"><span class="fl_ico"></span><a href="'.URL().'"><span class="fl">'.L(HOME_FONT).'</span>'.$indexen.'</a></li>';
	 
		$number=1;
		if(!$child) $child = $this->getchild($bid);
		$total = count($child);
		$effected = $effected_id ?  ' id="'.$effected_id.'_box"' : '';
		$class=  $style? ' class="'.$style.'"' : '';
        if(!$recursion)	$this->ret .='<ul'.$effected.$class.'>'.$homefont;
        foreach($child as $id=>$a) {
        	@extract($a);
			if(!$this->level){	
				$this->level= $level ? $level+$maxlevel-1 : $maxlevel;
			}
			$ischild =$this->getchild($id);
			$foldertype =  $ischild ? 'folder' : 'file';
        	$floder_status = ' id="'.$effected_id.'_'.$id.'"';
			$first = $number==1 ?   'first ' : '';
			$floder_status .=  $number==$total ?  ' class="foot '.$foldertype.'"' :  ' class="'.$first.$foldertype.'"';
			$this->ret .= $recursion ? '<ul><li'.$floder_status.'>' : '<li'.$floder_status.'>';
            $recursion = FALSE;
			if($enhomefont){
				$enzm = $enname ? '<em>'.$enname.'</em>' :  '<em>'.$catdir.'</em>';
			}
            if($ischild && $level < $this->level){
				$this->ret .= '<span class="fd_ico"></span><a href="'.$url.'"><span class="fd">'.$catname.'</span>'.$enzm.'</a>';
                $this->get_nav($id,$maxlevel,$effected_id,$style,'',TRUE,$ischild,$enhomefont,$lang);
            } else {
			   $this->ret .= '<span class="fl_ico"></span><a href="'.$url.'"><span class="fl">'.$catname.'</span>'.$enzm.'</a>';
            }
           $this->ret .=$recursion ? '</li></ul>': '</li>';
		   $number++;
        }
        if(!$recursion)  $this->ret .='</ul>';
        return $this->ret;
    }
}

