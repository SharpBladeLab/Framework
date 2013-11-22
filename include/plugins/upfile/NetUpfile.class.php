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
 * @version     $Id: NetUpfile.class.php 623 2013-11-07 06:03:30Z wgw $
 *
 * 文件上传
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
