<?php 
/**
 * 财付通支付模块
 */
require_once ("classes/PayRequestHandler.class.php");
require_once ("classes/PayResponseHandler.class.php");
class Tenpay extends Framework{
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

		/* 商户号 */
		$this->config['bargainor_id'] = "1212574601";
		/* 密钥 */
		$this->config['key'] = "ebfe75b2b82f4a95f0d4b84c8401d245";	

		/* 网关接口 */
		$this->config['api']="https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi";

		/* 返回处理地址 */
		$this->config['return_url'] =Helper::createLink("pay/index/tenpayreturnurl");

	}
	
	/**
	 * 提交支付
	 */
	public function payto(){

		//date_default_timezone_set(PRC);
		$strDate = date("Ymd");
		$strTime = date("His");
		
		//4位随机数
		$randNum = rand(1000, 9999);
		
		//10位序列号,可以自行调整。
		$strReq = $strTime . $randNum;
		
		/* 商家订单号,长度若超过32位，取前32位。财付通只记录商家订单号，不保证唯一。 */
		$sp_billno = $_POST['sp_billno'];
		
		/* 财付通交易单号，规则为：10位商户号+8位时间（YYYYmmdd)+10位流水号 */
		$transaction_id = $this->config['bargainor_id'] . $strDate . $strReq;
		
		/* 商品价格（包含运费），以分为单位 */
		$total_fee = $_POST['price']*100;;
		
		/* 商品名称 */
		$desc = $_POST['subject'];
		
		//默认为财付通余额支付  当选择网银支付时 使用网银直接支付
		$bank_type=empty($_POST['bank_type']) ? 0 : $_POST['bank_type'];
		
		/* 创建支付请求对象 */
		$reqHandler = new PayRequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($this->config['key']);
		//----------------------------------------
		//设置支付参数
		//----------------------------------------
		$reqHandler->setParameter("bargainor_id", $this->config['bargainor_id']);			//商户号
		$reqHandler->setParameter("sp_billno", $sp_billno);					                //商户订单号
		$reqHandler->setParameter("transaction_id", $transaction_id);		                //财付通交易单号
		$reqHandler->setParameter("total_fee", $total_fee);					                //商品总金额,以分为单位
		$reqHandler->setParameter("return_url", $this->config['return_url']);				//返回处理地址
		$reqHandler->setParameter("desc", $desc);	                                        //商品名称
		$reqHandler->setParameter("bank_type",$bank_type);                                   //网银充值 银行ID
		$reqHandler->setParameter("cs", "utf-8");
		//用户ip,测试环境时不要加这个ip参数，正式环境再加此参数
		$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
		
		//请求的URL
		$reqUrl = $reqHandler->getRequestURL();
		//echo $reqUrl;
		$reqHandler->doSend();
	}
	
	public function returnurl(){
		/*
		 * verify_result 验证状态 0失败 ; 1成功 
		 * trade_no      订单号
		 * trade_status  接口返回订单状态
		 * record_status 本地系统处理后得到的订单状态
		 */
		$reault=array();
		/* 创建支付应答对象 */
		$resHandler = new PayResponseHandler();
		$resHandler -> setKey($this->config['key']);
		//判断签名
		if($resHandler->isTenpaySign()) {

			//交易单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			/* 商户订单号 */
			$sp_billno = $resHandler->getParameter("sp_billno");
			//金额,以分为单位
			$total_fee = $resHandler->getParameter("total_fee");

			//支付结果
			$pay_result = $resHandler->getParameter("pay_result");

			if( "0" == $pay_result ) {
				//------------------------------
				//处理业务开始
				//------------------------------
		
				$rel=record_pay_status($sp_billno,4,1);
		
				$reault['verify_result']=1;
				$reault['trade_no']=$sp_billno;
				$reault['trade_status']=1;
				$reault['record_status']=$rel;
		
				//调用doShow, 打印meta值跟js代码,告诉财付通处理成功,并在用户浏览器显示$show页面.
				//$show = "http://localhost/tenpay/show.php";
				//$resHandler->doShow($show);
		
			} else {
				//当做不成功处理
				$rel=record_pay_status($sp_billno,3,0);
				$reault['verify_result']=1;
				$reault['trade_no']=$sp_billno;
				$reault['trade_status']=0;
				$reault['record_status']=$rel;
			}
		
		} else {
			$reault['verify_result']=0;
			$reault['trade_no']=$sp_billno;
			$reault['trade_status']=0;
			$reault['record_status']=$rel;
		}
		return $reault;
	}
	
}