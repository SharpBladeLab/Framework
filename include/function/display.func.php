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
 * @version     $Id: display.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 显示输出函数库
 */ 
 
/**
 * 错误输出
 *
 * @param string $error 错误信息
 */
 function halt($error) {
	
	/* 是否命令模式输出信息 */
    if(IS_CLI) exit ($error);	
    $e = array();	
    
    
    if( config('APP_DEBUG') ) {	

    	/* 调试模式下输出错误信息 */
        if( !is_array($error) ) {
			
            $trace = debug_backtrace();
			$e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
			
            $traceInfo='';
            $time = date("y-m-d H:i:m");
			
            foreach($trace as $t) {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .= ")<br/>";
            }
            $e['trace']  = $traceInfo;
        } else {
            $e = $error;
        }
		
        /* 包含异常页面模板 */ 
        include config('TMPL_EXCEPTION_FILE');
		
    } else {
	
        /* 否则定向到错误页面 */
        $error_page = config('ERROR_PAGE');		
        if(!empty($error_page)){
            redirect($error_page);
            
        } else {            
			if( config('SHOW_ERROR_MSG') ) {
                $e['message'] =  is_array($error)?$error['message']:$error;
            } else {
                $e['message'] = config('ERROR_MESSAGE');
			}			
            /* 包含异常页面模板 */ 
            include config('TMPL_EXCEPTION_FILE');
        }
    }
    exit;
 }
 
 
/**
 * 浏览器友好的变量输出
 *
 * @param string  $var  变量
 * @param boolean $echo 是否输出
 * @param string  $labe 标签
 * @param boolean $strict 
 *
 * @return 
 */
 function dump($var, $echo = true, $label = null, $strict = true) {

    $label = ($label===null) ? '' : rtrim($label) . ' ';
	
    if( !$strict ) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre style="text-align:left">'.$label.htmlspecialchars($output,ENT_QUOTES).'</pre>';
        } else {
            $output = $label . " : " . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre style="text-align:left">'. $label. htmlspecialchars($output, ENT_QUOTES). '</pre>';
        }
    }
    
    if ($echo) {
        echo($output);
        return null;
    } else {
        return $output;
    }
}

 /**
  * print_r 函数的另一种写法
  * 
  * @param array $arr
  *
  * @return void
  */
 function printr($arr) {  
    echo '<pre>';   
    print_r($arr);  
    echo '</pre><br>';  
 }
 
 /**
  * 对php代码文件进行语法高亮显示
  * 
  * @param  $str
  */
 function print_php($str){
 	return highlight_file($str,$return);
 }
 
 /**
  * 打印出整个运行时的变量定义
  */
 function print_work_var() {
 	printr(get_defined_vars());
 }

 