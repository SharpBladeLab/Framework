<?php
/**
 * Smarty模板引擎解析类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 *
 * $Id: TemplateSmarty.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TemplateSmarty
 {
    /**
     * 渲染模板输出
     *
     * @access public
     *
     * @param string $templateFile 模板文件名
     * @param array  $var          模板变量
     * @param string $charset      模板输出字符集
     *
     * @return void
     */
    public function fetch($templateFile, $var, $charset) {
	
        $templateFile = substr($templateFile, strlen(TMPL_PATH));		
        
		include_once('./library/Smarty.class.php');
        $tpl = new Smarty();
		
		/* Smarty模拟引擎配置信息 */
        if( config('TMPL_ENGINE_CONFIG') ) {
            $config  =  config('TMPL_ENGINE_CONFIG');
            foreach ($config as $key=>$val) {
                $tpl->{$key} = $val;
            }
			
        } else {		
			/* 默认配置信息 */
            $tpl->caching = config('TMPL_CACHE_ON');
            $tpl->template_dir = TMPL_PATH;
            $tpl->compile_dir = CACHE_PATH ;
            $tpl->cache_dir = TEMP_PATH ;
        }
		
		/* 变量赋值 */
        $tpl->assign($var);		
        $tpl->display($templateFile);
    }
 }
