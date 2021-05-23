<?php   
/*下单和出货,图表高度固定
分隔线数量和代表金额值自动根据最高值计算//立体柱形图**********电信---yang 20120801
*/
//关闭当前页面的PHP警告及提示信息
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 
include "../basic/chksession.php";
include "../basic/parameter.inc";
$CheckMonths=$ChooseMonth;				//要计算的月份数
$CheckMonth=date("Y-m-01");		//当前月第一天
$StartDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$StartMonth=date("Y-m",strtotime("$CheckMonth -$CheckMonths month"));
$StartY=date("Y",strtotime("$StartDate"));
$ToDate=date("Y-m-d");
include "chartgetcolor.php";   //取得相对应类别的颜色
$ClientStr=$ClientStr==""?" AND M.CompanyId='$CID'":$ClientStr;
$Tile=$Forshort." 月下单、出货金额统计图 ";

//出货或下单最高金额
$TjOut="DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth'";
$TjIn="DATE_FORMAT(M.OrderDate,'%Y-%m')>='$StartMonth'";
//********************客户颜色
$ClientColor=mysql_fetch_array(mysql_query("SELECT B.ColorCode FROM $DataIn.chart2_color B 
      LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
      WHERE 1 AND C.CompanyId='$CID' LIMIT 1",$link_id));
$ColorCode=$ClientColor["ColorCode"];
$RGB=hexdec(substr($ColorCode,0,2));
$rColor= hexdec(substr($ColorCode,0,2));
$gColor= hexdec(substr($ColorCode,2,2));
$bColor= hexdec(substr($ColorCode,-2));


$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Amount) AS MaxValue1 FROM ( 
		SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		    LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
			WHERE $TjOut $ClientStr GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		) B GROUP BY Month
	UNION ALL 
		SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE $TjIn $ClientStr GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
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
$imH=$Ys*40+100;								//Y轴高
//********************

$MonthStep=80;													//月份间隔步长
$imW=($ChooseMonth+1)*$MonthStep+160;										//图像宽度
$Diameter=5;													//圆点直径
$UseFont = "../model/Fonts/simhei.ttf"; 						//使用的中文字体
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
$alpha_white=imagecolorallocatealpha($image, 255, 255, 255,30); 

$titleX=$imW/2-130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出

$imL=50;														//顶部X:左边距
$imT=50;														//顶部Y:上边距
$imR=$imW-110;													//底部X:
$imB=$imH-50;													//底部Y:
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
$MoveLeft=0;													//上下偏移量，柱体厚度

imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imR-$MoveLeft,$imB+$MoveLeft,$TextBlack);	//左移线
imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imL-$MoveLeft,$imT+$MoveLeft,$TextBlack);	//下移线

$MonthTemp=date("m")-1;
$Temp=$MonthTemp;
for($i=1;$i<=$ChooseMonth+1;$i++){
	$MonthTemp++;
	if($ChooseMonth>6){$MonthTemp=$MonthTemp%12==1?1:$MonthTemp;}
	else {
	      if($ChooseMonth>$Temp){$MonthTemp=12-($ChooseMonth-$Temp);$MonthTemp++;}
		  else {$MonthTemp=($Temp-$ChooseMonth);$MonthTemp++;}
		  $Temp++;
	     }
	if($MonthTemp%12==0)$StartY=$StartY+1;
	if($i%2==0){
		//矩形
		imagefilledrectangle($image,($imL+($i-1)*$MonthStep),$imB-1,($imL+($i-1)*$MonthStep)+80,$imT+1,$Gridbgcolor); //画填充矩形
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
$wCube=20;		//柱体宽度
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
	$SumOutAmount=0;
	do{
		$theMonth=date("Y-m",strtotime($MonthRow["Date"]));
		//出货总数
		$clientSql=mysql_query("
			SELECT SUM(Amount) AS Amount FROM (
				SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		        LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
				WHERE M.Estate=0 $ClientStr  AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube*2+$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽 改4
			$XR=$XL+$wCube;
			$ComPanyIdS="";
			$SumAmount=0;
			$Amount=sprintf("%.0f",$clientRow["Amount"]);
			$SumOutAmount=$SumOutAmount+$Amount;
			$shipAmount=round($Amount/($Yvalue/40));		//出货总额高度
			$YT =$YB-$shipAmount;
			//画柱形图正面
			if($Amount>0){
				$mS=$mS+1;
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$T1001); //画填充矩形	//改3
				imagettftext($image,10,0,$XL-$MoveL,$YT-2,$T1001,$UseFont,number_format($Amount));
				}
			$YB=$YT;
			}
		//下单总数
		$clientSql=mysql_query("
			SELECT SUM(Amount) AS Amount FROM(
				SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
				FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		        LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
				WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' $ClientStr
			) A
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube*2;									//改2
			$XR=$XL+$wCube;
			$Amount=sprintf("%.0f",$clientRow["Amount"]);
			$OrderAmount=round($Amount/($Yvalue/40));		//出货总额高度
			$YT =$YB-$OrderAmount;
			//画柱形图正面
			if($Amount>0){
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$T1002); //画填充矩形	//改1
				//imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$alpha_white); //画透明矩形
				imagettftext($image,10,30,$XL-$MoveL+5,$YT-2,$T1002,$UseFont,number_format($Amount));
				}
			$YB=$YT;
			}
		//**********************
		$i++;
		}while ($MonthRow=mysql_fetch_array($MonthSql));
	//出货平均值
	//$LastY=$Y==date("Y")?date("n"):12;
	$checkPreResult=mysql_query("
		SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign)/($ChooseMonth) AS AvgAmount 
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
		WHERE M.Estate=0 $ClientStr  AND DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth'
	",$link_id);
	 if ($checkPreResult)   {
	         $checkPreSql=mysql_fetch_array($checkPreResult);
	         $AvgAmount=intval($checkPreSql["AvgAmount"]);
	}
	
	if($AvgAmount>0){
		$tempY=round($AvgAmount/($Yvalue/40));						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$TextRed);	//画平均线
		imagestring($image,3,$imR+10,$imB-$tempY-7,number_format($AvgAmount),$TextRed);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+18,$TextRed,$UseFont,"出货均线");
		}
		
	//下单平均值
	$CheckOrderResult=mysql_query("
				SELECT SUM(S.Qty*S.Price*C.Rate)/($ChooseMonth) AS AvgorderAmount
				FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		        LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
				WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')>='$StartMonth' $ClientStr",$link_id);
      if ($CheckOrderResult) {
	       $CheckOrderSql=mysql_fetch_array($CheckOrderResult);
	       $AvgorderAmount=intval($CheckOrderSql["AvgorderAmount"]);
      }
	 
	  if($AvgorderAmount>0){
		$tempY=round($AvgorderAmount/($Yvalue/40));						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$TextGreen);	//画平均线
		imagestring($image,3,$imR+10,$imB-$tempY-7,number_format($AvgorderAmount),$TextGreen);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+18,$TextGreen,$UseFont,"下单均线");
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