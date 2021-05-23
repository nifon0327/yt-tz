<?php
//独立已更新*********电信---yang 20120801

//关闭当前页面的PHP警告及提示信息
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ;
include "../basic/parameter.inc";
$CheckMonths=$M==""?5:$M-1;				//要计算的月份数
$CheckMonth=date("Y-m-01");		//当前月第一天
$StratDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$TJA=" AND M.Date >='$StratDate'";			//出货条件
$TJB=" AND M.OrderDate >='$StratDate'";		//下单条件

include"charttopng_total_1.php";
/*
计算月份内总额下单$SumInAmount、出货$SumOutAmount
单月最高的下单出货金额；$MaxAmount
有效客户数量	$clientNum
*/
$Diameter=5;//圆点直径
//$MaxAmount=28940000;
//$Height_A=ceil(intval($MaxAmount/10000)/50)*50;	//条形图高度范围=ceil(intval(月份内出货或下单最高总额/固定参数10000)取整万/行高50)向上取整行*行高50;
$Max_Height=intval($MaxAmount/10000);
$tmpCount=ceil($Max_Height/600);
$tmpCount=$tmpCount<3?3:$tmpCount;
$unitHeight=($tmpCount-1)*25;
$Height_A=$tmpCount*600*(25/$unitHeight);

$Month_W=100;									//月份间隔步长:固定宽度100
$imW=($CheckMonths+1)*$Month_W+200;				//图像宽度=(计算的月份数+1)*月份间隔宽度+均线说明区(左边50，右边150)
$Height_A=$Height_A<400?400:$Height_A;			//设置条形图最小高度

$imH=$Height_A+150;								//图像高度=条形图高度+上下说明区划100

//$clientNumH=$imH+($clientNum+1)*50;				//+客户条形图高度
$clientNumH=$imH+($clientNum+1)*50;				//+客户条形图高度
$CimW=$imW-50;

$imWight=2*$imW+100;
//加分类统计的高度
$checkTypeSql=mysql_query("SELECT Name FROM $DataIn.productmaintype",$link_id);
$TypeNum= mysql_num_rows($checkTypeSql);

//$TypeNumH=$clientNumH+($TypeNum+1)*50;			//加分类统计的高度
$TypeNumH=$clientNumH+($TypeNum+1)*50;		//加分类统计的高度
//$image = imagecreate ($imW,$TypeNumH-30);			//输出空白图像

$image=imagecreatetruecolor($imWight,$TypeNumH-30);			//图形区域
$back = imagecolorallocate($image, 255, 255, 255);	//底图
imagefilledrectangle($image, 0, 0, $imWight- 1, $TypeNumH-30 - 1, $back);

$alpha_white=imagecolorallocatealpha($image, 255, 255, 255,50);
include"charttopng_total_2.php";//颜色设置文件

//$Tile="研砼客户月下单、出货总额条形图";
$Tile="客户月下单、出货总额条形图";
$titleX=$imW/2-130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX,35,$TextBlack,$UseFont,$Tile);	//输出标题

$imL=50;		//顶部X:左边距
$imT=50;		//顶部Y:上边距
$imR=$imW-150;	//底部X:
$imB=$imH-100;	//底部Y:
$AllimL=$imL;
$AllimT=$imH+10;
$AllimR=$imR;
$AllimB=$clientNumH-40;

$TypeimL=$imL;
$TypeimT=$clientNumH;
$TypeimR=$imR;
$TypeimB=$TypeNumH-50;

//imagefilledrectangle($image,$imL,$imB,$imR,$imT,$TextWhite); //画填充矩形
imagefilledrectangle($image,$imL,$imB,$imR,$AllimT,$JgColor);  //间隔矩形1
imagefilledrectangle($image,$imL,$AllimB,$imR,$TypeimT,$JgColor);  //间隔矩形2
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 		   //画矩形：条形图范围

include"charttopng_total_3.php";//条形图框架文件

//客户过滤:最高出货、或下单金额超过20万的客户

//初始化XL,XR,YB,YT
$wCube=20;		//柱体宽度
$jCube=10;		//柱体间隔
//月份检查:包括出货或下单的月份
	$i=1;
	$mNum=0;

for($i=0;$i<=($CheckMonths+1);$i++){
	$theMonth=date("Y-m",strtotime("$StratDate +$i month"));//计算的起始日期
	include "charttopng_total_4.php";
	if($i<=$CheckMonths){
		imagefilledrectangle($image,$imL+$Month_W*($i)+$jCube*1,$imB-1,$imL+$Month_W*($i)+$jCube+$wCube+7,$alpha_T,$alpha_white); //画透明矩形
		}
	}
//色块说明
$MovePoint=-20;

//输出说明
imagettftext($image,10,0,55,$imB+50,$TextBlack,$UseFont,"第一柱:当月下单金额");
imagettftext($image,10,0,55,$imB+65,$TextBlack,$UseFont,"第三柱:当月出货总额");
imagettftext($image,10,0,55,$imB+80,$TextBlack,$UseFont,"第二、四柱:以主分类统计的金额");//出货订单中采购单下单金额为美金的需求单总额(换算成RMB)
imagettftext($image,10,0,55,$imB+95,$TextBlack,$UseFont,"第五柱:当月车间人工薪资");

//总出货下单图例
//出货下单总额图
//imagefilledrectangle($image,$AllimL,$AllimT,$AllimR,$AllimB,$TextWhite);

$Tile1="客户未出订单总额/毛利统计图";
$titleX1=$imW+130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX1,$AllimT-25,$TextBlack,$UseFont,$Tile1);	//输出标题
$MoveY=ceil($imW)-150; //向右移动宽度

imagefilledrectangle($image,$imL+$MoveY,$AllimB,$imR+$MoveY,$TypeimT,$JgColor);  //间隔矩形3
imagefilledrectangle($image,$TypeimR,$TypeimB,$TypeimR+50,$AllimT,$JgColor);  //间隔矩形4

imagerectangle($image,$AllimL,$AllimT,$AllimR,$AllimB,$TextBlack); 			//画矩形：曲线图范围
include "charttopng_total_5.php";

//按主分类统计
imagefilledrectangle($image,$TypeimL,$TypeimT,$TypeimR,$TypeimB,$TextWhite);
imagerectangle($image,$TypeimL,$TypeimT,$TypeimR,$TypeimB,$TextBlack); 			//画矩形：曲线图范围
include "charttopng_total_6.php";

imagerectangle($image,$AllimL+$MoveY,$AllimT,$AllimR+$MoveY,$AllimB,$TextBlack); //画矩形：曲线图范
include "charttopng_wc_total_5.php";

imagerectangle($image,$TypeimL+$MoveY,$TypeimT,$TypeimR+$MoveY,$TypeimB,$TextBlack); //画矩形：曲线图范围
include "charttopng_wc_total_6.php";

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);   //释放资源
?>