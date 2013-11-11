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
 * @version     $Id: SessionDb.class.php 516 2013-07-30 09:02:02Z wgw $
 *
 * Sessiony数据库管理类
 */
 class SessionDb extends SessionBase {

    private $model;

    public function init() {
        if($this->model === null){
        	$this->model = Helper::createBusiness('Sessions');
        }
    }

    public function start() {
		session_start();
	}

    public function open($path, $name) {
        return true;
    }

    public function close() {
        if(session_id() != '') {
            session_write_close();
        }
        return true;
    }

    public function destroy($id) {
        return $this->model->delete($id);
    }

    public function gc($maxLifetime) {
        $now = time();
        return $this->model->where("Expire<$now")->delete();
    }



    /**
     * 写入session
     *
     * @param string $id Session ID
     * @param string $data Session 序列号数据
     * @return boolean
     */
    public function write($id, $data) {
        $expire = time() + $this->getTimeout();
        $data = array(
            'ID' => $id,
            'Data' => $data,
            'Expire' => $expire,
        );
        if($this->model->find($id)) {
            return $this->model->save($data);
        }
        $data = array_merge($data, array(
            'IP' => get_client_ip(),
            'CreateTime' => time(),
            'IPSecurity' => 1,
        ));
        return $this->model->add($data) ? true : false;
    }

    /**
     * 读取session
     *
     * @param string $id
     * @return mixed
     */
    public function read($id) {
        $now = time();
        $data = $this->model->where("Expire>$now AND ID='$id'")->find();
        return $data ? $data['Data'] : false;
    }
 }
