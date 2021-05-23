<?php   
//电信-zxq 2012-08-01
/*下单和出货，以配件分类读取数据
图表高度固定
分隔线数量和代表金额值自动根据最高值计算
独立已更新
*/
include "../basic/chksession.php";
include "../basic/parameter.inc";

$nowMonth=date("Y-m");
$Diameter=5;													//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
$imW=730;
$imH=520;
$image = imagecreate ($imW,$imH);								//输出空白图像
imagecolorallocate($image,221,221,221);							//图像背景色

////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);
$SH= imagecolorallocate($image,102,102,102);
//颜色列表
$T1001=imagecolorallocate($image,0,60,121); 	$F1001=imagecolorallocate($image,0,60,121); 	$R1001=imagecolorallocate($image,0,60,121);
$T1002=imagecolorallocate($image,111,141,185);		$F1002=imagecolorallocate($image,111,141,185); 	$R1002=imagecolorallocate($image,111,141,185); 

//本月效率条形图//////////////////////////////////////////////////
$imL=25;
$imT=25;
	$imR=200;
	$imB=$imH-25;
imagerectangle($image,11,11,111,511,$TextWhite);
imagerectangle($image,10,10,110,510,$SH);
imagettftext($image,12,0,60,45,$TextBlack,$UseFont,$Leader);
//本月效率条形图//////////////////////////////////////////////////
//需求
$mySqlRow=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*C.Price) AS NeedPay
		FROM $DataIn.sc1_cjtj S
		LEFT JOIN $DataIn.cg1_stocksheet C ON C.POrderId=S.POrderId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=C.StuffId
		WHERE 1 AND DATE_FORMAT(S.Date,'%Y-%m')='$nowMonth' AND S.Leader='10295' AND A.TypeId=S.TypeId",$link_id));

//实际支出

//1 年曲线图////////////////////////////////////////////////////////
imagerectangle($image,121,11,721,161,$TextWhite);
imagerectangle($image,120,10,720,160,$SH);
imagerectangle($image,121,86,721,86,$TextWhite);
imagerectangle($image,120,85,720,85,$SH);
//1 年曲线图////////////////////////////////////////////////////////

//2 月曲线图////////////////////////////////////////////////////////
imagerectangle($image,121,186,721,336,$TextWhite);
imagerectangle($image,120,185,720,335,$SH);
imagerectangle($image,121,261,721,260,$TextWhite);
imagerectangle($image,120,260,720,260,$SH);
//2 月曲线图////////////////////////////////////////////////////////

//3 日曲线图////////////////////////////////////////////////////////
imagerectangle($image,121,361,721,511,$TextWhite);
imagerectangle($image,120,360,720,510,$SH);
imagerectangle($image,121,436,721,436,$TextWhite);
imagerectangle($image,120,435,720,435,$SH);
//3 日曲线图////////////////////////////////////////////////////////


//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>