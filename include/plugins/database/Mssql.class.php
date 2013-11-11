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
 * @version     $Id: Mssql.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * 微软 SQL Server 数据库驱动类
 */
 class Mssql extends DataBase
 {
    protected $selectSql  = 'SELECT T1.* FROM (SELECT ROW_NUMBER() OVER (%ORDER%) AS ROW_NUMBER, thinkphp.* FROM (SELECT %DISTINCT% %FIELDS% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%) AS thinkphp) AS T1 WHERE %LIMIT%';
   
   /**
    * 架构函数 读取数据库配置信息
    *
    * @access public
    *
    * @param array $config 数据库配置数组
    */
    public function __construct($config='') {
	
        if ( !function_exists('mssql_connect') ) {
            Helper::createException(L('_NOT_SUPPERT_').':mssql');
        }
		
        if(!empty($config)) {
            $this->config	=	$config;
        }
    }

    /**
     * 连接数据库方法
     *
     * @access public
     *
     * @throws TiwerException
     */
    public function connect($config='',$linkNum=0) {
	
        if ( !isset($this->linkID[$linkNum]) ) {
		
            if(empty($config))	$config  =  $this->config;
            $conn = $this->pconnect ? 'mssql_pconnect':'mssql_connect';
         
			/* 处理不带端口号的socket连接情况 */
            $host = $config['hostname'].($config['hostport']?":{$config['hostport']}":'');
           
			$this->linkID[$linkNum] = mssql_pconnect( $host, $config['username'], $config['password']);
			print_r($this->linkID);exit;
			
            if ( !$this->linkID[$linkNum] || (!empty($config['database'])  && !mssql_select_db($config['database'], $this->linkID[$linkNum])) ) {
                Helper::createException($this->error());
            }
			
            /* 标记连接成功 */
            $this->connected =  true;
			
            /* 注销数据库安全信息 */
            if(1 != config('DB_DEPLOY_TYPE')) unset($this->config);
			
        }
        return $this->linkID[$linkNum];
    }

    /**
     * 释放查询结果
     *
     * @access public
     */
    public function free() {
        mssql_free_result($this->queryID);
        $this->queryID = 0;
    }

    /**
     * 执行查询  返回数据集
     *
     * @access public
     *
     * @param string $str  sql指令
     *
     * @return mixed
     *
     * @throws TiwerException
     */
    public function query($str) {
	
        $this->initConnect(false);
        if ( !$this->_linkID ) return false;
		
        $this->queryStr = $str;
		
        /* 释放前次的查询结果 */
        if ( $this->queryID ) $this->free();
        $this->Q(1);
        $this->queryID = mssql_query($str, $this->_linkID);
        $this->debug();
		
        if ( false === $this->queryID ) {
            $this->error();
            return false;
        } else {
            $this->numRows = mssql_num_rows($this->queryID);
            return $this->getAll();
        }
    }

    /**
     * 执行语句
     *
     * @access public
     *
     * @param string $str  sql指令
     *
     * @return integer
     *
     * @throws TiwerException
     */
    public function execute($str) {
	
        $this->initConnect(true);
        if ( !$this->_linkID ) return false;
        $this->queryStr = $str;
		
        /* 释放前次的查询结果 */
        if ( $this->queryID ) $this->free();
		
        $this->W(1);
        $result	= mssql_query($str, $this->_linkID);
        $this->debug();
		
        if ( false === $result ) {
            $this->error();
            return false;
        } else {
            $this->numRows = mssql_rows_affected($this->_linkID);
            $this->lastInsID = $this->mssql_insert_id();
            return $this->numRows;
        }
    }

    /**
     * 用于获取最后插入的ID
     *
     * @access public
     *
     * @return integer
     */
    public function mssql_insert_id() {
	
        $query  =   "SELECT @@IDENTITY as last_insert_id";
        $result =   mssql_query($query, $this->_linkID);
		
        list($last_insert_id)   =   mssql_fetch_row($result);
        mssql_free_result($result);
        return $last_insert_id;
    }

    /**
     * 启动事务
     *
     * @access public
     *
     * @return void
     */
    public function startTrans() {
	
        $this->initConnect(true);
        if ( !$this->_linkID ) return false;
		
        /* 数据rollback 支持 */
        if ($this->transTimes == 0) {
            mssql_query('BEGIN TRAN', $this->_linkID);
        }
		
        $this->transTimes++;
        return ;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     *
     * @access public
     *
     * @return boolen
     */
    public function commit() {
        if ($this->transTimes > 0) {
            $result = mssql_query('COMMIT TRAN', $this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                Helper::createException($this->error());
            }
        }
        return true;
    }

    /**
     * 事务回滚
     *
     * @access public
     *
     * @return boolen
     */
    public function rollback() {
	
        if ($this->transTimes > 0) {
            $result = mssql_query('ROLLBACK TRAN', $this->_linkID);
            $this->transTimes = 0;
			
            if(!$result){
                Helper::createException($this->error());
            }
        }
        return true;
    }

    /**
     * 获得所有的查询数据
     *
     * @access private
     *
     * @return array
     *
     * @throws TiwerException
     */
    private function getAll() {
	
        /* 返回数据集 */
        $result = array();
		
        if($this->numRows >0) {
            while($row = mssql_fetch_assoc($this->queryID)) {
                $result[] = $row;
			}
        }
		
        return $result;
    }

    /**
     * 取得数据表的字段信息
     *
     * @access public
     *
     * @return array
     */
    function getFields($tableName) {
	
        $result = $this->query("SELECT   column_name,   data_type,   column_default,   is_nullable
				FROM    information_schema.tables AS t
				JOIN    information_schema.columns AS c
				ON  t.table_catalog = c.table_catalog
				AND t.table_schema  = c.table_schema
				AND t.table_name    = c.table_name
				WHERE   t.table_name = '$tableName'");
		
        $info   =   array();
		
        if($result) {
            foreach ($result as $key => $val) {
                $info[$val['column_name']] = array(
                    'name'    => $val['column_name'],
                    'type'    => $val['data_type'],
                    'notnull' => (bool) ($val['is_nullable'] === ''), // not null is empty, null is yes
                    'default' => $val['column_default'],
                    'primary' => false,
                    'autoinc' => false,
                );
            }
        }
        return $info;
    }

    /**
     * 取得数据表的字段信息
     *
     * @access public
     *
     * @return array
     */
    function getTables($dbName='') {
       
	   $result   =  $this->query("SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_TYPE = 'BASE TABLE' ");
        $info =  array();
		
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
		
        return $info;
		
    }

	/**
     * Order分析
     *
     * @access protected
     *
     * @param mixed $order
     *
     * @return string
     */
    protected function parseOrder($order) {
        return !empty($order)?  ' ORDER BY '.$order:' ORDER BY rand()';
    }

    /**
     * limit
     *
     * @access public
     *
     * @return string
     */
    public function parseLimit($limit) {
	
		if(empty($limit)) $limit=1;
		
        $limit	=	explode(',',$limit);
		
        if(count($limit)>1) {
            $limitStr =	'(T1.ROW_NUMBER BETWEEN '.$limit[0].' + 1 AND '.$limit[0].' + '.$limit[1].')';
		} else {
            $limitStr = '(T1.ROW_NUMBER BETWEEN 1 AND '.$limit[0].")";
		}
		
        return $limitStr;
    }

    /**
     * 关闭数据库
     *
     * @access public
     *
     * @throws TiwerException
     */
    public function close() {
	
        if (!empty($this->queryID))
            mssql_free_result($this->queryID);
        if ($this->_linkID && !mssql_close($this->_linkID)){
            Helper::createException($this->error());
        }		
        $this->_linkID = 0;
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     *
     * @access public
     *
     * @return string
     */
    public function error() {
	
        $this->error = mssql_get_last_message();
		
        if($this->debug && '' != $this->queryStr) {
            $this->error .= "\n [ SQL语句 ] : ".$this->queryStr;
        }
		
        return $this->error;
    }

    /**
     * SQL指令安全过滤
     *
     * @access public
     *
     * @param string $str SQL指令
     *
     * @return string
     */
    public function escape_string($str) {
        return addslashes($str);
    }

    /**
     * 析构方法
     *
     * @access public
     */
    public function __destruct() {
        /* 关闭连接 */
        $this->close();
    }
	
 }
