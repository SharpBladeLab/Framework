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
 * @version     $Id: Model.class.php 516 2013-07-30 09:02:02Z wgw $
 *
 * Model模型类 实现了ORM和Active Records模式
 */
 define('HAS_ONE',     1);
 define('BELONGS_TO',  2);
 define('HAS_MANY',    3);
 define('MANY_TO_MANY',4);
 class Model extends Framework {

    /* 操作状态 */
    const MODEL_INSERT			=   1;	// 插入模型数据
    const MODEL_UPDATE			=   2;	// 更新模型数据
    const MODEL_BOTH			=   3;	// 包含上面两种方式

    const EXISTS_VAILIDATE      =   0;	// 表单存在字段则验证
    const MUST_VALIDATE         =   1;	// 必须验证
    const VALUE_VAILIDATE       =   2;	// 表单值不为空则验证

    /* 当前使用的扩展模型 */
    private $_extModel=null;

    /* 当前数据库操作对象 */
    protected $db = null;

    /* 主键名称 */
    protected $pk  = 'id';

    /* 数据表前缀 */
    protected $tablePrefix  =   '';

    /* 数据表后缀 */
    protected $tableSuffix   =  '';

	/* 模型名称 */
    protected $name = '';

	/* 数据库名称 */
    protected $dbName  = '';

    /* 数据表名（不包含表前缀） */
    protected $tableName = '';

	/* 实际数据表名（包含表前缀） */
    protected $trueTableName ='';

    /* 最近错误信息 */
    protected $error = '';

    /* 字段信息 */
    protected $fields = array();

    /* 数据信息 */
    protected $data =   array();

    /* 查询表达式参数 */
    protected $options  =   array();

	/* 是否自动检测数据表字段信息 */
    protected $autoCheckFields   =   true;

	/* 字段映射定义 */
    protected $_map  = array();

    /**
	 * 自动验证设置(自动验证定义)
	 *
	 * 验证因子定义格式
     * array(field,rule,message,condition,type,when,params)
     *      0  字段
     *      1  函数或正则标实
     *      2  提示信息
     *      3  表单字段验证（0：表单存在字段则验证 1： 必须验证 2：表单值不为空则验证）
     *      4 type: 默认使用正则
     *                callback   调用方法进行验证
     *                function   使用函数进行验证
     *                confirm    验证两个字段是否相同
     *                in         验证是否在某个数组范围之内
     *                equal      验证是否等于某个值
     *                unique     验证某个值是否唯一
     *                regex      正则
     *       5 填充条件(1:插入模型数据 2:更新模型数据3:包含上面两种方式),
     *       6 调用函数时所用的参数
     *
	 * @var array
	 */
    protected $_validate = array();

    /**
	 * 自动填充设置(自动完成定义)
	 *  填充因子定义格式
     *  array('field','填充内容','填充条件','附加规则',[额外参数])
     *        0  字段
     *        1  填充内容(函数或方法)
     *        2  填充条件(1:插入模型数据  2:更新模型数据 3:包含上面两种方式)
     *        3  附加规则
     *                  function   使用函数进行填充 字段的值作为参数
     *                  callback   使用回调方法
     *                  field      用其它字段的值进行填充
     *                  string     默认作为字符串填充
     *        4  参数
     *
	 * @var array
	 */
    protected $_auto  = array();


    /**
     * 架构函数
     * 取得DB类的实例对象 字段检查
     *
     * @param string $name 模型名称
     *
     * @access public
     */
    public function __construct( $name = '' ) {

    	/* 模型初始化 */
        $this->_initialize();


        /* 获取模型名称 */
        if( !empty($name) ) {
            $this->name = $name;
        } elseif( empty($this->name) ) {
            $this->name = $this->getModelName();
        }


		if( !is_object($this->db) ) {
			/* 数据库初始化操作,获取数据库操作对象.当前模型有独立的数据库连接信息 */
			$this->db = DataBase::getInstance( empty($this->connection)? '' : $this->connection );
		}


        /* 设置表前缀 */
        $this->tablePrefix = $this->tablePrefix?$this->tablePrefix:config('DB_PREFIX');
        $this->tableSuffix = $this->tableSuffix?$this->tableSuffix:config('DB_SUFFIX');

		/* 字段检测 */
        if( !empty($this->name) && $this->autoCheckFields ) $this->_checkTableInfo();
    }

    /**
     * 自动检测数据表信息
     *
     * @access protected
     *
     * @return void
     */
    protected function _checkTableInfo() {

        /* 如果不是Model类 自动记录数据表信息 ;只在第一次执行记录 */
        if( empty($this->fields) ) {

            /* 如果数据表字段没有定义则自动获取 */
            if(config('DB_FIELDS_CACHE')) {
                $this->fields = Helper::createTempFile('table'.SEP.$this->getTableName());
                if(!$this->fields) $this->flush();

			} else {

                /* 每次都会读取数据表信息 */
                $this->flush();
            }
        }

    }

    /**
     * 获取字段信息并缓存
     *
     * @access public
     *
     * @return void
     */
    public function flush() {

        /* 缓存不存在则查询数据表信息 */
        $fields = $this->db->getFields($this->getTableName());
        $this->fields = array_keys($fields);
        $this->fields['_autoinc'] = false;

        foreach ($fields as $key=>$val) {

            /* 记录字段类型 */
            $type[$key] = $val['type'];

            if($val['primary']) {
                $this->fields['_pk'] = $key;
                if($val['autoinc']) $this->fields['_autoinc']   =   true;
            }
        }


        /* 记录字段类型信息 */
        if(config('DB_FIELDTYPE_CHECK')) $this->fields['_type'] =  $type;


        /* 增加缓存开关控制 */
        if(config('DB_FIELDS_CACHE')) {
            /* 永久缓存数据表信息 */
            Helper::createTempFile('table'.SEP.$this->getTableName(), $this->fields);
		}
    }



    /**
     * 动态切换扩展模型
     *
     * @access public
     *
     * @param string $type 模型类型名称
     * @param mixed $vars 要传入扩展模型的属性变量
     *
     * @return Model
     */
    public function switchModel($type,$vars=array()) {

    	/* 扩展模型类 */
    	$class = ucwords(strtolower($type)).'Model';
		if(!class_exists($class)) {
            Helper::createException($class.Helper::createLanguage('_MODEL_NOT_EXIST_'));
		}

		/* 实例化扩展模型 */
        $this->_extModel = new $class($this->name);
		if( !empty($vars) ) {


            /* 传入当前模型的属性到扩展模型 */
            foreach ($vars as $var) {
                $this->_extModel->setProperty($var,$this->$var);
			}
        }
        return $this->_extModel;
    }

    /**
     * 设置数据对象的值
     *
     * @access public
     *
     * @param string $name 名称
     * @param mixed $value 值
     *
     * @return void
     */
    public function __set($name,$value) {

        /* 设置数据对象属性 */
        $this->data[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     *
     * @access public
     *
     * @param string $name 名称
     *
     * @return mixed
     */
    public function __get($name) {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 检测数据对象的值
     *
     * @access public
     *
     * @param string $name 名称
     *
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * 销毁数据对象的值
     *
     * @access public
     *
     * @param string $name 名称
     *
     * @return void
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }

    /**
     * 利用__call方法实现一些特殊的Model方法
     *
     * @access public
     *
     * @param string $method 方法名称
     * @param array $args 调用参数
     *
     * @return mixed
     */
    public function __call($method, $args) {

        if(in_array(strtolower($method),array('field','table','where','order','limit','page','having','group','lock','distinct'),true)) {

			/* 连贯操作的实现 */
            $this->options[strtolower($method)] = $args[0];
            return $this;

        } elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)) {

			/* 统计查询的实现 */
            $field =  isset($args[0]) ? $args[0] : '*';
            return $this->getField(strtoupper($method).'('.$field.') AS out_'.$method);


        } elseif(strtolower(substr($method,0,5))=='getby') {

            /* 根据某个字段获取记录  */
        	Helper::createPlugin("String");
            $field = String::parse_name(substr($method ,5));

            $options['where'] =  $field.'=\''.$args[0].'\'';
            return $this->find($options);

        } else {
            Helper::createException( __CLASS__ . ':' . $method . Helper::createLanguage('_METHOD_NOT_EXIST_'));
            return;
        }
    }

    /**
	 * 回调方法 初始化模型
	 *
	 */
    protected function _initialize() {
    }

    /**
     * 对保存到数据库的数据进行处理
     *
     * @access protected
     *
     * @param mixed $data 要操作的数据
     *
     * @return boolean
     */
     protected function _facade($data) {

        /* 检查非数据字段 */
        if(!empty($this->fields)) {
            foreach ($data as $key=>$val){

                if(!in_array($key,$this->fields,true)){
                    unset($data[$key]);

                }elseif(config('DB_FIELDTYPE_CHECK') && is_scalar($val)) {

                    /* 字段类型检查 */
                    $fieldType = strtolower($this->fields['_type'][$key]);
                    if(false !== strpos($fieldType,'int')) {
                        $data[$key] = intval($val);
                    } elseif(false !== strpos($fieldType,'float') || false !== strpos($fieldType,'double')){
                        $data[$key] = floatval($val);
                    }
                }
            }
        }

        $this->_before_write($data);
        return $data;
     }

    /**
	 * 写入数据前的回调方法 包括新增和更新
	 *
	 * @param mixed  $data  数据
	 *
	 * @return mixed
	 */
    protected function _before_write(&$data) {}

    /**
     * 新增数据
     *
     * @access public
     *
     * @param mixed $data 数据
     * @param array $options 表达式
     *
     * @return mixed

     */
    public function add($data='',$options=array()) {
		if(empty($data)) {

            /* 没有传递数据，获取当前数据对象的值 */
            if( !empty($this->data) ) {
                $data = $this->data;
            } else {
                $this->error = Helper::createLanguage('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        /* 分析表达式 */
        $options =  $this->_parseOptions($options);

		/* 数据处理  */
        $data = $this->_facade($data);
        if( false === $this->_before_insert($data, $options)) {
            return false;
        }

        var_dump();
        /* 写入数据到数据库 */
        $result = $this->db->insert($data,$options);
        if(false !== $result ) {
            $insertId = $this->getLastInsID();

            if($insertId) {

                /* 自增主键返回插入ID */
                $data[$this->getPk()]  = $insertId;
                $this->_after_insert($data,$options);

                return $insertId;
            }
        }
        return $result;
    }

    /**
	 * 插入数据前的回调方法
	 *
	 * @param array $data    数据
	 * @param arrat $optuins 选项
	 *
	 * return void
	 */
    protected function _before_insert(&$data,$options) {}


    /**
	 * 插入成功后的回调方法
	 *
	 * @param array $data     数据
	 * @param array $options  选项
	 *
	 * @return void
	 */
    protected function _after_insert($data,$options) {}

    /**
     * 通过Select方式添加记录
     *
     * @access public
     *
     * @param string $fields 要插入的数据表字段名
     * @param string $table 要插入的数据表名
     * @param array $options 表达式
     *
     * @return boolean
     */
    public function selectAdd($fields='',$table='',$options=array()) {

		/* 分析表达式 */
        $options =  $this->_parseOptions($options);

		/* 写入数据到数据库 */
        if(false === $result = $this->db->selectInsert($fields?$fields:$options['field'],$table?$table:$this->getTableName(),$options)){

			/* 数据库插入操作失败 */
            $this->error = Helper::createLanguage('_OPERATION_WRONG_');
            return false;

        } else {

            /* 插入成功 */
            return $result;
        }
    }

    /**
     * 保存数据
     *
     * @access public
     *
     * @param mixed $data 数据
     * @param array $options 表达式
     *
     * @return boolean
     */
    public function save($data='',$options=array()) {
        if(empty($data)) {
            /* 没有传递数据，获取当前数据对象的值 */
            if(!empty($this->data)) {
                $data = $this->data;
            }else{
                $this->error = Helper::createLanguage('_DATA_TYPE_INVALID_');
                return false;
            }
        }

        /* 数据处理 */
        $data = $this->_facade($data);
        /* 分析表达式 */
        $options =  $this->_parseOptions($options);
        if(false === $this->_before_update($data,$options)) {
            return false;
        }

        if(!isset($options['where']) ) {

            /* 如果存在主键数据 则自动作为更新条件 */
            if(isset($data[$this->getPk()])) {
                $pk   =  $this->getPk();
                $options['where']  =  $pk.'=\''.$data[$pk].'\'';
                $pkValue = $data[$pk];
                unset($data[$pk]);

            } else {

                /* 如果没有任何更新条件则不执行 */
                $this->error = Helper::createLanguage('_OPERATION_WRONG_');
                return false;
            }
        }
        $result = $this->db->update($data,$options);
        if(false !== $result) {
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_update($data,$options);
        }
        return $result;
    }

	/**
	 * 更新数据前的回调方法
	 *
	 * @param array $data     数据
	 * @param array $options  选项
	 *
	 * @return void
	 */
    protected function _before_update(&$data,$options) {}

	/**
	 * 更新成功后的回调方法
	 *
	 * @param array $data     数据
	 * @param array $options  选项
	 *
	 * @return void
	 */
    protected function _after_update($data,$options) {}

    /**
     * 删除数据
     *
     * @access public
     *
     * @param  mixed $options 表达式
     *
     * @return mixed
     */
    public function delete($options=array()) {

        if(empty($options) && empty($this->options)) {

            /* 如果删除条件为空 则删除当前数据对象所对应的记录 */
            if(!empty($this->data) && isset($this->data[$this->getPk()]))
                return $this->delete($this->data[$this->getPk()]);
            else
                return false;
        }

        if(is_numeric($options)  || is_string($options)) {

            /* 根据主键删除记录 */
            $pk   =  $this->getPk();
            if(strpos($options,',')) {
                $where  =  $pk.' IN ('.$options.')';
            }else{
                $where  =  $pk.'=\''.$options.'\'';
                $pkValue = $options;
            }
            $options =  array();
            $options['where'] =  $where;
        }

        /* 分析表达式 */
        $options =  $this->_parseOptions($options);
        $result=    $this->db->delete($options);
        if(false !== $result) {
            $data = array();
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_delete($data,$options);
        }

        /* 返回删除记录个数 */
        return $result;
    }

	/**
	 * 删除成功后的回调方法
	 *
	 * @param array $data     数据
	 * @param array $options  选项
	 *
	 * @return void
	 */
    protected function _after_delete($data,$options) {}

    /**
     * 查询数据集
     *
     * @access public
     *
     * @param array $options 表达式参数
     *
     * @return mixed
     */
    public function select($options=array()) {

        if(is_string($options) || is_numeric($options)) {
            /* 根据主键查询 */
            $where   =  $this->getPk().' IN ('.$options.')';
            $options =  array();
            $options['where'] =  $where;
        }

        /* 分析表达式 */
        $options =  $this->_parseOptions($options);
        $resultSet = $this->db->select($options);

        if(false === $resultSet) {
            return false;
        }

        if(empty($resultSet)) {
			/* 查询结果为空 */
            return null;
        }

        $this->_after_select($resultSet,$options);
        return $resultSet;
    }

    /**
	 * 查询成功后的回调方法
	 *
	 * @param array $data     数据
	 * @param array $options  选项
	 *
	 * @return void
	 */
    protected function _after_select(&$resultSet,$options) {}

    public function findAll($options=array()) {
        return $this->select($options);
    }

    /**
     * 分析表达式
     *
     * @access private
     *
     * @param array $options 表达式参数
     *
     * @return array
     */
    private function _parseOptions($options) {

		if(is_array($options)) $options = array_merge($this->options,$options);


        /* 查询过后清空sql表达式组装 避免影响下次查询 */
        $this->options = array();

        /* 自动获取表名 */
        if(!isset($options['table'])) $options['table'] =$this->getTableName();

        /* 字段类型验证 */
        if(config('DB_FIELDTYPE_CHECK')) {
            if(isset($options['where']) && is_array($options['where'])) {

                /* 对数组查询条件进行字段类型检查 */
                foreach ($options['where'] as $key=>$val) {

					if(in_array($key,$this->fields,true) && is_scalar($val)){
                        $fieldType = strtolower($this->fields['_type'][$key]);
                        if(false !== strpos($fieldType,'int')) {
                            $options['where'][$key]   =  intval($val);
                        }elseif(false !== strpos($fieldType,'float') || false !== strpos($fieldType,'double')){
                            $options['where'][$key]   =  floatval($val);
                        }
                    }
                }


            }
        }

        /* 表达式过滤 */
        $this->_options_filter($options);
        return $options;
    }

    /**
	 * 表达式过滤回调方法
	 *
	 * @prarm mixed $options
	 */
    protected function _options_filter(&$options) {}

    /**
     * 查询数据
     *
     * @access public
     *
     * @param  mixed $options 表达式参数
     *
     * @return mixed
     */
     public function find($options=array()) {

		 if(is_numeric($options) || is_string($options)) {
             $where =  $this->getPk().'=\''.$options.'\'';
             $options = array();
             $options['where'] = $where;
         }

         /* 总是查找一条记录 */
        $options['limit'] = 1;

		/* 分析表达式 */
        $options =  $this->_parseOptions($options);
        $resultSet = $this->db->select($options);

        if(false === $resultSet) {
            return false;
        }

        if(empty($resultSet)) {
			/* 查询结果为空 */
            return null;
        }
        $this->data = $resultSet[0];
        $this->_after_find($this->data,$options);
        return $this->data;
     }

     /**
	  * 查询成功的回调方法
	  *
	  */
     protected function _after_find(&$result,$options) {}

    /**
	 * 设置记录的某个字段值(支持使用数据库字段和方法)
     *
     * @access public
     *
     * @param string|array $field  字段名
     * @param string|array $value  字段值
     * @param mixed $condition  条件
     *
     * @return boolean
     */
    public function setField($field, $value, $condition='') {

        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
		}

        $options['where'] =  $condition;

        if(is_array($field)) {
            foreach ($field as $key=>$val) {
                $data[$val]    = $value[$key];
			}
        } else{
            $data[$field]   =  $value;
        }

        return $this->save($data,$options);
    }

    /**
     * 字段值增长
     *
     * @access public
     *
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param integer $step  增长值
     *
     * @return boolean
     */
    public function setInc($field,$condition='',$step=1) {
        return $this->setField($field,array('exp',$field.'+'.$step),$condition);
    }

    /**
     * 字段值减少
     *
     * @access public
     *
     * @param string $field  字段名
     * @param mixed $condition  条件
     * @param integer $step  减少值
     *
     * @return boolean
     */
    public function setDec($field,$condition='',$step=1) {
        return $this->setField($field,array('exp',$field.'-'.$step),$condition);
    }

    /**
     * 获取一条记录的某个字段值
     *
     * @access public
     *
     * @param string $field     字段名
     * @param mixed  $condition 查询条件
     * @param string $spea      字段数据间隔符号
     *
     * @return mixed
     */
    public function getField($field, $condition='', $sepa=' ') {
        if(empty($condition) && isset($this->options['where']))
            $condition   =  $this->options['where'];

        $options['where'] =  $condition;
        $options['field']    =  $field;
        $options =  $this->_parseOptions($options);

        if(strpos($field,',')) {

			/* 多字段 */
            $resultSet = $this->db->select($options);
            if(!empty($resultSet)) {
                $field  =   explode(',',$field);
                $key =  array_shift($field);
                $cols   =   array();
                foreach ($resultSet as $result){
                    $name   = $result[$key];
                    $cols[$name] =  '';
                    foreach ($field as $val)
                        $cols[$name] .=  $result[$val].$sepa;
                    $cols[$name]  = substr($cols[$name],0,-strlen($sepa));
                }
                return $cols;
            }
        } else{

			/* 查找一条记录 */
            $options['limit'] = 1;
            $result = $this->db->select($options);

			if(!empty($result)) {
                return reset($result[0]);
            }
        }
        return null;
    }

    /**
     * 创建数据对象 但不保存到数据库
     *
     * @access public
     *
     * @param mixed  $data  创建数据
     * @param string $type  状态
     *
     * @return mixed
     */
     public function create($data='', $type='') {

        /* 如果没有传值默认取POST数据 */
        if(empty($data)) {
            $data = $_POST;
        }elseif(is_object($data)){
            $data = get_object_vars($data);
        }elseif(!is_array($data)) {
            $this->error = Helper::createLanguage('_DATA_TYPE_INVALID_');
            return false;
        }


        $table=$this->getTableName();


		/* 状态 */
        $type = $type ? $type : (!empty($data[$this->getPk()])? self::MODEL_UPDATE : self::MODEL_INSERT);

        /* 表单令牌验证 */
        if( config('TOKEN_ON') && !$this->autoCheckToken($data)) {
            $this->error = Helper::createLanguage('_TOKEN_ERROR_');
            return false;
        }


        /* 数据自动验证 */
        if(!$this->autoValidation($data, $type)) {
        	return false;
        }


        /* 检查字段映射 */
        if(!empty($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$key])) {
                    $data[$val] = $data[$key];
                    unset($data[$key]);
                }
            }
        }

        /* 验证完成生成数据对象 */
        $vo = array();
        foreach ($this->fields as $key=>$name){
        	if(substr($key,0,1)=='_') continue;
            $val = isset($data[$name]) ? $data[$name]: null;
            /* 保证赋值有效  */
            if(!is_null($val)) {
            	/* 过滤 */
            	$vo[$name] = (MAGIC_QUOTES_GPC && is_string($val))?  stripslashes($val) : $val;
            }
        }
        /* 创建完成对数据进行自动处理 */
        $this->autoOperation($vo,$type);

        $this->data = $vo;
        return $vo;
     }



    /* 自动表单令牌验证 */
    public function autoCheckToken($data) {

        $name  = config('TOKEN_NAME');

        if( isset($_SESSION[$name]) ) {

            /* 当前需要令牌验证 */
            if( empty($data[$name]) || $_SESSION[$name] != $data[$name]) {

                /* 非法提交 */
                return false;
            }

            /* 验证完成销毁session */
            unset($_SESSION[$name]);
        }
        return true;
    }

    /**
     * 使用正则验证数据
     *
     * @access public
     *
     * @param string $value  要验证的数据
     * @param string $rule   验证规则
     *
     * @return boolean
     */
    public function regex($value, $rule) {

        $validate = array(
            'require'     => '/.+/',
            'email'       => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'         => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            'currency'    => '/^\d+(\.\d+)?$/',
            'number'      => '/\d+$/',
            'zip'         => '/^[1-9]\d{5}$/',
        	'phone'       => '/^13[0-9]{9}|15[0-9]{9}|18[0-9]{9}|14[0-9]{9}$/',
            'integer'     => '/^[-\+]?\d+$/',
            'double'      => '/^[-\+]?\d+(\.\d+)?$/',
            'english'     => '/^[A-Za-z]+$/',
            'chineseGB'   => '/^['.chr(0xa1).'-'.chr(0xff).']+$/',
        	'chineseUTF8' => '/^[\x{4e00}-\x{9fa5}]+$/',
        	'qq'          => '/^[1-9]\d{4,8}$/',
        	'password'    => '/^[a-zA-Z0-9]{6,20}$/i',
        	'name'        => '/^([\xa1-\xff]{1,8}|[A-Za-z0-9]{2,20})$/',
        	'string'      => "/^[^`%&()=;:'\"/\\]*$/",
        	'int'         => "/^-?[1-9]+[0-9]*$/",
        	'time'        => "/^(20|21|22|23|1[0-9]{1}|0?[0-9]{1})(:[0-5]?[0-9]{1})(:(60)|([0-5]?[0-9]{1}))?$/",
        	'card'        => "/^[0-9]{15}([0-9]{2}[A-Za-z0-9])?$/",
        	'post'        => "/^[0-9]{6}$/",
        );

        /* 检查是否有内置的正则表达式 */
        if(isset($validate[strtolower($rule)]))
            $rule = $validate[strtolower($rule)];
        if( preg_match($rule, $value)===1 ) {
        	return true;
        } else {
        	return false;
        }
    }

    /**
     * 自动表单处理
     *
     * @access public
     *
     * @param array  $data  创建数据
     * @param string $type  创建类型
     *
     * @return mixed
     */
    private function autoOperation(&$data, $type) {

		/* 自动填充 */
        if(!empty($this->_auto)) {
            foreach ($this->_auto as $auto){

                // 填充因子定义格式
                // array('field','填充内容','填充条件','附加规则',[额外参数])
                // 0  字段
                // 1  填充内容(函数或方法)
                // 2  填充条件(1:插入模型数据 2:更新模型数据3:包含上面两种方式)
                // 3  附加规则
                //         function   使用函数进行填充 字段的值作为参数
                //         callback   使用回调方法
                //         field      用其它字段的值进行填充
                //         string     默认作为字符串填充
                // 4  参数
                //

				if(empty($auto[2])) {
					/* 默认为新增的时候自动填充 */
					$auto[2] = self::MODEL_INSERT;
				}

			   if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {

			   		switch($auto[3]) {
                        case 'function':
							/* 使用函数进行填充 字段的值作为参数 */
                        case 'callback':
							/* 使用回调方法 */
                            $args = isset($auto[4])?$auto[4]:array();
                            if(isset($data[$auto[0]])) {
                                array_unshift($args,$data[$auto[0]]);
                            }
                            if('function'==$auto[3]) {
                                $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                            }else{
                                $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                            }
                            break;

                        case 'field':
							/* 用其它字段的值进行填充 */
                            $data[$auto[0]] = $data[$auto[1]];
                            break;

                        case 'string':
                        default:
							/* 默认作为字符串填充 */
                            $data[$auto[0]] = $auto[1];
                    }
                    if(false === $data[$auto[0]] )   unset($data[$auto[0]]);
                }
            }
        }
        return $data;
    }

    /**
     * 自动表单验证
     *
     * @access public
     *
     * @param array $data 创建数据
     * @param string $type 创建类型
     *
     * @return boolean
     */
    private function autoValidation($data,$type) {

        /* 属性验证 */
        if(!empty($this->_validate)) {

            /* 如果设置了数据自动验证  则进行数据验证 重置验证错误信息 */
            foreach($this->_validate as $key=>$val) {

                // 验证因子定义格式
                // array(field,rule,message,condition,type,when,params)
                //  0 字段
                //	1  函数或正则标实
                //	2  提示信息
                //	3  表单字段验证（0：表单存在字段则验证1： 必须验证2：表单值不为空则验证）
                //	4 type: 默认使用正则
                //         callback   调用方法进行验证
                //         function   使用函数进行验证
                //         confirm    验证两个字段是否相同
                //         in         验证是否在某个数组范围之内
                //         equal      验证是否等于某个值
                //         unique     验证某个值是否唯一
                //         regex      正则
                //  5 填充条件(1:插入模型数据 2:更新模型数据3:包含上面两种方式),
                //  6 调用函数时所用的参数


                /* 判断是否需要执行验证 */
                if( empty($val[5]) || $val[5]== self::MODEL_BOTH || $val[5]== $type ) {

                    if(0==strpos($val[2],'{%') && strpos($val[2],'}')) {
                        /* 支持提示信息的多语言 使用 {%语言定义} 方式 */
                        $val[2]  =  Helper::createLanguage(substr($val[2],2,-1));
					}

                    $val[3] = isset($val[3])?$val[3]:self::EXISTS_VAILIDATE;
                    $val[4] = isset($val[4])?$val[4]:'regex';

                    /* 判断验证条件 */
                    switch($val[3]) {

                        case self::MUST_VALIDATE:
							/* 必须验证 不管表单是否有设置该字段 */
                            if(false === $this->_validationField($data,$val)){
                                $this->error = $val[2];
                                return false;
                            }
                            break;

                        case self::VALUE_VAILIDATE:
							/* 值不为空的时候才验证 */
                            if('' != trim($data[$val[0]])){
                                if(false === $this->_validationField($data,$val)){
                                    $this->error = $val[2];
                                    return false;
                                }
                            }
                            break;


                        default:
							/* 默认表单存在该字段就验证 */
                            if(isset($data[$val[0]])){
                                if(false === $this->_validationField($data,$val)){
                                    $this->error = $val[2];
                                    return false;
                                }
                            }


                    }

                }
            }
        }

        return true;
    }

    /**
     * 根据验证因子验证字段
     *
     * @access public
     *
     * @param array $data 创建数据
     * @param string $val 验证规则
     *
     * @return boolean
     */
    private function _validationField($data,$val) {

        switch($val[4]) {
            case 'function':
				/* 使用函数进行验证 */
            case 'callback':
				/* 调用方法进行验证 */
                $args = isset($val[6])?$val[6]:array();
                array_unshift($args,$data[$val[0]]);

                if('function'==$val[4]) {
                    return call_user_func_array($val[1], $args);
                } else {
                    return call_user_func_array(array(&$this, $val[1]), $args);
                }

            case 'confirm':
				/* 验证两个字段是否相同 */
                return $data[$val[0]] == $data[$val[1]];

            case 'in':
				/* 验证是否在某个数组范围之内 */
                return in_array($data[$val[0]] ,$val[1]);

            case 'equal':
				/* 验证是否等于某个值 */
                return $data[$val[0]] == $val[1];

            case 'unique':
				/* 验证某个值是否唯一 */
                if(is_string($val[0]) && strpos($val[0],',')) {
                    $val[0]  =  explode(',',$val[0]);
				}

                $map = array();

                if(is_array($val[0])) {
                    /* 支持多个字段验证 */
                    foreach ($val[0] as $field)
                        $map[$field]   =  $data[$field];
                } else{
                    $map[$val[0]] = $data[$val[0]];
                }

                if($this->where($map)->find()) {
                    return false;
				}
                break;

            case 'regex':
            default:
				/* 默认使用正则验证 可以使用验证类中定义的验证名称.检查附加规则 */
            	return $this->regex($data[$val[0]], $val[1]);
        }
        return true;
    }

    /**
     * SQL查询
     *
     * @access public
     *
     * @param mixed $sql  SQL指令
     *
     * @return mixed
     */
    public function query($sql)
    {
        if(!empty($sql)) {
            if(strpos($sql, '__TABLE__'))
                $sql = str_replace('__TABLE__', $this->getTableName(),$sql);
            return $this->db->query($sql);
        }else{
            return false;
        }
    }

    /**
     * 执行SQL语句
     *
     * @access public
     *
     * @param string $sql  SQL指令
     *
     * @return false | integer
     */
    public function execute($sql)
    {
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__'))
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            return $this->db->execute($sql);
        }else {
            return false;
        }
    }

    /**
     * 得到当前的数据对象名称
     *
     * @access public
     *
     * @return string
     */
    public function getModelName() {
        if( empty($this->name) ) {
            $this->name = substr(get_class($this),0,-5);
        }
        return $this->name;
    }



    /**
     * 得到完整的数据表名
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
            } else {
            	Helper::createPlugin("String");
                $tableName .= String::parse_name($this->name);
            }

            $tableName .= !empty($this->tableSuffix) ? $this->tableSuffix : '';
            if(!empty($this->dbName))
                $tableName =  $this->dbName.'.'.$tableName;
            $this->trueTableName = strtolower($tableName);
        }
        return $this->trueTableName;
    }

    /**
     * 启动事务
     *
     * @access public
     *
     * @return void
     */
    public function startTrans()
    {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     * 提交事务
     *
     * @access public
     *
     * @return boolean
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * 事务回滚
     *
     * @access public
     *
     * @return boolean
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * 返回模型的错误信息
     *
     * @access public
     *
     * @return string
     */
    public function getError() {
    	if( empty($this->error) ) {
    		return "操作失败！";
    	} else {
    		return $this->error;
    	}
    }


    /**
     * 返回数据库的错误信息
     *
     * @access public
     *
     * @return string
     */
    public function getDbError() {
        return $this->db->getError();
    }

    /**
     * 返回最后插入的ID
     *
     * @access public
     *
     * @return string
     */
    public function getLastInsID() {
        return $this->db->lastInsID;
    }

    /**
     * 返回最后执行的sql语句
     *
     * @access public
     *
     * @return string
     */
    public function getLastSql() {
        return $this->db->getLastSql();
    }

    /**
     * 获取主键名称
     *
     * @access public
     *
     * @return string
     */
    public function getPk() {
        return isset($this->fields['_pk'])?$this->fields['_pk']:$this->pk;
    }

    /**
     * 获取数据表字段信息
     *
     * @access public
     *
     * @return array
     */
    public function getDbFields(){
        return $this->fields;
    }

    /**
     * 设置数据对象值
     *
     * @access public
     *
     * @param mixed $data 数据
     *
     * @return Model
     */
    public function data($data){
        if(is_object($data)){
            $data = get_object_vars($data);
        }elseif(!is_array($data)){
            Helper::createException(Helper::createLanguage('_DATA_TYPE_INVALID_'));
        }
        $this->data = $data;
        return $this;
    }

    /**
     * 查询SQL组装 join
     *
     * @access public
     *
     * @param mixed $join
     *
     * @return Model
     */
    public function join($join) {
        if(is_array($join))
            $this->options['join'] =  $join;
        else
            $this->options['join'][]  =   $join;
        return $this;
    }

    /**
     * 设置模型的属性值
     *
     * @access public
     *
     * @param string $name 名称
     * @param mixed $value 值
     *
     * @return Model
     */
    public function setProperty($name,$value) {
        if(property_exists($this,$name))
            $this->$name = $value;
        return $this;
    }

	/**
     * 统计满足条件的记录个数
     *
     * @access public
     *
     * @param mixed $condition  条件
     * @param string $field     要统计的字段 默认为*
     *
     * @return integer
     */
	public function count($options = array(), $field='1') {

		$fields = 'count('.$field.') as count';

		/* 总是查找一条记录 */
        $options['limit'] = 1;
		$options['field'] = $fields;

        /* 分析表达式 */
        $options =  $this->_parseOptions($options);
        $result = $this->db->select($options);
		if($result) {
            return $result[0]['count'];
		}else{
            return false;
        }
	}

    /**
     * 查询数据
     *
     * @access public
     *
     * @param mixed $options 表达式参数
     * @param mixed $pageopt 分页参数
     *
     * @return mixed
     */
     public function findPage($pageopt, $count = false, $options = array() ) {

		/* 分析表达式 */
        $options =  $this->_parseOptions($options);

		/* 如果没有传入总数，则自动根据条件进行统计 */
		if( $count === false) {

			/* 查询总数 */
			$count_options = $options;
			$count_options['limit'] = 1;
			$count_options['field'] = 'count(1) as count';

			/*去掉统计时的排序提高效率 */
			unset($count_options['order']);

			$result = $this->db->select($count_options);
			$count	= $result[0]['count'];
			unset($result);
			unset($count_options);
		}


		if($count > 0) {

			/* 解析分页参数 */
			if( is_numeric($pageopt) ) {
				$pagesize = intval($pageopt);
			} else {
				$pagesize = intval(config('PAGE_LISTROWS'));
			}

			/* 查询数据 */
			$params=array('total'=>$count, 'rows'=>$pagesize, 'param'=>'');
			$page = Helper::createPlugin('Page', $params);
			$options['limit'] = $page->firstRow.','.$page->listRows;
			$resultSet = $this->select($options);
			if( $resultSet ) {
				$this->dataList = $resultSet;
			} else {
				$resultSet	=	'';
			}

			/* 输出控制 */
			$output['count']	  =	$count;
			$output['totalPages'] =	$page->totalPages;
			$output['totalRows']  =	$page->totalRows;
			$output['nowPage']	  =	$page->nowPage;
			$output['html']		  =	$page->show();
			$output['data']		  =	$resultSet;
			unset($resultSet);
			unset($p);
			unset($count);

		} else {
			$output['count']		=	0;
			$output['totalPages']	=	0;
			$output['totalRows']	=	0;
			$output['nowPage']		=	1;
			$output['html']			=	'';
			$output['data']			=	'';
		}

		/* 输出数据 */
		return $output;
     }

	public function findPageBySql($sql, $count = null, $pagesize = null) {
		//if (strtoupper(substr($sql, 0, 6)) !== 'SELECT' ) return false;

		/* 计算结果总数 */
		if ( !is_numeric($count) ) {
			$count_sql 				= explode(' FROM ', $sql);
			if (count($count_sql) != 2) return false;
			$count_sql 				= 'SELECT count(*) AS count FROM ' . $count_sql[1];
			$count	   				= $this->db->query($count_sql);
			$count					= $count[0]['count'];
		}

		$count = intval($count);

		/* 如果查询总数大于0 */
		if ($count > 0) {
			/* 解析分页参数 */
			$pagesize 				=	is_numeric($pagesize) ? intval($pagesize) : intval(config('LIST_NUMBERS'));
			//$p		  			=	new Page($count,$pagesize);
			$p		  				= Helper::createPlugin('Page', $count);
			/* 查询数据 */
			$limit					=	$p->firstRow.','.$p->listRows;
			$resultSet				=	$this->query($sql . ' LIMIT ' . $limit);
			if($resultSet){
				$this->dataList = $resultSet;
			}else{
				$resultSet			=	'';
			}

			/* 输出控制 */
			$output['count']		=	$count;
			$output['totalPages']	=	$p->totalPages;
			$output['totalRows']	=	$p->totalRows;
			$output['nowPage']		=	$p->nowPage;
			$output['html']			=	$p->show();
			$output['data']			=	$resultSet;

			unset($resultSet);
			unset($p);
			unset($count);
		} else {
			$output['count']		=	0;
			$output['totalPages']	=	0;
			$output['totalRows']	=	0;
			$output['nowPage']		=	1;
			$output['html']			=	'';
			$output['data']			=	'';
		}
        return $output;
    }

}
