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
 * @version     $Id: MongoModel.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * Mongo DB 数据库驱动类  ( 实现了ODM和ActiveRecords模式 )
 */
 class Mongo extends Model
 {    
 	/*  主键类型   */
    const TYPE_OBJECT   = 1; 
    const TYPE_INT      = 2;
    const TYPE_STRING   = 3;

    /* 主键名称 */
    protected $pk = '_id';    
    
    /* _id 类型 
     * 1:Object采用MongoId对象    
     * 2:Int 整形 支持自动增长    
     * 3:String 字符串Hash 
     */
    protected $_idType =  self::TYPE_OBJECT;
    
    
    /* 主键是否自动增长 支持Int型主键 */
    protected $_autoInc =  false;
    
    
    /* Mongo默认关闭字段检测 可以动态追加字段 */
    protected $autoCheckFields  =   false;
    
    
    /* 链操作方法列表 */
    protected $methods  = array('table','order','auto','filter','validate');

    
    /**
     * 利用__call方法实现一些特殊的Model方法
     * 
     * @param string $method 方法名称
     * @param array $args 调用参数
     * 
     * @access public
     * 
     * @return mixed
     */
    public function __call($method, $args) {
        
    	if(in_array(strtolower($method) ,$this->methods,true)) {

    		/* 连贯操作的实现 */
            $this->options[strtolower($method)] =   $args[0];
            return $this;
            
            
        } elseif(strtolower(substr($method,0,5))=='getby') {

        	/* 根据某个字段获取记录  */
            $field   = parse_name(substr($method,5));
            $where[$field] =$args[0];
            return $this->where($where)->find();
            
        } elseif(strtolower(substr($method,0,10))=='getfieldby') {

        	/*根据某个字段获取记录的某个值 */
            $name = parse_name(substr($method, 10));
            $where[$name] =$args[0];
            return $this->where($where)->getField($args[1]);
            
        } else {
        	Helper::createException(__CLASS__.':'.Helper::createLanguage('_METHOD_NOT_EXIST_'));        	
            return;
        }
    }

    /**
     * 获取字段信息并缓存 主键和自增信息直接配置
     * 
     * @access public
     * @return void
     */
    public function flush() {

    	/* 缓存不存在则查询数据表信息 */
        $fields =   $this->db->getFields();        
        
         /* 暂时没有数据无法获取字段信息 下次查询 */
        if(!$fields) return false;
                
        $this->fields =  array_keys($fields);
        $this->fields['_pk'] = $this->pk;
        $this->fields['_autoinc'] = $this->_autoInc;
        
        
         /* 记录字段类型   */ 
        foreach ($fields as $key=>$val) {
            $type[$key]= $val['type'];
        }
        
        
        /* 记录字段类型信息   */
        if(config('DB_FIELDTYPE_CHECK'))  $this->fields['_type'] = $type;

        
        
        /* 缓存开关控制 */
        if( config('DB_FIELDS_CACHE') ) {
            
            /* 永久缓存数据表信息   */
            $db = $this->dbName?$this->dbName:config('DB_NAME');
            Helper::createTempFile('table'.SEP.$db.'.'.$this->name,  $this->fields);
        }
    }

    
    
    /**
     * 写入数据前的回调方法 包括新增和更新
     */
    protected function _before_write(&$data) {
        $pk = $this->getPk();
        
        /* 根据主键类型处理主键数据 */
        if(isset($data[$pk]) && $this->_idType == self::TYPE_OBJECT) {
            $data[$pk] =  new MongoId($data[$pk]);
        }
    }

    
    /**
     * count统计 配合where连贯操作
     * 
     * @access public
     * @return integer
     */
    public function count() {
        $options =  $this->_parseOptions();
        return $this->db->count($options);
    }

    
    /**
     * 获取下一ID 用于自动增长型
     * 
     * @access public
     * @param string $pk 字段名 默认为主键
     * @return mixed
     */
    public function getMongoNextId($pk=''){
        if( empty($pk) ) $pk = $this->getPk();
        return $this->db->mongo_next_id($pk);
    }
    
    /**
     * 插入数据前的回调方法
     */
    protected function _before_insert(&$data,$options) {
    	
        /* 写入数据到数据库 */
        if($this->_autoInc && $this->_idType== self::TYPE_INT) {
            $pk = $this->getPk();
            if(  !isset($data[$pk])  ) {
                $data[$pk]= $this->db->mongo_next_id($pk);
            }
        }
    }

    public function clear() {
        return $this->db->clear();
    }

    /**
     * 查询成功后的回调方法
     */
    protected function _after_select(&$resultSet,  $options) {
        array_walk($resultSet,array($this,'checkMongoId'));
    }
	
    /**
     * 获取MongoId
     */
    protected function checkMongoId(&$result){
        if( is_object($result['_id']) ) {
            $result['_id'] = $result['_id']->__toString();
        }
        return $result;
    }

    /**
     * 表达式过滤回调方法
     */
    protected function _options_filter(&$options) {
        $id = $this->getPk();
        if(isset($options['where'][$id]) && is_scalar($options['where'][$id]) && $this->_idType== self::TYPE_OBJECT) {
            $options['where'][$id] = new MongoId($options['where'][$id]);
        }
    }

    /**
     * 查询数据
     * 
     * @access public
     * 
     * @param mixed $options 表达式参数
     * 
     * @return mixed
     */
     public function find( $options=array() ) {
     	
         if( is_numeric($options) || is_string($options)) {
            $id = $this->getPk();
            $where[$id] = $options;
            $options = array();
            $options['where'] = $where;
         }
         
         
        /* 分析表达式 */
        $options =  $this->_parseOptions($options);
        $result = $this->db->find($options);        
        if(false === $result) return false;

        
        /* 查询结果为空 */
        if( empty($result) ) {
            return null;
        } else {
            $this->checkMongoId($result);
        }
        
        $this->data = $result;
        $this->_after_find($this->data,$options);
        return $this->data;
     }

    /**
     * 字段值增长
     * 
     * @access public
     * 
     * @param string $field  字段名
     * @param integer $step  增长值
     * 
     * @return boolean
     */
    public function setInc($field,$step=1) {
        return $this->setField($field,array('inc',$step));
    }

    /**
     * 字段值减少
     * 
     * @access public
     * 
     * @param string $field  字段名
     * @param integer $step  减少值
     * 
     * @return boolean
     */
    public function setDec($field,$step=1) {
        return $this->setField($field,array('inc','-'.$step));
    }

    
    /**
     * 获取一条记录的某个字段值
     * 
     * @access public
     * 
     * @param string $field  字段名
     * @param string $spea  字段数据间隔符号
     * 
     * @return mixed
     */
    public function getField($field,$sepa=null) {
    	
        $options['field'] = $field;
        $options =  $this->_parseOptions($options);
        if(strpos($field,',')) { 
        	
        	/* 多字段 */
            if(is_numeric($sepa)) {            	
            	/* 限定数量 */
                $options['limit']   =   $sepa;
                /* 重置为null 返回数组 */
                $sepa=null;
            }
            
            
            $resultSet = $this->db->select($options);
            if(!empty($resultSet)) {
                $_field = explode(',', $field);
                $field  = array_keys($resultSet[0]);
                $key =  array_shift($field);
                $key2 = array_shift($field);
                $cols   =   array();
                $count  =   count($_field);
                foreach ($resultSet as $result){
                    $name   =  $result[$key];
                    if(2==$count) {
                        $cols[$name] = $result[$key2];
                    }else{
                        $cols[$name] = is_null($sepa)?$result:implode($sepa,$result);
                    }
                }
                return $cols;
            }
            
        } else {
        	
            /* 返回数据个数 */        	
            if(true !== $sepa) {
            
            	/* 当sepa指定为true的时候 返回所有数据 */
                $options['limit']   =   is_numeric($sepa)?$sepa:1;
                
            }            
            
            /* 查找一条记录 */
            $result = $this->db->find($options);
            
            if(!empty($result)) {
                if(1==$options['limit']) return reset($result[0]);
                foreach ($result as $val){
                    $array[] = $val[$field];
                }
                return $array;
            }
        }
        return null;
    }

    
    
    /**
     * 执行Mongo指令
     * 
     * @access public
     * 
     * @param array $command  指令
     * 
     * @return mixed
     */
    public function command($command) {
        return $this->db->command($command);
    }

    /**
     * 执行MongoCode
     * 
     * @access public
     * 
     * @param string $code  MongoCode
     * @param array $args   参数
     * 
     * @return mixed
     */
    public function mongoCode($code,$args=array()) {
        return $this->db->execute($code,$args);
    }

    /**
     * 数据库切换后回调方法
     */
    protected function _after_db() {
        /* 切换Collection */
        $this->db->switchCollection($this->getTableName(),$this->dbName?$this->dbName:config('db_name'));    
    }

    /**
     * 得到完整的数据表名 Mongo表名不带dbName
     * 
     * @access public
     * 
     * @return string
     */
    public function getTableName() {
    	
        if(empty($this->trueTableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if(!empty($this->tableName)) {
                $tableName .= $this->tableName;
            } else{
                $tableName .= parse_name($this->name);
            }
            $this->trueTableName    =   strtolower($tableName);
        }
        return $this->trueTableName;
    }
 }
