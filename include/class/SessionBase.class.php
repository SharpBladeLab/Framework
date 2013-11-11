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
 * @version     $Id: SessionBase.class.php 516 2013-07-30 09:02:02Z wgw $
 *
 * Session管理类
 */
abstract class SessionBase extends Framework
{

	public function __construct() {
		
		if( $this->_isOpenSession()) {
			$this->init();
			session_set_save_handler(
				array($this, 'open'),
				array($this, 'close'),
				array($this, 'read'),
				array($this, 'write'),
				array($this, 'destroy'),
				array($this, 'gc')
			);
			
			config('SESSION_AUTO_START') && $this->start();
			register_shutdown_function(array($this, 'close'));
		}
	}
	public function setTimeout($value) {
		return ini_set('session.gc_maxlifetime', $value);
	}
	public function getTimeout() {
		return (int)ini_get('session.gc_maxlifetime');;
	}

	// 初始
	abstract public function init();
	// 开始
	abstract public function start();
	// 打开
	abstract public function open($path, $name);
	// 关闭
	abstract public function close();
	// 删除
	abstract public function destroy($id);
	// 回收
	abstract public function gc($maxLifetime);
	// 写入
	abstract public function write($id, $data);
	// 读取
	abstract public function read($id);


	/**
     * 设置或者获取当前Session (name)
     *
     * @param string $name session名称
     *
     * @return string 返回之前的Session name
     */
    static function name($name = null) {
        return isset($name) ? session_name($name) : session_name();
    }

    /**
     * 解析Session数据内容
     *
     * @param string $name session数据内容
     *
     * @static
     * @access public
     */
    static function parse($string) {

		$arr = explode('|', $string);
		foreach ($arr as $key => $val) {

			if ($key==0) $k = $val;
			if (empty($arr[$key+1]) ) break;

			/* 数组形式存储内容  */
			$temp = explode('}', $arr[$key+1]);
			if ( count($temp)==2 ) {
				$res[$k] = unserialize( $temp[0].'}' );
				$k = $temp[1];
			} else {

				/* 键值存储内容   */
				$temp = explode(';', $arr[$key+1]);
				if ( count($temp)>=2 ) {
					$res[$k] = unserialize( $temp[0].';' );
					$k = $temp[1];
				}
			}
		}

		/* 重新设置会话信息  */
		foreach ($res as $key=>$val) $_SESSION[$key] = $val;
    }

	/**
	 * 是否开启 Sesioon
	 */
	private function _isOpenSession(){
		$data = config('SESSION_NOT_SAVE');
		if( $data[strtolower(APP_NAME).'/'. strtolower(CONTROLLER_NAME).'/'.strtolower(ACTION_NAME)] === true ||
		$data[strtolower(APP_NAME).'/'. strtolower(CONTROLLER_NAME).'/*'] === true ||
		$data[strtolower(APP_NAME).'/*/*'] === true) {
			return false;
		}
		return true;
	}
}
