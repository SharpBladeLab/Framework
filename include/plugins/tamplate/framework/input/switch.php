<?php
/**
 * Switch标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: switch.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagSwitch extends Tag
 {
    private $content;
    private $nested;
    private $attr;
     
    public function __construct(){
     	$this->content = "true";
     	$this->attr[]   = array('name'=>"name",'required'=>"true");
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
        $tag = $this->parseXmlAttr($attr,'switch');
        $name = $tag['name'];
        $varArray = explode('|',$name);
        $name   =   array_shift($varArray);
        $name = $this->autoBuildVar($name);
        if(count($varArray)>0)
            $name = $this->tpl->parseVarFunction($name,$varArray);
        $parseStr = '<?php switch('.$name.'): '.$content.'<?php endswitch; ?>';
        return $parseStr;
    }
 }
 