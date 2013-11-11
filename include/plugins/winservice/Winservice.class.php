<?php if(!defined('IN_SYS')) exit();
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
 * @version     $Id: Winservice.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * 服务接口抽象类
 */
 class Winservice extends Plugin  { 

 	/**
	 * 服务名称
	 */
	var $name='';
	
	/**
	 * 定义服务名称
	 */
	var $display='';
	
	
	/**
	 * 定义所要执行的程序
	 */
	var $params='';
	
	
	/**
	 * 定义php.exe存放路径
	 */
	var $path='D:\Website\xampp\php\php.exe';
	
	
	/**
	 * 构造函数
	 *
	 * @access private
	 * 
	 * @return void
	 */
	public function __construct( $serviceName, $program, $ShowName=null ) {
		$this->name = $serviceName;
		$this->params  = $program; 
		$this->display = isset($showName) ? $showName : $serviceName;
	}
	
	/**
	 * 安装服务
	 *
	 * @access public
     *
	 * @return void
	 */
	public function install() {
	
		/* 注册服务  */ 
		$x = win32_create_service ( array (
			'service' => $this->name, 
			'display' => $this->display, 
			'path'    => $this->path, 
			'params'  => $this->params, 
		)); 
  
		/* 启动服务 */ 
		win32_start_service ( $this->name ); 
		if ( $x !== true ) { 
			die ( '服务创建失败!' );
		} else { 
			die ( '服务创建成功!' );
		}
	}
	
	
	/**
	 * 卸载服务
	 *
	 * @access public
     *
	 * @return void
	 */
	public function uninstall() {
	
		/* 移除服务 */ 
		$removeService = win32_delete_service( $this->name ) ; 
		switch ($removeService) { 
			case   1060:  die ('服务不存在！' ) ;         break; 
			case   1072:  die ('服务不能被正常移除! ' ) ; break; 
			case      0:  die ('服务已被成功移除！' ) ;   break; 
			default    :  die ('未知错误!');              break; 
		}
	}
	
	/**
	 * 重启服务
	 *
	 * @access public
     *
	 * @return void
	 */
	public function restart() {
		
		/* 重启服务 */ 
		$svcStatus = win32_query_service_status( $this->name ); 

		if ( $svcStatus == 1060 ) {
			echo   "服务[" . $this->name . "]未被安装，请先安装"; 
		} else {
			if ( $svcStatus['CurrentState'] == 1 ) {
				$s = win32_start_service($this->name);				
				if ( $s != 0 ) { 
					echo  "服务无法被启动，请重试！ "; 
				} else { 
					echo  "服务已启动! "; 
				}
				
			} else {
				$s = win32_stop_service($this->name) ;
				if ( $s != 0 ) {
					echo " 服务正在执行，请重试！ " ; 					
				} else {				
					$s = win32_start_service( $this->name ) ; 
					 if ( $s != 0 ){ 
						echo   "服务无法被启动，请重试！ "; 
					 } else { 
						echo   "服务已启动! "; 
					 } 	
				}    
			} 
		} 
	}
	
	/**
	 * 启动服务
	 *
	 * @access public
     *
	 * @return void	 
	 */
	public function start() {
		$s = win32_start_service($this->name); 		  
		if ( $s != 0 ){ 
			echo   " 服务正在运行中！ " ; 
		  } else { 
			echo   " $s 服务已启动! " ; 
		  }
	}
	
	/**
	 * 停止服务
	 *
	 * @access public
     *
	 * @return void
	 */
	public function stop() {	
		$s = win32_stop_service($this->name ); 		  
		if ( $s != 0 ){ 
			echo   " 服务未启动！ " ; 
		} else { 
			echo   " 服务已停止！ " ; 
		}
	}
 }
