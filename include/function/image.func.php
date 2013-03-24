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
 * @version     $Id: image.func.php 5 2012-11-23 02:56:13Z wgw $
 * @link        http://www.tiwer.cn
 *
 * 图片函数库
 */
 
/**
 * bmp图像处理兼容函数
 *
 * BMP不是网络图像文件格式，但应用也比较广泛，最后放一个imagecreatefrombmp函数做备份吧，
 * 此函数可以用，但BMP文件一旦稍大就极其耗内存
 *
 * @param string $fname 文件名称
 *
 * @return mixed 
 */
 function imagecreatefrombmp( $fname ) {

	/* 打开文件 */
	$buf = @file_get_contents($fname);

	/* 文件大小小于54返回 */
	if( strlen($buf)<54 ) return false;

	/* 从二进制字符串打开数据,获取文件头。19778是位图的文件类型编号：0x4D42 */
	$file_header = unpack("sbfType/LbfSize/sbfReserved1/sbfReserved2/LbfOffBits", substr($buf,0,14));
	if( $file_header["bfType"] != 19778 ) return false;
	
	/* 位图信息头 */
	$info_header=unpack("LbiSize/lbiWidth/lbiHeight/sbiPlanes/sbiBitCountLbiCompression/LbiSizeImage/lbiXPelsPerMeter/lbiYPelsPerMeter/LbiClrUsed/LbiClrImportant", substr($buf,14,40));
	
	/* 不支持2色位图   */
	if($info_header["biBitCountLbiCompression"]==2) return false;
	
	$line_len=round($info_header["biWidth"]* $info_header["biBitCountLbiCompression"]/8);
	$x=$line_len%4;
	if($x>0) $line_len+=4-$x;

	/*  根据bmp图片的高、宽新建一个真彩色图像 */
	$img=imagecreatetruecolor($info_header["biWidth"], $info_header["biHeight"]);
	
	/* bmp图片的位数处理 */
	switch($info_header["biBitCountLbiCompression"]) {
		
		/* 4位位图  */
		case 4:
			$colorset=unpack("L*",substr($buf,54,64));
			
			for($y=0;$y<$info_header["biHeight"];$y++) {
			
				$colors=array();
				$y_pos=$y*$line_len+$file_header["bfOffBits"];
				
				for($x=0;$x<$info_header["biWidth"];$x++){
					if($x%2) {
						$colors[]=$colorset[(ord($buf[$y_pos+($x+1)/2])&0xf)+1];
					} else {
						$colors[]=$colorset[((ord($buf[$y_pos+$x/2+1])>>4)&0xf)+1];
					}
				}
				imagesetstyle($img,$colors);
				imageline($img,0,$info_header["biHeight"]-$y-1,$info_header["biWidth"],$info_header["biHeight"]-$y-1,IMG_COLOR_STYLED);
			}
			break;
		
		/* 8位位图  */
		case 8:
			$colorset=unpack("L*",substr($buf,54,1024));
			for($y=0;$y<$info_header["biHeight"];$y++){
			$colors=array();
			$y_pos=$y*$line_len+$file_header["bfOffBits"];
			for($x=0;$x<$info_header["biWidth"];$x++){
			$colors[]=$colorset[ord($buf[$y_pos+$x])+1];
			}
			imagesetstyle($img,$colors);
			imageline($img,0,$info_header["biHeight"]-$y-1,$info_header["biWidth"],$info_header["biHeight"]-$y-1,IMG_COLOR_STYLED);
			}
			break;
			
		/* 16位位图  */
		case 16:
			for($y=0;$y<$info_header["biHeight"];$y++){
			$colors=array();
			$y_pos=$y*$line_len+$file_header["bfOffBits"];
			for($x=0;$x<$info_header["biWidth"];$x++){
			$i=$x*2;
			$color=ord($buf[$y_pos+$i])|(ord($buf[$y_pos+$i+1])<<8);
			$colors[]=imagecolorallocate($img,(($color>>10)&0x1f)*0xff/0x1f,(($color>>5)&0x1f)*0xff/0x1f,($color&0x1f)*0xff/0x1f);
			}
			imagesetstyle($img,$colors);
			imageline($img,0,$info_header["biHeight"]-$y-1,$info_header["biWidth"],$info_header["biHeight"]-$y-1,IMG_COLOR_STYLED);
			}
			break;
		
		/* 24位位图  */
		case 24:
			for($y=0;$y<$info_header["biHeight"];$y++){
			$colors=array();
			$y_pos=$y*$line_len+$file_header["bfOffBits"];
			for($x=0;$x<$info_header["biWidth"];$x++){
			$i=$x*3;
			$colors[]=imagecolorallocate($img,ord($buf[$y_pos+$i+2]),ord($buf[$y_pos+$i+1]),ord($buf[$y_pos+$i]));
			}
			imagesetstyle($img,$colors);
			imageline($img,0,$info_header["biHeight"]-$y-1,$info_header["biWidth"],$info_header["biHeight"]-$y-1,IMG_COLOR_STYLED);
			}
			break;
			default:
			return false;
			break;
	}
	
	/* 返回图片 */
	return $img;
}

/**
 * bmp图像处理兼容函数
 *
 */ 
 function imagebmp(&$im, $filename = '', $bit = 8, $compression = 0) {
 
    if (!in_array($bit, array(1, 4, 8, 16, 24, 32))) {
        $bit = 8;
    } else if ($bit == 32) {
        $bit = 24;
    }

    $bits = pow(2, $bit);

    /* 调整调色板 */
    imagetruecolortopalette($im, true, $bits);
    $width = imagesx($im);
    $height = imagesy($im);
    $colors_num = imagecolorstotal($im);

    if ($bit <= 8) {
	
        /* 颜色索引 */
        $rgb_quad = '';
        for ($i = 0; $i < $colors_num; $i ++) {
            $colors = imagecolorsforindex($im, $i);
            $rgb_quad .= chr($colors['blue']) . chr($colors['green']) . chr($colors['red']) . "\0";      
		}

        /* 位图数据 */
        $bmp_data = '';

        /* 非压缩 */
        if ( $compression == 0 || $bit < 8) {
            if (!in_array($bit, array(1, 4, 8))) {
                $bit = 8;
            }
			
            $compression = 0;

            /* 每行字节数必须为4的倍数，补齐。 */
            $extra = '';
            $padding = 4 - ceil($width / (8 / $bit)) % 4;
            if ($padding % 4 != 0) {
                $extra = str_repeat("\0", $padding);
            }

            for ($j = $height - 1; $j >= 0; $j --) {
                $i = 0;
                while ($i < $width) {
                    $bin = 0;
                    $limit = $width - $i < 8 / $bit ? (8 / $bit - $width + $i) * $bit : 0;

                    for ($k = 8 - $bit; $k >= $limit; $k -= $bit) {
                        $index = imagecolorat($im, $i, $j);
                        $bin |= $index << $k;
                        $i ++;
                    }
                    $bmp_data .= chr($bin);
                }
                $bmp_data .= $extra;
            }
        }
		
        /*  RLE8 压缩 */
        else if ($compression == 1 && $bit == 8)
        {
            for ($j = $height - 1; $j >= 0; $j --)
            {
                $last_index = "\0";
                $same_num   = 0;
                for ($i = 0; $i <= $width; $i ++)
                {
                    $index = imagecolorat($im, $i, $j);
                    if ($index !== $last_index || $same_num > 255)
                    {
                        if ($same_num != 0)
                        {
                            $bmp_data .= chr($same_num) . chr($last_index);
                        }

                        $last_index = $index;
                        $same_num = 1;
                    } else {
                        $same_num ++;
                    }
                }
                $bmp_data .= "\0\0";
            }
            $bmp_data .= "\0\1";
        }
        $size_quad = strlen($rgb_quad);
        $size_data = strlen($bmp_data);
    } else {
        /* 每行字节数必须为4的倍数，补齐。 */
        $extra = '';
        $padding = 4 - ($width * ($bit / 8)) % 4;
        if ($padding % 4 != 0) {
            $extra = str_repeat("\0", $padding);
        }
		
        /* 位图数据 */
        $bmp_data = '';

        for ($j = $height - 1; $j >= 0; $j --) {
            for ($i = 0; $i < $width; $i ++) {
                $index = imagecolorat($im, $i, $j);
                $colors = imagecolorsforindex($im, $index);

                if ($bit == 16)  {
                    $bin = 0 << $bit;

                    $bin |= ($colors['red'] >> 3) << 10;
                    $bin |= ($colors['green'] >> 3) << 5;
                    $bin |= $colors['blue'] >> 3;

                    $bmp_data .= pack("v", $bin);
                } else {
                    $bmp_data .= pack("c*", $colors['blue'], $colors['green'], $colors['red']);
                }
                // todo: 32bit;
            }
            $bmp_data .= $extra;
        }

        $size_quad = 0;
        $size_data = strlen($bmp_data);
        $colors_num = 0;
    }

    /* 位图文件头 */
    $file_header = "BM" . pack("V3", 54 + $size_quad + $size_data, 0, 54 + $size_quad);

    /* 位图信息头 */
    $info_header = pack("V3v2V*", 0x28, $width, $height, 1, $bit, $compression, $size_data, 0, 0, $colors_num, 0);
    
	/* 写入文件 */ 
    if ($filename != '')
    {
        $fp = fopen("test.bmp", "wb");

        fwrite($fp, $file_header);
        fwrite($fp, $info_header);
        fwrite($fp, $rgb_quad);
        fwrite($fp, $bmp_data);
        fclose($fp);

        return 1;
    }

    /* 浏览器输出 */
    header("Content-Type: image/bmp");
	
    echo $file_header . $info_header;
    echo $rgb_quad;
    echo $bmp_data;

    return 1;
 }
 
 if( !function_exists ('mime_content_type')) {
 
    /**
     * 获取文件的mime_content类型
     *
     * @return string
     */
    function mime_content_type($filename) {
       static $contentType = array(
			    'ai'	=> 'application/postscript',
				'aif'	=> 'audio/x-aiff',
				'aifc'	=> 'audio/x-aiff',
				'aiff'	=> 'audio/x-aiff',
				'asc'	=> 'application/pgp', //changed by skwashd - was text/plain
				'asf'	=> 'video/x-ms-asf',
				'asx'	=> 'video/x-ms-asf',
				'au'	=> 'audio/basic',
				'avi'	=> 'video/x-msvideo',
				'bcpio'	=> 'application/x-bcpio',
				'bin'	=> 'application/octet-stream',
				'bmp'	=> 'image/bmp',
				'c'	    => 'text/plain', // or 'text/x-csrc', //added by skwashd
				'cc'	=> 'text/plain', // or 'text/x-c++src', //added by skwashd
				'cs'	=> 'text/plain', //added by skwashd - for C# src
				'cpp'	=> 'text/x-c++src', //added by skwashd
				'cxx'	=> 'text/x-c++src', //added by skwashd
				'cdf'	=> 'application/x-netcdf',
				'class'	=> 'application/octet-stream',//secure but application/java-class is correct
				'com'	=> 'application/octet-stream',//added by skwashd
				'cpio'	=> 'application/x-cpio',
				'cpt'	=> 'application/mac-compactpro',
				'csh'	=> 'application/x-csh',
				'css'	=> 'text/css',
				'csv'	=> 'text/comma-separated-values',//added by skwashd
				'dcr'	=> 'application/x-director',
				'diff'	=> 'text/diff',
				'dir'	=> 'application/x-director',
				'dll'	=> 'application/octet-stream',
				'dms'	=> 'application/octet-stream',
				'doc'	=> 'application/msword',
				'dot'	=> 'application/msword',//added by skwashd
				'dvi'	=> 'application/x-dvi',
				'dxr'	=> 'application/x-director',
				'eps'	=> 'application/postscript',
				'etx'	=> 'text/x-setext',
				'exe'	=> 'application/octet-stream',
				'ez'	=> 'application/andrew-inset',
				'gif'	=> 'image/gif',
				'gtar'	=> 'application/x-gtar',
				'gz'	=> 'application/x-gzip',
				'h'	=> 'text/plain', // or 'text/x-chdr',//added by skwashd
				'h++'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hh'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hpp'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hxx'	=> 'text/plain', // or 'text/x-c++hdr', //added by skwashd
				'hdf'	=> 'application/x-hdf',
				'hqx'	=> 'application/mac-binhex40',
				'htm'	=> 'text/html',
				'html'	=> 'text/html',
				'ice'	=> 'x-conference/x-cooltalk',
				'ics'	=> 'text/calendar',
				'ief'	=> 'image/ief',
				'ifb'	=> 'text/calendar',
				'iges'	=> 'model/iges',
				'igs'	=> 'model/iges',
				'jar'	=> 'application/x-jar', //added by skwashd - alternative mime type
				'java'	=> 'text/x-java-source', //added by skwashd
				'jpe'	=> 'image/jpeg',
				'jpeg'	=> 'image/jpeg',
				'jpg'	=> 'image/jpeg',
				'js'	=> 'application/x-javascript',
				'kar'	=> 'audio/midi',
				'latex'	=> 'application/x-latex',
				'lha'	=> 'application/octet-stream',
				'log'	=> 'text/plain',
				'lzh'	=> 'application/octet-stream',
				'm3u'	=> 'audio/x-mpegurl',
				'man'	=> 'application/x-troff-man',
				'me'	=> 'application/x-troff-me',
				'mesh'	=> 'model/mesh',
				'mid'	=> 'audio/midi',
				'midi'	=> 'audio/midi',
				'mif'	=> 'application/vnd.mif',
				'mov'	=> 'video/quicktime',
				'movie'	=> 'video/x-sgi-movie',
				'mp2'	=> 'audio/mpeg',
				'mp3'	=> 'audio/mpeg',
				'mpe'	=> 'video/mpeg',
				'mpeg'	=> 'video/mpeg',
				'mpg'	=> 'video/mpeg',
				'mpga'	=> 'audio/mpeg',
				'ms'	=> 'application/x-troff-ms',
				'msh'	=> 'model/mesh',
				'mxu'	=> 'video/vnd.mpegurl',
				'nc'	=> 'application/x-netcdf',
				'oda'	=> 'application/oda',
				'patch'	=> 'text/diff',
				'pbm'	=> 'image/x-portable-bitmap',
				'pdb'	=> 'chemical/x-pdb',
				'pdf'	=> 'application/pdf',
				'pgm'	=> 'image/x-portable-graymap',
				'pgn'	=> 'application/x-chess-pgn',
				'pgp'	=> 'application/pgp',//added by skwashd
				'php'	=> 'application/x-httpd-php',
				'php3'	=> 'application/x-httpd-php3',
				'pl'	=> 'application/x-perl',
				'pm'	=> 'application/x-perl',
				'png'	=> 'image/png',
				'pnm'	=> 'image/x-portable-anymap',
				'po'	=> 'text/plain',
				'ppm'	=> 'image/x-portable-pixmap',
				'ppt'	=> 'application/vnd.ms-powerpoint',
				'ps'	=> 'application/postscript',
				'qt'	=> 'video/quicktime',
				'ra'	=> 'audio/x-realaudio',
				'rar'=>'application/octet-stream',
				'ram'	=> 'audio/x-pn-realaudio',
				'ras'	=> 'image/x-cmu-raster',
				'rgb'	=> 'image/x-rgb',
				'rm'	=> 'audio/x-pn-realaudio',
				'roff'	=> 'application/x-troff',
				'rpm'	=> 'audio/x-pn-realaudio-plugin',
				'rtf'	=> 'text/rtf',
				'rtx'	=> 'text/richtext',
				'sgm'	=> 'text/sgml',
				'sgml'	=> 'text/sgml',
				'sh'	=> 'application/x-sh',
				'shar'	=> 'application/x-shar',
				'shtml'	=> 'text/html',
				'silo'	=> 'model/mesh',
				'sit'	=> 'application/x-stuffit',
				'skd'	=> 'application/x-koan',
				'skm'	=> 'application/x-koan',
				'skp'	=> 'application/x-koan',
				'skt'	=> 'application/x-koan',
				'smi'	=> 'application/smil',
				'smil'	=> 'application/smil',
				'snd'	=> 'audio/basic',
				'so'	=> 'application/octet-stream',
				'spl'	=> 'application/x-futuresplash',
				'src'	=> 'application/x-wais-source',
				'stc'	=> 'application/vnd.sun.xml.calc.template',
				'std'	=> 'application/vnd.sun.xml.draw.template',
				'sti'	=> 'application/vnd.sun.xml.impress.template',
				'stw'	=> 'application/vnd.sun.xml.writer.template',
				'sv4cpio'	=> 'application/x-sv4cpio',
				'sv4crc'	=> 'application/x-sv4crc',
				'swf'	=> 'application/x-shockwave-flash',
				'sxc'	=> 'application/vnd.sun.xml.calc',
				'sxd'	=> 'application/vnd.sun.xml.draw',
				'sxg'	=> 'application/vnd.sun.xml.writer.global',
				'sxi'	=> 'application/vnd.sun.xml.impress',
				'sxm'	=> 'application/vnd.sun.xml.math',
				'sxw'	=> 'application/vnd.sun.xml.writer',
				't'	    => 'application/x-troff',
				'tar'	=> 'application/x-tar',
				'tcl'	=> 'application/x-tcl',
				'tex'	=> 'application/x-tex',
				'texi'	=> 'application/x-texinfo',
				'texinfo'	=> 'application/x-texinfo',
				'tgz'	=> 'application/x-gtar',
				'tif'	=> 'image/tiff',
				'tiff'	=> 'image/tiff',
				'tr'	=> 'application/x-troff',
				'tsv'	=> 'text/tab-separated-values',
				'txt'	=> 'text/plain',
				'ustar'	=> 'application/x-ustar',
				'vbs'	=> 'text/plain', //added by skwashd - for obvious reasons
				'vcd'	=> 'application/x-cdlink',
				'vcf'	=> 'text/x-vcard',
				'vcs'	=> 'text/calendar',
				'vfb'	=> 'text/calendar',
				'vrml'	=> 'model/vrml',
				'vsd'	=> 'application/vnd.visio',
				'wav'	=> 'audio/x-wav',
				'wax'	=> 'audio/x-ms-wax',
				'wbmp'	=> 'image/vnd.wap.wbmp',
				'wbxml'	=> 'application/vnd.wap.wbxml',
				'wm'	=> 'video/x-ms-wm',
				'wma'	=> 'audio/x-ms-wma',
				'wmd'	=> 'application/x-ms-wmd',
				'wml'	=> 'text/vnd.wap.wml',
				'wmlc'	=> 'application/vnd.wap.wmlc',
				'wmls'	=> 'text/vnd.wap.wmlscript',
				'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
				'wmv'	=> 'video/x-ms-wmv',
				'wmx'	=> 'video/x-ms-wmx',
				'wmz'	=> 'application/x-ms-wmz',
				'wrl'	=> 'model/vrml',
				'wvx'	=> 'video/x-ms-wvx',
				'xbm'	=> 'image/x-xbitmap',
				'xht'	=> 'application/xhtml+xml',
				'xhtml'	=> 'application/xhtml+xml',
				'xls'	=> 'application/vnd.ms-excel',
				'xlt'	=> 'application/vnd.ms-excel',
				'xml'	=> 'application/xml',
				'xpm'	=> 'image/x-xpixmap',
				'xsl'	=> 'text/xml',
				'xwd'	=> 'image/x-xwindowdump',
				'xyz'	=> 'chemical/x-xyz',
				'z'	=> 'application/x-compress',
				'zip'	=> 'application/zip',
       );
       $type = strtolower(substr(strrchr($filename, '.'),1));
       if(isset($contentType[$type])) {
            $mime = $contentType[$type];
       }else {
       	    $mime = 'application/octet-stream';
       }
       return $mime;
    }
 }

 /**
  * 取得图像类型的文件后缀
  *
  * @param $imagetype 图片类型
  */
 if( !function_exists('image_type_to_extension') ) {
 
    function image_type_to_extension($imagetype) {
   
       if(empty($imagetype)) return false;
	   
	   /* 图片类型 */
        switch($imagetype) {
           case IMAGETYPE_GIF     : return '.gif';
           case IMAGETYPE_JPEG    : return '.jpg';
           case IMAGETYPE_PNG     : return '.png';
           case IMAGETYPE_SWF     : return '.swf';
           case IMAGETYPE_PSD     : return '.psd';
           case IMAGETYPE_BMP     : return '.bmp';
           case IMAGETYPE_TIFF_II : return '.tiff';
           case IMAGETYPE_TIFF_MM : return '.tiff';
           case IMAGETYPE_JPC     : return '.jpc';
           case IMAGETYPE_JP2     : return '.jp2';
           case IMAGETYPE_JPX     : return '.jpf';
           case IMAGETYPE_JB2     : return '.jb2';
           case IMAGETYPE_SWC     : return '.swc';
           case IMAGETYPE_IFF     : return '.aiff';
           case IMAGETYPE_WBMP    : return '.wbmp';
           case IMAGETYPE_XBM     : return '.xbm';
           default                : return false;
        }
    }
 }