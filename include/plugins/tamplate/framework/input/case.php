<?php
/**
 * case标签
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: case.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TagCase extends Tag 
 {
     private $content;
     private $nested;
     private $attr;
     
     public function __construct() {
     	$this->content = "true";
     	$this->attr[]   = array('name'=>"value",'required'=>"true");
     	$this->attr[]   = array('name'=>"break",'required'=>"false");
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
     	static $caseCount = 0;
   		$tag = $this->parseXmlAttr($attr,'case');
        $value = $tag['value'];
        if('$' == substr($value,0,1)) {
            $varArray = explode('|',$value);
            $value	=	array_shift($varArray);
            $value  =  $this->autoBuildVar(substr($value,1));
            if(count($varArray)>0)
                $value = $this->tpl->parseVarFunction($value,$varArray);
            $value   =  'case '.$value.': ';
        }elseif(strpos($value,'|')){
            $values  =  explode('|',$value);
            $value   =  '';
            foreach ($values as $val){
                $value   .=  'case "'.addslashes($val).'": ';
            }
        }else{
            $value	=	'case "'.$value.'": ';
        }
        if($caseCount == 0){
        	$parseStr = ''.$value.' ?>'.$content;
        }else{
        	 $parseStr = '<?php '.$value.' ?>'.$content;
        }
        $caseCount ++;
        if('' ==$tag['break'] || $tag['break']) {
            $parseStr .= '<?php break;?>';
        }
        return $parseStr;
     }
 }
