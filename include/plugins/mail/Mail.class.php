<?php if(!defined('IN_SYS')) exit();
/**
 * 邮件服务
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * Site: http://www.tiwer.cn
 * 
 * $Id: Mail.class.php 519 2012-12-27 02:38:41Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 include_once( PLUGIN_PATH.SEP.'mail'.SEP.'library'.SEP.'class.phpmailer.php');
 include_once( PLUGIN_PATH.SEP.'mail'.SEP.'library'.SEP.'class.pop3.php');
 include_once( PLUGIN_PATH.SEP.'mail'.SEP.'library'.SEP.'class.smtp.php');
 class Mail extends Plugin {
 
	/* 系统邮件信息可配置;发送html格式、图片、视频等; AddReplyTo已注释掉   */
	private $option = array();
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		$emailset = Helper::createBusiness('Config')->getList('email');
		$this->option = array(
			'email_sendtype'	  => $emailset['email_sendtype'],
			'email_host'		  => $emailset['email_host'],
			'email_port'		  => $emailset['email_port'],
			'email_ssl'			  => $emailset['email_ssl'],
			'email_account'		  => $emailset['email_account'],
			'email_password'	  => $emailset['email_password'],
			'email_sender_name'	  => $emailset['email_sender_name'],
			'email_sender_email'  => $emailset['email_sender_email'],
			'email_reply_account' => $emailset['email_sender_email']
		);
	}
	
	/**
	 * 发送邮件
	 * 
	 * @param string $sendto_email  接收邮箱
	 * @param string $subject       主题
	 * @param string $body          内容
	 * @param string $senderInfo    配置信息
	 */
	public function sendEmail( $sendto_email, $subject, $body, $senderInfo = '' ) {
        $mail = new PHPMailer();
		if( empty($senderInfo) ) {
			$sender_name  = $this->option['email_sender_name'];
			$sender_email = $this->option['email_account'];
		} else {
			$sender_name = $senderInfo['email_sender_name'];
			$sender_email = $senderInfo['email_account'];
		}	
		if($this->option['email_sendtype'] =='smtp'){
			$mail->Mailer = "smtp";
			$mail->Host	= $this->option['email_host'];
			$mail->Port	= $this->option['email_port'];	
			if($this->option['email_ssl']){
				$mail->SMTPSecure = "ssl";
			}
			$mail->SMTPAuth = true;
			$mail->Username = $this->option['email_account'];
			$mail->Password = $this->option['email_password'];
		}
		

		/* 发件人姓名 */
		$mail->FromName	= $sender_name;
		
		/* 发件人邮箱 */
		$mail->From		= $sender_email;

		/* 这里指定字符集 */
		$mail->CharSet	= "UTF-8";
		$mail->Encoding	= "base64";

		if(is_array($sendto_email)){
			foreach($sendto_email as $v){
				$mail->AddAddress($v);
			}			
		}else{
			$mail->AddAddress($sendto_email);
		}

		/* 以HTML方式发送 */
		$mail->IsHTML(true); 
		
		/* 邮件主题 */
		$mail->Subject = $subject;
		
		/* 邮件内容 */
		$mail->Body = $body;
		
		$mail->AltBody	 =	"text/html";
		
		$mail->SMTPDebug =	false;
		return $mail->Send();
	}
 }
