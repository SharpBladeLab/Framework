<?php
/**
 * 财付通支付接口
 *
 * Project: Product Library Management System
 * This is NOT a freeware, use is subject to license terms
 *
 * Site: http://www.bobo123.cn
 *
 * $Id: TenpayDriver.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2008-2009 Tiwer Framework. All Rights Reserved.
 */
include_once PLUGIN_PATH.SEP.'pay'.SEP.'drivers/tenpay/PayRequestHandler.class.php';
include_once PLUGIN_PATH.SEP.'pay'.SEP.'drivers/tenpay/PayResponseHandler.class.php';

class TenpayDriver extends PaymentBase{

    public function payment($title, $body, $tradeno, $amount) {
        $amount = $amount * 100;

        // 生成财付通交易号
        $rand = rand(1000, 9999);
        $dealTradeno = $this->_config['bargainor_id'] . date('YmdHis') . $rand;
		$blank = empty($_POST['blank']) ? 0 : $_POST['blank'];

        // 构造请求
        $request = new PayRequestHandler();
        $request->init();
        $request->setKey($this->_config['key']);

		$request->setParameter('bargainor_id', $this->_config['bargainor_id']);			//商户号
		$request->setParameter('sp_billno', $tradeno);					                //商户订单号
		$request->setParameter('transaction_id', $dealTradeno);		                    //财付通交易单号
		$request->setParameter('total_fee', $amount);					                //商品总金额,以分为单位
		$request->setParameter('return_url', $this->_config['return_url']);				//返回处理地址
		$request->setParameter('desc', $title);	                                    //商品名称
		$request->setParameter('bank_type', $blank);                                    //网银充值 银行ID
		$request->setParameter('cs', 'utf-8');
		$request->setParameter('spbill_create_ip', $_SERVER['REMOTE_ADDR']);

        $url = $request->getRequestURL();
        $request->doSend();
    }

    public function verifyReturn() {
        $result = false;
        $response = new PayResponseHandler();
        $response->setKey($this->_config['key']);

        if($response->isTenpaySign()) {
            $tradeno = $response->getParameter('sp_billno');
            $dealTradeno = $response->getParameter('transaction_id');
			$amount = $response->getParameter('total_fee');
            $status = $response->getParameter('pay_result');
            $result = array(
                'tradeno' => $tradeno,
                'dealTradeno' => $dealTradeno,
                'amount' => $amount / 100,
                'status' => $status,
            );

        } else {
            $result = false;
        }

        return $result;
    }

    public function verifyNotify() {

    }

    /**
     * 财付通默认配置
     *
     * @return void
     */
    protected function initConfig() {
		$this->_config['bargainor_id'] = '';
		$this->_config['key']          = '';
		$this->_config['api']          = 'https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi';
		$this->_config['return_url']   = '';
    }
}
