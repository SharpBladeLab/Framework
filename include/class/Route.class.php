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
 * @version     $Id: Route.class.php 516 2013-07-30 09:02:02Z wgw $
 *
 * 系统URL路由类
 */
 class Route
 {
	/**
	 * 请求URL参数
	 */
	var $param;

	/**
	 * 配置URL参数
	 */
	var $config;

	/**
	 * 域名解析文件
	 */
	var $domina;

	
	
	/**
	 * 初始化路由解析类
	 */
	static function _init() {
		/* PHP版本检测 */
		if( version_compare(PHP_VERSION, '5.0.0', '<')) {
			die('PHP版过低! 运行系统必须大于5.0。谢谢合作!');
		}
		new Route();
	}



	/**
	 * 构造函数
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function __construct() {

		
		/* 获取相关参数 */
		$this->config = include SITE_PATH.SEP.'include'.SEP.'config'.SEP.'url.inc.php';
		$temp = include SITE_PATH.SEP.'include'.SEP.'config'.SEP.'system.inc.php';
		$this->config = array_merge($this->config, $temp);

		
		
		/* 域名解析文件 */
		if ( file_exists(SITE_PATH.SEP.'data'.SEP.'temp'.SEP.'sysinfo'.SEP.'domain.php') ) {
			$this->domina = include SITE_PATH.SEP.'data'.SEP.'temp'.SEP.'sysinfo'.SEP.'domain.php';
		} else {
			$this->domina = false;
		}
		
		
		
		if ( $this->parsePath() ) {
			/* application */
			!defined('APP_NAME') && define('APP_NAME', $this->param['APP']);

			/* controller */
			!defined('CONTROLLER_NAME') && define('CONTROLLER_NAME', $this->param['CONTROLLER']);

			/* action */
			!defined('ACTION_NAME') && define('ACTION_NAME', $this->param['ACTION']);

			/* params filter and process */
			$this->Request();

			/* request filter */
			$this->RequestFilter();
		}
	}

	
	/**
	 * 解析URL路径
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function parsePath() {

		/* script name verification */
		if ( strtolower(substr($_SERVER['SCRIPT_NAME'],-3,3)) != 'php' ) {
			die('Illegal request!');
		}


		/* get params infomation */
		$url = (strlen($_SERVER['SCRIPT_NAME']) > strlen($_SERVER['REQUEST_URI'])) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_URI'];


		/* url Weite Rule */
		if ( $this->config['URL_REWRITERULE'] ) {
			$url = $_SERVER['REQUEST_URI'];
			$scriptname = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
			$param = explode('/',trim(str_replace($scriptname, '', $url), '/'));
		} else {
			$param = explode('/',trim(str_replace($_SERVER['SCRIPT_NAME'], '', $url), '/'));
		}


		/* delete html */
		if( $this->config['URL_HTML'] && count($param) > 0 ) {
			array_splice($param, -1, 1, str_replace( $this->config['URL_HTML_EXTENDED'], '', end($param)));
		}


		/* digcity open safe mode */
		if( count($param)==1 && $this->config['URL_DIGCITY_SAFE'] && strlen($param[0]) > 12 ) {
			$param = explode('/', base64_decode($param[0]));
		}


		/* application */
		if( isset($_GET[$this->config['VAR_APP']]) || isset($_POST[$this->config['VAR_APP']]) ) {
			/* General Mode */
			$app_name = !empty($_POST[$this->config['VAR_APP']]) ? $_POST[$this->config['VAR_APP']] : $_GET[$this->config['VAR_APP']];
			$this->param['APP'] = strtolower(str_replace(array('/','\\'), '', strip_tags(urldecode($app_name)) ));

		} elseif( empty($param[0]) || strtolower($param[0]) == 'index.php' ) {
			
			/* Domain Mode */
			if ( !empty($this->domina[$_SERVER['SERVER_NAME']])  ) {
				$this->param['APP'] = trim($this->domina[$_SERVER['SERVER_NAME']]);			
			} else if ( !empty($this->domina) && count($this->domina)>0 ) {
				foreach ($this->domina as $key=>$value) {
					$str1 = str_replace('*.', '', $key);
					$str2 = substr($_SERVER['SERVER_NAME'], -strlen($str1)); 
				 	if( $str1 == $str2 ) {
			            $this->param['APP'] = trim($value);
			            break;
		        	}
				}
			}
			
			/* Default Mode */
			empty($this->param['APP']) && $this->param['APP'] = $this->config['DEFAULT_APP'];

		} else {
			
			/* Router Mode */
			$this->param['APP'] = $param[0];
		}



		/* controller */
		$count = count($param);
		if( isset($_GET[$this->config['VAR_MODULE']]) || isset($_POST[$this->config['VAR_MODULE']]) ) {
			/* General Mode */
			$this->param['CONTROLLER'] = !empty($_POST[$this->config['VAR_MODULE']]) ? $_POST[$this->config['VAR_MODULE']] : $_GET[$this->config['VAR_MODULE']];

		} elseif ( $count >= 2 && isset($param[1]{0}) ) {
			/* Router Mode */
			$this->param['CONTROLLER'] = str_replace('.', SEP, $param[1]);

		} else {
			/* Default Mode */
			$this->param['CONTROLLER'] = $this->config['DEFAULT_MODULE'];
		}


		/* URL Address Conversion */
		if( $this->config['URL_CASE_INSENSITIVE'] ) {
            $this->param['CONTROLLER'] = ucfirst($this->param['CONTROLLER']);
        }


		/* action */
		$count = count($param);
		if( isset($_GET[$this->config['VAR_ACTION']]) || isset($_POST[$this->config['VAR_ACTION']]) ) {
			/* General Mode */
			$this->param['ACTION'] = !empty($_POST[$this->config['VAR_ACTION']]) ? $_POST[$this->config['VAR_ACTION']] : $_GET[$this->config['VAR_ACTION']];

		} elseif ( $count >= 3 && isset($param[2]{0}) ) {
			/* Router Mode */
			$this->param['ACTION'] = $param[2];
			$i=3;

		} else {
			/* Default Mode */
			$this->param['ACTION'] = $this->config['DEFAULT_ACTION'];
			$i=2;
		}

		if ( isset($i) ) {
			/* GET Values */
			for (; $i < $count; $i++) {
				$this->param[$param[$i]] = @$param[++$i];
			}
		}

		return true;
	}

	/**
	 * 设置参数并进行过滤
	 *
	 * @access protected
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	protected function Request() {

		/* delete core infomation */
		unset($this->param['APP'], $this->param['CONTROLLER'], $this->param['ACTION']);

		/* filter */
		if ( $this->config['URL_AUTO_FILTER'] ) {
			$this->_filter($this->param);
			if ( !get_magic_quotes_gpc() ) {
				$this->_filter($_POST);
				$this->_filter($_COOKIE);
				$this->_filter($_FILES);
			}
		}

		/* get and param merge  */
		$_GET = array_merge($this->param, $_GET);

		/* delete other data */
		unset($_GET['app'], $_GET['model'], $_GET['action']);
		unset($_ENV);
		unset($HTTP_ENV_VARS);
		unset($HTTP_POST_VARS);
		unset($HTTP_GET_VARS);
		unset($HTTP_POST_FILES);
		unset($HTTP_COOKIE_VARS);
	}


	/**
	 * 请求内容过滤
	 *
	 * @access protected
	 *
	 * @return void
	 */
	protected function RequestFilter() {

		/* filter GET */
		$filter = "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
		foreach($_GET as $key=>$value) {
			$this->_stopAttack($key, $value, $filter);
		}


		/* filter POST */
		$filter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
		foreach($_POST as $key=>$value){
			$this->_stopAttack($key, $value, $filter);
		}


		/* filter COOKIE */
		$filter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
		foreach($_COOKIE as $key=>$value){
			$this->_stopAttack($key, $value, $filter);
		}
	}




	/**
	 * 转义
	 *
	 * @param array $array 要过滤的数组
	 */
	private function _filter(&$array) {
		/* 数据过滤 */
		if ( is_array($array) ) {
			foreach ($array as $key => $value) {
				is_array($value) ? $this->_filter($value) : $array[$key] = addslashes($value);
			}
		}
	}
	/**
	 * 停止攻击
	 *
	 * @param string $StrFiltKey
	 * @param mixed  $StrFiltValue
	 * @param string $ArrFiltReq
	 */
	private function _stopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq) {
		is_array($StrFiltValue) && $StrFiltValue = @implode($StrFiltValue);
		if ( preg_match("/".$ArrFiltReq."/is", $StrFiltValue)==1) {
			 $this->_printLog("<br><br>
				 操作IP:  " .$_SERVER["REMOTE_ADDR"]."<br>
				 操作时间: " .strftime("%Y-%m-%d %H:%M:%S")."<br>
				 操作页面: " .$_SERVER["PHP_SELF"]."<br>
				 提交方式: " .$_SERVER["REQUEST_METHOD"]."<br>
				 提交参数: " .$StrFiltKey."<br>
				 提交数据: " .$StrFiltValue);
			print "illegal";
			exit();
		}
	}
	/**
	 * 输出攻击日志
	 *
	 * @param string $logs
	 */
	private function _printLog($logs) {
		$toppath = $_SERVER["DOCUMENT_ROOT"]."/log.htm";
		$Ts=fopen($toppath, "a+");
		fputs($Ts,$logs."\r\n");
		fclose($Ts);
	}

 }

