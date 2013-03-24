<?php
/**
 * The model class file of Tiwer Developer Framework.
 *
 * Tiwer Developer Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Tiwer Developer Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with Tiwer Developer Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright (C) 2007-2011 Tiwer Studio. All Rights Reserved.
 * @author      wgw8299 <wgw8299@gmail.com>
 * @package     Tiwer Developer Framework
 * @version     $Id: View.class.php 24 2012-11-28 03:59:21Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 视图输出(支持缓存和页面压缩)
 */
 class View extends Framework
 {
	/* 模板输出变量 */
    protected $tVar = array();
	
	/* 页面trace变量 */
    protected $trace  = array(); 
	
	 /* 模板文件名 */
    protected $templateFile = '';

    /**
     * 模板变量赋值
     *
     * @access public
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function assign($name, $value = '') {
    	
        if( is_array($name) ) {            
			/* 数据 */
			$this->tVar = array_merge($this->tVar,$name);
			
        } elseif( is_object($name) ) {
			
			/* 对象 */
            foreach( $name as $key =>$val ) {
                $this->tVar[$key] = $val;
			}
			
        } else {
			/* 数值 */
            $this->tVar[$name] = $value;
        }
    }

    /**
     * Trace变量赋值
     *
     * @access public
     *
     * @param mixed $name
     * @param mixed $value
	 *
	 * @return void
     */
    public function trace($title, $value = '') {	
        if(is_array($title)) {
            $this->trace = array_merge($this->trace,$title);			
        } else {
            $this->trace[$title] = $value;
		}
    }

    /**
     * 取得模板变量的值
     *
     * @access public
	 *
     * @param  string $name
	 *
     * @return mixed
     */
    public function get($name) {
        if(isset($this->tVar[$name])) {
            return $this->tVar[$name];			
        } else {
            return false;
		}
    }

    /**
     * 加载模板和页面输出 (可以返回输出内容)
     *
     * @access public
     *
     * @param string $templateFile 模板文件名 (留空为自动获取)
     * @param string $charset      模板输出字符集
     * @param string $contentType  输出类型
     *
     * @return mixed
     */
    public function display($templateFile = '', $charset = '', $contentType = 'text/html') {
		/* 取得内容 */
        $this->fetch($templateFile, $charset, $contentType, true);
    }

    /**
     * 输出布局模板
     *
     * @access protected
     *
     * @param string $charset     输出编码
     * @param string $contentType 输出类型
     * @param string $display     是否直接显示
     *
     * @return mixed
     */
    protected function layout($content, $charset='', $contentType = 'text/html') {
	
        if( false !== strpos($content, '<!-- layout')) {
		
            /* 查找布局包含的页面 */
            $find = preg_match_all('/<!-- layout::(.+?)::(.+?) -->/is',$content,$matches);
			
            if($find) {
                for ($i=0; $i< $find; $i++) {
				
                    /* 读取相关的页面模板替换布局单元 */
                    if(0 === strpos($matches[1][$i],'$')) {
                        /* 动态布局 */ 
                        $matches[1][$i] = $this->get(substr($matches[1][$i],1));
					}
					
                    if( 0 != $matches[2][$i] ) {
					
                        /* 设置了布局缓存 检查布局缓存是否有效 */
                        $guid  =  md5($matches[1][$i]);
                        $cache =  Helper::createCache($guid);
						
                        if($cache) {
                            $layoutContent = $cache;
                        } else {
                            $layoutContent = $this->fetch($matches[1][$i],$charset,$contentType);
                            Helper::createCache($guid,$layoutContent,$matches[2][$i]);
                        }						
                    } else {
                        $layoutContent = $this->fetch($matches[1][$i],$charset,$contentType);
                    }					
                    $content    =   str_replace($matches[0][$i],$layoutContent,$content);
                }
            }			
        }		
        return $content;
    }

    /**
     * 加载模板和页面输出
     *
     * @access public
     *
     * @param string $templateFile 模板文件名 留空为自动获取
     * @param string $charset      模板输出字符集
     * @param string $contentType  输出类型
     * @param string $display      是否直接显示
     *
     * @return string/false
     */
    public function fetch($templateFile = '', $charset = '',$contentType = 'text/html', $display = false) {
	
		/* 注入全局变量dc  */
		global	$dc;
		
		$this->tVar['ts'] = $this->tVar['dc'] = $dc;
		
        $GLOBALS['_viewStartTime'] = microtime(TRUE);
		
        if(null === $templateFile) {
            /* 使用null参数作为模板名直接返回不做任何输出 */
            return;
		}
		
        if( empty($charset) ) {
			$charset = config('DEFAULT_CHARSET');
		}
		
        /* 网页字符编码 */
        header("Content-Type:".$contentType."; charset=".$charset);
		
		/* 支持页面回跳 */
        header("Cache-control: private"); 
		
        /* 页面缓存 */
        ob_start();
        ob_implicit_flush(0);

        if(!file_exists_case($templateFile)) {
            /* 自动定位模板文件 */
            $templateFile   = $this->parseTemplateFile($templateFile);
		}
		
		/* 模板引擎 */
        $engine  = strtolower(config('TMPL_ENGINE_TYPE'));
		
        if('php' == $engine) {
			echo 'php';
            /* 模板阵列变量分解成为独立变量 */
            extract($this->tVar, EXTR_OVERWRITE);
			
            /* 直接载入PHP模板 */
            include $templateFile;
			
        } elseif( 'TiwerException' == $engine && $this->checkCache($templateFile)) {
			echo 'TiwerException';
            
            /* 如果是模板引擎并且缓存有效 分解变量并载入模板缓存 */
            extract($this->tVar, EXTR_OVERWRITE);
			
            /* 载入模版缓存文件 */
            include config('CACHE_PATH').md5($templateFile).config('TMPL_CACHFILE_SUFFIX');
			
        } else {
			
            /* 模板文件需要重新编译 */ 
            $className   = 'Template'.ucwords($engine);    
			
			import($className);
			
            $tpl = new $className;
            $tpl->fetch($templateFile,$this->tVar,$charset);			
        }
		
        $this->templateFile = $templateFile;
		
		
        /* 获取并清空缓存 */
        $content = ob_get_clean();
		
        /* 模板内容替换 */ 
        $content = $this->templateContentReplace($content);
		
        /* 布局模板解析 */
        $content = $this->layout($content,$charset,$contentType);
		
        /* 输出模板文件 */
        return $this->output($content,$display);
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
        if (!config('TMPL_CACHE_ON')) return false;
		
        $tmplCacheFile = config('CACHE_PATH').md5($tmplTemplateFile).config('TMPL_CACHFILE_SUFFIX');
        if(!is_file($tmplCacheFile)){
            return false;
        } elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) {
            /* 模板文件如果有更新则缓存需要更新 */
            return false;
			
        } elseif (config('TMPL_CACHE_TIME') != -1 && time() > filemtime($tmplCacheFile) + config('TMPL_CACHE_TIME')) {
            /* 缓存是否在有效期 */
            return false;
        }
		
        /* 缓存有效 */
        return true;
    }

    /**
     * 创建静态页面
     *
     * @access public
     *
     * @param string $htmlfile     生成的静态文件名称
     * @param string $htmlpath     生成的静态文件路径
     * @param string $templateFile 指定要调用的模板文件(默认为空由系统自动定位模板文件)
     * @param string $charset      输出编码
     * @param string $contentType  输出类型
     *
     * @return string
     */
    public function buildHtml($htmlfile, $htmlpath='', $templateFile='', $charset='', $contentType='text/html') {
	
        $content = $this->fetch($templateFile,$charset,$contentType);
		
        $htmlpath = !empty($htmlpath)?$htmlpath:HTML_PATH;
        $htmlfile =  $htmlpath.$htmlfile.config('HTML_FILE_SUFFIX');

        if(!is_dir(dirname($htmlfile))) {
		
            /* 如果静态目录不存在 则创建 */
            mk_dir(dirname($htmlfile));
		}
		
        if(false === file_put_contents($htmlfile,$content)) {
            Helper::createException(Helper::createLanguage('_CACHE_WRITE_ERROR_'));
		}
		
        return $content;
    }

    /**
     * 输出模板
     *
     * @access protected
     *
     * @param string $content 模板内容
     * @param boolean $display 是否直接显示
     *
     * @return mixed
     */
    protected function output($content,$display) {
	
        if(config('HTML_CACHE_ON')) HtmlCache::writeHTMLCache($content);
		
        if($display) {
            if(config('SHOW_RUN_TIME')) {
                $runtime = '<div  id="TiwerException_run_time" class="TiwerException_run_time">'.$this->showTime().'</div>';
                
				if(strpos($content,'{__RUNTIME__}')) {
                     $content = str_replace('{__RUNTIME__}',$runtime,$content);
                } else {
                     $content .= $runtime;
				}
            }
            echo $content;
            if(config('SHOW_PAGE_TRACE')) $this->showTrace();
            return null;
        } else {
            return $content;
        }
    }

    /**
     * 模板内容替换
     *
     * @access protected
     *
     * @param string $content 模板内容
     *
     * @return string
     */
    protected function templateContentReplace($content) {
        
		/* 系统默认的特殊变量替换 */
        $replace =  array(
        	'__APP__'	   => __APP__,         // 应用地址
        	'__APP_SKIN__' => APP_SKIN_PATH,   // 应用样式  
             
            '__SKIN__'	   => WEB_SKIN_PATH,   // 样式地址  
			'__JS__'	   => WEB_JS_PATH,     // 样式脚本
        	'__IMAGE__'	   => WEB_IMAGE_PATH,  // 样式图片
        
            '__ROOT__'	   => SITE_URL,        // 网站地址
            '__URL__'	   => __URL__,         // 模块地址        
            '__SELF__'	   => __SELF__,        // 当前地址
			'__THEME__'	   => __THEME__,	   // 网站主题
        	'__ADMIN__'	   => __ADMIN__,	   // 后台主题
			'__UPLOAD__'   => __UPLOAD__,	   // 上传文件
        );
		
        if(config('TOKEN_ON')) {
            if(strpos($content,'{__TOKEN__}')) {
                /* 指定表单令牌隐藏域位置 */
                $replace['{__TOKEN__}'] =  $this->buildFormToken();
				
            } elseif(strpos($content,'{__NOTOKEN__}')) {
                /* 标记为不需要令牌验证 */
                $replace['{__NOTOKEN__}'] =  '';
				
            }elseif(preg_match('/<\/form(\s*)>/is',$content,$match)) {
			
                /* 智能生成表单令牌隐藏域 */
                $replace[$match[0]] = $this->buildFormToken().$match[0];				
            }
        }
		
        /* 允许用户自定义模板的字符串替换 */
        if(is_array(config('TMPL_PARSE_STRING')) ) {
            $replace =  array_merge($replace,config('TMPL_PARSE_STRING'));
		}
			
        $content = str_replace(array_keys($replace),array_values($replace),$content);
        return $content;
    }

    /**
     * 创建表单令牌隐藏域
     *
     * @access private
     *
     * @return string
     **/
    private function buildFormToken() {
	
        /* 开启表单验证自动生成表单令牌 */
        $tokenName = config('TOKEN_NAME');		
        $tokenType = config('TOKEN_TYPE');
		
        $tokenValue = $tokenType(microtime(TRUE));
        $token   =  '<input type="hidden" name="'.$tokenName.'" value="'.$tokenValue.'" />';
        
		$_SESSION[$tokenName]  =  $tokenValue;
		
        return $token;
    }

    /**
     * 自动定位模板文件
     *
     * @access private
     *
     * @param string $templateFile 文件名
     *
     * @return string
     *
     * @throws TiwerException
     */
    private function parseTemplateFile($templateFile) {
	
        if( '' == $templateFile ) {
            /* 如果模板文件名为空 按照默认规则定位 */
            $templateFile = config('TMPL_FILE_NAME');
			
        }elseif(strpos($templateFile,'&')){
            /* 引入其它模块的操作模板 */
            $templateFile   =   str_replace('&','/',$templateFile).config('TMPL_TEMPLATE_SUFFIX');
			
		/* 修改结束 */
        }elseif(strpos($templateFile,'@')){
            /* 引入其它主题的操作模板 必须带上模块名称 例如 blue@User:add */
            $templateFile   =   TMPL_PATH.str_replace(array('@',':'),'/',$templateFile).config('TMPL_TEMPLATE_SUFFIX');
       
	    } elseif(strpos($templateFile,':')) {
            /* 引入其它模块的操作模板 */
            $templateFile   =   VIEW_PATH.'/'.str_replace(':','/',$templateFile).config('TMPL_TEMPLATE_SUFFIX');

        }elseif(!is_file($templateFile))    {
            /* 引入当前模块的其它操作模板 */
            $templateFile =  dirname(config('TMPL_FILE_NAME')).'/'.$templateFile.config('TMPL_TEMPLATE_SUFFIX');
        }
		
        if(!file_exists_case($templateFile)) {
            Helper::createException(Helper::createLanguage('_TEMPLATE_NOT_EXIST_').'['.$templateFile.']');
		}		
        return $templateFile;
    }

    /**
     * 显示运行时间、数据库操作、缓存次数、内存使用信息
     *
     * @access private
     *
     * @return string
     */
    private function showTime() {
	
        /* 显示运行时间 */
        $startTime =  $GLOBALS['_viewStartTime'];
		
        $endTime = microtime(TRUE);
        $total_run_time =   number_format(($endTime - $GLOBALS['dc_beginTime']), 3);
        $showTime   =   'Process: '.$total_run_time.'s ';
		
        if(config('SHOW_ADV_TIME')) {
		
            /* 显示详细运行时间 */
            $_load_time =   number_format(($GLOBALS['dc_loadTime'] -$GLOBALS['dc_beginTime'] ), 3);
            $_init_time =   number_format(($GLOBALS['dc_initTime'] -$GLOBALS['dc_loadTime'] ), 3);
            $_exec_time =   number_format(($startTime  -$GLOBALS['dc_initTime'] ), 3);
            $_parse_time    =   number_format(($endTime - $startTime), 3);
            $showTime .= '( Load:'.$_load_time.'s Init:'.$_init_time.'s Exec:'.$_exec_time.'s Template:'.$_parse_time.'s )';
        }
		
        if(config('SHOW_DB_TIMES') && class_exists('DataBase',false) ) {
		
            /* 显示数据库操作次数 */ 
            $db =   DataBase::getInstance();
            $showTime .= ' | DB :'.$db->Q().' queries '.$db->W().' writes ';
        }
		
        if(config('SHOW_CACHE_TIMES') && class_exists('Cache',false)) {
            /* 显示缓存读写次数 */
            $cache  =   Cache::getInstance();
            $showTime .= ' | Cache :'.$cache->Q().' gets '.$cache->W().' writes ';
        }
		
        if(MEMORY_LIMIT_ON && config('SHOW_USE_MEM')) {
            /* 显示内存开销 */
            $startMem    =  array_sum(explode(' ', $GLOBALS['_startUseMems']));
            $endMem     =  array_sum(explode(' ', memory_get_usage()));
            $showTime .= ' | UseMem:'. number_format(($endMem - $startMem)/1024).' kb';
        }
        return $showTime;
    }

    /**
     * 显示页面Trace信息
     *
     * @access private
     */
    private function showTrace() {
	
        /* 显示页面Trace信息,读取Trace定义文件,定义格式 return array('当前页面'=>$_SERVER['PHP_SELF'],'通信协议'=>$_SERVER['SERVER_PROTOCOL'],...); */
        $traceFile  = APP_PATH.'trace.php';
        $_trace = is_file($traceFile)? include $traceFile : array();
		
         /* 系统默认显示信息 */
        $this->trace('当前页面',  $_SERVER['REQUEST_URI']);
        $this->trace('模板缓存',  config('CACHE_PATH').md5($this->templateFile).config('TMPL_CACHFILE_SUFFIX'));
        $this->trace('请求方法',  $_SERVER['REQUEST_METHOD']);
        $this->trace('通信协议',  $_SERVER['SERVER_PROTOCOL']);
        $this->trace('请求时间',  date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));
        $this->trace('用户代理',  $_SERVER['HTTP_USER_AGENT']);
        $this->trace('会话ID',    session_id());
       
  	    $log = Log::$log;		
        $this->trace('日志记录',count($log)?count($log).'条日志<br/>'.implode('<br/>',$log):'无日志记录');
        
		$files = get_included_files();
        $this->trace('加载文件', count($files).str_replace("\n",'<br/>',substr(substr(print_r($files,true),7),0,-2)));
        $_trace = array_merge($_trace,$this->trace);
        
		/* 调用Trace页面模板 */
        include config('TMPL_TRACE_FILE');
    }
 }
