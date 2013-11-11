<?php
/**
 * Include标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: include.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagInclude extends Tag
 {
    private $content;
    private $nested;
    private $attr;
     
	/**
	 * 结构函数
	 */
    public function __construct() {
	
     	$this->content = "empty";
     	$this->nested  = "true";
		
     	$this->attr[]  = array('name' => "file", 'required' => "true");
     	$this->setTpl();
    }
     
	/**
	 * 获取内容
	 */ 
    public function getContent(){
    	return $this->content;
    }
	
	/**
	 * 嵌入内容
	 */
    public function getNested(){
    	return $this->nested;
    }
     
    public function getAttr(){
     	return $this->attr;
    }
    
	/**
	 * 解析模板
	 */
    public function parse($attr, $content) {
	
        $tag = $this->parseXmlAttr($attr, 'include');
        $file= $tag['file'];
		
        return $this->tpl->parseInclude($file);
    }
 }
 