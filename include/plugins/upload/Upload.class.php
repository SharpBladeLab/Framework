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
 * 文件上传(Ajax文件异步上传)
 *
 * 需加载的js文件有：
 * 	  1、jquery-1.4.4.min jquery包
 *    2、ajaxupload.js    文件上传插件
 *    3、pics_upload.js   文件上传插件
 */
 class Upload extends Plugin {

	/* 允许的扩展名 */
	private $allowExts;

	/* 运行的文件类型 */
	private $allowTypes;

	/* 上传最大文件限制 */
	private $allowMaxSize;

	/* 说明：上传文件扩展名  */
	private $exts;

	/* 说明：上传文件类型  */
	private $type;

	/* 说明：上传文件大小 */
	private $size;

	/* 说明：返回的信息  */
	private $info;


	/**
	 * 构造函数
	 *
	 * @access public
	 */
	public function __construct($data=array()) {
		$this->allowExts = config("allowExts");
		$this->allowTypes = config("allowTypes");
		$this->allowMaxSize = config("maxSize");
		if(!is_array($this->allowExts)) $this->allowExts = explode(',', $this->allowExts);
		if(!is_array($this->allowTypes)) $this->allowTypes = explode(',', $this->allowTypes);

		$this->info=array(
			"showPath" => "",
			"savePath" => "",
			"success"  => false,
			"message"  => "",
			"fileInfo" => array()
		);
		$this->_init();
	}

	/**
	 * 设置允许扩展名
	 *
	 * @access public
	 */
	public function _init(){

		$z=explode('.',$_FILES['myfile']['name']);
		$this->exts = strtolower($z[count($z)-1]);
		$this->type = $_FILES['myfile']['type'];
		$this->size = $_FILES['myfile']['size'];

		/* 是否是允许的扩展名 */
		if(!in_array($this->exts, $this->allowExts)){
			$this->info['message']="不允许的扩展名";
			echo arrayToJson($this->info);
			exit(1);
		}


		/* 是否是允许的文件类型 */
		if(!in_array($this->type, $this->allowTypes)){
			$this->info['message']="不允许的文件类型";
			echo arrayToJson($this->info);
			exit(2);
		}


		/* 是否在允许的最大上传文件范围内 */
		if($this->size>$this->allowMaxSize){
			$this->info['message'] = "上传的文件大小超过了规定大小";
			exit(3);
		}

		/* 判断是否选择了上传文件 */
		if($this->size == 0){
			$this->info['message'] = "请选择上传的文件";
			exit(4);
		}
	}


	/**
	 * 执行上上传
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function doLoad($uppath) {

		$catalog = toDate(time(),"Ymd");
		$upload_dir = $uppath."/" . $catalog."/";
		$z=explode('.',$_FILES['myfile']['name']);
		$filename = md5(rand(0,10).time().get_client_ip()).".".$z[count($z)-1];

		/* 文件保存日期目录 */
		$file_path = $upload_dir  . $filename;
		/* 判断上传文件夹是否存在 */
		if(!is_dir($upload_dir)) {
			if(!mkdir($upload_dir)) {
				$this->info['message'] = "文件上传目录不存在并且无法创建文件上传目录";
				exit;
			}
			if(!chmod($upload_dir,0755)){
				$this->info['message'] = "文件上传目录的权限无法设定为可读可写";
				exit;
			}
		}

		if(!move_uploaded_file( $_FILES['myfile']['tmp_name'], $file_path)) {
			$this->info['message'] = "复制文件失败，请重新上传";
			exit;
		}

		switch($_FILES['myfile']['error']) {
			case 0:
				$this->info['savePath'] = $uppath."/".$catalog."/".$filename;
				$this->info['showPath'] = SITE_URL.$uppath."/".$catalog."/".$filename;
				$this->info['success'] = true;
				$this->info['message'] = "上传成功";
				$this->info['fileInfo'] = array(
						"tyle" => $this->type,
						"exts" => $this->exts,
						"size" => $this->size
						);
				break;
			case 1:
				$this->info['message'] = "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
				break;
			case 2:
				$this->info['message'] = "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
				break;
			case 3:
				$this->info['message'] = "文件只有部分被上传";
				break;
			case 4:
				$this->info['message'] = "没有文件被上传";
				break;
		}
		echo arrayToJson($this->info);
	}
}
