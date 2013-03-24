<?php
/**
 * PHP标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: php.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */ 
 class TagPhp extends Tag
 {
    private $content;
    private $nested;
    private $attr;
     
    public function __construct(){
     	$this->content = "true";
     	$this->nested  = "true";
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
        $parseStr = '<?php ' . $content . ' ?>';
        return $parseStr;
    }
 }