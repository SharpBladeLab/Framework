<?php if(!defined('IN_SYS')) exit();
/**
 * 标签<文章分词>服务
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * $Id: Tag.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class Tag extends Plugin 
 {
	/* 所有设置的值 */
	protected $scws	= array();
	protected $text	= '';
	
	protected $dict	= '';
	protected $rule	= '';
	
	/**
     * 架构函数
     *
     * @access public
     */
    public function __construct($text) {
		
    	$this->dict = PLUGIN_PATH . SEP. 'scws'.SEP.'scws'.SEP.'dict.utf8.xdb';
		$this->rule = PLUGIN_PATH . SEP. 'scws'.SEP.'scws'.SEP.'rules.utf8.ini';
		
		/* 分词类 */
		if( function_exists('scws_new') ){
			$this->scws = scws_new('utf8');
		} else {
			require_cache(PLUGIN_PATH.SEP.'scws'.SEP.'pscws4.class.php');
			$this->scws = new PSCWS4('utf8');
		}
		
		$this->scws->set_charset('utf8');
		$this->scws->set_dict($this->dict);
		$this->scws->set_rule($this->rule);
		$this->setText($text);
    }
	
	/**
     * 设置待分词文本
     *
     * @access public
     *
     * @param string $text 待分词文本
     *
     * @return void
     */
	public function setText($text='') {
		$this->text	= $text;
		$this->scws->send_text($text);
		return $this->text;
	}

	/**
     * 设置字典路径
     *
     * @access public
     *
     * @param string $dict 字典路径
     *
     * @return void
     */
	public function setDict($dict='') {
		if(file_exists($dict)) {
			$this->dict	=$dict;
			return $this->dict;
		} else {
			return false;
		}
	}

	/**
     * 设置规则路径
     *
     * @access public
     *
     * @param string $rule 规则路径
     *
     * @return void
     */
	public function setRule($rule = '') {
		if(file_exists($rule)) {
			$this->rule	= $rule;
			return $this->rule;
		} else {
			return false;
		}
	}

	/**
     * 获取权重前几位的标签
     *
     * @access public
     *
     * @param string $text		待分词字符串
	 * @param int	 $top		获取条数
	 * @param string $attr		过滤规则（暂时取消）
	 * @param string $format	输出格式
     *
     * @return array
	 * a,ad,an,b,d,f,i,j,l,mq,n,nr,nz,nt,ns,nv,nnz,nrnr,ntnr,r,s,un,v,vd,vg,vn,y,z,zl
     */
	public function getTop($top=10,$format='string',$attr='',$text=false){
		/* 设置带分词字符串 */
		if($text) {
			$this->setText($text);
		} 		
		/* 获取前几位的标签 */
		$tops = $this->scws->get_tops($top);
		$tags = $this->_formatTags($tops,$format);		
		return $tags;
	}

	/**
     * 获取所有分词关键字
     *
     * @access public
     *
	 * @param string $format	输出格式
     * @param string $text		待分词字符串
     *
     * @return array
     */
	public function getAll($format='array',$text=false) {	
		/* 设置带分词字符串 */
		if($text){
			$this->setText($text);
		}
		
		/* 获取所有分词关键字 */
		while ($tmp = $this->scws->get_result()) {
			$words[]	=	$tmp;
		}
		return $words;
	}

	/**
     * 获取标签云
     *
     * @access public
     *
	 * @param string $data	输出数据，数组形式array(array('tag1',100),array('tag2',200));
     *
     * @return string
     */
	public function getCloud($data) {
		return $words;
	}

	/**
     * 格式化tag，输出以逗号分隔的词组或json数组
     *
     * @access protected
     *
     * @param string $tags	 待格式化数组
	 * @param string $output 输出类型(string,json)
     *
     * @return array
     */
	protected function _formatTags($tags=array(),$type='string') {		
		$output	=	'';
		foreach($tags as $k=>$v) {
			$tagWords[]	=	$v['word'];
		}
		switch($type){
			case 'json'	 : $output	=	json_encode($tagWords);break;
			case 'string': $output	=	implode(',',$tagWords);break;
			case 'array' : $output	=	$tags;break;
		}
		return $output;
	}
	

	/**
	 * 析构方法
	 */
	public function __destruct() {
		$this->scws->close();
	}
}
