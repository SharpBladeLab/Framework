<?php
/**
 * Default标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 *
 * $Id: default.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagDefault extends Tag
 {
    private $content;
    private $nested;
    private $attr;
     
    public function __construct(){
    	$this->content = "empty";
     	$this->setTpl();
    }
     
    public function getContent(){
     	return $this->content;
    }
    
	public function getNested(){
     	return $this->nested;
    }
     
    public function getAttr(){
     	return $this->attr;
    }
     
    public function parse($attr,$content){
   		$parseStr = '<?php default: ?>';
        return $parseStr;
    }
 }
 