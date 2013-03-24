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
 * @version     $Id: TiwerException.class.php 43 2012-12-03 10:13:15Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 系统异常基类
 */
 class TiwerException extends Exception
 {
    /**
     * 异常类型
     *
     * @var    string
     * @access private
     */
    private $type;

    
    /**
	 * 是否存在多余调试信息
	 * 
	 * @var    string 
	 * @access private
	 */
    private $extra;

    
    /**
     * 架构函数
     *
     * @access public
     *
     * @param  string $message  异常信息
     */
    public function __construct($message, $code=0, $extra=false) {
        parent::__construct($message,$code);
		
        $this->type = get_class($this);
        $this->extra = $extra;
    }
     
    /**
     * 输出异常的详细信息和调用堆栈
     *
     * <code>
     *     <?php
	 *         ## 例子
     *         TiwerException::dump($ex);
     *     ?>
     * </code>
     * 
     */
    static function dump(Exception $ex) {
		
    	/* 异常出错的地方   */
    	$out = "exception '" . get_class($ex) . "'";
		
    	
        /* 错误信息   */
        if ( $ex->getMessage() != '')
            $out .= " with message '" . $ex->getMessage() . "'";        
		
            
        $out .= ' in '.$ex->getFile().':'.$ex->getLine() . "\n\n";
        $out .= $ex->getTraceAsString();        
        if (ini_get('html_errors')) {
            echo nl2br(htmlspecialchars($out));
        }  else {
            echo $out;
        }
    }
    
    
    /**
     * 异常输出 所有异常处理类均通过__toString方法输出错误，每次异常都会写入系统日志。该方法可以被子类重载
     *
     * @access public
     *
     * @return array
     */
    public function __toString() {
		
        $trace = $this->getTrace();

        /* 通过抛出的异常要去掉多余的调试信息 */
        if($this->extra) array_shift($trace);		
		
        $this->class = $trace[0]['class'];
        $this->function = $trace[0]['function'];		
        $this->file = $trace[0]['file'];
        $this->line = $trace[0]['line'];
		
        $file = file($this->file);
        $traceInfo='';
        $time = date("y-m-d H:i:m");
		
        
        foreach($trace as $t) {
            $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
            $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
            $traceInfo .= implode(', ', $t['args']);
            $traceInfo .=")\n";
        }
		
        
        $error['message']  =  $this->message;
        $error['type']     =  $this->type;		
        $error['detail']   =  Helper::createLanguage('_MODULE_').'['.CONTROLLER_NAME.'] '.Helper::createLanguage('_ACTION_').'['.ACTION_NAME.']'."\n";
        $error['detail']  .=  ($this->line-2).': '.$file[$this->line-3];
        $error['detail']  .=  ($this->line-1).': '.$file[$this->line-2];
        $error['detail']  .=  '<font color="#FF6600" >'.($this->line).': <b>'.$file[$this->line-1].'</b></font>';
        $error['detail']  .=  ($this->line+1).': '.$file[$this->line];
        $error['detail']  .=  ($this->line+2).': '.$file[$this->line+1];
        $error['class']    =  $this->class;
        $error['function'] =  $this->function;
        $error['file']     =  $this->file;
        $error['line']     =  $this->line;
        $error['trace']    =  $traceInfo;

        /* 记录系统日志 */
        Log::Write('('.$this->type.') '.$this->message);
        return $error ;
    }	
 }
