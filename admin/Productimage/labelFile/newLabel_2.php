<?php   
Header("Content-type: image/png"); //输出一个PNG 图片文件

$im= imagecreatetruecolor(500, 268);
$black=imagecolorallocate($im,0,0,0); //定义黑色
$white=imagecolorallocate($im,255,255,255); //定义白色
$yellow=imagecolorallocate($im,255,240,0); //定义黄色
$font1="Fonts/ARIALBD.TTF";//定义字体
$font2="Fonts/OMB___WT.TTF";
$font3="Fonts/TIMESBD.TTF";
$font4="Fonts/simhei.ttf";
$font5="Fonts/comic.ttf";
imagefilledrectangle ($im, 0,0,500,268,$yellow);
for($i=1;$i<3;$i++){
  imagerectangle($im, 5+$i,5+$i,495-$i,263-$i,$black);
}
for($i=1;$i<3;$i++){
  imagerectangle($im, 10+$i,10+$i,490-$i,258-$i,$black);
}
$lineh=15;$lined=4;
//输出shipper:
$font5_size=9;
$StartPlace="Shipper:  " . $StartPlace;
$curX=15;$curY=26;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$StartPlace);
$curY+=$lined;
imageline($im,12,$curY,488,$curY,$black);
//输出endplace:
$EndPlace="Consignee:  " . $EndPlace;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$EndPlace);
$curY+=$lined;
imageline($im,12,$curY,488,$curY,$black);
//输出Address:
$Address="Les planes ,2-4- Poligono Fontsanta 08970 Sant Joan Despi-Barcelona";
$Address="Address:  " . $Address;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$Address);
$curY+=$lined;
imageline($im,12,$curY,488,$curY,$black);
//输出Attention:
$Attention="Attention:  " . "Mary Recio";
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$Attention);
$curY+=$lined;
imageline($im,12,$curY,488,$curY,$black);
//输出OrderPO:
$OrderPO="Pedido Número:  PO#" . $OrderPO;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$OrderPO);
$curY+=$lined;
imageline($im,12,$curY,488,$curY,$black);
imageline($im,240,68,240,105,$black);//输出分隔线
//输出BoxTotal：
if ($BoxTotal>0){
   $BoxTotal="Número de caja:  " . $PreWord . "1 / " . $BoxTotal;
}else{
   $BoxTotal="Número de caja:  ";  
}	  
   imagettftext($im,$font5_size,0,244,82,$black,$font5,$BoxTotal);

//输出eCode：
   $eCode="Referencia:  " . $eCode;
   imagettftext($im,$font5_size,0,244,102,$black,$font5,$eCode);
//输出Description:
$Description="Descripción:  " . $Description;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$Description);
$curY+=$lined+15;
imageline($im,12,$curY,488,$curY,$black);
imageline($im,240,$curY,240,$curY+115,$black);//输出分隔线
//输出BoxPcs:
$BoxPcs="Cantidad:  " . $BoxPcs . " " . $PackingUnit;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$BoxPcs);
$curY+=$lined;
imageline($im,12,$curY,240,$curY,$black);
//输出BoxSpec:
$BoxSpec="Medidas:  " . $BoxSpec;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$BoxSpec);
$curY+=$lined;
imageline($im,12,$curY,240,$curY,$black);
//输出WG:
$WG="Peso bruto:  " . $WG . " Kilos";
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$WG);
$curY+=$lined;
imageline($im,12,$curY,240,$curY,$black);
//输出NG:
$NG="Peso neto:  " . $NG . " Kilos";
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$NG);
$curY+=$lined;
imageline($im,12,$curY,240,$curY,$black);
//输出date:
$Description="Date:  " . $Udate;
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$Udate);
$curY+=$lined;
imageline($im,12,$curY,240,$curY,$black);
//输出Cod.barras:
$Codbarras="Cod.barras:  ";
$curY+=$lineh;
imagettftext($im,$font5_size,0,$curX,$curY,$black,$font5,$Codbarras);
//输出cName
$font4_size=8;
$curFontSize=setFontSize($cName,$font4,$font4_size,220,15);
$curX=250;$curY=153;
$curX=floor($curX+(220-$curFontSize[1])/2);
$curY=floor($curY-(15-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font4,$cName);
//输出条码
if($BoxCode!=""){
   if (strlen($BoxCode)==13){
	   $code=$BoxCode;
   }else{
     $Field=explode("|",$BoxCode);$code=$Field[1];$BoxCode1=$Field[0];
      $BoxCode1=preg_replace("/,/"," ",$BoxCode1);
   }
}
if(is_numeric($code) &&  strlen($code)==13){
	 $font1_size=11;
	 $curX=250;$curY=180;
     $curFontSize=setFontSize($BoxCode1,$font1,$font1_size,200,15);
     $curX=floor($curX+(200-$curFontSize[1])/2)+5;
     $curY=floor($curY-(15-$curFontSize[2])/2);
     imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font1,$BoxCode1);
   //生成条码//$code=$BoxCode;
    $curX=250;$curY=185;$setWidth=200;
    $lw=1.4;$hi=45;
	createCode($im,$code,$curX,$curY,$setWidth,$lw,$hi);
}

// $outFile="../../download/labelFile/L" .$ProductId . ".png";
// imagepng($im,$outFile); //创建图形
 imagepng($im); 
 imagedestroy($im); //关闭图形
?>