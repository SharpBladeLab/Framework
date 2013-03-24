<?php
/**
 * ConstDefined标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 *
 * $Id: constDefined.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagConstDefined extends Tag
 {
    private $content;
    private $nested;
    private $attr;
    public $alias = "defined,notDefined";
     
    public function __construct(){
     	$this->content = "true";
     	$this->attr[]   = array('name'=>"name",'required'=>"true");
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
		$tag        = $this->parseXmlAttr($attr,'defined');
        $name     = $tag['name'];
        if($this->other == 'defined'){
        	$parseStr = '<?php if(defined("'.$name.'")): ?>'.$content.'<?php endif; ?>';
        }else{
        	$parseStr = '<?php if(!defined("'.$name.'")): ?>'.$content.'<?php endif; ?>';
        }
        return $parseStr;
    }
 }
