<?php   
//独立已更新**********电信---yang 20120801
//关闭当前页面的PHP警告及提示信息
 error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 
 
include "../basic/chksession.php";
include "../basic/parameter.inc";
$NowDate=date("Y-m-d");
$StartD=date("Y-m-d",strtotime("$NowDate -10 month"));
$StartM=date("Y-m",strtotime("$NowDate -12 month"));
$EndM=date("Y-m");

//$thisM=date("n月",strtotime("$StartD + $j month"));
//条件
$tjStr=" AND Month>'$StartM' AND Month<='$EndM'";
$MaxResult = mysql_fetch_array(mysql_query("
SELECT MAX(MaxValue1) AS MaxValue1 FROM( 
SELECT COUNT(*) AS MaxValue1 FROM $DataIn.cwxzsheet WHERE JobId='$JobId' AND Month>'$StartM' GROUP BY Month 
UNION ALL
SELECT COUNT(*) AS MaxValue1 FROM $DataPublic.staffmain M WHERE 1 AND M.JobId='$JobId' AND M.Estate=1
) A",$link_id));
$MaxValue1=$MaxResult["MaxValue1"];	//最高


$imW=720;
$imH=150;
$UseFont = "../model/fonts/simhei.ttf"; 					//使用的中文字体
$image = imagecreate ($imW,$imH);							//输出空白图像

if($Bc==1){
	imagecolorallocate($image,204,204,255);							//图像背景色
	}
else{
	imagecolorallocate($image,204,204,153);							//图像背景色
	}
$TextBlack = imagecolorallocate($image,0,0,0);
$BlackColor = 	imagecolorallocate($image,0,0,0);				//黑色
$redline 	= 	imagecolorallocate($image,255,0,0);				//红色
$greenline 	= 	imagecolorallocate($image,40,170,40);			//绿色
$blueline 	= 	imagecolorallocate($image,0,25,168);			//蓝色
$Gridcolor=imagecolorallocate($image,153,153,153);				//网络线

$Diameter=8;
$imL=55;														//顶部X:左边距
$imT=20;														//顶部Y:上边距
$imR=$imW-65;													//底部X:
$imB=$imH-30;													//底部Y:
//1点对应金额
$Point2Amount=ceil($MaxValue1/500);//向上取整
imagerectangle($image,$imL,$imT-10,$imR,$imB+10,$TextBlack); 			//画矩形：曲线图范围
$TextGreen= imagecolorallocate($image,0,204,0);
//imageline($image,$imL,$imH/2,$imR,$imH/2,$TextGreen);
imagettftext($image,12,0,660,16,$TextBlack,$UseFont,$yJgStep);	//输出标题

$jgSetp=50;
for($i=0;$i<12;$i++){
	$thisM=date("n月",strtotime("$StartD + $i months - 5 day"));
	imageline($image,$imL+$jgSetp*($i+1),$imB+10,$imL+$jgSetp*($i+1),$imB,$BlackColor);						//输出纵向分隔线:
	imageline($image,$imL+$jgSetp*($i+1),$imB,$imL+$jgSetp*($i+1),$imT-10,$Gridcolor);
  	//imagettftext($image,10,0,$imL+$jgSetp*$i+15,$imB+25,$BlackColor,$UseFont,$thisM);
	}
//Y轴
$yJgStep=ceil($MaxValue1/5);//Y轴每行间隔值
for($y=1;$y<=5;$y++){
	imageline($image,$imL,$imB+10-$y*20,$imL-10,$imB+10-$y*20,$BlackColor);
	imageline($image,$imL,$imB+10-$y*20,$imR,$imB+10-$y*20,$Gridcolor);
	$thisA=$y*$yJgStep;
	$Ltemp=$thisA<100?($thisA<10?20:25):30;
  	imagettftext($image,10,0,$imL-$Ltemp,$imB+10-$y*20+5,$BlackColor,$UseFont,$thisA);
	}
$jCube=30;//条形图宽度

//输出 每月的人数
$i=1;
for($j=0;$j<10;$j++){
	$thisM=date("Y-m",strtotime("$StartD + $j months  - 5 day"));
	$Month=date("m",strtotime("$StartD + $j months  - 5 day"));;
	$checkSql = mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.cwxzsheet WHERE JobId='$JobId' AND Month='$thisM'",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		$Nums=$checkRow["Nums"];
		//条形图高度
		///////////////////////////////////////////////////
		$YB=$imB+10;//条形图底，不变
		if ($yJgStep!=0)  $YT=$YB-round($Nums*20/$yJgStep); else $YT=$YB;//条形图顶
		$XL=$imL+$jgSetp*($i-1)+10;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽 改4
		$XR=$XL+$jCube;
		if($Nums>0){
			imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$blueline); //画条形图
			imagettftext($image,10,0,$XL+10,$YT-2,$redline,$UseFont,$Nums);
			}
			imagettftext($image,10,0,$XL+5,$YB+15,$BlackColor,$UseFont,($Month*1)."月");
			
		$i++;
		}
		///////////////////////////////////////////////////
	}
	
	
//没有生成工资的月份
$mySql=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataPublic.staffmain M WHERE 1 AND M.JobId='$JobId' AND M.Estate=1",$link_id));
$NowNums=$mySql["Nums"];
for($j=$i;$j<13;$j++){
	$YB=$imB+10;//条形图底，不变
		if ($yJgStep!=0) $YT=$YB-round($NowNums*20/$yJgStep); else $YT=$YB;//条形图顶
		$XL=$imL+$jgSetp*($j-1)+10;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽 改4
		$XR=$XL+$jCube;
		$Month++;
		if($NowNums>0){
			imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$blueline); //画条形图
			imagettftext($image,10,0,$XL+10,$YT-2,$redline,$UseFont,$NowNums);
			}
			imagettftext($image,10,0,$XL+5,$YB+15,$BlackColor,$UseFont,$Month."月");
	}
//职位
$checkJob=mysql_fetch_array(mysql_query("SELECT J.Name FROM $DataPublic.jobdata J WHERE J.Estate=1 AND J.Id='$JobId'",$link_id));
$JobName=$checkJob["Name"];
imagettftext($image,12,0,$imL-50,$imT,$BlackColor,$UseFont,$JobName);
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>