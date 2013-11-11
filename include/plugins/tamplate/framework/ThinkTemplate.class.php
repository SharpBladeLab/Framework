<?php
/**
 * 内置模板引擎类（支持XML标签和普通标签的模板解析,编译型模板引擎.支持动态缓存）
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * Site: http://www.tiwer.cn
 *
 * $Id: ThinkTemplate.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class  ThinkTemplate extends Framework
 {
    /* 模板页面中引入的标签库列表 */
    protected $tagLib =  array();

	/* 当前模板文件 */
    protected $templateFile  =  '';

    /* 模板变量 */
    public  $tVar    = array();
    public  $config  = array();
    private $literal = array();

    /**
     * 取得模板实例对象（静态方法）
     *
     * @access public
     *
     * @return ThinkTemplate
     */
    static public function  getInstance() {

		/* 实例化模板引擎类 */
        return get_instance_of(__CLASS__);
    }

    /**
     ** 架构函数
     *
     * @access public
     *
     * @param array $config 模板引擎配置数组
     */
    public function __construct(){
        $this->config['cache_path']     =  config('CACHE_PATH');                     // 缓存路径设置
        $this->config['template_suffix']=  config('TMPL_TEMPLATE_SUFFIX');           // 默认模板文件后缀
        $this->config['cache_suffix']   =  config('TMPL_CACHFILE_SUFFIX');           // 默认模板缓存后缀
        $this->config['tmpl_cache']     =  config('TMPL_CACHE_ON');                  // 是否开启模板编译缓存
        $this->config['cache_time']     =  config('TMPL_CACHE_TIME');                // 模板缓存有效期
        $this->config['taglib_begin']   =  $this->stripPreg(config('TAGLIB_BEGIN')); // 标签库标签开始标记
        $this->config['taglib_end']     =  $this->stripPreg(config('TAGLIB_END'));   // 标签库标签结束标记
        $this->config['tmpl_begin']     =  $this->stripPreg(config('TMPL_L_DELIM')); // 模板引擎普通标签开始标记
        $this->config['tmpl_end']       =  $this->stripPreg(config('TMPL_R_DELIM')); // 模板引擎普通标签结束标记
        $this->config['default_tmpl']   =  config('TMPL_FILE_NAME');
        $this->config['tag_level']      =  config('TAG_NESTED_LEVEL');               // 标签嵌套级别
    }

	/**
	 * 字符串替换
	 *
	 * @param string $str 字符串
	 *
	 * @access private
	 *
	 * @return 替换后的字符串
	 */
    private function stripPreg($str) {

		$str = str_replace(array('{','}','(',')','|','[',']'),  array('\{', '\}', '\(', '\)', '\|', '\[', '\]' ),  $str);
		return $str;
    }

    /**
	 * 模板变量获取和设置
	 *
	 * @param string $name  变量名称
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
	 * 设置变量
	 *
	 * @param string $name  变量名称
	 * @param string $value 值
	 *
	 * @return void
	 */
    public function set($name,$value) {
        $this->tVar[$name]= $value;
    }

    /**
	 * 加载模板
	 *
	 * @param string $templateFile 模板名称
	 * @param mixed  $templateVar  模板变量
	 * @param string $charset      模板编码
	 *
	 * @return mixed  加载模版缓存文件
	 */
    public function load($templateFile, $templateVar, $charset) {

        $this->tVar = $templateVar;

        $templateCacheFile = $this->loadTemplate($templateFile);

        /* 模板阵列变量分解成为独立变量 */
        extract($templateVar, EXTR_OVERWRITE);

        /* 载入模版缓存文件 */
        include $templateCacheFile;
    }

    /**
     * 加载主模板并缓存
     *
     * @access public
     *
     * @param string $tmplTemplateFile 模板文件
     * @param string $varPrefix        模板变量前缀
     *
     * @return string
     *
     * @throws TiwerException
     */
    public function loadTemplate ($tmplTemplateFile='') {

        if(empty($tmplTemplateFile)) {
			$tmplTemplateFile = $this->config['default_tmpl'];
		}

        if(!is_file($tmplTemplateFile)) {

			/* 默认模板 */
            $tmplTemplateFile =  dirname($this->config['default_tmpl']).'/'.$tmplTemplateFile.$this->config['template_suffix'];

            if(!is_file($tmplTemplateFile)) {
				/* 没有找到模板 */
                Helper::createException(Helper::createLanguage('_TEMPLATE_NOT_EXIST_'));
			}
        }

        $this->templateFile = $tmplTemplateFile;

        /* 根据模版文件名定位缓存文件 */
        $tmplCacheFile = $this->config['cache_path']. md5($tmplTemplateFile) .'@'. basename($tmplTemplateFile, '.html') . $this->config['cache_suffix'];

        $tmplContent = '';

        /* 检查Cache文件是否需要更新 */
        if ( !$this->checkCache($tmplTemplateFile) ) {

            /* 需要更新模版,读出原模板内容 */
            $tmplContent = file_get_contents($tmplTemplateFile);

            /* 编译模板内容 */
            $tmplContent = $this->compiler($tmplContent);

            /* 检测分组目录 */
            if(!is_dir($this->config['cache_path'])) {
                mk_dir($this->config['cache_path']);
			}

            /* 重写Cache文件 */
            if( false === file_put_contents($tmplCacheFile, trim($tmplContent))) {

				/* 缓存文件写入失败 */
                Helper::createException( Helper::createLanguage('_CACHE_WRITE_ERROR_') );
			}
        }
        return $tmplCacheFile;
    }

    /**
     * 编译模板文件内容
     *
     * @access protected
     *
     * @param mixed $tmplContent 模板内容
     *
     * @return string
     */
    protected function compiler( $tmplContent) {

        /*模板解析 */
        $tmplContent = $this->parse($tmplContent);

        if( ini_get('short_open_tag') ) {

            /* 开启短标签的情况要将<?标签用echo方式输出 否则无法正常输出xml标识 */
            $tmplContent = preg_replace('/(<\?(?!php|=|$))/i', '<?php echo \'\\1\'; ?>'."\n", $tmplContent );
		}

        /* 还原被替换的Literal标签 */
        $tmplContent = preg_replace('/<!--###literal(\d)###-->/eis',"\$this->restoreLiteral('\\1')", $tmplContent);

        /* 添加安全代码 */
        $tmplContent  =  '<?php if (!defined(\'IN_SYS\')) exit();?>'.$tmplContent;

        if( config('TMPL_STRIP_SPACE') ) {
            /* 去除html空格与换行 */
            $find     = array("~>\s+<~","~>(\s+\n|\r)~");
            $replace  = array("><",">");
            $tmplContent = preg_replace($find, $replace, $tmplContent);
        }

        return trim($tmplContent);
    }

    /**
     * 检查缓存文件是否有效(如果无效则需要重新更新)
     *
     * @access public
     *
     * @param string $tmplTemplateFile  模板文件名
     *
     * @return boolen
     */
    protected function checkCache($tmplTemplateFile) {

		/* 优先对配置检测 */
        if (!$this->config['tmpl_cache'])  {
            return false;
		}

        $tmplCacheFile = $this->config['cache_path'] . md5($tmplTemplateFile)  .'@'. basename($tmplTemplateFile, '.html') . $this->config['cache_suffix'];

		if(!is_file($tmplCacheFile)){
			/* 不存在的情况 */
            return false;

        } elseif ( filemtime($tmplTemplateFile) > filemtime($tmplCacheFile) ) {
            /* 模板文件如果有更新则缓存需要更新 */
            return false;

        } elseif ($this->config['cache_time'] != -1 && time() > filemtime($tmplCacheFile)+$this->config['cache_time']) {
            /* 缓存是否在有效期 */
            return false;
        }

        /* 缓存有效 */
        return true;
    }

    /**
     * 模板解析入口(支持普通标签和TagLib解析,支持自定义标签库)
     *
     * @access public
     *
     * @param  string $content 要解析的模板内容
     *
     * @return string
     */
    public function parse($content) {

        $begin= $this->config['taglib_begin'];
        $end  = $this->config['taglib_end'];

		/* 首先替换literal标签内容.包含在literal标签中的代码将会直接输出，不进行任何解析 */
        $content = preg_replace('/'. $begin. 'literal'. $end.'(.*?)'.$begin.'\/literal'.$end.'/eis',  "\$this->parseLiteral('\\1')",$content);

        /* 获取需要引入的标签库列表,标签库只需要定义一次 */
        if(config('TAGLIB_LOAD')) {

            $this->getIncludeTagLib($content);

            if(!empty($this->tagLib)) {

                /* 对导入的TagLib进行解析 */
                $_taglibs = config('_taglibs_');
                foreach($this->tagLib as $tagLibName) {

					/* 内置标签库 */
                    if(!importTag('TagLib'.ucwords(strtolower($tagLibName)))) {

						/* 扩展标签库 */
                        if($_taglibs && isset($_taglibs[$tagLibName])) {

                            /* 'tagLibName'=>'importPath' */
                            import($_taglibs[$tagLibName]);

                        } else {
                            Helper::createException($tagLibName.Helper::createLanguage('_TAGLIB_NOT_EXIST_'));
						}
					}
                    $this->parseTagLib($tagLibName, $content);
                }
            }
        }

		/* 预先加载的标签库 无需在每个模板中使用taglib标签加载 */
        if(config('TAGLIB_PRE_LOAD')) {

            $tagLibs =  explode(',', config('TAGLIB_PRE_LOAD'));
            foreach ($taglibs as $tag){
                $this->parseTagLib($tag,$content);
            }
        }

        /* 内置标签库 无需使用taglib标签导入就可以使用 */
        $tagLibs =  explode(',',config('TAGLIB_BUILD_IN'));
        foreach ($tagLibs as $tag){
            $this->parseTagLib($tag, $content, true);
        }

		/* 解析普通模板标签 {tagName:} */
        $content = preg_replace('/('.$this->config['tmpl_begin'].')(\S.+?)('.$this->config['tmpl_end'].')/eis',"\$this->parseTag('\\2')",$content);
        return $content;
    }

    /**
     * 替换页面中的literal标签
     *
     * @access public
     *
     * @param  string $content  模板内容
     *
     * @return string|false
     */
    function parseLiteral($content) {

        if( trim($content) == '' ) return '';
        $content = stripslashes($content);

        $i = count($this->literal);
        $parseStr = "<!--###literal{$i}###-->";

        $this->literal[$i]  = $content;

        return $parseStr;
    }

    /**
     * 还原被替换的literal标签
     *
     * @access public
     *
     * @param  string $tag  literal标签序号
     *
     * @return string|false
     */
    function restoreLiteral($tag) {

        /* 还原literal标签 */
        $parseStr = $this->literal[$tag];

        /* 销毁literal记录 */
        unset($this->literal[$tag]);

        return $parseStr;
	}

    /***
     * 搜索模板页面中包含的TagLib库(并返回列表)
     *
     * @access public
     *
     * @param string $content  模板内容
     *
     * @return string|false
     */
    public function getIncludeTagLib(& $content) {

        /* 搜索是否有TagLib标签 */
        $find = preg_match('/'.$this->config['taglib_begin'].'taglib\s(.+?)(\s*?)\/'.$this->config['taglib_end'].'\W/is',$content,$matches);

	   if($find) {
            /* 替换TagLib标签 */
            $content = str_replace($matches[0],'',$content);

            /* 解析TagLib标签 */
            $tagLibs = $matches[1];

            $xml = '<tpl><tag '.$tagLibs.' /></tpl>';
            $xml = simplexml_load_string($xml);
            if(!$xml)
                Helper::createException(Helper::createLanguage('_XML_TAG_ERROR_'));
            $xml = (array)($xml->tag->attributes());
            $array = array_change_key_case($xml['@attributes']);
            $this->tagLib = explode(',',$array['name']);
        }
        return;
    }

    /**
     * TagLib库解析
     *
     * @access public
     *
     * @param string $tagLib  要解析的标签库
     * @param string $content 要解析的模板内容
     * @param boolen $hide    是否隐藏标签库前缀
     *
     * @return string
     */
    public function parseTagLib($tagLib,&$content,$hide=false)
    {
        $begin = $this->config['taglib_begin'];
        $end   = $this->config['taglib_end'];
        $tLib =  Framework::instance('TagLibCx');

        if($tLib->valid()) {
            /* 如果标签库有效则取出支持标签列表 */
            $tagList =  $tLib->getTagList();

            /* 遍历标签列表进行模板标签解析 */
            foreach($tagList as $tag) {

                /* 实际要解析的标签名称 */
                if( !$hide) {
                    $startTag = $tagLib.':'.$tag['name'];
                } else {
                    $startTag = $tag['name'];
				}

                /* 检查可嵌套标签以及嵌套级别 */
                if($tag['nested'] && $this->config['tag_level']>1) {
                    $level   =   $this->config['tag_level'];
                } else {
                    $level   =   1;
				}

                $endTag = $startTag;

                if ( false !== stripos($content,config('TAGLIB_BEGIN').$startTag) ) {

                    if ( empty($tag['attribute']) ) {
                        /* 无属性标签 */
                        if($tag['content'] !='empty'){
                            for($i=0;$i<$level;$i++)
                                $content = preg_replace('/'.$begin.$startTag.'(\s*?)'.$end.'(.*?)'.$begin.'\/'.$endTag.'(\s*?)'.$end.'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                        }else{
                            $content = preg_replace('/'.$begin.$startTag.'(\s*?)\/(\s*?)'.$end.'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','')",$content);
                        }
                    } elseif($tag['content'] !='empty') {
						/* 闭合标签解析 */
                        for($i=0;$i<$level;$i++)
                            $content = preg_replace('/'.$begin.$startTag.'\s(.*?)'.$end.'(.+?)'.$begin.'\/'.$endTag.'(\s*?)'.$end.'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                    } else {
						/* 开放标签解析 */
                        /* 开始标签必须有一个空格 */
                        $content = preg_replace('/'.$begin.$startTag.'\s(.*?)\/(\s*?)'.$end.'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','')",$content);
                    }
                }
            }
        }
    }

    /**
     *
     * 解析标签库的标签
     * 需要调用对应的标签库文件解析类
     *
     * @access public
     *
     * @param string $tagLib   标签库名称
     * @param string $tag      标签名
     * @param string $attr     标签属性
     * @param string $content  标签内容
     *
     * @return string|false
     *
     */
    public function parseXmlTag($tagLib,$tag,$attr,$content)
    {
        //if (MAGIC_QUOTES_GPC) {
            $attr = stripslashes($attr);
            $content = stripslashes($content);
        //}
        if(ini_get('magic_quotes_sybase'))
            $attr =  str_replace('\"','\'',$attr);
        $tLib =  get_instance_of('TagLibCx');
        if($tLib->valid()) {
            $parse = '_'.$tag;
            $content = trim($content);
            return $tLib->$parse($attr,$content);
        }
    }

    /**
     * 模板标签解析
     * 格式： {TagName:args [|content] }
     *
     * @access public
     *
     * @param string $tagStr 标签内容
     *
     * @return string
     */
    public function parseTag($tagStr) {

            $tagStr = stripslashes($tagStr);

        /* 还原非模板标签 */
        if(preg_match('/^[\s|\d]/is',$tagStr)) {
            /* 过滤空格和数字打头的标签 */
            return config('TMPL_L_DELIM') . $tagStr .config('TMPL_R_DELIM');
		}

        $flag =  substr($tagStr,0,1);
        $name   = substr($tagStr,1);

        if('$' == $flag) {
            /* 解析模板变量 格式 {$varName} */
            return $this->parseVar($name);

        } elseif(':' == $flag) {
            /* 输出某个函数的结果 */
            return  '<?php echo '.$name.';?>';
        } elseif('~' == $flag) {
            // 执行某个函数
            return  '<?php '.$name.';?>';

        } elseif('&' == $flag) {
            /* 输出配置参数 */
            return '<?php echo config("'.$name.'");?>';

        } elseif('%' == $flag) {
            /* 输出语言变量 */
            return '<?php echo Helper::createLanguage("'.$name.'");?>';

		} elseif('@' == $flag) {
			/* 输出SESSION变量 */
            if ( strpos($name,'.') ) {
                $array   =  explode('.',$name);
	    		return '<?php echo $_SESSION["'.$array[0].'"]["'.$array[1].'"];?>';

            } else {
    			return '<?php echo $_SESSION["'.$name.'"];?>';

            }
		} elseif('#' == $flag) {
			/* 输出COOKIE变量 */
            if(strpos($name,'.')) {
                $array   =  explode('.',$name);
	    		return '<?php echo $_COOKIE["'.$array[0].'"]["'.$array[1].'"];?>';
            }else{
    			return '<?php echo $_COOKIE["'.$name.'"];?>';
            }
		}elseif('.' == $flag){
            /* 输出GET变量 */
            return '<?php echo $_GET["'.$name.'"];?>';

        }elseif('^' == $flag){
            /* 输出POST变量 */
            return '<?php echo $_POST["'.$name.'"];?>';

        } elseif('*' == $flag) {
            /* 输出常量 */
            return '<?php echo constant("'.$name.'");?>';
        }

        $tagStr = trim($tagStr);

        if(substr($tagStr,0,2)=='//' || (substr($tagStr,0,2)=='/*' && substr($tagStr,-2)=='*/')) {
            /* 注释标签 */
            return '';
		}

        /* 解析其它标签,统一标签格式 {TagName:args [|content]}  */
        $pos =  strpos($tagStr,':');
        $tag  =  substr($tagStr,0,$pos);
        $args = trim(substr($tagStr,$pos+1));

        /* 解析标签内容 */
        if(!empty($args)) {
            $tag  =  strtolower($tag);
            switch($tag){
                case 'include':
                    return $this->parseInclude($args);
                    break;
                case 'load':
                    return $this->parseLoad($args);
                    break;

                /* 这里扩展其它标签 */
                default:
                    if(config('TAG_EXTEND_PARSE')) {
                        $method = config('TAG_EXTEND_PARSE');
                        if(array_key_exists($tag,$method))
                            return $method[$tag]($args);
                    }
            }
        }
        return config('TMPL_L_DELIM') . $tagStr .config('TMPL_R_DELIM');
    }


    /**
     * 加载js或者css文件
     *
     * @access public
     *
     * @param string $params  参数
     *
     * @return string
     *
     */
    public function parseLoad($str) {

        $type = strtolower(substr(strrchr($str, '.'),1));
        $parseStr = '';

        if($type=='js') {

			/* 加载JavaScript */
            $parseStr .= '<script type="text/javascript" src="'.$str.'"></script>';
        }  elseif($type=='css') {

			/* 加载CSS */
            $parseStr .= '<link rel="stylesheet" type="text/css" href="'.$str.'" />';
        }

        return $parseStr;
    }

    /**
     *
     * 模板变量解析,支持使用函数
     * 格式： {$varname|function1|function2=arg1,arg2}
     *
     * @access public
     *
     * @param string $varStr 变量数据
     *
     * @return string
     *
     */
    public function parseVar($varStr) {

        $varStr = trim($varStr);
        static $_varParseList = array();

        /* 如果已经解析过该变量字串，则直接返回变量值 */
		if(isset($_varParseList[$varStr])) return $_varParseList[$varStr];

		$parseStr ='';
        $varExists = true;

        if(!empty($varStr))  {
            $varArray = explode('|',$varStr);

            /* 取得变量名称 */
            $var = array_shift($varArray);

            //非法变量过滤 不允许在变量里面使用 ->
            //TODO：还需要继续完善

            if(preg_match('/->/is',$var)) {
                return '';
			}

            if('Think.' == substr($var,0,6)) {

                /* 所有以Think.打头的以特殊变量对待 无需模板赋值就可以输出 */
                $name = $this->parseThinkVar($var);

            } elseif( false !== strpos($var,'.')) {

                /* 支持 {$var.property}*/
                $vars = explode('.',$var);
                $var  =  array_shift($vars);

                switch(strtolower(config('TMPL_VAR_IDENTIFY'))) {

                    case 'array':
						/* 识别为数组 */
                        $name = '$'.$var;
                        foreach ($vars as $key=>$val) {
                            $name .= '["'.$val.'"]';
						}
                        break;

                    case 'obj':
						/* 识别为对象 */
                        $name = '$'.$var;
                        foreach ($vars as $key=>$val) {
                            $name .= '->'.$val;
						}
                        break;

                    default:
						/* 自动判断数组或对象 只支持二维 */
                        $name = 'is_array($'.$var.')?$'.$var.'["'.$vars[0].'"]:$'.$var.'->'.$vars[0];
                }
            }
            elseif(false !==strpos($var,':')) {
                /*  支持 {$var:property} 方式输出对象的属性*/
                $vars = explode(':',$var);

                $var  =  str_replace(':','->',$var);
                $name = "$".$var;
                $var  = $vars[0];
            }
            elseif(false !== strpos($var,'[')) {
                /* 支持 {$var['key']} 方式输出数组 */
                $name = "$".$var;
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
                $var = $match[1];

            } else {
                $name = "$$var";
            }

            /* 对变量使用函数 */
            if(count($varArray)>0)
                $name = $this->parseVarFunction($name,$varArray);
            $parseStr = '<?php echo ('.$name.'); ?>';
        }
        $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }

    /**
     *
     * 对模板变量使用函数
     * 格式 {$varname|function1|function2=arg1,arg2}
     *
     * @access public
     *
     * @param string $name 变量名
     * @param array $varArray  函数列表
     *
     * @return string
     *
     */
    public function parseVarFunction($name,$varArray){
        //对变量使用函数
        $length = count($varArray);
        //取得模板禁止使用函数列表
        $template_deny_funs = explode(',',config('TMPL_DENY_FUNC_LIST'));
        for($i=0;$i<$length ;$i++ ) {

            if (0===stripos($varArray[$i],'default=')) {
                $args = explode('=',$varArray[$i],2);
            } else {
                $args = explode('=',$varArray[$i]);
			}

            /* 模板函数过滤 */
            $args[0] = trim($args[0]);

            switch(strtolower($args[0])) {

				case 'default':
					/* 特殊模板函数 */
					$name   = '('.$name.')?('.$name.'):'.$args[1];
					break;

				default:
					/* 通用模板函数 */
					if(!in_array($args[0],$template_deny_funs)) {

						if(isset($args[1])) {
							if(strstr($args[1],'###')){
								$args[1] = str_replace('###',$name,$args[1]);
								$name = "$args[0]($args[1])";
							}else{
								$name = "$args[0]($name,$args[1])";
							}
						} else if(!empty($args[0])) {
							$name = "$args[0]($name)";
						}
					}
            }
        }
        return $name;
    }

    /**
     * 特殊模板变量解析 格式 以 $Think. 打头的变量属于特殊模板变量
     *
     * @access public
     *
     * @param string $varStr  变量字符串
     *
     * @return string
     */
    public function parseThinkVar($varStr) {

        $vars = explode('.',$varStr);

        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';

        if(count($vars)>=3) {

            $vars[2] = trim($vars[2]);

            switch($vars[1]) {

                case 'SERVER':
                    $parseStr = '$_SERVER[\''.strtoupper($vars[2]).'\']';break;

                case 'GET':
                    $parseStr = '$_GET[\''.$vars[2].'\']';break;

                case 'POST':
                    $parseStr = '$_POST[\''.$vars[2].'\']';break;

                case 'COOKIE':
                    if(isset($vars[3])) {
                        $parseStr = '$_COOKIE[\''.$vars[2].'\'][\''.$vars[3].'\']';
                    }else{
                        $parseStr = '$_COOKIE[\''.$vars[2].'\']';
                    }
					break;

                case 'SESSION':
                    if(isset($vars[3])) {
                        $parseStr = '$_SESSION[\''.$vars[2].'\'][\''.$vars[3].'\']';
                    } else {
                        $parseStr = '$_SESSION[\''.$vars[2].'\']';
                    }
                    break;

                case 'ENV':
                    $parseStr = '$_ENV[\''.$vars[2].'\']';
					break;

                case 'REQUEST':
                    $parseStr = '$_REQUEST[\''.$vars[2].'\']';
					break;

                case 'CONST':
                    $parseStr = strtoupper($vars[2]);
					break;

                case 'LANG':
                    $parseStr = 'Helper::createLanguage("'.$vars[2].'")';
					break;

				case 'CONFIG':
                    if(isset($vars[3])) {
                        $vars[2] .= '.'.$vars[3];
                    }
                    $parseStr = 'config("'.$vars[2].'")';
					break;

                default:
					break;

            }
        } else if(count($vars)==2) {
            switch($vars[1]){
                case 'NOW':
                    $parseStr = "date('Y-m-d g:i a',time())";
                    break;
                case 'VERSION':
                    $parseStr = 'THINK_VERSION';
                    break;
                case 'TEMPLATE':
                    $parseStr = 'config("TMPL_FILE_NAME")';
                    break;
                case 'LDELIM':
                    $parseStr = 'config("TMPL_L_DELIM")';
                    break;
                case 'RDELIM':
                    $parseStr = 'config("TMPL_R_DELIM")';
                    break;
                default:
                    if(defined($vars[1]))
                        $parseStr = $vars[1];
            }
        }
        return $parseStr;
    }

    /**
     * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
     *
     * @access public
     *
     * @param string $tmplPublicName  公共模板文件名
     *
     * @return string
     */
    public function parseInclude($tmplPublicName) {

		/* 导入特殊模板 */
		switch ( substr($tmplPublicName,0,10) ) {

		   case '__COMMON__':
				/* 核心模板的公共模板 */
				$tmplTemplateFile = COMMON_TPL_PATH . SEP . substr($tmplPublicName, 10).'.html';
				$parseStr = file_get_contents($tmplTemplateFile);
				return $this->parse($parseStr);

		    case '__PUBLIC__':
				/* 应用视图下面的公共模板 */
				$tmplTemplateFile = APP_VIEW_PUBLIC. SEP .substr($tmplPublicName,10).'.html';

				$parseStr = file_get_contents($tmplTemplateFile);
				return $this->parse($parseStr);

			case '__PUBLICS_':
				/* 系统视图下面的公共模板 */
				$tmplTemplateFile = PUBLIC_TPL_PATH.SEP.substr($tmplPublicName,10).'.html';
				$parseStr = file_get_contents($tmplTemplateFile);
				return $this->parse($parseStr);
		}

        if( substr($tmplPublicName, 0, 1) == '$' ) {
            /* 支持加载变量文件名 */
            $tmplPublicName = $this->get(substr($tmplPublicName, 1));
		}

        if(is_file($tmplPublicName)) {
            /* 直接包含文件 */
            $parseStr = file_get_contents($tmplPublicName);

        } else {

            if( strpos($tmplPublicName,'@') ) {
                /* 引入其它模块的操作模板 */
                $tmplTemplateFile   =   dirname(dirname(dirname($this->templateFile))).'/'.str_replace(array('@',':'),'/',$tmplPublicName);

			} elseif(strpos($tmplPublicName,':')) {

                /* 引入其它模块的操作模板 */
                $tmplTemplateFile   =   dirname(dirname($this->templateFile)).'/'.str_replace(':','/',$tmplPublicName);
            }else{
                /* 默认导入当前模块下面的模板 */
                $tmplTemplateFile = dirname($this->templateFile).'/'.$tmplPublicName;
            }

            $tmplTemplateFile .=  $this->config['template_suffix'];
            $parseStr = file_get_contents($tmplTemplateFile);
        }

        /* 再次对包含文件进行模板分析 */
        return $this->parse($parseStr);
    }

 }
