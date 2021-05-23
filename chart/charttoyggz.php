<?php   
//独立已更新*********电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
//$Number=10002;
$NowDate=date("Y-m-d");
$StartD=date("Y-m-d",strtotime("$NowDate -12 month"));
$StartM=date("Y-m",strtotime("$NowDate -12 month"));
$EndM=date("Y-m");

//$thisM=date("n月",strtotime("$StartD + $j month"));
//条件
$tjStr=" AND Month>'$StartM' AND Month<='$EndM'";
$MaxResult = mysql_fetch_array(mysql_query("
SELECT MAX(MaxValue1) AS MaxValue1 FROM(
SELECT MAX(Amount+Jz+Sb) AS MaxValue1 FROM $DataIn.cwxzsheet WHERE Number=$Number $tjStr) A
",$link_id));
$MaxValue1=intval($MaxResult["MaxValue1"]);	//最高

$checkNameRow=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Number LIMIT 1",$link_id));
$Name=$checkNameRow["Name"];
//最高差额

//$MaxValue1=$MaxValue1<1000?1000:$MaxValue1;
$imW=720;
$imH=560;
$UseFont = "c:/windows/fonts/simhei.ttf"; 					//使用的中文字体
$image = imagecreate ($imW,$imH);							//输出空白图像
imagecolorallocate($image,255,255,255);							//图像背景色
$TextBlack = imagecolorallocate($image,0,0,0);

$Diameter=8;
$imL=55;														//顶部X:左边距
$imT=30;														//顶部Y:上边距
$imR=$imW-5;													//底部X:
$imB=$imH-30;													//底部Y:
//1点对应金额
$Point2Amount=ceil($MaxValue1/500);//向上取整
imagerectangle($image,$imL,$imT-10,$imR,$imB+10,$TextBlack); 			//画矩形：曲线图范围
$TextGreen= imagecolorallocate($image,0,204,0);
//imageline($image,$imL,$imH/2,$imR,$imH/2,$TextGreen);
$BlackColor = 	imagecolorallocate($image,0,0,0);				//黑色
$redline 	= 	imagecolorallocate($image,255,0,0);				//红色
$greenline 	= 	imagecolorallocate($image,40,170,40);			//绿色
$blueline 	= 	imagecolorallocate($image,0,25,168);			//蓝色
$Gridcolor=imagecolorallocate($image,153,153,153);				//网络线
imagettftext($image,12,0,660,16,$TextBlack,$UseFont,$Name);	//输出标题

$jgSetp=50;
for($i=0;$i<12;$i++){
	$thisM=date("n月",strtotime("$StartD + $i months - 5 day"));
	imageline($image,$imL+$jgSetp*($i+1),$imB+10,$imL+$jgSetp*($i+1),$imB,$BlackColor);						//输出纵向分隔线:
	imageline($image,$imL+$jgSetp*($i+1),$imB,$imL+$jgSetp*($i+1),$imT-10,$Gridcolor);
  	imagettftext($image,10,0,$imL+$jgSetp*($i+1)-10,$imB+25,$BlackColor,$UseFont,$thisM);
	}
//Y轴
for($y=1;$y<=25;$y++){
	imageline($image,$imL,$imB+10-$y*20,$imL-10,$imB+10-$y*20,$BlackColor);
	imageline($image,$imL,$imB+10-$y*20,$imR,$imB+10-$y*20,$Gridcolor);
	$thisA=$Point2Amount*$y*20;
	$Ltemp=$thisA<1000?35:40;
  	imagettftext($image,10,0,$imL-$Ltemp,$imB+10-$y*20+5,$BlackColor,$UseFont,$thisA);
	}

$PrePointX="";
$PrePointY="";
for($j=0;$j<12;$j++){
	$thisM=date("Y-m",strtotime("$StartD + $j months  - 5 day"));
	$gzResult = mysql_query("
	SELECT (Amount+Jz+Sb) AS Amount FROM $DataIn.cwxzsheet WHERE Number=$Number AND Month='$thisM'
	",$link_id);
	if($gzRow = mysql_fetch_array($gzResult)){
		$Amount=sprintf("%.0f",$gzRow["Amount"]);
		$PointX=$imL+$jgSetp*($j+1);
		$PointY=$imB-intval($Amount/$Point2Amount)+10;//相隔点数
		//$PointY=$imH/2-$ValueY;
		if($PrePointY=="" || $PointY==$PrePointY){
			$TempColor=$BlackColor;
			}
		else{
			if($PointY>$PrePointY){//升工资
				$TempColor=$greenline;
				}
			else{					//降工资
				$TempColor=$redline;
				}
			}
		imagefilledarc($image,$PointX,$PointY,$Diameter,$Diameter,0,360,$TempColor,IMG_ARC_PIE);
		//输出工资
		imagettftext($image,10,0,$PointX-13,$PointY+15,$TempColor,$UseFont,$Amount);
		//画线
		if($PrePointX!="" && $PrePointY!=""){
			imageline($image,$PrePointX,$PrePointY,$PointX,$PointY,$TempColor);
			}
		$PrePointX=$PointX;
		$PrePointY=$PointY;
		}
	}

//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>