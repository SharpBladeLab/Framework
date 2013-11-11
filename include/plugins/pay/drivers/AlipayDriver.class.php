<?php
/**
 * 支付宝接口
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * Site: http://www.96160.cc
 *
 * $Id: AlipayDriver.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer  Developer Team. All Rights Reserved.
 */
include_once PLUGIN_PATH.SEP.'pay'.SEP.'drivers/alipay/alipay_core.function.php';
include_once PLUGIN_PATH.SEP.'pay'.SEP.'drivers/alipay/alipay_notify.class.php';
include_once PLUGIN_PATH.SEP.'pay'.SEP.'drivers/alipay/alipay_service.class.php';
include_once PLUGIN_PATH.SEP.'pay'.SEP.'drivers/alipay/alipay_submit.class.php';

/**
 * 支付宝接口
 */
class AlipayDriver extends PaymentBase{

    /**
     * 跳转到支付宝支付
     *
     * @param string $tradeno 订单号
     * @param float $amount 金额
     */
    public function payment($title, $body, $tradeno, $amount) {
        $paymethod = '';
        $defaultbank = '';
        $antiPhshingKey = '';
        $exterInvokeIp = '';

        $showUrl = '';
        $extraCommonParam = '';
        $royaltyType = '';
        $royaltyParameters = '';

        $params = array(
            'service'            => 'create_direct_pay_by_user',
            'subject'            => $title,
            'body'               => $body,
            'out_trade_no'       => $tradeno,
            'total_fee'          => $amount,
            'payment_type'       => 1,
            'partner'            => trim($this->_config['partner']),
            '_input_charset'     => trim(strtoupper($this->_config['input_charset'])),
            'seller_email'       => trim($this->_config['seller_email']),
            'return_url'         => trim($this->_config['return_url']),
            'notify_url'         => trim($this->_config['notify_url']),
            'paymethod'          => $paymethod,
            'defaultbank'        => $defaultbank,
            'anti_phishing_key'  => $antiPhshingKey,
            'exter_invoke_ip'    => $exterInvokeIp,
            'show_url'           => $showUrl,
            'extra_common_param' => $extraCommonParam,
            'royalty_type'       => $royaltyType,
            'royalty_parameters' => $royaltyParams,
        );

        $service = new AlipayService($this->_config);
        $html = $service->create_direct_pay_by_user($params);
        echo $html;
    }

    /**
     * 验证支付宝回调
     *
     * @return mixed
     */
    public function verifyReturn() {
        $notify = new AlipayNotify($this->_config);
        return $notify->verifyReturn() ? $this->formatResult($_GET) : false;
    }

    /**
     * 支付宝异步通知
     *
     * @return mixed
     */
    public function verifyNotify() {
        $notify = new AlipayNotify($this->_config);
        return $notify->verifyNotify() ? $this->formatResult($_POST) : false;
    }

    /**
     * 格式化支付宝返回数据
     *
     * @param array $data
     * @return array
     */
    public function formatResult($data)
    {
        $result = array(
            'tradeno' => $data['out_trade_no'],
            'dealTradeno' => $data['trade_no'],
            'amount' => $data['total_fee'],
            'status' => $data['trade_status'],
        );

        return $result;
    }

    /**
     * 支付宝默认配置
     *
     * @return void
     */
    protected function initConfig() {
		$this->_config['partner']       = '';
		$this->_config['key']           = '';
		$this->_config['seller_email']  = '';
		$this->_config['return_url']    = '';
		$this->_config['notify_url']    = '';
		$this->_config['sign_type']     = 'MD5';
		$this->_config['input_charset'] = 'utf-8';
		$this->_config['transport']     = 'http';
    }
}
