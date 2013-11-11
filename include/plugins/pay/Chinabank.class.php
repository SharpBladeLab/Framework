<?php
/**
 * 网银在线支付模块
 * 
 * Project: Product Library Management System
 * This is NOT a freeware, use is subject to license terms
 * 
 * Site: http://www.bobo123.cn
 * 
 * $Id: Chinabank.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2008-2009 Tiwer Framework. All Rights Reserved.
 */ 
 class Chinabank extends Framework {
 	
	/* 支付配置信息 */
	public  $config = array();
	
	
	
	/* 作者 */
	private $author='Tiwer Framework';
	/* 版本 */
	private $version='1.0.0';	
	/* 网站 */
	private $website='http://www.chinabank.com.cn';	
	
	
	
	/**
	 * 构造函数
	 * 
	 * @param array $config
	 *  
     * @access public
	 */
    public function __construct($config=array()) {    	
        
    	$this->config = $config; 
        
		$this->config['gateway_method'] = 'POST';
		$this->config['notify_url'] = SITE_URL.'/index.php?app=pay&model=record&action=respond&code=chinabank';
		$this->config['return_url'] = SITE_URL.'/index.php?app=pay&model=record&action=respond&code=chinabank';
    }   

    
     /**
     * 配置支付方法
     * 
     * @access public
     */
	public function config() {		
		
		$modules['name']        = '网银在线'; 
		$modules['code']        = 'Chinabank';
		$modules['description'] = '网银在线与中国银行、中国工商银行、中国农业银行、中国建设银行、招商银行等国内各大银行，以及VISA、MasterCard、JCB等国际信用卡组织保持了长期、紧密、良好的合作关系。<a href="http://www.chinabank.com.cn" target="_blank">立即在线申请</a>';   
		$modules['isonline']    = '1';
		$modules['enabled']     = '1';
		$modules['fee']         = '0';		
		$modules['author']      = $this->author;
		$modules['website']     = $this->website;
		$modules['version']     = $this->version;
		$modules['config']      = array(
			 array('name' => 'chinabank_account', 'type' => 'text', 'value' => ''),    // 网银在线商户号
			 array('name' => 'chinabank_key',     'type' => 'text', 'value' => ''),    // 网银在线MD5私钥
		);
		return $modules;
	}
	
	/**
	 * 获取显示表单HTML代码
	 * 
     * @access public
     */
	public function code($info,$value) { 
		
		$data_vid           = trim($this->config['chinabank_account']);
        $data_orderid       = $this->config['order_sn'];
        $data_vamount       = $this->config['order_amount'];
        $data_vmoneytype    = 'CNY';
        $data_vpaykey       = trim($this->config['chinabank_key']);
        $data_vreturnurl    = $this->config['return_url'];
		$remark1			= $this->config['body'];        

        $MD5KEY = $data_vamount.$data_vmoneytype.$data_orderid.$data_vid.$data_vreturnurl.$data_vpaykey;
        $MD5KEY = strtoupper(md5($MD5KEY));

        $def_url  = '<span style="clean:both;"><form  method=post action="https://pay3.chinabank.com.cn/PayGate" target="_blank">';
        $def_url .= "<input type=HIDDEN name='v_mid' value='".$data_vid."'>";
        $def_url .= "<input type=HIDDEN name='v_oid' value='".$data_orderid."'>";
        $def_url .= "<input type=HIDDEN name='v_amount' value='".$data_vamount."'>";
        $def_url .= "<input type=HIDDEN name='v_moneytype'  value='".$data_vmoneytype."'>";
        $def_url .= "<input type=HIDDEN name='v_url'  value='".$data_vreturnurl."'>";
        $def_url .= "<input type=HIDDEN name='v_md5info' value='".$MD5KEY."'>";
        $def_url .= "<input type=HIDDEN name='remark1' value='".$remark1."'>";
        $def_url .= "<input type=submit class='button' value='" .Helper::createLanguage('PAY_NOW'). "'>";
        $def_url .= "</form></span>";

        return $def_url;

	}
	
	
	/**
	 * 支付请求返回处理
	 * 
	 * @access public
     */
	public function respond() {		
        
		$v_oid          = trim($_POST['v_oid']);       // 订单编号
        $v_pmode        = trim($_POST['v_pmode']);     // 支付方式
        $v_pstatus      = trim($_POST['v_pstatus']);   // 支付状态 20（表示支付成功）30（表示支付失败）
        $v_pstring      = trim($_POST['v_pstring']);   // 支付结果信息
        $v_amount       = trim($_POST['v_amount']);    // 订单总金额
        $v_moneytype    = trim($_POST['v_moneytype']); // 币种
        $remark1        = trim($_POST['remark1' ]);    // 备注字段1
        $remark2        = trim($_POST['remark2' ]);    // 备注字段2
        $v_md5str       = trim($_POST['v_md5str' ]);   // 订单MD5校验码

        /* 重新计算md5的值  */
        $key = $this->config['chinabank_key'];
        $md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));

        /* 检查秘钥是否正确 */
        if ($v_md5str==$md5string) {
            if ($v_pstatus == '20') {
				record_pay_status($v_oid,'2');
                return true;
            }
        } else {
            return false;
        } 
	}	
	
	
 }
