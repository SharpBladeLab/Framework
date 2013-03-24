<?php
/**
 * The model class file of Tiwer Developer Framework.
 *
 * Tiwer Developer Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Tiwer Developer Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with Tiwer Developer Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright (C) 2007-2011 Tiwer Studio. All Rights Reserved.
 * @author      wgw8299 <wgw8299@gmail.com>
 * @package     Tiwer Developer Framework
 * @version     $Id: Page.class.php 588 2013-01-03 05:57:32Z zzy $
 * @link        http://www.tiwer.cn
 *
 * 分页显示类
 */
 class Page extends Plugin
 {
    /**
     * 分页起始行数
     *
     * @var integer
     * @access protected
     *
     */
    protected $firstRow ;

    /**
     *
     * 列表每页显示行数
     *
     * @var integer
     * @access protected
     *
     */
    protected $listRows ;

    /**
     * 页数跳转时要带的参数
     *
     * @var integer
     * @access protected
     */
    protected $parameter  ;

    /**
     * 分页总页面数
     *
     * @var integer
     * @access protected
     */
    protected $totalPages  ;

    /**
     * 总行数
     *
     * @var integer
     * @access protected
     */
    protected $totalRows  ;

    /**
     * 当前页数
     *
     * @var integer
     * @access protected
     */
    protected $nowPage;

    /**
     * 分页的栏的总页数
     *
     * @var integer
     * @access protected
     */
    protected $coolPages;

    /**
     * 分页栏每页显示的页数
     *
     * @var integer
     * @access protected
     */
    protected $rollPage   ;

    /**
     * 分页记录名称
     *
     * @var integer
     * @access protected
     */

    /* 分页显示定制 */ 
    protected $config =  array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页');

    /**
     * 架构函数
     *
     * @access public
     *
     * @param array $total  总的记录数
     * @param array $rows   每页显示记录数
     * @param array $param  分页跳转的参数
     */
    public function __construct($params=array('total'=>0, 'rows'=>10, 'param'=>'')) {
    	
    	/* 分页参数   */
    	if ( is_array($params) ) {
    		$this->totalRows = $params['total'];
        	$this->parameter = $params['param'];
    	} else {
    		$this->totalRows = $params;
    		$params['rows']=intval(config('PAGE_LISTROWS'));
    	}
    	
        /* 显示页数 */
        $this->rollPage = intval(config('PAGE_ROLLPAGE'));
                
        /* 每页显示数 */
        $this->listRows = !empty($params['rows'])?  $params['rows']  : intval(config('PAGE_LISTROWS'));
		        
		/* 总页数  */
        $this->totalPages = ceil($this->totalRows/$this->listRows); 
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);        
        $this->nowPage  = !empty($_GET[config('VAR_PAGE')])&&($_GET[config('VAR_PAGE')] >0)?$_GET[config('VAR_PAGE')]:1;
        if( (!empty($this->totalPages) && $this->nowPage>$this->totalPages) || $_GET[config('VAR_PAGE')]=='last' ) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }
    
    
    /**
     * 分页配置
     * 
     * @param string $name
     * @param string $value
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
    
    /**
     * 分页显示(用于在页面显示的分页栏的输出) 
     *
     * @access public
     *
     * @return string
     */
    public function show($isArray=false) {

        if(0 == $this->totalRows) return;
        $nowCoolPage = ceil($this->nowPage/$this->rollPage);
		
		/* 路由方式 */
		if( config('URL_DIGCITY') ) {
			$url = $_SERVER['REQUEST_URI'].$this->parameter;		
			$url = eregi_replace(config('URL_HTML_EXTENDED'), '', $url);	
			$urlSign = '/';
			$urlSqual = '/';
			$urlExtended = config('URL_HTML_EXTENDED');
		} else {
			$url =	$_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
			$url =	eregi_replace("&p=[0-9]+",'',$url);
			$urlSign = '&';
			$urlSqual = '=';
			$urlExtended = '';
		}
		//当分页有参数时，进行解析分隔
		$patterns[0] = "/\?/"; 
		$patterns[1] = "/\=/"; 
		$patterns[2] = "/\&/"; 
		$url=preg_replace($patterns[0], '/', $url);
		$url=preg_replace($patterns[1], '/', $url);
		$url=preg_replace($patterns[2], '/', $url);
	
		/* 总数 */
		$countPage = '共' . $this->totalRows . $this->config['header'] . '&nbsp;&nbsp;&nbsp;';
		
        /* 上下翻页字符串 */
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
		if ( $upRow > 0 ) {
			$upPage="<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual.$upRow.$_SESSION["pageParam"].$urlExtended."'>".$this->config['prev']."</a>";
		} else {
			$upPage='<span class=disabled">'.$this->config['prev'].' </span>';
		}
		
		if ($downRow <= $this->totalPages){
			$downPage="<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual.$downRow.$_SESSION["pageParam"].$urlExtended."'>".$this->config['next']."</a>";
		} else {
			$downPage=' &nbsp;<span class=disabled">'.$this->config['next'].'</span>';
		}
        

        /* << < > >> */
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        } else {
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual.$preRow.$_SESSION["pageParam"].$urlExtended."' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual."1'".$_SESSION["pageParam"].$urlExtended." >1</a>...";
        }
	
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        } else {
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual.$nextRow.$_SESSION["pageParam"].$urlExtended."' >下".$this->rollPage."页</a>";
            $theEnd = "...<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual.$theEndRow.$_SESSION["pageParam"].$urlExtended."' >{$theEndRow}</a>";
        }
		
        /* 1 2 3 4 5 */
        $linkPage = "";
        for($i=1;$i<=$this->rollPage+1;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if( $page<=$this->totalPages ) {
                    $linkPage .= "<a href='".$url.$urlSign.config('VAR_PAGE').$urlSqual.$page.$_SESSION["pageParam"].$urlExtended."'>".$page."</a>";
                } else {
                    break;
                }
            } else {
                if($this->totalPages != 1){
                    $linkPage .= " <span class='current'>".$page."</span>";
                }
            }
        }
				
		$pageStr = $countPage.$upPage.$theFirst.$linkPage.$theEnd.$downPage;
		if($this->totalPages <= 1) return '';
		
        if( $isArray ) {
			
            $pageArray['totalRows'] = $this->totalRows;
            $pageArray['upPage']    = $url.$urlSign.config('VAR_PAGE').$urlSqual.$upRow.$urlExtended;
            $pageArray['downPage']  = $url.$urlSign.config('VAR_PAGE').$urlSqual.$downRow.$urlExtended;
			
            $pageArray['totalPages']= $this->totalPages;
			
            $pageArray['firstPage'] = $url.$urlSign.config('VAR_PAGE').$urlSqual.'1'.$urlExtended;
            $pageArray['endPage']   = $url.$urlSign.config('VAR_PAGE').$urlSqual.$theEndRow.$urlExtended;
            $pageArray['nextPages'] = $url.$urlSign.config('VAR_PAGE').$urlSqual.$nextRow.$urlExtended;
            $pageArray['prePages']  = $url.$urlSign.config('VAR_PAGE').$urlSqual.$preRow.$urlExtended;
            $pageArray['linkPages'] = $linkPage;
			
            $pageArray['nowPage']   = $this->nowPage;
			
            return $pageArray;
        }
        return $pageStr;
    }
 }
