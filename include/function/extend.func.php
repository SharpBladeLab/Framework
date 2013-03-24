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
 * @version     $Id: extend.func.php 367 2012-12-20 04:15:22Z zzy $
 * @link        http://www.tiwer.cn
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
  * 获取输入类型列表
  * 
  * @param string  $key     键
  * @param boolean $isGet   是否获取列表
  * @param boolean $isHtml  是否获取HTML
  */
  function getUserGroup($key, $isGet=false, $isHtml=false) {  	
  	$array = array('1' => '普通会员','2' => 'VIP会员');
	if( $isGet ) {
		if( $isHtml ) {
			$res = '<select name="UserGroup" id="UserGroup"><option value="">请选择</option>';
			foreach ($array as $k=>$v) {
				$selected = $k==$key ?  'selected="selected"' : '';				
				$res.='<option value="'.$k.'"  '.$selected.' >'.$v.'</option>';	
			}
			$res .= '</select>';
			return $res;
		}
		return $array;
	} else {
		if (isset($array[$key])) return $array[$key];
		return false;		
	}
 } 
 
 /**
  * 管理员等级权限判断
  */
 function isRank() {
 	
 	/* 更换为session的rank */
	$rank = intval($_SESSION["rank"]);
	
	/* 更换为session的userName */
	$admin = trim($_SESSION["admin"]);
	
	$sql[1] = "";
	$sql[2] = " and admin='".$admin."'";
	return $sql[$rank];
 }
 
 
  /**
  * 时间处理
  */
 function toDate($time,$format='Y年m月d日 H:i:s'){
	if( empty($time)) {
		return '';
	}
    $format = str_replace('#',':',$format);
	return date(auto_charset($format),$time);
 }
 
 /**
  * ajaxPage
  * 
  * @param unknown_type $js
  */
 function ajaxPage($js="") {
	
 	strlen($_REQUEST["ajaxId"])<3?$id="content":$id=$_REQUEST["ajaxId"];
	
 	$htm = "<script type=\"text/javascript\">";
	$htm .= "var ".$id."Url = function(url){\n";
	$htm .= "$('#".$id."').load(url);return false;}\n";
	$htm .= "$(\"#".$id."page a\").bind('click',
		function(){
			".$id."Url(this.href);
			return false;
		});";
	$htm .= $js."</script>";
	$htm .= "<input type='hidden' value='".$_SERVER['REQUEST_URI']."' id='".$id."PageUrl' />";
	
	return $htm;
 }
 

 /**
  * 取得字段值
  * 
  * @param string $query  查询
  * @param string $filed  字段
  * @param string $table  数据表
  */
 function getFiled( $query = "", $filed="name", $table="Sort", $app="cms"){
	if(!empty($query)) {
		$Form = Helper::createModel($table, $app);		
		$test = $Form->where($query)->field($filed)->find();
		return $test[$filed];
	} else {
		return "";
	}
 }


 /**
  * 选中
  * 
  * @param unknown_type $iCid
  * @param unknown_type $arrId
  * @param unknown_type $ed
  * @param unknown_type $html
  */
 function isCheck($iCid, $arrId, $ed="checked",$html="") {	
 	if($iCid!=$arrId)$ed = $html;
	return $ed;
 }


/**
 * 上传表单
 * 
 * @param unknown_type $val
 * @param unknown_type $id
 * @param unknown_type $str
 * @param unknown_type $size
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
 * 传入参数取得SQL执行后的数组
 * 
 * @param unknown_type $table
 * @param unknown_type $field
 * @param unknown_type $query
 * @param unknown_type $limit
 * @param unknown_type $order
 * @param unknown_type $state
 */
function cmsList($table,$field='*',$query='',$limit='1',$order = ' state desc,rec desc,id desc',$state = ' and state in(0,2)'){
	
	if(empty($table)) {
		return false;
	} else {
		$table = "cms_".$table;
	}
	
	$sql = "select ".$field." from ".__TABLE__.$table." where 1=1".$query.$state." order by ".$order." limit ".$limit;
	$array = Helper::createModel('', '', true)->query($sql);
	return $array;
}
/********* 常用函数调用结束  *********/





/********* 分类调用函数   ***********/
/**
 * 取得分类数组
 * 
 * @param string $symbol
 * @param integer $sortid
 */
function getSortArr($symbol='',$sortid=0) {
	
	$sort = Helper::createModel("Sort", 'cms');
	$query = "1=1";	
	if(strlen($symbol)>1) {
		$query .= " and symbol LIKE '".$symbol."%'";
	} 
	if($sortid>0){
		$query .= " and id='".$sortid."' or parent='".$sortid."'";
	}
	
	
	$list=$sort->where($query)->field('id,name,parent,py,symbol')->order('sort asc,id asc')->select();
	if($list) {			
		
		include_once PLUGIN_PATH.SEP.'tree'.SEP."Tree.class.php"; 		
		$Tree = new Tree();	
			
		foreach($list as $key=>$row) {
			if($row['symbol']==$symbol) $row['parent']=0;
			$Tree->setNode($row["id"],$row["parent"],$row["name"],$row["py"]);
		}
		
		$arr = array();		
		$cat = $Tree->getChilds();		
		foreach($cat as $key=>$row){
			$id = $row;
			$arr[$key]["id"] = $id;
			$arr[$key]["name"] = $Tree->getValue($id);
			$arr[$key]["pr"] = $Tree->getLayer($id);
			$arr[$key]["py"] = substr($Tree->getArr($id),0,1);//取得拼音第一个字母
		}
	}
	
	return $arr;
}

//取得分类下拉菜单，多数用在搜索查询
function getSelectSel($symbol="",$name = "sortid",$str="",$text){
	$html = "<select name=\"".$name."\" id=\"".$name."id\" ".$str.">";

	//取得分类数组
	$arr = getSortArr($symbol);
	foreach($arr as $key=>$row){
		$html .= "<option value=\"".$row['name']."\" ".isCheck($text,$row["name"],'selected')."> ".$row['name']."</option>";
	}
	$html .= "</select>";
	return $html;
}

/**
 * 取得分类下拉菜单，多数用在搜索查询
 * 
 * @param string   $symbol   分类
 * @param integer  $id
 * @param string   $name
 */
function getSortSelectSel($symbol="",$id = 0,  $name="sortid"){
	
	$html = "<select name=\"".$name."\" id=\"".$name."id\">";
	$html .= "<option value=\"-1\">". Helper::createLanguage("StateIndex")."</option>";
	
	/* 取得分类数组 */
	$arr = getSortArr($symbol);
	
	foreach($arr as $key=>$row){
		$html .= "<option value=\"".$row['id']."\" ".isCheck($id,$row["id"],'selected').">".$row['py']." ".$row['pr'].$row['name']."</option>";
	}
	$html .= "</select>";
	return $html;
}

/**
 * 取得分类，能在工作区直接添加分类
 * 
 * @param string $symbol
 * @param string $id
 * @param string $name
 * @param string $no
 */
function getSortSelect($symbol="",$id = 0,$name = "sortid",$no = 1){
	
//	$arr = explode("_", $symbol);
//	if(is_array($arr)&&count($arr)>0) {		
//		$temp="";		
//		for($i=0; $i<count($arr)-1; $i++) {
//			$temp.=$arr[$i];
//			if(count($arr)-2!=$i)$temp.="_";
//		}
//		$symbol=$temp;
//		//echo $temp;exit;
//		//$symbol = $arr[0];
//	}

	$html = "<div id='".$name."text'><select name=\"".$name."\" id=\"".$name."id\">";	
	$arr = getSortArr($symbol);
	foreach($arr as $key=>$row) {
		$html .= "<option value=\"".$row['id']."\" ".isCheck($id,$row["id"],'selected').">".$row['py']." ".$row['pr'].$row['name']."</option>";
	}
	
	/* 缓存分类选项,主要为执行 reset() 选中项不在丢失 */
	$addUrl = Helper::createLink('cms/Sort/sel', "symbol=".$symbol."&name=".$name."&no=".$no);	
	$html .= "</select><script type=\"text/javascript\">
		function ".$name."sel() {
			var ".$name."id = \$('#".$name."id').val();
			\$('#".$name."text').load('".$addUrl."',{id:".$name."id});
		}
	</script>";

	
	/* 默认允许在添加内容环节中添加分类 */
	if($no) {
	$html .= "<script type=\"text/javascript\">				
				//增加分类添加表单
				function ".$name."adda(){\n
					\$(\"#".$name."addhtml\").html(\"<input type='text' name='".$name."val' id='".$name."val' size='8' /> <input type='button' class='button' value='". Helper::createLanguage("sortAdd")."' onclick='".$name."insert();' />\");
				}
				//执行添加新分类选项
				function ".$name."insert(){
					var ts = \$('#".$name."val').val();
					symbol = '".$symbol."';
					if(ts.length>1&&symbol.length>1){
						url = '". Helper::createLink('cms/Sort/insert') ."';
						postData = 'parent='+$('#".$name."id').val()+'&name='+$('#".$name."val').val()+'&symbol=".$symbol."';
						\$.post(url,postData,function(json){
							if(json.status==1){
								var ".$name."id = \$('#".$name."id').val();
								\$('#".$name."text').load('". Helper::createLink('cms/Sort/sel', "".$symbol."&name=".$name."&no=".$no) ."',{id:".$name."id});
							}
						},'json');
					}else{
						$(\"#".$name."addhtml\").html('<input type=\"button\" class=\"button\" value=\"". Helper::createLanguage("sortAdd")."\" onclick=\"".$name."adda()\" />');
					}
				}
			</script><span id=\"".$name."addhtml\"><input type=\"button\" class=\"button\" value=\"". Helper::createLanguage("sortAdd")."\" onclick=\"".$name."adda()\" /></span>";
	}
	$html .= " <input type=\"button\" class=\"button\" value=\"". Helper::createLanguage("sortSel")."\" onclick=\"".$name."sel()\" /></div>";
	return $html;
}



//分类二级联动选项调用
function getSortLink($parent=450,$zoneId=0,$tieup = "zone",$frm = "form1"){
	//取得地区大类
	$Sort = Helper::createModel("Sort", 'cms');
	$zoneArr = $Sort->where("parent='".$parent."'")->field("id,name")->order('sort asc')->select();
	
	//取得默认的地区父ID
	if($zoneId<1){
		$prId = $zoneArr[0]['id'];
	}else{
		$prArr = $Sort->where("id='".$zoneId."'")->field("parent")->order('sort asc')->select();
		$prId = $prArr[0]['parent'];
	}
	
	//父地区列表
	$html = "";
	$html .= "<select name='".$tieup."_str' onChange='".$tieup."rec(this)'>";
	foreach($zoneArr as $key=>$row){
		$html .="<option value='".$row["id"]."' ".isCheck($row["id"],$prId,"selected").">".$row["name"]."</option>";
	}
	$html .= "</select>&nbsp;";
	
	
	//默认下拉地区	
	$html .="<select name='".$tieup."'>";
		//下拉地区列表
		$zoneSmallArr = $Sort->where("parent='".$prId."'")->field("id,name")->order('sort asc')->select();
		foreach($zoneSmallArr as $key=>$row){
			$ed = "";
			if($row["id"]==$zoneId){
				$ed = "selected";
			}else{
				$ed = "";
			}			
			$html .="<option value='".$row["id"]."' ".$ed.">".$row["name"]."</option>";
		}
	$html .="</select>";
		
	//联动菜单javascript代码
	$html .="<script language='javascript'>";
		//联动函数
		$html .="function ".$tieup."rec(obj){";
		
			//获取一级菜单长度
			$html .="x = obj.options.selectedIndex;";
			$html .="var select".$tieup."_len = obj.options.length;";
			$html .="var select".$tieup." = new Array(select".$tieup."_len);";
			//把一级菜单都设为数组
				
			$html .="for (i=0; i<select".$tieup."_len; i++){";
				$html .="select".$tieup."[i] = new Array();";
			$html .="}";
			//定义基本选项
			
			//JS 联动代码	
			//$html .="select2[1][0] = new Option('PHP', ' ');";
			foreach($zoneArr as $i=>$rs){
				$sortsmallArr = $Sort->where("parent='".$rs['id']."'")->field("id,name")->order('sort asc')->select();
				foreach($sortsmallArr as $t=>$row){
					$html .="select".$tieup."[".$i."][".$t."] = new Option('".$row["name"]."', '".$row["id"]."');";
				}
			}
				
			$html .="var temp".$tieup." = obj.form.".$tieup.";";
			$html .="while (temp".$tieup.".options.length>0){temp".$tieup.".remove(0);}";
			$html .="for (i=0;i<select".$tieup."[x].length;i++){";
				$html .="temp".$tieup.".options[i]=new Option(select".$tieup."[x][i].text,select".$tieup."[x][i].value);}";
				$html .="temp".$tieup.".options[0].selected=true;";
			$html .="}";

	$html .="</script>";
	
	return $html;
}
/* 分类调用函数   结束                                    */


/* 文本字段函数                                    */
//文本字段类型
function getTextFieldType($str,$name='fieldtype'){
	$htm = "";
	$checkbox = "selected";
	
	$htm = '<select id="'.$name.'" name="'.$name.'">
		<option value="input" '.isCheck("input",$str,$checkbox).'>文本框</option>
		<option value="textarea" '.isCheck("textarea",$str,$checkbox).'>文本区域</option>
		<option value="select" '.isCheck("select",$str,$checkbox).'>列表框</option>
		<option value="checkbox" '.isCheck("checkbox",$str,$checkbox).'>复选框</option>
	</select>';
	return $htm;
}

//返回分类执行数组，方便添加和修改中调用文本字段
function getTextField($symbol,$json,$ape = "textfield",$isHtml=true){
	$Field = Helper::createModel("Field", 'cms');
	$arr = array();

	$fieldArr = $Field->where("symbol='".$symbol."'")->field("id,name,value,fieldtype,width")->order('sort asc,id desc')->select();
	foreach($fieldArr as $i=>$row){
		$arr[$i]['name'] = $row['name'];
		$arr[$i]['html'] = getFieldHtml($row,$json,$ape,$isHtml);
	}
	return $arr;
}

//取得文本字段HTML代码
function getFieldHtml($ar,$json,$ape,$ishtml = true){
	$html = "";
	$value = "";
	$json = unserialize($json);
	$val = "";
	
	//取得内容
	if(strlen($json[$ar['id']])<1){
		$value = $ar['value'];
	}else{
		$value = $json[$ar['id']];
	}
				
	//判断是否需要生成HTML表单
	if($ishtml){
		$width = $ar["width"];
		if($width<4){
			$width = 30;
		}
			
		switch($ar['fieldtype']){
			case "input":
				$html = "<input type=\"text\" name=\"".$ape."[".$ar['id']."]\" value=\"".$value."\" size=\"".$width."\" class=\"fieldInput\" />";
				break;
			case "textarea":
				$html = "<textarea name=\"".$ape."[".$ar['id']."]\"  rows=\"4\" cols=\"".$width."\" class=\"fieldTextarea\">".$value."</textarea>";
				break;
			case "select":
				$op = explode("\n",$ar['value']);
				$html = "<select name=\"".$ape."[".$ar['id']."]\">";
				for($i=0;$i<count($op);$i++){
					if($op[$i]==$json[$ar['id']]){
						$en = "selected";
					}else{
						$en = "";
					}
					$html .= "<option value=\"".$op[$i]."\" ".$en.">".$op[$i]."</option>";
				}
				$html .= "</select>";
				break;
			case "checkbox":
				$op = explode("\n",$ar['value']);
				for($i=0;$i<count($op);$i++){
					if($op[$i] == $json[$ar['id']]){
						$en = "checked";
					}else{
						$en = "";
					}
					$html .= "<input type=\"checkbox\" value=\"".$op[$i]."\" name=\"".$ape."[".$ar['id']."][".$i."]\" ".$en." class=\"fieldCheckbox\" />".$op[$i]."&nbsp;&nbsp;";
				}
				break;
		}
	}else{//如果不需要生成HTML表单直接返回内容
		$html = $json[$ar['id']];
	}	
	return $html;
}
/* 文本字段函数   结束                                 */


/**
 * 取得网站基本状态，一般添加内容时使用
 * 
 * @param unknown_type $state
 * @param unknown_type $name
 */
function getState($state = 1,$name='state'){
	$stateText =  Helper::createLanguage("stateText");

	$html = "";
	$stateText = explode(',',$stateText);
			
	$html = "<input type='radio' value='0' name='".$name."' ".isCheck(0,$state)." />".$stateText[0]."&nbsp;";
	$html .= "<input type='radio' value='1' name='".$name."' ".isCheck(1,$state)." />".$stateText[1]."&nbsp;";
	$html .= "<input type='radio' value='2' name='".$name."' ".isCheck(2,$state)." />".$stateText[2]."&nbsp;";
	$html .= "<input type='radio' value='3' name='".$name." '".isCheck(3,$state)." />".$stateText[3];
	return $html;
}

/**
 * 取得网站基本状态下拉菜单，搜索查询使用
 * 
 * @param unknown_type $state
 * @param unknown_type $name
 */
function getStateSel($state = -1,$name='state'){
	$stateText =  Helper::createLanguage("stateText");
	
	$html = "<select name=\"".$name."\">";
	$html .= "<option value=\"-1\">". Helper::createLanguage("StateIndex")."</option>";
	$stateText = explode(',',$stateText);
	for($i=0;$i<count($stateText);$i++){
		$html .= "<option value=\"".$i."\" ".isCheck($i,$state).">".$stateText[$i]."</option>";
	}
	return $html."</select>";
}


/**
 * 取得网站基本状态文字
 * 
 * @param unknown_type $state
 */
function getStateText($state = 1){
	$stateText =  Helper::createLanguage("stateText");
	$stateText = explode(',',$stateText);
	return $stateText[$state];
}

/**
 * 记录状态控制，列表状态使用
 * 
 * @param unknown_type $state
 * @param unknown_type $table
 * @param unknown_type $froId
 * @param unknown_type $ajaxId
 * @param unknown_type $estate
 */
function setState($state,$table,$froId,$ajaxId='#content',$estate='setState'){
	$te = explode(',', Helper::createLanguage("stateTextSmall"));
	
	$str = "";
	$str .= isCheck($state,0,"<font color=\"#FF0000\">".$te[0]."</font>&nbsp;","<a style=\"cursor:pointer\" onclick=\"".$estate."({state:'0',model:'".$table."',id:'".$froId."',ajaxId:'".$ajaxId."'})\">".$te[0]."</a>&nbsp;");
	$str .= isCheck($state,1,"<font color=\"#FF0000\">".$te[1]."</font>&nbsp;","<a style=\"cursor:pointer\" onclick=\"".$estate."({state:'1',model:'".$table."',id:'".$froId."',ajaxId:'".$ajaxId."'})\">".$te[1]."</a>&nbsp;");
	$str .= isCheck($state,2,"<font color=\"#FF0000\">".$te[2]."</font>&nbsp;","<a style=\"cursor:pointer\" onclick=\"".$estate."({state:'2',model:'".$table."',id:'".$froId."',ajaxId:'".$ajaxId."'})\">".$te[2]."</a>&nbsp;");
	$str .= isCheck($state,3,"<font color=\"#FF0000\">".$te[3]."</font>&nbsp;","<a style=\"cursor:pointer\" onclick=\"".$estate."({state:'3',model:'".$table."',id:'".$froId."',ajaxId:'".$ajaxId."'})\">".$te[3]."</a>&nbsp;");
	return $str;
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
function nation(){
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
 * return array
 */ 
 function getIndustryPathName($industryid)
 {
	 $busines= Helper::createBusiness('Industry');
	 $strs=$busines->getIndustryName($industryid);
	 return $strs;
 }