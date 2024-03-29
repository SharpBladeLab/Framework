<?php if(!defined('IN_SYS')) exit();
/**
 * Google短网址服务
 *
 * Project: Tiwer Developer Framework
 * This is NOT a freeware, use is subject to license terms!
 * 
 * $Id: Short.class.php 524 2013-07-31 02:26:10Z wgw $
 *
 * Copyright (C) 2007-2011 Tiwer Developer Framework. All Rights Reserved.
 */
 class Short extends Plugin
 { 
 	
 	/**
 	 * 构造函数
 	 */
    public function __construct($data) {
    }
	function getShort($url) {
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, 'http://goo.gl/api/url');   //goo.gl api url
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_POST, 1); 
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'user=toolbar@google.com&url='.urlencode( $url ).'&auth_token='.$this->googlToken($url)); 
		$saida = curl_exec($curl); 
		curl_close($curl);		
		if($saida) {
			$json = json_decode($saida);
		    return $json->short_url;  
		}
	}
	
	
	//goo.gl token
	function googlToken($b){
	    $i = $this->tke($b);
	    $i = $i >> 2 & 1073741823;
	    $i = $i >> 4 & 67108800 | $i & 63;
	    $i = $i >> 4 & 4193280 | $i & 1023;
	    $i = $i >> 4 & 245760 | $i & 16383;
	    $j = "7";
	    $h = $this->tkf($b);
	    $k = ($i >> 2 & 15) << 4 | $h & 15;
	    $k |= ($i >> 6 & 15) << 12 | ($h >> 8 & 15) << 8;
	    $k |= ($i >> 10 & 15) << 20 | ($h >> 16 & 15) << 16;
	    $k |= ($i >> 14 & 15) << 28 | ($h >> 24 & 15) << 24;
	    $j .= $this->tkd($k);
	    return $j;
	}
	function tkc() {
	    $l = 0;
	    foreach (func_get_args() as $val) {
	        $val &= 4294967295;
	        $val += $val > 2147483647 ? -4294967296 : ($val < -2147483647 ? 4294967296 : 0);
	        $l   += $val;
	        $l   += $l > 2147483647 ? -4294967296 : ($l < -2147483647 ? 4294967296 : 0);
	    }
	    return $l;
	}	
	function tkd($l){
	    $l = $l > 0 ? $l : $l + 4294967296;
	    $m = "$l";
	    $o = 0;
	    $n = false;
	    for($p = strlen($m) - 1; $p >= 0; --$p){
	        $q = $m[$p];
	        if($n){
	            $q *= 2;
	            $o += floor($q / 10) + $q % 10;
	        } else {
	            $o += $q;
	        }
	        $n = !$n;
	    }
	    $m = $o % 10;
	    $o = 0;
	    if($m != 0){
	        $o = 10 - $m;
	        if(strlen($l) % 2 == 1){
	            if ($o % 2 == 1){
	                $o += 9;
	            }
	            $o /= 2;
	        }
	    }
	    return "$o$l";
	}
	function tke($l){
	    $m = 5381;
	    for($o = 0; $o < strlen($l); $o++){
	        $m = $this->tkc($m << 5, $m, ord($l[$o]));
	    }
	    return $m;
	}
	function tkf($l){
	    $m = 0;
	    for($o = 0; $o < strlen($l); $o++){
	        $m = $this->tkc(ord($l[$o]), $m << 6, $m << 16, -$m);
	    }
	    return $m;
	}
}
