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
 * @version     $Id: Form.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * 模型表单生成
 */
 class Form extends Plugin 
 {
 	/* 相关配置*/
	public $data = array();
	
	/* 是否管理员 */
	public $isadmin=1;	
	
	/* 生成缩影图 */
	public $doThumb=1;	
	
	/* 附件 */
	public $doAttach=1;	
	
	/* 语言 */
	public $lang;
	
	/* 错误信息   */
	protected $error;
	
	/* 插件版本    */
 	protected  $version = '0.1';
 	
 	/**
     * 构造函数
     *
     * @access public
     */
    public function __construct($data=array()) {
         $this->data = $data;
         
    }
    
    /**
     * 表单数据
     * 
     * @param array  $data     表单数据
     * @param array  $fields   字段
     * 
     * @return mixed 
     */
    public function dataVerify($data, $fields) {
    	if ( is_array($data) && is_array($fields) ) { 		
    		
    		foreach ($data as $key=>$value) { 
    			foreach ($fields as $k=>$v) {
    				
    				/*  数据源过滤 */
    				if ( $key == $v['FieldName'] ) {    					
    					
	    				/*  时间类型 */
	    				if ( $v['FieldInput'] == 'datetime' ) {		
	    					$data[$key] =  isset($data[$key]) ? strtotime($value) : time(); 
	    				}  
	    				
	    				/* 数组序列化   */
	    				if ( $v['FieldInput']=='files'|| $v['FieldInput']=='images' )  {    					
	    					if (is_array($value)) $data[$key] = serialize($value);
	    					if ( empty($data[$key]) )  $data[$key] = "";
	    				}	    				
	    				
	    				/* 数组转换成字符串   */
	    				if ( $v['FieldInput'] == 'checkbox' || $v['FieldInput']=='groupid' || $v['FieldInput']=='radio' ) {    					
	    					if (is_array($value)) $data[$key] = implode(',', $value); 
	    					if ( empty($data[$key]) )  $data[$key] = 0;
	    				}
    				
	    				/* 关联内容   */
	    				if ($v['FieldInput']=='relation' && !empty($v['FieldDefaultValue'])) {
	    					$data[$key]= $v['FieldDefaultValue'];
	    				} 
    				}  
    				
    				/* 表单输入限制 */
    				if ( $key == $v['FieldName'] && isset($v['FieldInputFilter']) && !empty($v['FieldInputFilter']) ) {
    					if( !Helper::createModel('', '', true)->regex($data[$key], $v['FieldInputFilter']) ) {    						
    						$this->error =  Helper::createModel('Fields','content')->getFieldInputFilterError($v['FieldInputFilter'],$v['FieldTitle']);
    						return false;
    					} 
    				}
    			} // end $fields
    		} // end $data
    		    		 
    		return $data;
    		    		
    	} else {
    		$this->error = '表单数据有误或字段为空！';
    		return false;
    	}
    }        
    
    
    
 	/**
	 * 获取数据源的值
	 * 
	 * @param mixed  $id       对应数据ID
	 * @param string $source   数据源配置<xml标签>
	 * 
	 * @return mixed  返回源数据值
	 */
	public function getFieldSourceValue($id, $source) {
		if( empty($id) || empty($source) ) return 'null';	
		
		$data = $this->_getSourceFilter($source);
		foreach($data as $key=>$value) {			
			if( $value['key'] == $id) return $value['value'];
		}
		return 'null';
	}
	
	
    /**
     * 获取错误 信息
     * 
     * @return string
     */
    public function error() {
    	if ( isset($this->error) ) {
    		return $this->error;
    	} else {
    		return "";
    	}
    }
    
        
    /**
     * 标题
     */
	public function title($info,$value) {
		$id = $field = $info['FieldName'];	
        if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        } 
		$parseStr = '<input type="text" class="input-text"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'" />  '. $this->_getFontColor('',$id).'  '.$this->_getColor('',$id).'';
		return $parseStr;
	}
	

	/**
     * 单行文本
     */
	public function text($info,$value) {
		$id = $field = $info['FieldName'];		
        if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }
		$parseStr   = '<input type="text" class="input-text"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'"  /> ';
		return $parseStr;
	}
	
	/**
	 * 密码 
	 */
 	public function password($info,$value) {		
		$id = $field = $info['FieldName'];		
        if(ACTION_NAME == 'add') {
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }
		$parseStr = '<input type="password" class="input-text"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'"  /> ';
		return $parseStr;
	}	
	
	/**
	 * 验证码
	 */
	public function verify($info,$value){
		$id = $field = $info['FieldName'];	
		$parseStr = '<input class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="" size="'.$info['setup']['size'].'"   /><img src="'. Helper::createLink('content/content/createVerify').'" class="checkcode" align="absmiddle"  title="点击刷新验证码" id="verifyImage"/>';
		return $parseStr;
	}
	
	/**
	 * 数字
	 */
	public function number($info,$value){
		$id = $field = $info['FieldName'];
        if(ACTION_NAME=='add') {
			$value = $info['FieldDefaultValue'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }       
		$parseStr = '<input class="input-text   class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="'.$value.'"   /> ';
		return $parseStr;
	}
	
 	/**
	 * 小数
	 */
	public function float($info,$value){
		$id = $field = $info['FieldName'];
        if(ACTION_NAME=='add') {
			$value = $info['FieldDefaultValue'];
        }else{
			$value = $value ? $value : $this->data[$field];
        }       
		$parseStr = '<input class="input-text   class="input-text '.$info['class'].'" name="'.$field.'"  id="'.$id.'" value="'.$value.'"   /> ';
		return $parseStr;
	}
	
	/**
	 * 多行文本
	 */
	public function textarea($info,$value) {
		$id = $field = $info['FieldName'];	
		if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }
		$parseStr = '<textarea  name="'.$field.'"  style="width:100%; height:150px"  id="'.$id.'"  />'.stripcslashes($value).'</textarea>';
		return $parseStr;
	}
	
	
	/**
	 * 下拉选项框
	 */
	public function select($info,$value) {	
		
		$id = $field = $info['FieldName'];
		if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
			if(!empty($this->data) && empty($value)){
				$value = $value ? $value : $this->data[$field];
			}
        } else {
			$value = $value ? $value : $this->data[$field];
        }
        
        /* 下拉列表数据 */
		$data = $this->_getSourceFilter($info['FieldDataSource'], true);
		$parseStr = '<select id="'.$id.'" name="'.$field.'" style="width:350px;"><option value="0">请选择</option>';		
       
		if(is_array($data)) {
			foreach($data as $key=>$val) {
				$selected='';
				$name = '';
				if( (trim($value)==trim($val['key']))  ) $selected = ' selected="selected"';				
				/* 无限级分能 */
				if( isset($val['arr'] ) ) $name = $val['pr'].$val['value']; else $name = $val['value'];				
				if(!empty($value)) {					    						
				    $parseStr   .= '<option '.$selected.' value="'.$val['key'].'">'.$name.'</option>';
				}else{
					$parseStr   .= '<option value="'.$val['key'].'">'.$name.'</option>';
				}
			}
		}		
        $parseStr .= '</select>';
        return $parseStr;
	}
	
	
	/**
	 * 多选按钮
	 */
	public function checkbox($info,$value) {		
		$id = $field = $info['FieldName'];		
		if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }     
        if($value != '') $value = strpos($value, ',') ? explode(',', $value) : $value;
        
        /* 下拉列表数据 */
		$data = $this->_getSourceFilter($info['FieldDataSource']);	
	    
		if($value != '') $value = (strpos($value, ',') && !is_array($value)) ? explode(',', $value) :  $value ;
		$value = is_array($value) ? $value : array($value);        
        $i = 1;
        
        $parseStr = '';
         if(is_array($data)) {
			foreach($data as $key=>$val) {
				$checked = ($value==$val['key'] || in_array($val['key'], $value)) ? 'checked' : '';
				$parseStr .= '<input type="checkbox" class="input_checkbox"  name="'.$field.'[]"  id="'.$id.'_'.$i.'"  '.$checked.'  value="'.htmlspecialchars($val['key']).'"  > '.htmlspecialchars($val['value']);			
				$i++;
			}
         } else {
         	$parseStr = '没有可选数据';
         }          
		return $parseStr;
	}
	
	
	
	/**
	 * 单选按钮
	 */
	public function radio($info,$value) {		
		 $id = $field = $info['FieldName'];		
		 if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
         } else {
			$value = $value ? $value : $this->data[$field];
         }
                    
         /* 下拉列表数据 */
		 $data = $this->_getSourceFilter($info['FieldDataSource']);	
         $i = 1;
        
         $parseStr = '';
         if(is_array($data)) {
			foreach($data as $key=>$val) {
				$checked = (trim($value)==trim($val['key'])) ? 'checked' : '';
				$parseStr .= '<input type="radio" class="input_radio"  name="'.$field.'[]"  id="'.$id.'_'.$i.'"  '.$checked.'  value="'.htmlspecialchars($val['key']).'" > '.htmlspecialchars($val['value']);			
				$i++;
			}
         } else {
         	$parseStr = '没有可选数据';
         }          
		return $parseStr;
	}

	/**
	 * CKE辑器
	 */
	public function CKEditor($info,$value) { 		
		$id = $field = $info['FieldName'];	
		if(ACTION_NAME == 'add') {
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }        
        $parseStr='
				<script type="text/javascript" src="__SKIN__/Js/ckeditor/ckeditor.js"></script>
				<textarea id="'.$field.'" name="'.$field.'">'.stripcslashes($value).'</textarea>
				<script type="text/javascript"> 
					CKEDITOR.replace( \''.$field.'\', {skin : \'kama\'});
				</script>';  
		return $parseStr;
		
	}
	
	/**
	 * FCK编辑器
	 */
	public function FCKEditor($info,$value) {	
		$id = $field = $info['FieldName'];	
		if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }        
        $parseStr='
				<script type="text/javascript" src="__SKIN__/js/FCKeditor/fckeditor.js"></script>
				<textarea id="'.$field.'" name="'.$field.'">'.stripcslashes($value).'</textarea>
				<script type="text/javascript"> 
					var oFCKeditor = new FCKeditor( "'.$field.'","100%","400px" ); 
					oFCKeditor.BasePath = "__SKIN__/js/FCKeditor/"; 
					oFCKeditor.ReplaceTextarea() ;function resetEditor(){
						setContents("'.$field.'",document.getElementById("'.$field.'").value)
					}; 
					function saveEditor(){
						document.getElementById("'.$field.'").value = getContents("'.$field.'");
					} 
					function InsertHTML(html) { 
						var oEditor = FCKeditorAPI.GetInstance("'.$field.'") ;
						if (oEditor.EditMode == FCK_EDITMODE_WYSIWYG ){
							oEditor.InsertHtml(html) ;
						} else	{
							alert( "FCK必须处于WYSIWYG模式!" );
						}
					}
				</script>';
		return $parseStr;
	}
	
	/**
	 * 时间输入框
	 */
	public function datetime($info, $value){
		$id = $field = $info['FieldName'];	
		
		if(ACTION_NAME == 'add') {
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }
        
        $value = $value ? toDate($value,"Y-m-d H:i:s") : toDate(time(),"Y-m-d H:i:s");        
		$parseStr = '<input type="text" class="input-text"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})"  />';		
        return $parseStr;
	}
	
	
	/**
	 * 用户组
	 */
	public function groupid($info, $value) {
		
		$id = $field = $info['FieldName'];	
		$UserGroup = getUserGroup('', true);
		if(ACTION_NAME == 'add') {
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        }        
        if($value != '') $value = (strpos($value, ',') && !is_array($value)) ? explode(',', $value) :  $value ;
		$value = is_array($value) ? $value : array($value);        
        $i = 1;
		foreach($UserGroup as $key=>$r) {			
			$checked = ($value && in_array($key, $value)) ? 'checked' : '';
			
			$parseStr .= '<input type="radio" class="input_checkbox"  name="'.$field.'[]"  id="'.$id.'_'.$i.'"  '.$checked.'  value="'.htmlspecialchars($key).'"  > '.htmlspecialchars($r);			
			$i++;
		}		
        return $parseStr;
    }    

	/**
	 * 单张图片上传
	 */
	public function image($info,$value){

		$id = $field = $info['FieldName'];	
        if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        } 
		
		Helper::createPlugin("String");
        $random=String::rand_number(1000, 9999);
		$parseStr = '<input type="text" class="input-text oneimgvie"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'" />  ' . '<span id="btn_txt_cover" lang="'.Helper::createLink("content/public/upload").'" class="button">上传</span>&nbsp;&nbsp;&nbsp;<a id="imgview" href="javascript:imageview(\'imgview\',\''.SITE_URL."/".'\')">预览</a>';
		return $parseStr;
	}

	/**
	 * 图片集上传
	 */
	public function images($info,$value){
		$id = $field = $info['FieldName'];	
        if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        } 
        
        $imagesinput = "";
        $imagesArr=array();
        if(!empty($value)) {
        	
			$imagesArr = unserialize( $value );
			Helper::createPlugin("String");
        	foreach($imagesArr as $kimg => $kval){
        		$random=String::rand_number(1000, 9999);
        		$imagesinput .="<div id='image".$random."' style='padding:5px 0;'><input  style='width:370px;' type='text' name='".$field."[]' value='".$kval."' />&nbsp;&nbsp;<a href=\"javascript:remove_div('image".$random."');\">移除</a> &nbsp;&nbsp;&nbsp;<a id='imgview' href=\"javascript:imageview('imgview".$random."','".SITE_URL."/".$kval."')\">预览</a></div>";
        	}
        }
        
        $parseStr = "";
        $parseStr .= '<div class="imagesdiv" lang="'.$field.'[]" style="border:solid 1px #dce3ed; width:450px; padding:20px;"><div>最多上传50张图片</div>'.$imagesinput.'</div>';
        $parseStr .= '<span class="buttons" lang="'.Helper::createLink("content/public/upload").'">上传</span>';
		return $parseStr;
	}
	
	/**
	 * 单文件上传
	 */
	public function file($info,$value) {
		$id = $field = $info['FieldName'];	
        if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        } 
		$parseStr = '<input type="text" class="input-text"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'" />  '. $this->_getFontColor('',$id).'  '.$this->_getColor('',$id).'';
		return $parseStr;
	}

	/**
	 * 文件集上传
	 */
	public function files($info,$value) {
		$id = $field = $info['FieldName'];	
        if(ACTION_NAME == 'add'){
			$value = $info['FieldDefaultValue'];
        } else {
			$value = $value ? $value : $this->data[$field];
        } 
		$parseStr = '<input type="text" class="input-text"  name="'.$field.'"  id="'.$id.'" value="'.stripcslashes($value).'" maxlength="'.$info['FieldSize'].'" />  '. $this->_getFontColor('',$id).'  '.$this->_getColor('',$id).'';
		return $parseStr;
	}
	
 	/****************** private ********************************************************************/
 	 	
	/**
	 * 标题颜色
	 * 
	 * @param string $str
	 * @param string $name
	 * @param string $_color
	 */
	private function _getColor($str = "",$name = "Title",$_color = "_color") {
		$html = "&nbsp;<select name=\"".$name."_color\" id=\"".$name.$_color."\" onchange=\"var color=this.value; $('input[@name=".$name."]').css('color',color)\">";
		$html .= "<option value=\"\">颜色</option>";
		$html .= "<option value=\"#000000\"  ".isCheck($str,"#000000",'selected')."  style=\"background-color:#000000;align:center\"></option>";
		$html .= "<option value=\"#FFFFFF\"  ".isCheck($str,"#FFFFFF",'selected')."  style=\"background-color:#FFFFFF;align:center\"></option>";
		$html .= "<option value=\"#008000\"  ".isCheck($str,"#008000",'selected')."  style=\"background-color:#008000;align:center\"></option>";
		$html .= "<option value=\"#800000\"  ".isCheck($str,"#800000",'selected')."  style=\"background-color:#800000;align:center\"></option>";
		$html .= "<option value=\"#808000\"  ".isCheck($str,"#808000",'selected')."  style=\"background-color:#808000;align:center\"></option>";
		$html .= "<option value=\"#000080\"  ".isCheck($str,"#000080",'selected')."  style=\"background-color:#000080;align:center\"></option>";
		$html .= "<option value=\"#800080\"  ".isCheck($str,"#800080",'selected')."  style=\"background-color:#800080;align:center\"></option>";
		$html .= "<option value=\"#808080\"  ".isCheck($str,"#808080",'selected')."  style=\"background-color:#808080;align:center\"></option>";
		$html .= "<option value=\"#FFFF00\"  ".isCheck($str,"#FFFF00",'selected')."  style=\"background-color:#FFFF00;align:center\"></option>";
		$html .= "<option value=\"#00FF00\"  ".isCheck($str,"#00FF00",'selected')."  style=\"background-color:#00FF00;align:center\"></option>";
		$html .= "<option value=\"#00FFFF\"  ".isCheck($str,"#00FFFF",'selected')."  style=\"background-color:#00FFFF;align:center\"></option>";
		$html .= "<option value=\"#FF00FF\"  ".isCheck($str,"#FF00FF",'selected')."  style=\"background-color:#FF00FF;align:center\"></option>";
		$html .= "<option value=\"#FF0000\"  ".isCheck($str,"#FF0000",'selected')."  style=\"background-color:#FF0000;align:center\"></option>";
		$html .= "<option value=\"#0000FF\"  ".isCheck($str,"#0000FF",'selected')."  style=\"background-color:#0000FF;align:center\"></option>";
		$html .= "<option value=\"#008080\"  ".isCheck($str,"#008080",'selected')."  style=\"background-color:#008080;align:center\"></option>";
		$html .= "</select>";
		return $html;
	}
	
	/**
	 * 标题颜色
	 * 
	 * @param string $str
	 * @param string $name
	 * @param string $_color
	 */
	private function _getFontColor($str = "",$name = "Title",$_color = "_font"){
		$html .= "&nbsp;<input type=\"radio\" value=\"bold\" name=\"".$name.$_color."\" id=\"".$name.$_color."\" ".isCheck("bold",$str)." onclick=\"var color=this.value;$('input[@name=".$name."]').css('font-weight',color)\" /> <b>加粗</b>&nbsp;";
		$html .= "<input type=\"radio\" value=\"\" name=\"".$name.$_color."\" id=\"".$name.$_color."\" ".isCheck("",$str)."  onclick=\"var color=this.value;$('input[@name=".$name."]').css('font-weight',color)\" /> 普通";
		return $html;
	}
	
	
	/**
	 * 数据源 过滤
	 * 
	 * @param string $fieldDataSource
	 * 
	 * <code>
	 * 		模板标签：
	 *     	   <source nodeid='x' guid='x' value='x' pid='x' where='x' order='x' ><\/source>
	 *     
	 *      字符分隔：
	 *          1=贵阳,2=铜仁,3=遵义,4=兴义,5=安顺
	 * </code>
	 */
	private function _getSourceFilter($fieldDataSource, $isTree) {
		
	 	$preg = '/source(.*)><\/source>/isU';
 		preg_match_all($preg, $fieldDataSource, $match);  		
 		
 		if ( isset($match[1][0]) ) {	        
			
			/* 标签数据源（调 数据结点数据） */
 			include_once PLUGIN_PATH.SEP.tamplate.SEP.framework.SEP.'TagLib.class.php';
	        $tag = new TagLib();
	        $tags =$tag->parseXmlAttr($match[1][0], 'source');
	        
	        /* 获取数据源 */
	        if ( is_array($tags) ) {
	        	return Helper::createModel('Index', 'content')->getFieldDataSource($tags, $isTree);
	        }
	        
		} else {
			/* 字符数据源 */			
			$array = explode(',', $fieldDataSource);
			foreach ($array as $value) {
				$temp = explode("=", $value);
				$arr['key']   = $temp[0];
				$arr['value'] = $temp[1];
				$data[] = $arr ;
			}
			return $data;
		}		
		return false;		
	}
    
 }
