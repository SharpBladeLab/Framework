<?php
/**
 * 支付宝支付模块
 * 
 * Project: Product Library Management System
 * This is NOT a freeware, use is subject to license terms
 * 
 * Site: http://www.bobo123.cn
 * 
 * $Id: Alipay.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2008-2009 Tiwer Framework. All Rights Reserved.
 */ 
require_once("lib/alipay_service.class.php");
require_once("lib/alipay_notify.class.php");
 class Alipay extends Framework {
	/* 版本 */
	private $version='1.0.0';	

	private $config = array();

	/**
	 * 构造函数
	 * 
	 * @param array $config
	 *  
     * @access public
	 */
    public function __construct($config=array()) {
    	/*初始化配置文件*/
    	$this->config();
    	/* 根据需要填充配置文件 */
    	foreach($config as $key=>$value){
    		$this->config[$key]=$config[$key];
    	}
    }
    
    /**
     * 配置支付方法
     * 
     * @access public
     */
	public function config() {
		
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者id，以2088开头的16位纯数字
		$this->config['partner']      = '';
		
		//安全检验码，以数字和字母组成的32位字符
		$this->config['key']          = '';
		
		//签约支付宝账号或卖家支付宝帐户
		$this->config['seller_email'] = '';
		
		//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		//return_url的域名不能写成http://localhost/create_direct_pay_by_user_php_utf8/return_url.php ，否则会导致return_url执行无效
		$this->config['return_url']   = Helper::createLink("pay/index/alipayreturnurl");
		
		
		//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$this->config['notify_url']   = Helper::createLink("pay/popedom/alipaynotifyurl");
		
		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

		//签名方式 不需修改
		$this->config['sign_type']    = 'MD5';
		
		//字符编码格式 目前支持 gbk 或 utf-8
		$this->config['input_charset']= 'utf-8';
		
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$this->config['transport']    = 'http';
		
	}

	
	/**
	 *提交 支付
	 */
	public function payto(){

		/**************************请求参数**************************/
		
		//必填参数//
		
		//请与贵网站订单系统中的唯一订单号匹配
		$out_trade_no = $_POST['tradeno'];
		//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
		$subject      = $_POST['subject'];
		//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
		$body         = $_POST['body'];
		//订单总金额，显示在支付宝收银台里的“应付总额”里
		
		$total_fee    = $_POST['price'];

		//扩展功能参数——默认支付方式//
		
		//默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
		$paymethod    = '';
		//默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
		$defaultbank  = '';
		
		
		//扩展功能参数——防钓鱼//
		
		//防钓鱼时间戳
		$anti_phishing_key  = '';
		//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
		$exter_invoke_ip = '';
		//注意：
		//1.请慎重选择是否开启防钓鱼功能
		//2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
		//3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
		//示例：
		//$exter_invoke_ip = '202.1.1.1';
		//$ali_service_timestamp = new AlipayService($config);
		//$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数

		//扩展功能参数——其他//
		
		//商品展示地址，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
		$show_url			= 'http://syks.net/order/myorder.php';
		//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
		$extra_common_param = '';
		
		//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
		$royalty_type		= "";			//提成类型，该值为固定值：10，不需要修改
		$royalty_parameters	= "";
		//注意：
		//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
		//各分润金额的总和须小于等于total_fee
		//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
		//示例：
		//royalty_type 		= "10"
		//royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"
		
		/************************************************************/
		
		//构造要请求的参数数组
		$parameter = array(
				"service"			=> "create_direct_pay_by_user",
				"payment_type"		=> "1",
		
				"partner"			=> trim($this->config['partner']),
				"_input_charset"	=> trim(strtolower($this->config['input_charset'])),
				"seller_email"		=> trim($this->config['seller_email']),
				"return_url"		=> trim($this->config['return_url']),
				"notify_url"		=> trim($this->config['notify_url']),
		
				"out_trade_no"		=> $out_trade_no,
				"subject"			=> $subject,
				"body"				=> $body,
				"total_fee"			=> $total_fee,
		
				"paymethod"			=> $paymethod,
				"defaultbank"		=> $defaultbank,
		
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
		
				"show_url"			=> $show_url,
				"extra_common_param"=> $extra_common_param,
		
				"royalty_type"		=> $royalty_type,
				"royalty_parameters"=> $royalty_parameters
				);
				
				//构造即时到帐接口
				$alipayService = new AlipayService($this->config);
				$html_text = $alipayService->create_direct_pay_by_user($parameter);
				echo $html_text;
	}
	
	/**
	 * 返回 array
	 * 
	 */
	public function returnurl(){
		
		/*
		 * verify_result 验证状态 0失败 ; 1成功
		* trade_no      订单号
		* trade_status  接口返回订单状态
		* record_status 本地系统处理后得到的订单状态
		*/
		$reault=array();
		$alipayNotify = new AlipayNotify($this->config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
		
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			$out_trade_no	= $_GET['out_trade_no'];	//获取订单号
			$trade_no		= $_GET['trade_no'];		//获取支付宝交易号
			$total_fee		= $_GET['total_fee'];		//获取总价格
		
			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
				$rel=record_pay_status($out_trade_no,4,1);
				
				$reault['verify_result']=1;
				$reault['trade_no']=$out_trade_no;
				$reault['trade_status']=1;
				$reault['record_status']=$rel;
			}else {
				$paystatus=1;
				switch($_GET['trade_status']){
					case "WAIT_BUYER_PAY":
						$paystatus=2;
						break;
					case "TRADE_CLOSED":
						$paystatus=3;
						break;
				}
				$rel=record_pay_status($out_trade_no,$paystatus,0);
				$reault['verify_result']=1;
				$reault['trade_no']=$out_trade_no;
				$reault['trade_status']=0;
				$reault['record_status']=$rel;
			}

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数，比对sign和mysign的值是否相等，或者检查$responseTxt有没有返回true
			$reault['verify_result']=0;
			$reault['trade_no']=$out_trade_no;
			$reault['trade_status']=0;
			$reault['record_status']='';
		}
		
		return $reault;
	}
	
	/**
	 * 隐式返回 用于处理掉单情况 该方法要求在任何情况下可访问
	 */
	public function clientNotifyurl() {	
		$alipayNotify = new AlipayNotify($this->config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result){
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * 隐式返回 用于处理掉单情况 该方法要求在任何情况下可访问
	 */
	public function notifyurl() {
	
		$alipayNotify = new AlipayNotify($this->config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {
			//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
			$out_trade_no	= $_POST['out_trade_no'];	    //获取订单号
   			$trade_no		= $_POST['trade_no'];	    	//获取支付宝交易号
   			$total_fee		= $_POST['total_fee'];			//获取总价格
			
			if($_POST['trade_status'] == 'TRADE_FINISHED' ||$_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
				$rel=record_pay_status($out_trade_no,4,1);
				echo "success";
			} else {
				$paystatus=1;
				switch($_POST['trade_status']){
					case "WAIT_BUYER_PAY":
						$paystatus=2;
						break;
					case "TRADE_CLOSED":
						$paystatus=3;
						break;
				}
				$rel=record_pay_status($out_trade_no,$paystatus,0);
				echo "success";
			}
		} else {
			echo "fail";
		}
	}
	
	
	
	
 }
 