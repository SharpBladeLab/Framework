<?php
/**
 * 模板引擎解析类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 *
 * $Id: TemplateThink.class.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class TemplateThink
 {
    /**
     * 渲染模板输出
     *
     * @access public
     *
     * @param string $templateFile 模板文件名
     * @param array  $var          模板变量
     * @param string $charset      模板输出字符集
     * @param string $varPrefix    模板变量前缀
     *
     * @return void
     */
    public function fetch($templateFile, $var, $charset) {
	
        if( !$this->checkCache($templateFile) ) {
		
            /* 缓存无效,重新编译 */
            $tpl = Framework::instance('ThinkTemplate');
			
            /* 编译并加载模板文件 */
            $tpl->load($templateFile, $var, $charset);
			
        } else {
		
            /* 缓存有效 直接载入模板缓存 模板阵列变量分解成为独立变量 */
            extract($var, EXTR_OVERWRITE);
			
            /* 载入模版缓存文件 */
            include config('CACHE_PATH').md5($templateFile). '@'. basename($templateFile, '.html') .config('TMPL_CACHFILE_SUFFIX');
        }
    }

    /**
     * 检查缓存文件是否有效(如果无效则需要重新编译)
     *
     * @access public
     *
     * @param string $tmplTemplateFile  模板文件名
     *
     * @return boolen
     */
    protected function checkCache($tmplTemplateFile) {
		
		/* 优先对配置设定检测 */
        if ( !config('TMPL_CACHE_ON') ) return false;
		
        $tmplCacheFile = config('CACHE_PATH').md5($tmplTemplateFile). '@'. basename($tmplTemplateFile, '.html') .config('TMPL_CACHFILE_SUFFIX');
		
        if( !is_file($tmplCacheFile) ) {
			/* 文件不存在 */
            return false;
			
		} elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) {
            /* 模板文件如果有更新则缓存需要更新 */
            return false;
			
        } elseif (config('TMPL_CACHE_TIME') != -1 && time() > filemtime($tmplCacheFile)+config('TMPL_CACHE_TIME')) {
            /* 缓存是否在有效期 */
            return false;
        }
		
        /* 缓存有效 */
        return true;
    }
 }
