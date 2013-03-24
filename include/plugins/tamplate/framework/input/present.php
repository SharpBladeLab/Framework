<?php
/**
 * Present标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: present.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */ 
 class TagPresent extends Tag
 {
    private $content;
    private $nested;
    private $attr;
    public $alias = "isset,notIsset";
     
    public function __construct(){
     	$this->content = "true";
     	$this->nested  = "true";
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
    	
        $tag      = $this->parseXmlAttr($attr,'present');
        $name   = $tag['name'];
        $name   = $this->autoBuildVar($name);
        if($this->other == "isset"){
        	$parseStr  = '<?php if(isset('.$name.')): ?>'.$content.'<?php endif; ?>';
        }else{
        	$parseStr  = '<?php if(!isset('.$name.')): ?>'.$content.'<?php endif; ?>';
        }
        return $parseStr;
    }
 }
