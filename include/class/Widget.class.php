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
 * @version     $Id: Widget.class.php 24 2012-11-28 03:59:21Z wgw $
 * @link        http://www.tiwer.cn
 *
 * Widget类(抽象类)
 */
 abstract class Widget extends Framework 
 {
	/* 使用的模板引擎 每个Widget可以单独配置不受系统影响 */
	protected $template = '';
	protected $attr = array ();
	protected $cacheChecked = false;

	/**
	 * 渲染输出 render方法是Widget唯一的接口( 使用字符串返回 不能有任何输出 )
     *
	 * @access public
     *
	 * @param mixed $data  要渲染的数据
     *
	 * @return string
	 */
	abstract public function render($data);

	/**
	 * 渲染模板输出 供render方法内部调用
     *
	 * @access public
     *
	 * @param string $templateFile  模板文件
	 * @param mixed $var  模板变量
	 * @param string $charset  模板编码
     *
	 * @return string
	 */
	protected function renderFile($templateFile = '', $var = '', $charset = 'utf-8') {
		
		ob_start ();
		ob_implicit_flush ( 0 );
		
		if ( !file_exists_case($templateFile) ) {
		
			/* 自动定位模板文件 */
			$name = substr ( get_class ( $this ), 0, - 6 );
			$filename = empty ( $templateFile ) ? $name : $templateFile;
			
			$templateFile = WIDGET_PATH . SEP . $name . SEP . $filename . config( 'TMPL_TEMPLATE_SUFFIX' );
			if (! file_exists_case ( $templateFile )) {
				Helper::createException( Helper::createLanguage( '_TEMPLATE_NOT_EXIST_' ) . '[' . $templateFile . ']' );
			}
		}
		
		$template = $this->template ? $this->template : strtolower ( config( 'TMPL_ENGINE_TYPE' ) ? config( 'TMPL_ENGINE_TYPE' ) : 'php' );
		if ('php' == $template) {
			
			/* 使用PHP模板 */
			if (! empty ( $var ))
				extract ( $var, EXTR_OVERWRITE );
			
			/* 直接载入PHP模板 */
			include $templateFile;
			
		} else {
			$className = 'Template' . ucwords ( $template );
			require_cache ( CORE_PATH. SEP .'template'. SEP .$className . '.class.php' );
			$tpl = new $className ();
			$tpl->fetch ( $templateFile, $var, $charset );
		}
		
		$content = ob_get_clean ();
		return $content;
	}
	
 }
