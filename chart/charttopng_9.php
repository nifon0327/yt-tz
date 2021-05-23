<?php   
/*下单和出货，以配件分类读取数据**********电信---yang 20120801
图表高度固定
分隔线数量和代表金额值自动根据最高值计算
独立已更新
*/
include "../basic/chksession.php";
//立体柱形图
include "../basic/parameter.inc";

$ToDate=date("Y-m-d");
$Tile=$Y."年 透明壳/硅胶套月下单、出货数量统计图";
//$TypeId=9039;
//统计的分类
$StuffStr="AND D.TypeId='9037'";//透明壳
$ProductType="AND P.TypeId='8021'";//硅胶套
//出货或下单最高金额
$TjOut="DATE_FORMAT(M.Date,'%Y')='$Y'";
$TjIn="DATE_FORMAT(M.OrderDate,'%Y')='$Y'";

$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Qty) AS MaxValue1 FROM ( 
		SELECT SUM(Qty) AS Qty FROM(
			SELECT SUM(G.OrderQty) AS Qty,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE $TjOut $StuffStr GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
			UNION ALL
			SELECT SUM(S.Qty) AS Qty,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE $TjOut $ProductType GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		) B GROUP BY Month
	UNION ALL 
		SELECT SUM(Qty) AS Qty FROM(
			SELECT SUM(G.OrderQty) AS Qty,DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month 
				FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE $TjIn $StuffStr GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
			UNION ALL
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
$imH=$Ys*25+100;								//Y轴高
//********************

$MonthStep=100;													//月份间隔步长
$imW=12*$MonthStep+160;											//图像宽度
$Diameter=5;													//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
$image = imagecreate ($imW,$imH);								//输出空白图像
imagecolorallocate($image,255,255,255);							//图像背景色

////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);

//颜色列表
$T1out=imagecolorallocate($image,0,60,121);$T1in=imagecolorallocate($image,111,141,185);
$T2in=imagecolorallocate($image,153,204,0);$T2out=imagecolorallocate($image,103,144,0);

$titleX=$imW/2-130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出标题

$imL=50;														//顶部X:左边距
$imT=50;														//顶部Y:上边距
$imR=$imW-110;													//底部X:
$imB=$imH-50;													//底部Y:
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
$MoveLeft=0;													//上下偏移量，柱体厚度

imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imR-$MoveLeft,$imB+$MoveLeft,$TextBlack);	//左移线
imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imL-$MoveLeft,$imT+$MoveLeft,$TextBlack);	//下移线

$MonthTemp=11;
for($i=0;$i<13;$i++){
	$MonthTemp++;
	$MonthTemp=$MonthTemp>12?1:$MonthTemp;
	if($i%2!=0){
		//矩形
		imagefilledrectangle($image,($imL+$i*$MonthStep),$imB-1,($imL+$i*$MonthStep)+100,$imT+1,$Gridbgcolor); //画填充矩形
		}

	if($i==0 || $i==12){
		imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep,$imB,$TextBlack);
		imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft+20,$TextBlack);
		}
	else{
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imT-15,$Gridcolor);
		imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep,$imB,$Gridcolor);
		imageline($image,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft,$imL+$i*$MonthStep-$MoveLeft,$imB+$MoveLeft+20,$Gridcolor);
		}
	//输出月份
	if($i>0){
		if($MonthTemp==1){
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20+$MoveLeft,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imB+35+$MoveLeft,$TextRed,$UseFont,$Y."年");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imT-20,$TextRed,$UseFont,$Y."年");
			}
		else{
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20+$MoveLeft,$TextBlack,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextBlack,$UseFont,$MonthTemp."月");
			}
		}
	}

//金额间隔线
$YStep=25;
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
		SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE $TjOut GROUP BY DATE_FORMAT(Date,'%Y-%m')
		UNION ALL 
		SELECT M.OrderDate AS Date FROM $DataIn.yw1_ordermain M WHERE $TjIn GROUP BY DATE_FORMAT(OrderDate ,'%Y-%m')
	)A GROUP BY DATE_FORMAT(Date,'%Y-%m')
	",$link_id);
if($MonthRow=mysql_fetch_array($MonthSql)){
	$i=1;
	$mS=0;
	$SumOutQty=0;
	do{
		$theMonth=date("Y-m",strtotime($MonthRow["Date"]));
		//透明壳出货总数
		$clientSql=mysql_query("
			SELECT SUM(Qty) AS Qty FROM (
				SELECT SUM(G.OrderQty) AS Qty FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE M.Estate=0  $StuffStr AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'
			) A
			",$link_id);
		$YBnext=$imB;
		$QtyIn=0;
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube*2+$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$XR=$XL+$wCube;
			$ComPanyIdS="";
			$SumQty=0;
			$Qty=$clientRow["Qty"];
			$QtyIn=$QtyIn+$Qty;
			$SumOutQty=$SumOutQty+$Qty;
			$shipQty=round($Qty/($Yvalue/25));		//出货总额高度
			$YT =$YB-$shipQty;
			//画柱形图正面
			if($Qty>0){
				$mS=$mS+1;
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$T1out); //画填充矩形
				}
			$YBnext=$YT;//硅胶套画图底坐标
			}
		//硅胶套出货总数
		$clientSql=mysql_query("
			SELECT SUM(Qty) AS Qty FROM (
				SELECT SUM(S.Qty) AS Qty FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE M.Estate=0  $ProductType AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			$Qty=$clientRow["Qty"];
			$QtyIn=$QtyIn+$Qty;
			$SumOutQty=$SumOutQty+$Qty;
			$shipQty=round($Qty/($Yvalue/25));		//出货总额高度
			$YT =$YBnext-$shipQty;
			//画柱形图正面
			if($Qty>0){
				$mS=$mS==0?$i:$mS;
				imagefilledrectangle($image,$XL,$YBnext,$XR,$YT,$T2out); //画填充矩形
				imagettftext($image,12,0,$XL-$MoveL,$YT-2,$TextBlack,$UseFont,$QtyIn);
				}
			}
		$YBnext=$imB;
		$QtyOut=0;
		//透明壳下单总数
		$clientSql=mysql_query("
			SELECT SUM(Qty) AS Qty FROM(
				SELECT SUM(G.OrderQty) AS Qty FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' $StuffStr
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube*2;
			$XR=$XL+$wCube;
			$Qty=$clientRow["Qty"];
			$QtyOut=$QtyOut+$Qty;
			$OrderQty=round($Qty/($Yvalue/25));		//出货总额高度
			$YT =$YB-$OrderQty;
			//画柱形图正面
			if($Qty>0){
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$T1in); //画填充矩形
				}
			$YBnext=$YT;
			}
		$clientSql=mysql_query("
			SELECT SUM(Qty) AS Qty FROM(
				SELECT SUM(S.Qty) AS Qty FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' $ProductType
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			$Qty=$clientRow["Qty"];
			$QtyOut=$QtyOut+$Qty;
			$shipQty=round($Qty/($Yvalue/25));		//出货总额高度
			$YT =$YBnext-$shipQty;
			//画柱形图正面
			if($Qty>0){
				$mS=$mS==0?$i:$mS;
				imagefilledrectangle($image,$XL,$YBnext,$XR,$YT,$T2in); //画填充矩形
				imagettftext($image,12,30,$XL-$MoveL,$YT-2,$TextBlack,$UseFont,$QtyOut);
				}
			}
		//**********************
		$i++;
		}while ($MonthRow=mysql_fetch_array($MonthSql));
	//出货平均值
	$LastY=$Y==date("Y")?date("n"):12;
	$checkPreSql=mysql_fetch_array(mysql_query("
	SELECT SUM(AvgQty) AS AvgQty FROM (
	SELECT SUM(G.OrderQty)/($LastY-DATE_FORMAT(Min(M.Date),'%c')+1) AS AvgQty 
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	WHERE M.Estate=0  $StuffStr AND DATE_FORMAT(M.Date,'%Y')='$Y'
	UNION ALL
	SELECT SUM(S.Qty)/(DATE_FORMAT(Max(M.Date),'%c')-DATE_FORMAT(Min(M.Date),'%c')+1) AS AvgQty 
	FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE M.Estate=0  $ProductType AND DATE_FORMAT(M.Date,'%Y')='$Y') A
				
	",$link_id));
	$AvgQty=intval($checkPreSql["AvgQty"]);
	if($AvgQty>0){
		$tempY=round($AvgQty/($Yvalue/25));						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$TextRed);	//画平均线
		imagestring($image,3,$imR+10,$imB-$tempY-7,number_format($AvgQty),$TextRed);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+18,$TextRed,$UseFont,"(出货均线)");
		}
	}
//说明
imagettftext($image,10,0,$imW-105,$imT+5,$TextBlack,$UseFont,"下单 出货");
imagefilledrectangle($image,$imW-100,$imT+20,$imW-80,$imT+40,$T2in); 	imagefilledrectangle($image,$imW-70,$imT+20,$imW-50,$imT+40,$T2out);	imagettftext($image,10,0,$imW-40,$imT+35,$T2out,$UseFont,"硅胶套");
imagefilledrectangle($image,$imW-100,$imT+60,$imW-80,$imT+80,$T1in); 	imagefilledrectangle($image,$imW-70,$imT+60,$imW-50,$imT+80,$T1out);	imagettftext($image,10,0,$imW-40,$imT+75,$T1out,$UseFont,"透明壳");

//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>