<?php
/**
 * SiteSession标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: siteSession.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */ 
 class TagSiteSession extends Tag
 {
    private $content;
    private $nested;
    private $attr;
    public $alias = "session,notsession";
     
     
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
        $tag = $this->parseXmlAttr($attr,'session');
        $name = $tag['name'];
        if($this->other == 'session'){
        	$parseStr = '<?php if(isset($_SESSION["'.$name.'"])): ?>'.$content.'<?php endif; ?>';
        } else {
        	$parseStr = '<?php if(!isset($_SESSION["'.$name.'"])): ?>'.$content.'<?php endif; ?>';
        }
        return $parseStr;
    }
 }
