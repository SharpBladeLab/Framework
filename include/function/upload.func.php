<?php

$upload_dir = '../upload/';
$z=explode('.',$_FILES['myfile']['name']);
$filename=time().".".$z[count($z)-1];
$file_path = $upload_dir . $filename;
$MAX_SIZE = 20000000;

if(!is_dir($upload_dir)) {
    if(!mkdir($upload_dir))
       echo "文件上传目录不存在并且无法创建文件上传目录";

    if(!chmod($upload_dir,0755))
       echo "文件上传目录的权限无法设定为可读可写";
}


if($_FILES['myfile']['size']>$MAX_SIZE)
   echo "上传的文件大小超过了规定大小";


if($_FILES['myfile']['size'] == 0)
   echo "请选择上传的文件";
   
if(!move_uploaded_file( $_FILES['myfile']['tmp_name'], $file_path))
  echo "复制文件失败，请重新上传"; 

switch($_FILES['userfile']['error']) {
    case 0:
		echo $filename;
        break;
        
    case 1:
       echo "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
        break;
    case 2:
        echo "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
       break;
    case 3:
        echo "文件只有部分被上传";
       break;
    case 4:
       echo "没有文件被上传";
        break;
}

