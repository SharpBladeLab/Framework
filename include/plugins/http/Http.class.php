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
 * @version     $Id: Http.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * Http工具类(提供一系列的Http方法)
 */
 class Http extends Plugin
 {
	/**
	 * Post 方式请求网页数据
	 * 
     * @static
	 *
	 * @access public
	 *
	 * @param string $url     网页地址
	 * @prarm string $host    主机
	 * @param string $session 会话值
	 * @prarm string $type    类型(POST、GET)
	 * @prarm string $port    端口
	 * @prarm string $data    数据
	 *
	 * @return mixed
	 */
	 static public function getPageConent( $url, $host, $session = "", $type = "POST", $port = 80, $data = "", $herleng=0, $conleng=1024) {

	 	/* SESSION名称:(J2ee是JSESSIONID)  */
	 	$sessionName = 'JSESSIONID';
	 	
	 	
	 	/* 请求数据 */
	    $post_data = $data;
	    $lenght = strlen($post_data);
	    
	    
	    /* PHP User Agent */
	    @ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; GTB6; CIBA; .NET CLR 4.0.20506)');
	    
	    
		/* HTTP头信息 */
	    $headers  = "{$type} {$url} HTTP/1.1\r\n";
	    $headers .= "Accept: */*\r\n";
		$headers .= "UA-CPU: x86\r\n";
		$headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
	    $headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; GTB6; CIBA; .NET CLR 4.0.20506)\r\n";
	    if($session != "" ) $headers .= "Cookie:{$sessionName}={$session}\r\n";
	    $headers .= "Host: {$host}:{$port}\r\n";
	    $headers .= "Content-Length: {$lenght}\r\n";
	    $headers .= "Connection: Keep-Alive\r\n\r\n";
	    $headers .= $post_data;
		
	    
		/* 获取返回信息 */
		if( $fp = fsockopen($host, $port, &$errno, &$errstr, 10) ) {	
			fwrite($fp, $headers);
			stream_set_blocking($fp, true);
			stream_set_timeout($fp, 60);		
			if($herleng == 0 && $conleng == 0 ) {
			
				/* 获取全部内容 */
				while(!feof($fp)){
					$content .= fgets($fp, 512);
				}
				
			} else {
			
				/* HTTP头信息与内容信息分开 */
				if($herleng > 0) $header  = fread($fp, $herleng); 		
				if($conleng > 0) $content = fread($fp, $conleng);
			}		
	        fclose($fp);
			
	    } else {
			echo "$errstr : ($errno)\n";
			exit;		
		}
		
		return $content;  
	}
	
	
	/**
	 * 图片下载到本地
	 * 
     * @static
	 *
     * @access public
	 *
	 * @param string $url       地址
	 * @param string $filename  存储到本地的文件名称
	 *
	 * @return string 网页内容
	 */
	static public function getImage($url,$filename="") {
		
		if(!$url) return false;	
		if(!$filename) {	
			$ext=strrchr(strtolower($url),".") ;
			if($ext!=".gif" && $ext!=".jpg" && $ext!=".png") {
				return false ;
			}		
			$str=explode('/',$url) ;
			$filename=$str[count($str)-1] ;
		}
		
		if(is_function('readfile')) {
			ob_start();
			readfile($url);	
			$img = ob_get_contents();
			ob_end_clean();		
			$size = strlen($img); 
			$fp2=@fopen($filename, "a") ;
			fwrite($fp2,$img); 
			fclose($fp2);
			return $filename;
		} else {
			return false;
		}
	}
	
   /**
    * GET方式获取网页内容
    * 
    * @static
	*
    * @access public
    *
    * @param string $url      地址
    * @param string $referer  来源地址
    * @param string $cookie   Cookie
    *
    * @return string 网页内容
    */
	static public function doget($url, $referer, $cookie) {   
	    $optionget = array('http' => array('method' => "GET", 'header' => "User-Agent:Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.5.21022; .NET CLR 3.0.04506; CIBA)\r\nAccept:*/*\r\nReferer:" . $referer . "\r\nCookie:" . $cookie));   
	    $file = file_get_contents($url, false , stream_context_create($optionget));   
	    return $file;   
	} 
	
	
	/**
	 * 获取URL地址内容
	 * 
	 * @param string $url 地址
	 *
	 * @return mixed 页面内容
	 */  
	static public function getUrlContent($url) {
	      
	    $url_parsed = parse_url($url);
	    $host = $url_parsed['host'];
	    $port = $url_parsed['port'];
	     
	    /* 端口 */
	    if ( $port == 0 ) {
	        $port = 80;
	    }
	     
	    /* 路径 */
	    $path = $url_parsed['path'];
	    if (empty($path)) {
	        $path = "/";
	    }
	     
	     /* 查询 */
	     if ( $url_parsed['query'] != "" ) {
	         $path .= "?".$url_parsed['query'];
	     }
	     
	    /* Open Page Content */
	    $out = "GET {$path} HTTP/1.1\r\nHost: {$host}\r\n\r\n";
	    if ($fp = @fsockopen( $host, $port, $errno, $errstr, 30 )) {
	        fwrite($fp,$out);
	        $header  = fread($fp,1024);        
	        fclose($fp);
	        return $header;
	    } else {
	        return false;
	    }
	} 
 
    /**
     * 远程下载文件（curl）
     *
     * @access public
     *
     * @param string $remote 远程文件名
     * @param string $local 本地保存文件名
     *
     * @return mixed
     */
    static public function curlDownload($remote, $local) {
	
        $cp = curl_init($remote);
        $fp = fopen($local, "w");
		
        curl_setopt($cp, CURLOPT_FILE, $fp);
        curl_setopt($cp, CURLOPT_HEADER, 0);
		
        curl_exec($cp);
        curl_close($cp);
		
        fclose($fp);
    }

    /**
     * 下载文件
     * 
     * 可以指定下载显示的文件名，并自动发送相应的Header信息
     * 如果指定了content参数，则下载该参数的内容
     *
     * @static
	 *
     * @access public
     *
     * @param string $filename 下载文件名
     * @param string $showname 下载显示的文件名
     * @param string $content  下载的内容
     * @param integer $expire  下载内容浏览器缓存时间
     *
     * @return void
     *
     * @throws Execption
     */
    static public function download($filename, $showname='',$content='',$expire=180) {
	
        if(is_file($filename)) {
            $length = filesize($filename);
        } elseif(is_file(UPLOAD_PATH.$filename)) {
            $filename = UPLOAD_PATH.$filename;
            $length = filesize($filename);
			
        } elseif($content != '') {
            $length = strlen($content);
        } else {
            Helper::createException($filename.Helper::createLanguage('下载文件不存在！'));
        }
		
        if(empty($showname)) {
            $showname = $filename;
        }
		
		/* 造成部分中文文件的乱码问题 */
        //$showname = basename($showname);
		
		if(!empty($filename)) {
	        $type = mime_content_type($filename);
		}else{
			$type = "application/octet-stream";
		}
		
        /* 发送Http：Header信息开始下载 */
        header("Pragma: public");
        header("Cache-control: max-age=".$expire);
        header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
        header("Content-Length: ".$length);
        header("Content-type: ".$type);
        header("Content-Disposition: attachment; filename= ".$showname." ");
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary" );
		
        if( $content == '' ) {
            readfile($filename);
        } else {
        	echo($content);
        }
        exit();
    }

    
    /**
     * 显示HTTP Header 信息
     *
     * @return string
     */
    static public function get_header_info($header='',$echo=true) {
        ob_start();
        
        /* 获取Header信息 */
        $headers = self::get_headers();
        if( !empty($header) ) {
            $info = $headers[$header];
            echo($header.':'.$info."\n"); ;
        } else {
            foreach($headers as $key=>$val) {
                echo("$key:$val\n");
            }
        }
        
        $output = ob_get_clean();
        if ( $echo ) {
            echo (nl2br($output));
        } else {
            return $output;
        }
    }
    
    /**
     * 发送常用http协议header信息
     *    utf8,html,wml,xml,图片、文档类型 等常用header
     *
     * @param string $type 类型
     *
     * @return void
     */
    static public function send_http_header($type='utf8') {    	
    	switch($type) {
    		case 'utf8':
    			header("Content-type: text/html; charset=utf-8");
    			break;
    
    		case 'xml':
    			header("Content-type: text/xml; charset=utf-8");
    			break;
    	}
    }
    
    
    /**
     * HTTP协议定义的状态码
	 *
     * @param integer $code  状态码
     */
	static public function send_http_status($code) {	
		static $_status = array(
		
			/* 信息 1xx */
			100 => '继续',
			101 => '协议需转换，服务器将遵从客户的请求转换到另外一种协议',

			/* 成功 2xx */
			200 => '请求成功',
			201 => '请求全部成功，且创建了新资源',
			202 => '请求已经接受处理，但是处理还没有完成',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',

			
			/* 重定向 3xx */ 
			300 => '多种选择',
			301 => '永久移动',
			302 => '发现',  // 1.1
			303 => '看其他的',
			304 => '没有更改',
			305 => '使用代理',
			306 => '状态码在前版规范中使用，现在没用了，应保留该代码',
			307 => '临时改变',

			
			/* 客户端错误码 4xx */
			400 => '请求出现语法错误',
			401 => '客户试图未经授权访问受密码保护的页面',
			402 => 'Payment Required',
			403 => '禁止,服务器理解客户的请求，但拒绝处理它',
			404 => '没有发现',
			405 => '请求方法（GET、POST、HEAD、DELETE、PUT、TRACE等）对指定的资源不适用',
			406 => '指定的资源已经找到，但它的MIME类型和客户在Accpet头中所指定的不兼容（HTTP 1.1新）',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',

			
			/* 服务器错误5xx */
			500 => '服务器内部错误',
			501 => '未被执行',
			502 => '坏网关',
			503 => '服务不可用',
			504 => '网关超时',
			505 => 'HTTP版本不支持',
			509 => '超过带宽的限制'
		);
		
		if(array_key_exists($code,$_status)) {
			header('HTTP/1.1 '.$code.' '.$_status[$code]);
		}
	}
	
	
	/**
	 * 获取Header信息
	 * 
	 * php 语言在的getallheaders函数功能
	 * ( getallheaders仅在 PHP 作为 Apache 模块安装时才可使用。 )
	 * 
	 * @return array
	 */
	static public function getHeaders() {		
		foreach($_SERVER as $name => $value) {
			if(substr($name, 0, 5) == 'HTTP_')
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
		}
		return $headers;
	}
	
	/**
	 * 是否手机端访问
	 * 
	 * @access public 
	 * 
	 * @return boolean
	 */
	static public function isMobileRequest() {
		
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
	
		$mobile_browser = '0';
	
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
			$mobile_browser++;
	
		if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
			$mobile_browser++;
	
		if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
			$mobile_browser++;
	
		if(isset($_SERVER['HTTP_PROFILE']))
			$mobile_browser++;
	
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array(
				'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
				'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
				'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
				'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
				'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
				'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
				'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
				'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
				'wapr','webc','winw','winw','xda','xda-'
		);
	
		if(in_array($mobile_ua, $mobile_agents))
			$mobile_browser++;
	
		if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
			$mobile_browser++;
	
		// Pre-final check to reset everything if the user is on Windows
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
			$mobile_browser=0;
	
		// But WP7 is also Windows, with a slightly different characteristic
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
			$mobile_browser++;
	
		if($mobile_browser>0)
			return true;
		else
			return false;
	}
	
 }
 
