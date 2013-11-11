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
 * @version     $Id: Log.class.php 516 2013-07-30 09:02:02Z wgw $
 *
 * 日志处理类
 */
 class Log extends Framework {
 	 	
 	
    /* 日志级别 从上到下，由低到高 */
    const EMERG  = 'EMERG'; // 严重错误: 导致系统崩溃无法使用
    const ALERT  = 'ALERT'; // 警戒性错误: 必须被立即修改的错误
    const CRIT   = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR    = 'ERR';   // 一般错误: 一般性错误
    const WARN   = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE = 'NOTIC'; // 通知: 程序可以运行但是还不够完美的错误
    const INFO   = 'INFO';  // 信息: 程序输出信息
    const DEBUG  = 'DEBUG'; // 调试: 调试信息
    const SQL    = 'SQL';   // SQL：SQL语句 注意只在调试模式开启时有效

    
    /* 日志记录方式 */
    const SYSTEM= 0;
    const MAIL  = 1;
    const TCP   = 2;
    const FILE  = 3;
    

    /* 日志信息 */
    static $log =   array();

    
    /* 日期格式 */
    static $format =  '[ c ]';

    /**
     * 记录日志 并且会过滤未经设置的级别
     *
     * @static
     * @access public
     *
     * @param string $message  日志信息
     * @param string $level    日志级别
     * @param boolean $record  是否强制记录
     *
     * @return void
     */
    static function record($message, $level=self::ERR, $record=false) {	
        if( $record || in_array($level, config('LOG_RECORD_LEVEL')) ) {
            $now = date(self::$format);			
            self::$log[] = "{$now} {$level}: {$message}\r\n";
        }
    }

    
    /**
     * 日志保存
     *
     * @static
	 *
     * @access public
     *
     * @param integer $type        日志记录方式
     * @param string  $destination 写入目标
     * @param string  $extra       额外参数
     *
     * @return void
     */
    static function save($type=self::FILE, $destination='', $extra='') {
	
        if( empty($destination) ) {
            $destination = LOG_PATH.date('y_m_d').".tiwer.log";
		}
			
        if(self::FILE == $type) { 
		
			/* 文件方式记录日志信息.检测日志文件大小，超过配置大小则备份日志文件重新生成 */
            if(is_file($destination) && floor(config('LOG_FILE_SIZE')) <= filesize($destination) ) {
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
			}			
        }
		
		/* 函数向服务器错误记录、文件或远程目标发送一个错误 */
        error_log(implode("", self::$log), $type, $destination, $extra);
		
        /* 保存后清空日志缓存 */ 
        self::$log = array();
    }
    

    /**
     * 日志直接写入
     *
     * @static
     * @access public
     *
     * @param string  $message     日志信息
     * @param string  $level       日志级别
     * @param integer $type        日志记录方式
     * @param string  $destination 写入目标
     * @param string  $extra       额外参数
     *
     * @return void
     */
    static function write($message, $level=self::ERR, $type=self::FILE, $destination='', $extra='') {
	
        $now = date(self::$format);
		
        if(empty($destination)) {
            $destination = LOG_PATH.date('y_m_d').".tiwer.log";
		}
			
        if(self::FILE == $type) {
		
            /*  文件方式记录日志. 检测日志文件大小，超过配置大小则备份日志文件重新生成 */
            if(is_file($destination) && floor(config('LOG_FILE_SIZE')) <= filesize($destination) ) {
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
			}
        }
		
		/* 函数向服务器错误记录、文件或远程目标发送一个错误 */
        error_log("{$now} {$level}: {$message}\r\n", $type,$destination,$extra );
    }
    
 }
