<?php
/**
 * If标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: if.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagIf extends Tag
 {
    private $content;
    private $nested;
    private $attr;
     
    public function __construct() {
     	$this->content = "true";
     	$this->nested  = "true";
     	$this->attr[]   = array('condition'=>"true",'required'=>"true");
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
        $tag = $this->parseXmlAttr($attr,'if');
        $condition   = $this->parseCondition($tag['condition']);
		
        dump($condition);
		
        $parseStr  = '<?php if('.$condition.'): ?>'.$content.'<?php endif; ?>';
        return $parseStr;
    }
 }
