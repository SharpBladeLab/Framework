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
 * @version     $Id: crontab.php 410 2012-12-23 05:48:09Z wgw $
 * @link        http://www.tiwer.cn
 *
 * <code>
 * 	   $crontab=new crontab('D:/website/crontab/', "filename");
 *     $crontab->setDateParams(1, "*", "*", "*", "*");
 *     $crontab->setCommand("curl http://wwww.www.www/");
 *     $crontab->saveCronFile();
 *     $crontab->addToCrontab();
 *     $crontab->destroyFilePoint();
 * </code>
 *
 * 任务计划类
 */
 class crontab
 {	  
	/* 分种 */
	var $minute=NULL;
	/* 小时 */
	var $hour=NULL;
	/* 天 */
	var $day=NULL;
	/* 月 */
	var $month=NULL;
	/* 周 */
	var $dayofweek=NULL;
	
	
	/* 命令 */
	var $command=NULL;
	/* 目录 */
	var $directory=NULL;
	/* 文件 */
	var $filename="crons";
	
	
	
	/* 应用程序路径 */
	var $crontabPath=NULL;	
	/* handle */
	var $handle=NULL;
	
	
	
	/**
	 *	构造函数
	 *
	 *	@param	string	$dir		 任务设计目录
	 *	@param	string	$filename	 写入文件
	 *	@param	string	$crontabPath 应用程序路径
	 *	@access	public
	 */
	function crontab($dir=NULL, $filename=NULL, $crontabPath=NULL){
		
		/* 创建目录 */
		$result	 =(!$dir) ? $this->setDirectory("~/my_crontabs") : $this->setDirectory($dir);
		if( !$result ) exit('Directory error');
		
		
		/* 创建文件 */
		$result	=(!$filename) ? $this->createCronFile("crons") : $this->createCronFile($filename);
		if( !$result ) exit('File error');
		
		
		/* 应用程序路径 */
		$this->pathToCrontab=($crontabPath) ? NULL : $crontabPath;
	}
	
	

	/**
	 *	设置日期参数（默认值“*”）  具体详情请参考Linux Crontab 时间格式
	 *
	 *	@param	mixed	$min		Minute(s)... 0 to 59
	 *	@param	mixed	$hour		Hour(s)... 0 to 23
	 *	@param	mixed	$day		Day(s)... 1 to 31
	 *	@param	mixed	$month		Month(s)... 1 to 12 or short name
	 *	@param	mixed	$dayofweek	Day(s) of week... 0 to 7 or short name. 0 and 7 = sunday
	 *
	 *	$access	public
	 */
	function setDateParams($min=NULL, $hour=NULL, $day=NULL, $month=NULL, $dayofweek=NULL){
		
		/* 分种 */
		if($min=="0")
			$this->minute=0;
		elseif($min)
			$this->minute=$min;
		else
			$this->minute="*";
		
		/* 小时 */
		if($hour=="0")
			$this->hour=0;
		elseif($hour)
			$this->hour=$hour;
		else
			$this->hour="*";
		
		$this->month=($month) ? $month : "*";
		$this->day=($day) ? $day : "*";
		$this->dayofweek=($dayofweek) ? $dayofweek : "*";
	}
	
	/**
	 *	设置或创建目录（0700）
	 *
	 *	@param	string	$directory	目录，完整路径
	 *
	 *	@access	public
	 *
	 *	@return	boolean
	 */
	public function setDirectory($directory){
		if( !$directory ) return false;		
		if( is_dir($directory) ) {
			/* 开始 */
			if( $dh = opendir($directory) ) {
				$this->directory=$directory;
				return true;
			} else {
				return false;
			}
			
		} else {
			/* 创建 */
			if(mkdir($directory, 0700)) {
				$this->directory=$directory;
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 *	创建任务设计文件
	 *
	 *	This will create a cron job file for you and set the filename
	 *	of this class to use it. Make sure you have already set the directory
	 *	path variable with the consructor. If the file exists and we can write
	 *	it then return true esle false. Also sets $handle with the resource handle
	 *	to the file
	 *
	 *	@author	Richard Sumilang	<richard@richard-sumilang.com>
	 *	@param	string	$filename	Name of file you want to create
	 *	@access	public
	 *	@return	boolean
	 */
	function createCronFile($filename=NULL){
		if(!$filename) return false;
		if(file_exists($this->directory.$filename)){
			if($handle=fopen($this->directory.$filename, 'a')){
				$this->handle=&$handle;
				$this->filename=$filename;
				return true;
			} else {
				return false;
			}
		}
		
		if(!$handle=fopen($this->directory.$filename, 'a')) {
			return false;
		} else {
			$this->handle=&$handle;
			$this->filename=$filename;
			return true;
		}
	}
	
	
	/**
	 *	设置要执行的命令
	 *
	 *	@param	string	$command	执行的命令
	 *
	 *	@access	public
	 */
	function setCommand($command){
		if( $command ) {
			$this->command=$command;
			return false;
		} else {
			return false;
		}
	}
	
	
	
	/**
	 *	保存文件到任务计划中
	 *
	 *	@access	public
	 *
	 *	@return	void
	 */
	function saveCronFile(){
		$command=$this->minute." ".$this->hour." ".$this->day." ".$this->month." ".$this->dayofweek." ".$this->command."\n";
		if( !fwrite($this->handle, $command) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *	保存到Linux Crontab 命令
	 *
	 *	@access	public
	 *
	 *	@return boolean	是否成功
	 */
	function addToCrontab() {		
		if(!$this->filename) exit('No name specified for cron file');
		if( exec($crontabPath."crontab ".$this->directory.$this->filename) ) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 *	关闭文件流
	 *
	 *	@access	public
	 *
	 *	@return void
	 */
	function destroyFilePoint(){
		fclose($this->handle);
		return true;
	}
 }
 