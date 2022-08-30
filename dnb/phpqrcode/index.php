<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Magento\Framework\App\Bootstrap;
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
    require '../../app/bootstrap.php';

	$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

	$state = $objectManager->get('Magento\Framework\App\State');
	$state->setAreaCode('frontend');


	$directory = $objectManager->get('\Designnbuy\Base\Helper\Data')->getCustomerImageDir();

   // echo "<h1>PHP QR Code</h1><hr/>";
    
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    $uniqueid =  $_REQUEST['uniqueid'];
	//added by Ajay on 11/12/2013 start
	// if($_REQUEST['color']=='000000'){
		// $colorCode='0';
	// } else	{
		// $colorCode = hexdec($_REQUEST['color']);		
	// }
	//added by Ajay on 11/12/2013 end
	
	$colorCode = $_REQUEST['color'];	
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

	$backColor='';
    if (isset($_REQUEST['data'])) { 
    
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');
            
        // user data
        //$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
		$filename = 'QRcode'.date("YmdHis").'.svg';
		// $directory = $webpath.'/qrcode/';
		// $directory = $webpath;
		if (!file_exists($directory)){
			mkdir($directory, 0777);
		}
		if(is_dir($directory))
		{
			QRcode::svg($_REQUEST['data'],$directory.'/'.$filename, "L", 4, 0, false, $backColor, $colorCode);
			// QRcode::svg($text = $_REQUEST['data'], $outfile = $directory.'/'.$filename, $level = QR_ECLEVEL_L, $size = 4, 0, $margin = 0, $saveandprint=false, $backColor, $colorCode);
			//QRcode::png($_REQUEST['data'],$directory.'/'.$filename, $errorCorrectionLevel, $matrixPointSize, 2, '',$colorCode);   
			//Save qr code image into database table

			$id = $objectManager->get('\Designnbuy\Customer\Helper\Data')->saveUserImage($filename);
			$data['id'] = $id;
			$data['imageName'] = $filename;
			$jsonData = json_encode($data);
			echo $jsonData;
		}	
        
    } else {    
    
        //default data
        echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    }  
   

    