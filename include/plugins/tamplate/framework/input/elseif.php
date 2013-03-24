<?php
/**
 * ElseIf标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 *
 * $Id: elseif.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagElseif extends Tag
 {
    private $content;
    private $nested;
    private $attr;
     
    public function __construct(){
     	$this->content = "empty";
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
        $tag          = $this->parseXmlAttr($attr,'if');
        $condition   = $this->parseCondition($tag['condition']);
        $parseStr   = '<?php elseif('.$condition.'): ?>';
        return $parseStr;
    }
 }
