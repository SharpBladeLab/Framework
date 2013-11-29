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
 * @version     $Id: extend.func.php 518 2013-07-30 09:04:47Z wgw $
 *
 * 系统扩展函数库
 */



/**
 * 根据access.inc.php检查访问权限
 *
 * @return mixed
 */
function canAccess() {
	$acl = config('access');
	return $acl[APP_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME] === true || $acl[APP_NAME.'/'.CONTROLLER_NAME.'/*'] === true || $acl[APP_NAME.'/*/*'] === true;
}



/**
 * 时间处理
 */
function toDate($time,$format='Y年m月d日 H:i:s'){
	if( empty($time)) return '';
	$format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
}


/**
 * 取得字段值
 *
 * @param string $query  查询
 * @param string $filed  字段
 * @param string $table  数据表
 */
function getFiled( $query = "", $filed="name", $table="Sort", $app="cms") {
	if(!empty($query)) {
		$Form = Helper::createModel($table, $app);
		$test = $Form->where($query)->field($filed)->find();
		return empty($test[$filed]) ? '未知' : $test[$filed];
	} else {
		return "未知";
	}
}




/**
 * 选中
 *
 * @param boolean $iCid
 * @param integer $arrId
 * @param strng   $ed
 * @param string  $html
 */
function isCheck($iCid, $arrId, $ed="checked",$html="") {
	if($iCid!=$arrId)$ed = $html;
	return $ed;
}


/**
 * 上传表单
 *
 * @param strng   $val
 * @param integer $id
 * @param strng   $str
 * @param integer $size
 */
function upLoadHtml($val = "",$id="pic",$str="w=120&h=90",$size="20") {
	$urlView = Helper::createLink('manage/File/file', array('run'=>1));

	/* 上传地址 */
	$urlUp = Helper::createLink('manage/File/upfile', $str);

	return "<input type=\"text\" id=\"".$id."\" name=\"".$id."\" value=\"".$val."\" title=\"click view\" onclick=\"openImg(this);\" size=\"$size\" />
	<input type=\"button\" class=\"button\" value=\"". Helper::createLanguage("uploadText")."\" onclick=\"upfile({id:'#".$id."',url:'".$urlUp."'});\" /> 
	<input type=\"button\" class=\"button\" value=\"". Helper::createLanguage("uploadViewText")."\" onclick=\"upfile({id:'#".$id."',url:'".$urlView."'});\" />";
}


/**
 * checkbox 选中
 *
 * @param integer $iCid
 * @param integer $arrId
 */
function isSelect($iCid,$arrId){
	$tok = strstr($arrId,$iCid);
	if($tok){
		$ed = "selected";
	}else{
		$ed = "";
	}
	return $ed;
}


/**
 * 文件大小
 *
 * @param integer  $fileSize
 */
function formatsize($fileSize) {

	$size = sprintf("%u", $fileSize);
	if($size == 0) {
		return("0 Bytes");
	}
	$sizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizename[$i];
}


/**
 * 验证手机号是否合法
 *
 * @param string $str
 *
 * @retrun boolean
 */
function checkMobile($str) {
	$pattern = "/^13[0-9]{9}$|15[0-9]{9}$|18[0-9]{9}$/";
	if ( preg_match($pattern, $str) && strlen($str) == "11" ) {
		return true;
	} else {
		return false;
	}
}



/**
 * 获取民族
 * return array
 */
function nation() {
	$arr=array();

	$arr[0]=array("name"=>"汉族","code"=>"01");
	$arr[1]=array("name"=>"蒙古族","code"=>"02");
	$arr[2]=array("name"=>"回族","code"=>"03");
	$arr[3]=array("name"=>"藏族","code"=>"04");
	$arr[4]=array("name"=>"维吾尔族","code"=>"05");
	$arr[5]=array("name"=>"苗族","code"=>"06");
	$arr[6]=array("name"=>"彝族","code"=>"07");
	$arr[7]=array("name"=>"壮族","code"=>"08");
	$arr[8]=array("name"=>"布依族","code"=>"09");
	$arr[9]=array("name"=>"朝鲜族","code"=>"10");
	$arr[10]=array("name"=>"满族","code"=>"11");
	$arr[11]=array("name"=>"侗族","code"=>"12");
	$arr[12]=array("name"=>"瑶族","code"=>"13");
	$arr[13]=array("name"=>"白族","code"=>"14");
	$arr[14]=array("name"=>"土家族","code"=>"15");
	$arr[15]=array("name"=>"哈尼族","code"=>"16");
	$arr[16]=array("name"=>"哈萨克族","code"=>"17");
	$arr[17]=array("name"=>"傣族","code"=>"18");
	$arr[18]=array("name"=>"黎族","code"=>"19");
	$arr[19]=array("name"=>"傈僳族","code"=>"20");
	$arr[20]=array("name"=>"佤族","code"=>"21");
	$arr[21]=array("name"=>"畲族","code"=>"22");
	$arr[22]=array("name"=>"高山族","code"=>"23");
	$arr[23]=array("name"=>"拉祜族","code"=>"24");
	$arr[24]=array("name"=>"水族","code"=>"25");
	$arr[25]=array("name"=>"东乡族","code"=>"26");
	$arr[26]=array("name"=>"纳西族","code"=>"27");
	$arr[27]=array("name"=>"景颇族","code"=>"28");
	$arr[28]=array("name"=>"柯尔克孜族","code"=>"29");
	$arr[29]=array("name"=>"土族","code"=>"30");
	$arr[30]=array("name"=>"达斡尔族","code"=>"31");
	$arr[31]=array("name"=>"仫佬族","code"=>"32");
	$arr[32]=array("name"=>"羌族","code"=>"33");
	$arr[33]=array("name"=>"布朗族","code"=>"34");
	$arr[34]=array("name"=>"撒拉族","code"=>"35");
	$arr[35]=array("name"=>"毛南族","code"=>"36");
	$arr[36]=array("name"=>"仡佬族","code"=>"37");
	$arr[37]=array("name"=>"锡伯族","code"=>"38");
	$arr[38]=array("name"=>"阿昌族","code"=>"39");
	$arr[39]=array("name"=>"普米族","code"=>"40");
	$arr[40]=array("name"=>"塔吉克族","code"=>"41");
	$arr[41]=array("name"=>"怒族","code"=>"42");
	$arr[42]=array("name"=>"乌孜别克族","code"=>"43");
	$arr[43]=array("name"=>"俄罗斯族","code"=>"44");
	$arr[44]=array("name"=>"鄂温克族","code"=>"45");
	$arr[45]=array("name"=>"德昂族","code"=>"46");
	$arr[46]=array("name"=>"保安族","code"=>"47");
	$arr[47]=array("name"=>"裕固族","code"=>"48");
	$arr[48]=array("name"=>"京族","code"=>"49");
	$arr[49]=array("name"=>"塔塔尔族","code"=>"50");
	$arr[50]=array("name"=>"独龙族","code"=>"51");
	$arr[51]=array("name"=>"鄂伦春族","code"=>"52");
	$arr[52]=array("name"=>"赫哲族","code"=>"53");
	$arr[53]=array("name"=>"门巴族","code"=>"54");
	$arr[54]=array("name"=>"珞巴族","code"=>"55");
	$arr[55]=array("name"=>"基诺族","code"=>"56");
	$arr[56]=array("name"=>"其  他 ","code"=>"97");
	$arr[57]=array("name"=>"外国血统中国籍人士 ","code"=>"98");
	return $arr;
}

/**
 * 根据行业ID获取行业路径
 *
 * return array
 */
function getIndustryPathName($industryid) {
	$busines= Helper::createBusiness('Industry');
	$strs=$busines->getIndustryName($industryid);
	return $strs;
}