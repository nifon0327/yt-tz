<?php
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "../cjgl/phpqrcode/phpqrcode.php";
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$POrderIds = explode('|',$_REQUEST['selectsPOrderIds']);
foreach ($POrderIds as $v) {
    $mySql = "SELECT DISTINCT P.cName FROM $DataIn.yw1_scsheet SC
          INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
          INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
          WHERE 1 and S.Estate>0 AND SC.POrderId=$v ";

    $myResult = mysql_query($mySql." $PageSTR",$link_id);
    if($myRow = mysql_fetch_array($myResult)) {
        $cName = $myRow['cName'];
    }
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'QRCode'.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'QRCode/';
    if (!file_exists($PNG_TEMP_DIR)){
        mkdir($PNG_TEMP_DIR,0777);
    }
    $errorCorrectionLevel = 'H';
    $matrixPointSize = 6;
    $filename = $PNG_TEMP_DIR.$v.".png";
    if (!file_exists($filename)) {
        QRcode::png($cName, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }


    /*logo*/
    $tempArr = explode('-', $cName);
    $count = count($tempArr)-1;
    $SN = explode('.',$tempArr[$count]);

    $im =imagecreate(100,60);
    $background_color = ImageColorAllocate ($im, 255, 255, 255);
    $col = imagecolorallocate($im, 0, 0, 0);
    $font="../fonts/msyhbd.ttf"; //字体所放目录
    $come=iconv("gb2312","utf-8",$SN[0]);
    $fontWidth = imagefontwidth(26);//获取文字宽度
    $textWidth = $fontWidth * mb_strlen($come);
    $x = ceil((50 - $textWidth) / 2); //计算文字的水平位置
    imagettftext($im,26,0,$x,45,$col,$font,$come); //写 TTF 文字到图中
    header('Content-type:image/png');
    ImagePng($im,'QRCode/SN.png');
    ImageDestroy($im);

    $logo = 'QRCode/SN.png';  //准备好的logo图片
    $QR = $filename;            //已经生成的原始二维码图

    if (file_exists($logo)) {
        $QR = imagecreatefromstring(file_get_contents($QR));        //目标图象连接资源。
        $logo = imagecreatefromstring(file_get_contents($logo));    //源图象连接资源。
        $QR_width = imagesx($QR);           //二维码图片宽度
        $QR_height = imagesy($QR);          //二维码图片高度
        $logo_width = imagesx($logo);       //logo图片宽度
        $logo_height = imagesy($logo);      //logo图片高度
        $logo_qr_width = $QR_width / 5;     //组合之后logo的宽度(占二维码的1/5)
        $scale = $logo_width / $logo_qr_width;    //logo的宽度缩放比(本身宽度/组合后的宽度)
        $logo_qr_height = $logo_height / $scale;  //组合之后logo的高度
        $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点

        //重新组合图片并调整大小
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        imagepng($QR, $filename);

    }
    unlink($logo);

}


?>