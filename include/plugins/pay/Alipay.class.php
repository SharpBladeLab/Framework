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
 class Alipay extends Framework {
 	
 	/* 支付配置信息 */
	public  $config = array();
		
	
	
	/* 作者 */
	private $author='Tiwer Framework';
	/* 版本 */
	private $version='1.0.0';	
	/* 网站 */
	private $website='http://www.alipay.com';
	
	
	
	/**
	 * 构造函数
	 * 
	 * @param array $config
	 *  
     * @access public
	 */
    public function __construct($config=array()) {
    	        
    	$this->config = $config;    	
    	
    	
		if ($this->config['alipay_pay_type']==1) {						
			/* 担保交易接口 */
			$this->config['service'] = 'create_partner_trade_by_buyer'; 			
		} elseif($this->config['alipay_pay_type']==3) {
			/* 即时到账交易接口 */
			$this->config['service'] = 'create_direct_pay_by_user'; 			
		} else {
			/* 标准双接口 */
			$this->config['service'] = 'trade_create_by_buyer';	
		} 
		
		
        /* 支付网关地址 */
		$this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?';		
		
		/* 请求HTTP方法 */
		$this->config['gateway_method'] = 'POST';
		$this->config['notify_url'] = SITE_URL.'/index.php?app=pay&model=record&action=respond&code=alipay';
		$this->config['return_url'] = SITE_URL.'/index.php?app=pay&model=record&action=respond&code=alipay';		
    }
    
    /**
     * 配置支付方法
     * 
     * @access public
     */
	public function config() {
		
		$modules['name']        = "支付宝";   
		$modules['code']        = 'Alipay';
		$modules['description'] = '支付宝网站(www.alipay.com) 是国内先进的网上支付平台。<br/>支付宝收款接口：在线即可开通，<font color="red"><b>零预付，免年费</b></font>，单笔阶梯费率，无流量限制。<br/><a href="http://cloud.ecshop.com/payment_apply.php?mod=alipay" target="_blank"><font color="red">立即在线申请</font></a>';
		$modules['isonline']    = '1';
		$modules['enabled']     = '1';
		$modules['fee']         = '0';
		$modules['author']      = $this->author;
		$modules['website']     = $this->website;
		$modules['version']     = $this->version;						
		$modules['config'] = array(
			array('name' => 'alipay_account',           'type' => 'text',   'value' => ''),    // 账号
			array('name' => 'alipay_key',               'type' => 'text',   'value' => ''),    // 交易安全校验码(key)
			array('name' => 'alipay_partner',           'type' => 'text',   'value' => ''),    // 合作者身份(parterID)
			array('name' => 'alipay_pay_type',          'type' => 'select', 'value' => '' ,    // 选择接口类型
				  'option' =>  array('1'=>'担保交易接口','2'=>'标准双接口','3'=>'即时到账交易接口'))
			);
		return $modules;
	}

	/**
	 * 获取显示表单HTML代码
	 * 
     * @access public
     */
	public function code() {

		$parameter = array(
		
            'service'           =>  $this->config['service'],
            'partner'           =>  trim($this->config['alipay_partner']),
            '_input_charset'    =>  'utf-8',
            'notify_url'        =>  trim($this->config['notify_url']),
            'return_url'        =>  trim($this->config['return_url']),
		
            /* 商品信息  */
            'subject'           => $this->config['order_sn'],
            'out_trade_no'      => $this->config['order_sn'],
            'price'             => $this->config['order_amount'],
			'body'				=> $this->config['body'],
            'quantity'          => 1,
            'payment_type'      => 1,
		
            /* 物流参数   */
            'logistics_type'    => 'EXPRESS',
            'logistics_fee'     => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
			//'agent'           => $this->config['agent'], 

            /* 买卖双方信息 */
            'seller_email'      =>  trim($this->config['alipay_account'])
        );
        
        ksort($parameter);
        reset($parameter);
        
        $param = '';
        $sign  = '';

        foreach ($parameter AS $key => $val) {
            $param .= "$key=" .urlencode($val). "&";
            $sign  .= "$key=$val&";
        }

        $param = substr($param, 0, -1);
        $sign  = substr($sign, 0, -1). $this->config['alipay_key'];

        
        $button = '<span><input type="button"  class="button" onclick="window.open(\''.
        		$this->config['gateway_url'].$param. '&sign='.
        		MD5($sign).'&sign_type=MD5\')" value="'.
        		Helper::createLanguage('PAY_NOW').'" /></span>';
        		
		return $button;
	}

	/**
	 * 支付请求返回处理
	 * 
	 * @access public
     */
	public function respond()  {
		
		if (!empty($_POST)) {
            foreach($_POST as $key => $data) {
                $_GET[$key] = $data;
            }
        }

        $seller_email = rawurldecode($_GET['seller_email']);
        $order_sn = trim($_GET['out_trade_no']);

        /* 检查数字签名是否正确 */
        ksort($_GET);
        reset($_GET);

        $sign = '';
        foreach ($_GET AS $key=>$val) {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key != 'g' && $key != 'm' && $key != 'a') {
                $sign .= "$key=$val&";
            }
        }
        $sign = substr($sign, 0, -1) . $this->config['alipay_key'];
        if (md5($sign) != $_GET['sign']) {
            return false;
        }
    

        if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $_GET['trade_status'] =='WAIT_BUYER_CONFIRM_GOODS' ||  $_GET['trade_status'] =='WAIT_BUYER_PAY') {
            /* 改变订单状态 进行中*/
			record_pay_status($order_sn,'1');
            return true;            
            
        } elseif ($_GET['trade_status'] == 'TRADE_FINISHED') {
            /* 改变订单状态 */
			record_pay_status($order_sn,'2');
            return true;
            
        } elseif ($_GET['trade_status'] == 'TRADE_SUCCESS') {
            /* 改变订单状态 即时交易成功*/		
			record_pay_status($order_sn,'2');
            return true;            
        } else {
            return false;
        }
	}
	
	
 }
 