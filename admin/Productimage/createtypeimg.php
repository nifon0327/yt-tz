<?php   
//电信-ZX  2012-08-01        
Header("Content-type: image/gif"); //输出一个PNG 图片文件
include "../../basic/parameter.inc";
//取得产品主类颜色及名称
//for ($i=1;$i<8;$i++){
//$mainType=$i;
$mySql="SELECT Name,rColor,gColor,bColor FROM $DataIn.productmaintype  WHERE Id=$mainType LIMIT 1";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
     $writeName=$myRow["Name"];
     $rColor=$myRow["rColor"];
     $gColor=$myRow["gColor"];
     $bColor=$myRow["bColor"];
 }


$im_w=105;
$im_h=28;
$FontName="Fonts/simhei.ttf";
$FontSize=10;

$im=imagecreate($im_w,$im_h);
//$bg_color=imagecolorallocate($im,$rColor,$gColor,$bColor);
$bg_color=imagecolorallocatealpha($im,$rColor,$gColor,$bColor,30);
imagefilledrectangle($im,0,0,$im_w,$im_h,$bg_color);

$white=imagecolorallocate($im,255,255,255); //定义白色
$white2=imagecolorallocate($im,254,254,254); //定义字体颜色

RoundedRectangle($im,3,3,102,25,3,$white); 
RoundedRectangle($im,2,2,103,26,3,$white); 
RoundedRectangle($im,1,1,104,27,3,$white); 

imagefilledrectangle($im,0,0,2,28,$white);
imagefilledrectangle($im,0,0,105,2,$white);

imagefilledrectangle($im,103,0,105,28,$white);
imagefilledrectangle($im,0,26,105,28,$white);
$trans = imageColorTransparent($im,$white); 

$temp=imagettfbbox($FontSize,0,$FontName,$writeName); //取得使用 TrueType 字体的文本的范围 
$temp_w=$temp[2]-$temp[6]; 
$temp_h=$temp[3]-$temp[7]; 
unset($temp);

$dx=ceil((105-$temp_w)/2);
$dy=28-ceil((28-$temp_h)/2)-2;

imagettftext($im,$FontSize,0,$dx,$dy,$white2,$FontName,$writeName);
imagettftext($im,$FontSize,0,$dx+1,$dy,$white2,$FontName,$writeName);

$outFile="images/Type_" .$mainType . ".gif";

imagegif($im,$outFile); //输出图形
imagegif($im); 
imagedestroy($im);
//}

function RoundedRectangle($im,$xt,$yt,$xr,$yl,$r,$color)  
{  
  imageLine($im, $xt+$r,$yt,$xr-$r,$yt,$color);  
  imageLine($im, $xt+$r,$yl,$xr-$r,$yl,$color);  
  
  imageLine($im, $xt,$yt+$r,$xt,$yl-$r,$color);  
  imageLine($im, $xr,$yt+$r,$xr,$yl-$r,$color);  
  
  imageArc($im, $xt+$r,$yt+$r,$r*2,$r*2,180,270,$color);  
  imageArc($im, $xr-$r,$yt+$r,$r*2,$r*2,270,360,$color);  
  
  imageArc($im, $xt+$r,$yl-$r,$r*2,$r*2,90,180,$color);  
  imageArc($im, $xr-$r,$yl-$r,$r*2,$r*2,0,90,$color);  
}  
?>