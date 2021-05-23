<?php   
/*下单和出货，以配件分类读取数据**********电信---yang 20120801
图表高度固定
分隔线数量和代表金额值自动根据最高值计算
独立已更新
*/
include "../basic/chksession.php";
//立体柱形图
include "../basic/parameter.inc";
$CheckMonths=$Y;				//要计算的月份数
$CheckMonth=date("Y-m-01");		//当前月第一天
$StartDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$StartMonth=date("Y-m",strtotime("$CheckMonth -$CheckMonths month"));
$StartY=date("Y",strtotime("$StartDate"));
$ToDate=date("Y-m-d");
$productTypeResult=mysql_fetch_array(mysql_query("SELECT TypeName FROM $DataIn.producttype WHERE TypeId='$Id'",$link_id));
$Name=$productTypeResult["TypeName"];
$ProductType=$ProductType==""?" AND P.TypeId=$Id":$ProductType;

include "chartgetcolor.php";   //取得相对应类别的颜色

$Tile=$Y."年 ".$Name." 月下单、出货数量统计图 ";
//$TypeId=9039;
//统计的分类

//出货或下单最高金额
$TjOut="DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth'";
$TjIn="DATE_FORMAT(M.OrderDate,'%Y-%m')>='$StartMonth'";

$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Qty) AS MaxValue1 FROM ( 
		SELECT SUM(Qty) AS Qty FROM(
			SELECT SUM(S.Qty) AS Qty,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			WHERE $TjOut $ProductType GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		) B GROUP BY Month
	UNION ALL 
		SELECT SUM(Qty) AS Qty FROM(
			SELECT SUM(S.Qty) AS Qty,DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			WHERE $TjIn $ProductType GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
		) B GROUP BY Month
	)A",$link_id));
	
$MaxValue1=intval($MaxResult["MaxValue1"]);		//出货和下单的最大值,为与金额计算共用，加上取整函数
$MaxValue1=$MaxValue1<1000?1000:$MaxValue1;

//********************
//间隔值计算
$YvlaueLenght=strlen($MaxValue1)-2;
$Ystep=pow(10,$YvlaueLenght);					
$Yvalue=$Ystep*intval($MaxValue1/($Ystep*10));	//Y轴间隔值
//Y轴间隔数
$Ys=ceil($MaxValue1/$Yvalue)+1;					//Y轴间隔数
$imH=$Ys*40+150;								//Y轴高
//********************

$MonthStep=100;													//月份间隔步长
$imW=($Y+1)*$MonthStep+160;										//图像宽度
$Diameter=5;													//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
$image = imagecreate ($imW,$imH);								//输出空白图像
imagecolorallocate($image,255,255,255);							//图像背景色

////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$TextColor = imagecolorallocate($image,$rColor,$gColor,$bColor);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);

//颜色列表
$T1001=imagecolorallocate($image,0,60,121); 	
$F1001=imagecolorallocate($image,0,60,121); 	
$R1001=imagecolorallocate($image,0,60,121);
$T1002=imagecolorallocate($image,111,141,185);		
$F1002=imagecolorallocate($image,111,141,185); 	
$R1002=imagecolorallocate($image,111,141,185); 

$titleX=$imW/2-130;												//标题输出的起始X位置
//imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出标题
//imagettftext($image,15,0,$titleX,20,$TextColor,$UseFont,$Tile);	//输出标题

//$Tile=$Y."年 ".$Name." 月下单、出货数量统计图 ";
$TFontSize=15;
//imagettftext($image,$TFontSize,0,$titleX,20,$TextBlack,$UseFont,$Y."年 ");	//输出
$fontareaYear = ImageTTFBBox($TFontSize,0,$UseFont,$Y."年 ");  //获取文字的宽度
$Year_width = $fontareaYear[2]-$fontareaYear[0];//256
imagettftext($image,$TFontSize,0,$titleX+$Year_width,20,$TextColor,$UseFont,$Name);	//输出
//$font_height = $fontarea[1]-$fontarea[7];//19 
$fontareaName = ImageTTFBBox($TFontSize,0,$UseFont,$Name);  //获取文字的宽度
$Name_width = $fontareaName[2]-$fontareaName[0];//256
imagettftext($image,$TFontSize,0,$titleX+$Year_width+$Name_width,20,$TextBlack,$UseFont," 月下单、出货数量统计图 ");	//输出



$imL=50;														//顶部X:左边距
$imT=50;														//顶部Y:上边距
$imR=$imW-110;													//底部X:
$imB=$imH-50;													//底部Y:
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
$MoveLeft=0;													//上下偏移量，柱体厚度

imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imR-$MoveLeft,$imB+$MoveLeft,$TextBlack);	//左移线
imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imL-$MoveLeft,$imT+$MoveLeft,$TextBlack);	//下移线

$MonthTemp=date("m")-1;
for($i=1;$i<=$Y+1;$i++){
	$MonthTemp++;
	$MonthTemp=$MonthTemp%12==1?1:$MonthTemp;
	if($MonthTemp%12==0)$StartY=$StartY+1;
	if($i%2==0){
		//矩形
		imagefilledrectangle($image,($imL+($i-1)*$MonthStep),$imB-1,($imL+($i-1)*$MonthStep)+100,$imT+1,$Gridbgcolor); //画填充矩形
		}
		
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imT-15,$Gridcolor);
		imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep,$imB,$Gridcolor);
		imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft+20,$TextBlack);
		
	//输出月份
	
		if($MonthTemp==1){
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20+$MoveLeft,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imB+35+$MoveLeft,$TextRed,$UseFont,$StartY."年");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imT-20,$TextRed,$UseFont,$StartY."年");
			}
		else{
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20+$MoveLeft,$TextBlack,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextBlack,$UseFont,$MonthTemp."月");
			}
	}

//金额间隔线
$YStep=40;
for($i=0;$i<=$Ys;$i++){	
	$TempValue=$Yvalue*$i;//输出的数量
	if($i==0 || $i==$countY){
		imageline($image,$imL,$imB-$i*$YStep,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$TextBlack);		//斜线
		imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$TextBlack);//短线
		}
	else{
		imageline($image,$imL,$imB-$i*$YStep,$imR,$imB-$i*$YStep,$Gridcolor);			//间隔线
		imageline($image,$imL,$imB-$i*$YStep,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$Gridcolor);		//斜线
		imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$Gridcolor);//短线
		//输出金额
		$TempValueX=$TempValue<100000?10:3;
 		imagestring($image,3,$TempValueX-$MoveLeft,($imB-$YStep*$i)+$MoveLeft-7,$TempValue,$TextBlack);
		}
	}

//客户过滤:最高出货、或下单金额超过20万的客户

//初始化XL,XR,YB,YT
$wCube=30;		//柱体宽度
$jCube=10;		//柱体间隔
$ToDay=date("Y-m-d");
//月份检查:包括出货或下单的月份
$MonthSql=mysql_query("
	SELECT Date FROM ( 
		SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE $TjOut GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		UNION ALL 
		SELECT M.OrderDate AS Date FROM $DataIn.yw1_ordermain M WHERE $TjIn GROUP BY DATE_FORMAT(M.OrderDate ,'%Y-%m')
	)A GROUP BY DATE_FORMAT(Date,'%Y-%m')
	",$link_id);
if($MonthRow=mysql_fetch_array($MonthSql)){
	$i=1;$mS=0;
	$SumOutQty=0;
	do{
		$theMonth=date("Y-m",strtotime($MonthRow["Date"]));
		//出货总数
		$clientSql=mysql_query("
			SELECT SUM(Qty) AS Qty FROM (
				SELECT SUM(S.Qty) AS Qty FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE M.Estate=0  $ProductType AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube*2+$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽 改4
			$XR=$XL+$wCube;
			$ComPanyIdS="";
			$SumQty=0;
			$Qty=$clientRow["Qty"];
			$SumOutQty=$SumOutQty+$Qty;
			$shipQty=round($Qty/($Yvalue/40));		//出货总额高度
			$YT =$YB-$shipQty;
			//画柱形图正面
			if($Qty>0){
				$mS=$mS+1;
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$T1001); //画填充矩形	//改3
				imagettftext($image,12,0,$XL-$MoveL,$YT-2,$T1001,$UseFont,$Qty);
				}
			$YB=$YT;
			}
		//下单总数
		$clientSql=mysql_query("
			SELECT SUM(Qty) AS Qty FROM(
				SELECT SUM(S.Qty) AS Qty FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' $ProductType
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube*2;									//改2
			$XR=$XL+$wCube;
			$Qty=$clientRow["Qty"];
			$OrderQty=round($Qty/($Yvalue/40));		//出货总额高度
			$YT =$YB-$OrderQty;
			//画柱形图正面
			if($Qty>0){
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$T1002); //画填充矩形	//改1
				imagettftext($image,12,30,$XL-$MoveL+5,$YT-2,$T1002,$UseFont,$Qty);
				}
			$YB=$YT;
			}
		//**********************
		$i++;
		}while ($MonthRow=mysql_fetch_array($MonthSql));
	//出货平均值
	//$LastY=$Y==date("Y")?date("n"):12;
	$checkPreSql=mysql_fetch_array(mysql_query("
		SELECT SUM(S.Qty)/($Y+1) AS AvgQty 
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		WHERE M.Estate=0  $ProductType AND DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth'
	",$link_id));
	$AvgQty=intval($checkPreSql["AvgQty"]);
	if($AvgQty>0){
		$tempY=round($AvgQty/($Yvalue/40));						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$TextRed);	//画平均线
		imagestring($image,3,$imR+10,$imB-$tempY-7,number_format($AvgQty),$TextRed);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+18,$TextRed,$UseFont,"$PreAmount"."出货均线");
		}
	}
//说明
imagefilledrectangle($image,$imW-100,$imH-10,$imW-80,$imH-30,$T1001);
imagettftext($image,10,0,$imW-70,$imH-15,$T1001,$UseFont,"出货");
imagefilledrectangle($image,$imW-100,$imH-40,$imW-80,$imH-60,$T1002);
imagettftext($image,10,0,$imW-70,$imH-45,$T1002,$UseFont,"下单");
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>