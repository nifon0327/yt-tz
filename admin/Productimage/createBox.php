<?php   
Header("Content-type: image/png"); //输出一个PNG 图片文件
$box_image="images/outbox.png";
$BoxSpec=$_GET["BoxSpec"];
//$BoxSpec="38*32*22CM";
$bim=imagecreatefrompng($box_image);//装入背景图片
$black=imagecolorallocate($bim,0,0,0); //定义黑色
$red=imagecolorallocate($bim,255,0,0);
$gray=imagecolorallocate($bim,153,153,153);
$font1="Fonts/ARIALBD.TTF";//定义字体
$font4="Fonts/simhei.ttf";
$boxSize=explode("×",$BoxSpec);
if (count($boxSize)==3){
  $boxH=$boxSize[2];
  $boxUnit=preg_replace( '/[\d\.]/ ', '',$boxSize[2]);
  //$boxUnit="cm";
  $boxL=$boxSize[0] . $boxUnit;
  $boxW=$boxSize[1] . $boxUnit;
  $font1_size=11;
  $curX=162;$curY=220;$Angle=-20;
  imagettftext($bim,$font1_size,$Angle,$curX,$curY,$black,$font1,$boxL); 
  $curX=326;$curY=238;$Angle=13;
  imagettftext($bim,$font1_size,$Angle,$curX,$curY,$black,$font1,$boxW); 
  $curX=435;$curY=120;$Angle=-90;
  imagettftext($bim,$font1_size,$Angle,$curX,$curY,$black,$font1,$boxH); 
}else{
  $font4_size=20;
  $curX=110;$curY=160;
  ImageFilledRectangle($bim,$curX-5,$curY-30,$curX+315,$curY+10,$gray);
  imagettftext($bim,$font4_size,0,$curX+10,$curY,$red,$font4,"外箱尺寸格式无法识别！"); 
}
 imagepng($bim); //创建图形
 imagedestroy($bim); //关闭图形
?>