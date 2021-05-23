<?php   
Header("Content-type: image/png"); //输出一个PNG 图片文件
$Label_image="images/label_1.png";
$im_info=getimagesize($Label_image); 
$im_w=$water_info[0]; //取得图片的宽 
$im_h=$water_info[1]; //取得图片的高
$im=imagecreatefrompng($Label_image);//装入背景图片
$black=imagecolorallocate($im,0,0,0); //定义黑色
$white=imagecolorallocate($im,255,255,255); //定义白色
$yellow=imagecolorallocate($im,255,255,0); //定义黄色
$font1="Fonts/ARIALBD.TTF";//定义字体
$font2="Fonts/OMB___WT.TTF";
$font3="Fonts/TIMESBD.TTF";
$font4="Fonts/simhei.ttf";
//输出标题
$font2_size=42;
$curFontSize=setFontSize($eCode,$font2,$font2_size,466,46);
$curX=12;$curY=50;
$curX=floor($curX+(466-$curFontSize[1])/2);
$curY=floor($curY-(46-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font2,$eCode);
//输出产地
$font3_size=15.5;
$curFontSize=setFontSize($StartPlace,$font3,$font3_size,216,21);
$curX=12;$curY=85;
$curX=floor($curX+(216-$curFontSize[1])/2);
$curY=floor($curY-(21-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$yellow,$font3,$StartPlace);
//输出箱号
$font2_size=32;
$curFontSize=setFontSize($BoxNo,$font2,$font2_size,34,31);
$curX=322;$curY=91;
$curX=floor($curX+(34-$curFontSize[1])/2);
$curY=floor($curY-(31-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$yellow,$font2,$BoxNo);
//输出总箱数
$font2_size=18;
$curFontSize=setFontSize($BoxTotal,$font2,$font2_size,76,22);
$curX=406;$curY=85;
$curX=floor($curX+(76-$curFontSize[1])/2);
$curY=floor($curY-(22-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font2,$BoxTotal);
//输出日期
$mDate="";
$arrDate=explode(" ", $Udate);  //取得英文日期中日后面的英文字母
if (count($arrDate)==3){
	$Udate=preg_replace( '/[^\d]/ ', '',$arrDate[0]);
	$mDate=preg_replace( '/[\d]/ ', '',$arrDate[0]);
	$sDate=$arrDate[1] . " ". $arrDate[2];
}
$font1_size=12.12;
$curX=55;$curY=110;
if ($mDate==""){
   imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$Udate);
 }else{
   imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$Udate);
   $curX=65;
   if (strlen($Udate)==2) $curX=$curX+8;
   imagettftext($im,10,0,$curX,$curY-4,$black,$font1,$mDate);
   $curX=$curX+15;
   imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$sDate); 
}
//输出装箱数量
$font2_size=23;
$curFontSize=setFontSize($BoxPcs,$font2,$font2_size,36,22);
$curX=58;$curY=148;
$curX=floor($curX+(36-$curFontSize[1])/2);
$curY=floor($curY-(22-$curFontSize[2])/2);
imagettftext($im,$curFontSize[0],0,$curX,$curY,$yellow,$font2,$BoxPcs);
$font1_size=12.12;
$curX=103;$curY=148;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$PackingUnit);
//输出包装尺寸
$BoxSpec=preg_replace( "/\*/","×",$BoxSpec);
$font1_size=12.12;
$curX=123;$curY=175;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$BoxSpec); 
//输出NG WG
$font1_size=12.12;
$curX=55;$curY=195;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$WG); 
$curX=55;$curY=215;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$NG); 
//输出P/O NO INVOICE
$font1_size=12.12;
$curX=335;$curY=112;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$OrderPO); 
$curX=335;$curY=132;
imagettftext($im,$font1_size,0,$curX,$curY,$black,$font1,$InvoiceNO); 
//输出cName
//$cName = iconv("gb2312","UTF-8",$cName);
$font4_size=8;
$curFontSize=setFontSize($cName,$font4,$font4_size,220,15);
$curX=260;$curY=150;
$curX=floor($curX+(200-$curFontSize[1])/2);
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
	 $font1_size=10;
	 $curX=260;$curY=168;
     $curFontSize=setFontSize($BoxCode1,$font1,$font1_size,212,15);
     $curX=floor($curX+(212-$curFontSize[1])/2);
     $curY=floor($curY-(15-$curFontSize[2])/2);
     imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font1,$BoxCode1);
   //生成条码//$code=$BoxCode;
    $curX=260;$curY=170;$setWidth=220;
    $lw=1.5;$hi=35;
	createCode($im,$code,$curX,$curY,$setWidth,$lw,$hi);
}
//输出SHIPTO
 $font1_size=12.12;
 $curX=86;$curY=252;
 $curFontSize=setFontSize($EndPlace,$font1,$font1_size,395,18);
 $curX=floor($curX+(395-$curFontSize[1])/2);
 $curY=floor($curY-(18-$curFontSize[2])/2);
 imagettftext($im,$curFontSize[0],0,$curX,$curY,$black,$font1,$EndPlace);
 
// $outFile="../../download/labelFile/L" .$ProductId . ".png";
// imagepng($im,$outFile); //创建图形
 imagepng($im); 
 imagedestroy($im); //关闭图形
?>