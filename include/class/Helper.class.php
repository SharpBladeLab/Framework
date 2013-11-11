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
 * @copyright   Copyright 2009, Tiwer Studio
 * @author      wgw8299 <wgw8299@gmail.com>
 * @package     Tiwer Developer Framework
 * @version     $Id: Helper.class.php 516 2013-07-30 09:02:02Z wgw $
 *
 * 工具类对象，存放着各种杂项的工具方法
 *
 * Helper::createLink();        URL组装支持不同模式和路由
 * Helper::createModel();       实例化应用系统Model
 * Helper::createController();  实例化控件器
 * Helper::createLanguage();    获取和设置语言定义
 * Helper::createCache();       全局缓存和读取
 * Helper::createException();   抛出异常
 * Helper::createPlugin();      加载插件
 * Helper::createBusiness();    创建商务逻辑层类
 * Helper::createService();     创建系统服务层类
 * Helper::createTempFile();    快速文件数据读取和保存 
 * Helper::createApi();         获取API函数库
 * Helper::createTimer();       计数器方法
 * Helper::createDebug();       调试 时间与内存统计器
 * 
 * 
 * Helper::array2Object();      将一个数组转成对象格式
 * Helper::safe64Encode();      生成对框架安全的base64encode串
 * Helper::safe64Decode();      要解码的字符串列表
 * Helper::diffDate();          计算两个日期的差
 */
 class Helper extends Framework
 {
    /**
     * URL组装支持不同模式和路由
     *
	 * @prarm string   $url       表示URL规则
	 * @prarm boolean  $params    表示参数必须使用数组传入 
	 * @param boolean  $redirect  是否需要跳转到生成的URL地址
	 * @param boolean  $suffix    表示是否添加伪静态后缀，设置了伪静态后有效
     * 
	 * @static
     * @access public
     */
    public static function createLink($url, $params=false, $redirect = false, $suffix=true) {
		
		/* 普通模式 */
		if( false == strpos($url,'/') ) $url .= '//';
		
		/* 参数在地址中 */
		if( false != strpos($url,'?') ) {			
			$painfo = explode('?',$url);
			if($params==false) {
				$params = $painfo[1];
				$url =$painfo[0];
			}
		}
		
		/* 填充默认参数 */
		$urls = explode('/',$url);
		$app  = ($urls[0]) ? $urls[0] : APP_NAME;
		$mod  = ($urls[1]) ? $urls[1] : 'index';
		$act  = ($urls[2]) ? $urls[2] : 'index';
		$mod = strtolower($mod);
		
		
    	/* 组合网址路径 */
		if( config('URL_DIGCITY') ) {
			if( config('URL_DIGCITY_SAFE') ) {
				$site_url =	$app.'/'.$mod.'/'.$act;				
			} else {
				if ( config('URL_REWRITERULE') ) {
					$site_url = SITE_URL.'/'.$app.'/'.$mod.'/'.$act ;
				} else {
					$site_url = SITE_URL.'/index.php/'.$app.'/'.$mod.'/'.$act;
				}
			}			
		} else {
			if (config('URL_REWRITERULE')) {
				$site_url =	SITE_URL.'/?'.config('VAR_APP').'='.$app.'&'.config('VAR_MODULE').'='.$mod.'&'.config('VAR_ACTION').'='.$act;
			} else {
				$site_url =	SITE_URL.'/index.php?'.config('VAR_APP').'='.$app.'&'.config('VAR_MODULE').'='.$mod.'&'.config('VAR_ACTION').'='.$act;
			}
		}
		
		
		/* 标实 */
    	if ( $params==false || empty($params) || (is_array($params) && empty($params['symbol'])) ) {
    		$symbol = isset($_GET['symbol']) ? text($_GET['symbol']) : text($_POST['symbol']);
    		!empty($symbol) && $params['symbol'] = $symbol;
    	}
		
		
		/* 填充附加参数 */
		if( $params ) {
			if(is_array($params)) {
				$params	= http_build_query($params);
				$params	= urldecode($params);
			}
			if( config('URL_DIGCITY') ) {
				$params	= str_replace('&amp;','/',$params);
				$params	= str_replace('&','/',$params);
				$params	= str_replace('=','/',$params);	
				$site_url .= '/'.$params;
			} else {
				$params	   = str_replace('&amp;','&',$params);
				$site_url .= '&'.$params;
			}
		}
		
		
		/* 开启路由和Rewrite */
		if ( config('URL_ROUTER_ON') ) {

			/* 载入路由 */
			$router_ruler= config('router');
			$router_key	 = $app.'/'.ucfirst($mod).'/'.$act;

			
			/* 路由命中 */
			if( isset($router_ruler[$router_key]) ) {

				/* 填充路由参数 */
				$site_url =	SITE_URL.'/'.$router_ruler[$router_key];

				/* 填充附加参数 */
				if($params) {
					/* 解析替换URL中的参数 */
					parse_str($params,$r);
					foreach($r as $k=>$v){
						if(strpos($site_url,'['.$k.']')){
							$site_url	=	str_replace('['.$k.']',$v,$site_url);
						}else{
							$lr[$k]	=	$v;
						}
					}
					
					/* 填充剩余参数 */
					if(is_array($lr) && count($lr)>0){
						$site_url	.=	'?'.http_build_query($lr);
					}
				}
			}
		}
		
		
		/* 添加URL安全码 */
		if( config('URL_DIGCITY_SAFE') ) {
			if (config('URL_REWRITERULE')) {
				$site_url = SITE_URL.'/'.base64_encode($site_url);
			} else {
				$site_url = SITE_URL.'/index.php/'.base64_encode($site_url);
			}
		}
		
		
		/* 网址后加扩展名 */
		if( config('URL_HTML') ) {
			$site_url .= config('URL_HTML_EXTENDED');
		}
		
		/* 输出地址或跳转 */
		if( $redirect ) {
			redirect($site_url);
		} else {
			return $site_url;
		}
	}
	
	
	/**
	 * 实例化应用系统Model
	 *
	 * <code>
     *     <?php
	 *         ## 创建kernel应用下的user模型
     *         Helper::createModel('user', 'kernel');  D Function  D方法必须有创建模型类
	 *
	 *         ## 创建无Model文件的user数据表的模型
	 *         Helper::createModel('user', '', true);  M Function  M方法不需要创建模型类文件
	 *   
     *     ?>
     * </code>
	 *
	 * @param string  name    Model名称
	 * @param string  app     Model所在项目
	 * @param boolean isFile  是否存在Model文件  
	 *                        true:不存在Model文件   
	 *				          false:存在Model文件
	 *
	 * @param boolean class   Model文件
	 *
	 * @return Model
	 */
	public static function createModel( $name = '', $app = '', $isFile = false,  $class = 'Model', $param=null) {
	
		if( $isFile === true ) {
			/* 实例化一个没有模型文件的Model */
			static $_modelclass = array();			
			if( !isset($_modelclass[$name . '_' . $class]) ) {
				$_modelclass[$name.'_'.$class] = new $class($name);			
			}			
			return $_modelclass[$name . '_' . $class];
			
		} else {
		
			/* 对应存在模型文件 */
			static $_model = array();
			static $_app   = array();
			
			if(empty($name)) return new Model;
			if(empty($app)) $app =  APP_NAME;	
			
			if(isset($_model[$app.$name])) return $_model[$app.$name];
			
			/* 原型类名 */
			$OriClassName = $name;
			
			if(strpos($name, config('APP_GROUP_DEPR'))) {
				$array = explode(config('APP_GROUP_DEPR'),$name);
				$name = array_pop($array);
				$className = $name.'Model';				
				import($app.'.model.'.implode('.',$array).'.'.$className);
				
			} else {
				$className =  $name.'Model';
				$_ENV['app'] = $app;		
				import($app.'.model.'.$className);
			}
			
			if(class_exists($className)) {
				$model = empty($param) ? new $className() : new $className($param);	
			} else {
				$model  = new Model($name);
			}			
			$_model[$app.$OriClassName] =  $model;
			return $model;
		}
	}
	
	
	/**
	 * 实例化控件器工具方法
	 *
	 * <code>
     *     <?php Helper::createController('user', 'kernel'); ?>
     * </code>
	 *
	 * @param string name 控件器名称
	 * @param string app  控件器所在项目,默认为当前应用
	 *
	 * @return Controller
	 */
	public static function createController( $name, $app = '@') {
		
		/* 缓存功能 */
		static $_action = array();
	 
		/* 如果存在的话就直接返回该对象 */
		if(isset($_action[$app.$name])) return $_action[$app.$name];		
		$OriClassName = $name;
		
		
		if(strpos($name, config('APP_GROUP_DEPR'))) {
			$array = explode(config('APP_GROUP_DEPR'), $name);
			$name = array_pop($array);
			
			$className =  $name.'Controller';
			
			import($app.'.controller.'.implode('.',$array).'.'.$className);
			
		} else {		
			$className =  $name.'Controller';
			import($app.'.controller.'.$className);
		}
		
		if( class_exists($className) ) {
			$action = new $className();
			$_action[$app.$OriClassName] = $action;
			return $action;		
		} else {
			return false;
		}
	}	
	
	
	/**
	 * 创建商务逻辑层类
	 *
	 * <code>
     *     <?php Helper::createBusiness('user'); ?>
     * </code>
	 *
	 * @param string $name   名称
	 * @param array  $params 参数
	 *
	 * @return object
	 */
	public static function createBusiness($name, $params = array()) {
		return Instantiation($name, $params, 'Business');
	}
	
	
 	/**
	 * 加载插件
	 *
	 * <code>
     *     <?php Helper::createPlugin('Pinyin'); ?>
     * </code>
	 *
	 * @param string $name   名称
	 * @param array  $params 参数
	 *
	 * @return object
	 */
	public static function createPlugin($name, $params=array()) {
		return Instantiation($name, $params, 'Plugin');
	}

	
	/**
	 * 获取和设置语言定义(不区分大小写)
	 *
	 * <code>
     *     <?php
     *         Helper::createLanguage('_APP_INACTIVE_');
     *     ?>
     * </code>
	 *
	 * @param mixed $name  名称
	 * @param mixed $value 值
	 *
	 * @return mixed
	 */
	public static function createLanguage($name=null,$value=null) {	
		static $_lang = array();

		
		/* 空参数返回所有定义 */ 
		if(empty($name)) return $_lang;
		
		/* 判断语言获取(或设置) 若不存在,直接返回全大写$name */
		if (is_string($name) ) {
			
			$name = strtoupper($name);
			if ( is_null($value) ) {
				return isset($_lang[$name]) ? $_lang[$name] : $name;
			}
			
			/* 语言定义 */
			$_lang[$name] = $value;
			return;
		}
		
		/* 批量定义 */
		if (is_array($name)) {
			$_lang += array_change_key_case($name,CASE_UPPER);
		}
		return;
	}
	
	
	/**
	 * 抛出异常
	 *
	 * <code>
     *     <?php
     *         Helper::createException('抛出异常，系统出错了！！！');
     *     ?>
     * </code>
	 *
	 * @param mixed $name  名称
	 * @param mixed $value 值
	 *
	 * @return mixed
	 */
	public static function createException($message, $type='TiwerException', $code=0) {		
		if(IS_CLI) exit($message);		
	    if( class_exists($type, false)) {
	        throw new $type($message,$code,true);
	    } else {
	        halt($message);   
		}
	}
	
	
	/**
	 * 快速文件数据读取和保存 (针对简单类型数据 字符串、数组)
	 *
	 * @param string  $name  文件名称
	 * @param strubg  $value 值内容
	 * @param boolean $path  目录
	 *
	 * @return array
	 */
	public static function createTempFile($name, $value='', $path = false) {
		
	    static $_cache = array();
		
		/* 文件目录是否为空 */
	    if(!$path) $path = TEMPS_PATH.SEP.'sysinfo'.SEP;	
	    
		/* 文件权限 */
	    if( !is_dir($path) ) mkdir($path, 0777, true);  
	    	
		/* 文件的全路径 */
	    $filename = $path.$name.'.php';	
	    
	    
	    if( '' !== $value) {
	        if( is_null($value) ) {
	            return unlink($filename);
	        } else {
	        	
	            /* 缓存数据 */
	            $dir = dirname($filename);
				
	            /* 目录不存在则创建 */ 
	            if(!is_dir($dir))  mkdir($dir);
				
				$sysinfo = "<?php\n/**\n * 数据库{$name}表字段文件(些文件由系统自动生成,请不要任何形式的修改)\n *\n * Project: Tiwer Developer Framework\n * This is NOT a freeware, use is subject to license terms! \n * \n * Site: http://www.tiwer.cn  \n *\n * author: {$name}.php ".date('Y-m-d- H:i:s')." wgw8299<wgw8299@163.com> \n *\n * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.\n */\n \n if (!defined('IN_SYS')) exit(); \n \n";
	            return file_put_contents($filename, $sysinfo." return ".var_export($value,true).";\n?>");
	        }
	    }
		
	    if(isset($_cache[$name])) return $_cache[$name];
		
	    /* 获取缓存数据 */
	    if( is_file($filename) ) {
	        $value = include $filename;
	        $_cache[$name] = $value;
	    } else {
	        $value = false;
	    }
	    return $value;
	}

	
	
	/**
	 * 全局缓存和读取
	 *
	 * <code>
     *     <?php
     *         Helper::createCache('wgw8299');
     *     ?>
     * </code>
	 *
	 * @param string $name   名称
	 * @param array  $value  值
	 * @param string $expire 到期时间
	 * @param string $type   类型
	 *
	 * @return mixed  缓存数据
	 */
	public static function createCache($name=null,$value=null) {
	
		static $_cache = array();  
	
		alias_import('Cache');
		
		/* 取得缓存对象实例 */
		$cache  = Cache::getInstance($type);
		
		if( '' !== $value) {
		
			if(is_null($value)) {		
				/* 删除缓存 */
				$result = $cache->rm($name);
				if($result) unset($_cache[$type.'_'.$name]);
				return $result;
				
			} else {		
				/* 缓存数据 */
				$cache->set($name, $value, $expire);
				$_cache[$type.'_'.$name] = $value;
			}
			return;
		}
		
		if( isset($_cache[$type.'_'.$name]) ) {
			return $_cache[$type.'_'.$name];
		}
			
		/* 获取缓存数据 */
		$value = $cache->get($name);
		$_cache[$type.'_'.$name] = $value;
		
		return $value;
	}
	
	/**
     * 获取API函数库
     * 
     * @param array $name API名称
     * 
     * <code>
     *     <?php
     *       ## 获取用户接口
     *       Helper::createApi('UserInfo');
     *     ?>
     * </code>
	 *
     * @static
     * @access public
	 *
     * @return mixed
     */
    public static function createApi($name) {    	
	    static $_api = array();
		
	    if( isset($_api[$name]) ) {
	        return $_api[$name];
	    }
	    	
	    $OriClassName = $name;
	    $className = $name.'Api';
	    
		/* 载入API类 */
		require_once( API_PATH.SEP.$name.'.class.php');
	    
		/* 是否存在该API类 */
		if ( class_exists($className) ) {	
	        $api = new $className(true);
	        $_api[$OriClassName] = $api;		
	        return $api;
	        
	    } else {
	        return false;
	    }
    }
	
	
	/**
     * 计数器方法，被用于核心的查询、缓存统计的计数和统计
     * 
     * <code>
     *     <?php
     *        # Timer 
     *        Helper::createTimer('satrt', 1);
     *        
     *        # Get Timer 
     *        echo Helper::createTimer('satrt');
     *     ?>
     * </code>
     * 
     * @param string  $key    键名
     * @param integer $step   步进值<为空是获取计数值>
     * 
     * @static
     * @access  public
	 * 
     * @return  integer/null
     */
 	public static function createTimer($key, $step=0) {
	    static $_num = array();
	    
	    if ( !isset($_num[$key]) ) $_num[$key] = 0;	    
	    if (empty($step)) {
	        return $_num[$key];
	    } else {
	        $_num[$key] = $_num[$key] + (int) $step;
	    }
	}
	
	
	
	/**
	 * 调试 时间与内存统计器
	 * 
	 * <code>
     *     <?php
     *        # Start 
     *        Helper::createDebug('satrt');
     *        
     *        # End Print HTML
     *        Helper::createDebug('satrt', 'end', true);
     *     ?>
     * </code>
	 * 
	 * @param string  $start  开始
	 * @param string  $end    结束
	 * @param boolean $print  是否打印html
	 * @param integer $dec    统计精度小数点后位数据<默认6位>
	 * 
	 * @static
     * @access  public
	 * 
     * @return  mixed
	 */
	public static function createDebug($start='', $end='', $print=false, $dec=6) {
		static $_info = array();
		
		if( !empty($end) ) {
				
			
			/* 是否存在结束统计  */
			if( !isset($_info[$end]['time']) ) {
				$_info[$end]['time'] = microtime(TRUE);
			}
	        if( MEMORY_LIMIT_ON &&  !isset($_info[$end]['memory']) ) {
	        	$_info[$end]['memory'] = memory_get_usage();	
	        }
	        
	        
	        /* 目前为止调用它的运行进程的最高内存量 */
	        if ( function_exists('memory_get_peak_usage') ){
	        	$_info[$end]['peak'] = memory_get_peak_usage();
	        } else {
	        	$_info[$end]['peak'] = $_info[$end]['memory'] ;
	        }
       
	        
	        /* 统计 */
	        $reslut['peak'] = number_format(($_info[$end]['peak'] > $_info[$start]['peak'] ? $_info[$end]['peak'] : $_info[$start]['peak'])/1024).' k';
			$reslut['time'] = number_format(($_info[$end]['time']-$_info[$start]['time']), $dec).'s ';
        	if( MEMORY_LIMIT_ON ){
        	 	$reslut['memory'] = number_format(($_info[$end]['memory']-$_info[$start]['memory'])/1024).' k';
        	}
        	
        	
	        /* 是否打印统计结果  */
	        if( $print ) {
	        	 echo '<div style="text-align:center;width:100%">Process '.$start.'-'.$end.': Times '.$reslut['time'].' Memories '.$reslut['memory'].' Maximum memory：'. $reslut['peak'] .'</div>';
	        } else {
	        	return $reslut;
	        }
	        
	        
	        
		} else {			
			/* 区间统计  */
		 	$_info[$start]['time']= microtime(TRUE);
			if ( MEMORY_LIMIT_ON ) $_info[$start]['memory'] = memory_get_usage();	
			if ( function_exists('memory_get_peak_usage') ) $_info[$start]['peak'] = memory_get_peak_usage();
		}
	} 
		
	
    /**
     * 将一个数组转成对象格式。此函数只是返回语句，需要eval
     * 
     * <code>
     *     <?php
     *         $config['user'] = 'wgw8299';
     *         eval(Helper::array2Object($config, 'configobj');
     *         print_r($configobj);
     *     ?>
     * </code>
	 *
     * @param array     $array          要转换的数组
     * @param string    $objName        要转换成的对象的名字
     * @param string    $memberPath     成员变量路径，最开始为空，从根开始
     * @param bool      $firstRun       是否是第一次运行
	 *
     * @static
     * @access public
	 *
     * @return void
     */
    public static function array2Object($array, $objName, $memberPath = '', $firstRun = true) {
        
    	if( $firstRun ) {
            if( !is_array($array) or empty($array) ) return false;
        }
        
        static $code = '';
        $keys = array_keys($array);
		
        foreach($keys as $keyNO => $key) {
            $value = $array[$key];
            if(is_int($key)) $key = 'item' . $key;
            $memberID = $memberPath . '->' . $key;
			
            if(!is_array($value)) {
                $value = addslashes($value);
                $code .= "\$$objName$memberID='$value';\n";
            } else {
                Helper::array2object($value, $objName, $memberID, $firstRun = false);
            }
        }
        return $code;
    }
    
    
    /**
     * 生成对框架安全的base64encode串
     * 
     * @param  string $string   要编码的字符串列表。
	 *
     * @static
     * @access  public
	 *
     * @return  string
     */
    public static function safe64Encode($string) {
        return strtr(base64_encode($string), '+/=', '');
    }
    
    
    /**
     * 解码
     * 
     * @param   string  $string   要解码的字符串列表
	 *
	 * @static
     * @access  public
	 *
     * @return  string
     */
    public static function safe64Decode( $string ) {
        return base64_decode(strtr($string, '', '+/='));
    }
    

    /**
     * 计算两个日期的差
     * 
     * @param   date  $date1   第一个时间
     * @param   date  $date2   第二个时间
	 *
	 * @static
     * @access  public
	 *
     * @return  string
     */
    public static function diffDate($date1, $date2) {
        return round((strtotime($date1) - strtotime($date2)) / 86400, 0);
    }    
 }
