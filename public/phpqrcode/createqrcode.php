<?php 
    //$filename = $PNG_TEMP_DIR.'test.png';
    if ($code_data==""){$code_data="code data";}
    
    if ($code_level==""){
        $errorCorrectionLevel = 'L';//'L','M','Q','H'
       }
    else{
      $errorCorrectionLevel = $code_level;  
    }
    
    if ($code_size==""){
       $matrixPointSize=4; //1-10 
    }else{
       $matrixPointSize=$code_size;
    }
    
    
    $filename = $PNG_TEMP_DIR.md5($code_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
   // $filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
   if (!file_exists($filename)){
       QRcode::png($code_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
   }
   
    $qrcode_File=$PNG_WEB_DIR.basename($filename);
    //echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />'; 
?>

