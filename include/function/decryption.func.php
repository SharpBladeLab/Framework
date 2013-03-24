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
 * @version     $Id: decryption.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 加密解密函数库
 */ 
 
 /**
  * 加密函数
  *
  * @param strng   $txt 文本
  * @param boolean $key 键值
  *
  * @return  string 加密后的字符串
  */
 function jiami($txt, $key = null) {
 
	    if(empty($key)) $key = config('SECURE_CODE');
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=+";
		
	    $nh = rand(0,64);
	    $ch = $chars[$nh];
	    $mdKey = md5($key.$ch);
		
	    $mdKey = substr($mdKey,$nh%8, $nh%8+7);
	    $txt = base64_encode($txt);
		
	    $tmp = '';
	    $i=0;$j=0;$k = 0;
		
	    for ($i=0; $i<strlen($txt); $i++) {
	        $k = $k == strlen($mdKey) ? 0 : $k;
	        $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
	        $tmp .= $chars[$j];
	    }
	    return $ch.$tmp;
	}

 /**
  * 解密函数
  */
 function jiemi($txt,$key=null) { 
	
    if(empty($key)) $key = config('SECURE_CODE');
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=+";
	
    $ch = $txt[0];
    $nh = strpos($chars,$ch);
    $mdKey = md5($key.$ch);
	
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);
    $txt = substr($txt,1);
	
    $tmp = '';
    $i=0;$j=0; $k = 0;
	
    for ($i=0; $i<strlen($txt); $i++) {
        $k = $k == strlen($mdKey) ? 0 : $k;
        $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
        while ($j<0) $j+=64;
        $tmp .= $chars[$j];
    }
    return base64_decode($tmp);
 }
 
 
 function desencrypt($input,$key) {
 
	$size = mcrypt_get_block_size('des', 'ecb');
   	$input = pkcs5_pad($input, $size);
  
   	$td = mcrypt_module_open('des', '', 'ecb', '');
    $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	
    @mcrypt_generic_init($td, $key, $iv);
    $data = mcrypt_generic($td, $input);
	
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
	
    $data = base64_encode($data);
	
    return $data;
 }
	
 function desdecrypt($encrypted,$key) {
	$encrypted = base64_decode($encrypted);
	
	/* 使用MCRYPT_DES算法,cbc模式  */ 
   	$td = mcrypt_module_open('des','','ecb','');     
   	$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);   
   	$ks = mcrypt_enc_get_key_size($td);  

	/* 初始处理 */  
   	@mcrypt_generic_init($td, $key, $iv);    

	/* 解密 */
   	$decrypted = mdecrypt_generic($td, $encrypted);      

	/* 结束 */  
   	mcrypt_generic_deinit($td);  
	
   	mcrypt_module_close($td);      

    return pkcs5_unpad($decrypted);;
 }
 
 function pkcs5_pad ($text, $blocksize) {
   	$pad = $blocksize - (strlen($text) % $blocksize);
   	return $text . str_repeat(chr($pad), $pad);
 }
 
 function pkcs5_unpad($text) {
	$pad = ord($text{strlen($text)-1});
	if ($pad > strlen($text)) 
		return false;
	if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) 
		return false;
   	return substr($text, 0, -1 * $pad);
 } 	
