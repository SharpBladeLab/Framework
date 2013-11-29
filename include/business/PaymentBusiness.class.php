<?php if(!defined('IN_SYS')) exit();
/**
 * 产品购买 Business类
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: PaymentBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 include PLUGIN_PATH.SEP.'pay'.SEP.'PaymentBase.class.php';
 class PaymentBusiness
 {
    public $title;

    public $body;

    public $tradeno;

    public $paymentId;

    public $amount;

    public $companyId;

    public $userId;

    public $userType;

    public $consumeId;

    /**
     * 支付
     *
     * @access public
     */
    public function payment() {

    	$recordId = $this->createRecord();

        if( $recordId ) {

            /* 关联消费记录 */
            if($this->consumeId != null) {
                $ConsumeModel = Helper::createModel('Consume', 'pay');
                $ConsumeModel->save(array(
                    'ID' => $this->consumeId,
                    'RecordID' => $recordId,
                ));
            }

            $driver = $this->loadDriver($this->paymentId);

            $driver->payment($this->title, $this->body, $this->tradeno, $this->amount);
        }
        return false;
    }

    /**
     * 添加充值记录
     *
     * @return integer $recordId
     */
    protected function createRecord() {

        /* 添加支付记录  */
        $record = array(
            'Price'      => $this->amount,
            'Type'       => $this->userType,
            'Tradeno'    => $this->tradeno,
            'UserID'     => $this->userId,
            'CompanyID'  => $this->companyId,
            'PayTypeID'  => $this->paymentId,
            'PayStatus'  => RecordModel::PAY_STATUS_PAY,
            'CreateTime' => time(),
            'Status'     => RecordModel::STATUS_UNSUCCESS,
        );


        $RecordModel = Helper::createModel('Record', 'pay');
        $recordId = $RecordModel->add($record);

        if( !$recordId ) return false;


        /* 添加支付日志   */
        $log = array(
            'RecordID' => $recordId,
            'Price' => $record['Price'],
            'Tradeno' => $record['Tradeno'],
            'CreateTime' => $record['CreateTime'],
            'PayStatus' => $record['PayStatus'],
            'Status' =>  $record['Status'],
        );


        $LogModel = Helper::createModel('Log', 'pay');
        $LogModel->add($log);
        return $recordId;
    }

    /**
     * 更新充值记录
     *
     * @paran  array $retur 支付接口返回的数据
     *
     * @return array 更新后的充值记录
     */
    protected function updateRecord($return) {

    	$RecordModel = Helper::createModel('Record', 'pay');
        $addBalance = false;
        $tradeno = $return['tradeno'];
        $record = $RecordModel->where("Tradeno='$tradeno'")->find();
        if($record['Status'] == RecordModel::STATUS_SUCCESS) {
            return false;
        }

        $record['DealTradeno'] = $return['dealTradeno'];
        $record['PayStatus'] = $return['status'];
        if($record['PayStatus'] == RecordModel::PAY_STATUS_PAID) {
            $record['SuccessTime'] = time();
            $record['Status'] = RecordModel::STATUS_SUCCESS;
            $addBalance = true;
        }
        $RecordModel->save($record);


        /* 记录日志  */
        $log = array(
            'RecordID' => $record['ID'],
            'Price' => $record['Price'],
            'Tradeno' => $record['Tradeno'],
            'CreateTime' => $record['CreateTime'],
            'PayStatus' => $record['PayStatus'],
            'Status' =>  $record['Status'],
        );
        $LogModel = Helper::createModel('Log', 'pay');
        $LogModel->add($log);


        /* 更新余额  */
        if($addBalance) {
            $Model = Helper::createModel('Balance', 'pay');
            if($record['Type'] == RecordModel::PAY_TYPE_FRONTEND) {
                $Model->addBalance($record['UserID'], $record['CompanyID'], $record['Price']);
            } else {
                $UserModel = Helper::createModel('User', 'platform');
                $user = $UserModel->where("CompanyID={$record['CompanyID']} AND Type=1")->find();
                $Model->addBalance($user['ID'], $record['CompanyID'], $record['Price']);
            }
        }

        return $record;
    }

    /**
     * 响应支付接口回调
     *
     * @param boolean $asyn
     */
    public function response($asyn = false) {
        $driver = $this->loadDriver($_GET['code'], true);
        $return = $asyn ? $driver->verifyNotify() : $driver->verifyReturn();

        if($return != false) {
            $return = $this->resolveReturn($return);
            $result = $this->updateRecord($return);
        }

        return $result;
    }

    /**
     * 响应支付接口异步回调
     */
    public function notify() {
        return $this->response(true);
    }

    /**
     * 加载支付驱动
     *
     * @param mixed $id
     * @return IPayment
     */
    protected function loadDriver($id, $isName = false) {
        unset($_GET['code']);
        empty($id) && exit('Unknow payment driver');

        $TypeModel = Helper::createModel('Type', 'pay');
        $condition = $isName ? "Code='$id'" : "ID='$id'";
        $type = Helper::createModel('Type', 'pay')->where($condition)->find();
        $config = $this->resolveConfig($type);
        return PaymentBase::factory($type['Code'], $config);
    }

    /**
     * 处理支付接口配置
     *
     * @param TypeModel $type
     */
    protected function resolveConfig($type) {
        $config = unserialize($type['Config']);

        /* 设置前台回调地址  */
        if(APP_NAME == 'product') {
            $config['return_url'] = Helper::createLink('product/platform/response', array('code' => $type['Code']));
            $config['notify_url'] = Helper::createLink('popedom/platform/notify', array('code' => $type['Code']));
        } else {
            $config['return_url'] = Helper::createLink('pay/agent/response', array('code' => $type['Code']));
            $config['notify_url'] = Helper::createLink('platform/popedom/notify', array('code' => $type['Code']));
        }
        return $config;
    }

    /**
     * 处理支付接口返回数据
     *
     * @param  array $return
     * @return array
     */
    public function resolveReturn($return) {
        $status = $return['status'];
        if(is_numeric($status)) {
            $status = $status == 0 ? RecordModel::PAY_STATUS_PAID : RecordModel::PAY_STATUS_CANCEL;
        } elseif(is_string($status)) {
            if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {
                $status = RecordModel::PAY_STATUS_PAID;
            } elseif($status == 'WAIT_BUYER_PAY') {
                $status = RecordModel::PAY_STATUS_WAIT;
            } elseif($status == 'TRADE_CLOSED') {
                $status = RecordModel::PAY_STATUS_CANCEL;
            }
        }
        $return['status'] = $status;
        return $return;
    }
}
