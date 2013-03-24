<?php if(!defined('IN_SYS')) exit();
/**
 * 文件上传
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * Site: http://www.tiwer.cn
 *
 * $Id: NetUpfile.class.php 5 2012-11-23 02:56:13Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class NetUpfile extends Plugin {
 	
 	/**
 	 * 插件版本
 	 */
 	protected  $version = '0.1';
 	
 	
	/**
	 * 获取url地址
	 * 
	 * @return mixed
	 */
	function getURL() {
		return str_replace("index.php","",$_SERVER["SCRIPT_NAME"]);
	}
	
	/**
	 * 传入字符取得远程文件地址
	 * 
	 * @param string $str
	 */
	function getimages($str){
		$match_str = "/((http:\/\/)+([^ \r\n\(\)\^\$!`\"'\|\[\]\{\}<>]*)((.gif)|(.jpg)|(.bmp)|(.png)|(.GIF)|(.JPG)|(.PNG)|(.BMP)))/";  
		preg_match_all ($match_str,$str,$out,PREG_PATTERN_ORDER);
		return $out;
	}
	
	/**
	 * 取文件的扩展名 
	 * 
	 * @param string $filename
	 */ 
	function getextension($filename){
		return substr(strrchr($filename,"."),1);
	}
	
	/**
	 * 重命名文件名称
	 * 
	 * @param string $file
	 */  
	function getnewname($file){
		$ext = self::getextension($file);
		$newname = md5(mac_rand(4).time().get_client_ip()).".".$ext;
		return $newname;
	}
	
	//目标地址  
	//$url = "http://chishuiren.com";  
	  
	 //保存到本地服务器  
	//$img = getimages(join(file($url)));取得远程文件名  
	
	/**
	 * 传入文章内容保存远程文件和替换的图片连接
	 * 
	 * @param string $str
	 * @param string $path
	 * 
	 * @return Ambigous <unknown, mixed>
	 */
	function upLoadFile($str,$path="."){
		
		$htm = $str;
		
		/* 传入内容取得图片连接地址数组 */
		$img = self::getimages($htm); 
		
		set_time_limit(1200);
		
		$sServerDir = $path;
		if(!file_exists($sServerDir)){mkdir($sServerDir);}//如果用户指定的文件不存在则创建一个
		
		$sServerDir .= date("Ym")."/";
		if(!file_exists($sServerDir)){mkdir($sServerDir);}//如果用户指定的文件不存在则创建一个	
		
		/* 保存文件 */
		for($i=0;$i<count($img[0]);$i++){
			$fileUrl = $img[0][$i];	
			
			/* 取得远程图片 */
			$data = join(file($fileUrl));
			
			/* 取得远程图片保存到本地名称 */
			$newname = self::getnewname($fileUrl);
			
			/* 把图片保存到本地硬盘 */
			$temp_data = fopen($sServerDir.$newname,"w");
			
			fwrite($temp_data,$data);
			
			fclose($temp_data);

			flush();
			$htm = str_replace($fileUrl,self::getURL().$sServerDir.$newname,$htm);
			$waterImage = $path.$logo;
		}
		return $htm;
	}
	
 }
