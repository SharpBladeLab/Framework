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
 * @version     $Id: Framework.class.php 41 2012-12-03 08:07:29Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 开发框架基类
 */
 class Framework {
 	
	/* 实例化的存储数组 */
    private static $_instance = array();

    
    /**
     * 自动变量设置
     *
     * @access public
     *
     * @param $name   属性名称
     * @param $value  属性值
     */
    public function __set($name, $value) {
        if( property_exists($this, $name) ) {
            $this->$name = $value;
		}
    }

    
    /**
     * 自动变量获取
     *
     * @access public
     *
     * @param $name 属性名称
     *
     * @return mixed
     */
    public function __get($name) {
        return isset($this->$name) ? $this->$name : null;
    }

    
    /**
     * 系统自动加载类库 (并且支持配置自动加载路径)
     *
     * @param string $classname 对象类名
     *
     * @return void
     */
    public static function autoload($classname) {
	
        /* 检查是否存在别名定义 */
        if(alias_import($classname)) return ;
		
        /* 自动加载当前项目的Controller类和Model类 */
        if( substr($classname, -5) == "Model" ) {
            require_cache(MODEL_PATH. $classname.  '.class.php');
			
            
		} elseif(substr($classname, -6) == "Controller") {
            require_cache( CONTROL_PATH .$classname. '.class.php');
        
            
		} else {
			
            /* 根据自动加载路径设置进行尝试搜索 */
            if(config('APP_AUTOLOAD_PATH')) {            
				$paths  =   explode(',', config('APP_AUTOLOAD_PATH'));				
                foreach ($paths as $path){
					if(import($path.$classname.'.class.php')) {                       
                        return;
					}
                }
            }
        }
        return;
    }

    
    /**
     * 取得对象实例 支持调用类的静态方法
     *
     * @param string $class  对象类名
     * @param string $method 类的静态方法名
     *
     * @return object
     */
    static public function instance($class, $method = '' ) {
	
		/* 获取对象实例标实 */
        $identify = $class.$method;		
        if( !isset(self::$_instance[$identify]) ) {
		
        	
			/* 是否存在类 */
            if( class_exists($class) ) {			
                $o = new $class();                
				if( !empty($method) && method_exists($o, $method) ) {
                    self::$_instance[$identify] = call_user_func_array(array(&$o, $method));
                } else {
                    self::$_instance[$identify] = $o;
				}
            
			} else {
                halt(Helper::createLanguage('_CLASS_NOT_EXIST_').' = '.$class.' = '.$method);
			}
        }
        return self::$_instance[$identify];
    }	
    
 }
