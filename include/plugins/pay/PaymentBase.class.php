<?php
/**
 * 支付接口
 *
 * Project: Product Library Management System
 * This is NOT a freeware, use is subject to license terms
 *
 * Site: http://www.bobo123.cn
 *
 * $Id: PaymentBase.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2008-2009 Tiwer Framework. All Rights Reserved.
 */
interface IPayment {
    public function payment($title, $body, $tradeno, $amount);

    public function verifyReturn();

    public function verifyNotify();
}

/**
 * 支付驱动基类
 */
abstract class PaymentBase implements IPayment{

    /**
     * @var 标题
     */
    protected $_title;

    /**
     * @var 描述
     */
    protected $_body;

    /**
     * @var 配置
     */
    protected $_config = array();

    /**
     * 获取支付驱动实例
     *
     * @param string $driver
     * @param array
     * @return IPayment
     */
    final public function factory($driver, $config) {
        static $_instances = array();
        if(isset($_instances[$driver])) {
            return $_instances[$driver];
        }

        $className = ucfirst($driver) . 'Driver';
        include PLUGIN_PATH.SEP.'pay'.SEP.'drivers'.SEP.$className.'.class.php';
        $_instances[$driver] = new $className($config);
        return $_instances[$driver];
    }

    /**
     * __construct
     *
     * @param array $config
     */
    protected function __construct($config) {
        $this->initConfig();
        $this->_config = array_merge($this->_config, $config);
    }

    /**
     * 初始化配置
     */
    protected function initConfig() {
    }

    /**
     * 设置一项配置
     *
     * @param string $key
     * @param mixed $value
     */
    public function setConfig($key, $value) {
        $this->_config[$key] = $value;
    }

    /**
     * 设置交易标题
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->_title = $title;
    }

    /**
     * 获取交易标题
     * @return string
     */
    public function getTitle($title) {
        return $this->_title;
    }

    /**
     * 设置交易描述
     *
     * @param string $body
     */
    public function setBody($body) {
        $this->_body = $body;
    }

    /**
     * 获取交易描述
     *
     * @return string
     */
    public function getBody() {
        return $this->_body;
    }
}
