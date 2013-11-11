<?php if(!defined('IN_SYS')) exit();
/**
 * 产品购买
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 *
 * $Id: BuyBusiness.class.php 515 2013-07-30 08:59:14Z wgw $
 *
 * Copyright (C) 2007-2010 Tiwer Developer Team. All Rights Reserved.
 */
 import('pay.model.RecordModel');
 class BuyBusiness
 {
    /**
     * 购买
     *
     * @param integer $paymentId
     * @param integer $productId
     * @param float   $price
     * @param integer $number
     * @param integer $companyId
     * @param integer $userId
     * @param integer $userType
     */
    public function buy($paymentId, $productId, $price, $number, $companyId, $userId, $adminId = null) {

    	$total = $price * $number;
        $userType = $adminId ? RecordModel::PAY_TYPE_BACKEND : RecordModel::PAY_TYPE_FRONTEND;
        $aliasId = $adminId ? $adminId : $userId;

        $ConsumeModel = Helper::createModel('Consume', 'pay');
        $BalanceModel = Helper::createModel('Balance', 'pay');
        $consume = array(
            'ProductID' => $productId,
            'Price' => $price,
            'Number' => $number,
            'CompanyID' => $companyId,
            'UserID' =>$userId,
            'Type' => $userType,
            'Status' => ConsumeModel::STATUS_UNSUCCESS,
            'CreateTime' => time(),
        );

        /* 扣除余额成功或产品价格为0直接购买 */
        if($total == 0 || ($userType != RecordModel::PAY_TYPE_BACKEND && $BalanceModel->subBalance($userId, $total) > 0)) {
            // 添加消费记录
            $consume['Status'] = ConsumeModel::STATUS_SUCCESS;
            if($ConsumeModel->add($consume)) {
                return $this->createBuyRecord($productId, $price, $number, $companyId, $userId, $userType);
            }
            return false;
        } else {

            // 添加消费记录
            $consumeId = $ConsumeModel->add($consume);

            $title = '商务之星' . date('Ymd');
            $tradeno = $this->buildTradeno();

            // 调用支付
            $payment = Helper::createBusiness('Payment');
            $payment->title =$title;
            $payment->body = $title;
            $payment->tradeno = $tradeno;
            $payment->paymentId = $paymentId;
            $payment->amount = $total;
            $payment->companyId = $companyId;
            $payment->userId =$userId;
            $payment->userType = $userType;
            $payment->consumeId = $consumeId;

            $payment->payment();
            return false;
        }
    }

    /**
     * 支付后更新消费记录
     */
    public function updateConsume($record) {
        $result = false;
        $BalanceModel = Helper::createModel('Balance', 'pay');
        $ConsumeModel = Helper::createModel('Consume', 'pay');

        // 更新消费状态
        $consume = $ConsumeModel->where("RecordID='{$record['ID']}'")->find();
        $consume['Status'] = ConsumeModel::STATUS_SUCCESS;
        $ConsumeModel->save($consume);

        // 扣费
        $total = $consume['Price'] * $consume['Number'];
        if($BalanceModel->subBalance($consume['UserID'], $total) > 0) {
            $result = $this->createBuyRecord($consume['ProductID'], $consume['Price'], $consume['Number'], $consume['CompanyID'], $consume['UserID'], $consume['Type']);
        }
        return $result;
    }

    /**
     * 创建购买记录
     *
     * @param integer $productId
     * @param float $price
     * @param integer $number
     * @param integer $compnayId
     * @param integer $userId
     * @param integer $userType
     */
    public function createBuyRecord($productId, $price, $number, $companyId, $userId, $userType) {
        $ProductModel = Helper::createModel('Product', 'product');
        return $ProductModel->payUpdate(array(
            'ProductID' => $productId,
            'Price' => $price,
            'Number' => $number,
            'CompanyID' => $companyId,
            'UserID' => $userId,
            'Type' => $userType,
        ));
    }

    /**
     * 生成订单号
     */
    protected function buildTradeno() {
        Helper::createPlugin("String");
        return date('Ymdhis') . implode('', String::build_format_rand("#", 4));
    }
 }
