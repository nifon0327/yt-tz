<?php   
//独立已更新电信---yang 20120801

include "../basic/parameter.inc";

$CheckYear=$CheckYear==""?date("Y"):$CheckYear;

// add by zx 2011-01-27 begin 
$NowYear=date("Y"); 
$NowMonth=date("m"); 
if (($NowMonth<"03")  && ($NowYear==$CheckYear) ){  //就是从去年到今年的一起,跨年的
	$CheckYear=$CheckYear-1;  //去年1月份开始,计算到现在
	$TJA=" AND DATE_FORMAT(M.Date,'%Y')>='$CheckYear' AND M.Date>='2008-09-01'";
	$TJB=" AND DATE_FORMAT(M.OrderDate,'%Y')>='$CheckYear' AND M.OrderDate>='2008-09-01'";
	$TotalMonth=12+$NowMonth*1;  //总月数
}
else{
	$TJA=" AND DATE_FORMAT(M.Date,'%Y')='$CheckYear' AND M.Date>='2008-09-01'";
	$TJB=" AND DATE_FORMAT(M.OrderDate,'%Y')='$CheckYear' AND M.OrderDate>='2008-09-01'";
	$TotalMonth=12;
	$NowYear="";
}




$imW=100;											//图像宽度
$imH=0;	//100										//图像高度
$imH=$imH+0;  //50
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体

$TJA=" AND DATE_FORMAT(M.Date,'%Y')='$CheckYear' AND M.Date>='2008-09-01'";
//为了步调一致，用了上一个图的宽度，及间隔
$ShipResult = mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType,R.Name,R.Color,R.rColor,R.gColor,R.bColor
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA  AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY T.mainType
",$link_id);
$clientNum= mysql_num_rows($ShipResult);
$clientNumH=$clientNum*50+50;
$CimW=$CimW==""?1500:$CimW;
//$imW=$CimW+50

$image = imagecreate ($CimW+150,$imH+$clientNumH); //利用上一个图的宽度在$CimW,charttopng_2_mType.php	(1200,500)
	
imagecolorallocate($image,255,255,255);							//图像背景色
////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);
$Tallout=imagecolorallocate($image,0,55,152);



$imL=50;														//顶部X:左边距
$imT=0;   //50
$imR=$imW-50;													//底部X:
														//顶部Y:上边距
//总出货下单图例
//出货下单总额图
$AllimL=$imL+99;
$AllimT=$imH+10;     //10
$AllimR=$imR+100;    //100
$AllimB=$imH+$clientNumH-40;
imagerectangle($image,$AllimL,$AllimT,$AllimR,$AllimB,$TextBlack); 			//画矩形：曲线图范围
//Y轴间隔线
for($k=0;$k<=$clientNum;$k++){
	imageline($image,$imL,$AllimB-$k*50,1250,$AllimB-$k*50,$TextBlack);
	}

//取最大值,用来求百分比
//$TJA=" AND DATE_FORMAT(M.Date,'%Y')='$CheckYear' AND M.Date>='2008-09-01'"; 在charttopng_2_mType.php
//总计
$CheckMaxRow= mysql_fetch_array(mysql_query("
			SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS MAXClientAmount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA  AND T.mainType IS NOT NULL  			
",$link_id));
$TotalAmount=$CheckMaxRow["MAXClientAmount"];


//
$CheckMaxRow= mysql_fetch_array(mysql_query("SELECT MAX(MAXClientAmount) AS MAXClientAmount FROM(
			SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS MAXClientAmount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA  AND T.mainType IS NOT NULL	GROUP BY T.mainType			
) A",$link_id));
$MAXClientAmount=$CheckMaxRow["MAXClientAmount"];
//echo "MAXClientAmount:$MAXClientAmount";
$xValue=ceil($MAXClientAmount/1000000)*1000;//1X像素点的金额
$xStep=50;//每隔像素值
//出货总额
if($CheckAllRow=mysql_fetch_array($ShipResult)){
	$CimL=$AllimL+1;			//固定值
	$CimB=$AllimB-1;		//初始化位置为底部上升5
	$i=1;
	do{
		$CimT=$CimB-20;
		$MName=$CheckAllRow["Name"];
		$OutAmount=sprintf("%.0f",$CheckAllRow["Amount"]);
		$R=$CheckAllRow["rColor"];
		$G=$CheckAllRow["gColor"];
		$B=$CheckAllRow["bColor"];
		//$mtColor="mtColor".strval($Id);
		//$$mtColor=imagecolorallocate($image,$R,$G,$B);
		$mtColor=imagecolorallocate($image,$R,$G,$B);
		imagettftext($image,12,0,$imL,$CimB-5,$mtColor,$UseFont,$i."-".$MName);
		
		//画出货条形图
		
		$OutPC=sprintf("%.1f",$OutAmount*100/$TotalAmount);
		$CimR2=intval(sprintf("%.0f",$OutAmount/$xValue));
		$CimB2=$CimT;
		$CimT2=$CimB2-20;
		if($CimR2>0){
			imagefilledrectangle($image,$CimL,$CimB2+11,$CimL+$CimR2,$CimT2-2,$mtColor);
			}
		//////////////////////////////主分类在出货中的比例////////////////////////////////////
		///////////////////////////////////////////////////////////////////////////////
		if($OutAmount>0){
			imagettftext($image,11,0,$CimL+$CimR2+10,$CimB2,$TextBlack,$UseFont,number_format($OutAmount)."(".$OutPC."%)");
			}
		//Y轴累加
		$CimB=$CimT2-10;
		$i++;
		}while ($CheckAllRow=mysql_fetch_array($ShipResult));
	}

//X轴间隔线
//echo "Gridcolor:$Gridcolor";
for($k=1;$k<23;$k++){
	imageline($image,$AllimL+$k*$xStep,$AllimT,$AllimL+$k*$xStep,$AllimB+10,$Gridcolor);//间隔线
	if($k<22){
		$m=$xValue*$xStep*$k/10000;
	imagettftext($image,12,0,$AllimL+$k*$xStep-15,$AllimB+25,$TextBlack,$UseFont,$m);//间隔值
	}
	}
imagettftext($image,12,0,60,$AllimB+25,$TextBlack,$UseFont,"(单位:万元)");

imagefilledrectangle($image,990,$AllimT+1,1248,$AllimT+40,$Gridbgcolor);

if ($NowYear!=""){  //跨年显示
	imagettftext($image,14,0,1050,$AllimT+30,$TextBlack,$UseFont,$CheckYear."-".$NowYear."年出货(按类别)");
}
else{
	imagettftext($image,14,0,1050,$AllimT+30,$TextBlack,$UseFont,$CheckYear."年出货(按类别)");
	
}

//---------------------------------------------------------------------------



/*


$strMName="";
$strValue="";
$strColor="";
$ShipResult = mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType,R.Name,R.Color
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA  AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY T.mainType
",$link_id);

if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$cbAmount=0;
		$TempRMB=0;
		$TempPC=0;
		$mainType=$ShipRow["mainType"];
		$MName=$ShipRow["Name"];
		$Color=$ShipRow["Color"];
		$Color=str_replace("#","0x",$Color)*1;
		$TempRMB=sprintf("%.0f",$ShipRow["Amount"]);
		
		if($i<2){
			$strMName="$MName";  //客户名称
			$strValue="$TempRMB";  //客户名称
			$strColor="$Color";  //客户名称
		}
		else{
			$strMName=$strMName."|".$MName;  //客户名称
			$strValue=$strValue."|".$TempRMB;  //客户名称
			$strColor=$strColor."|".$Color;  //客户名称
		}
		
	
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}


$labLst=explode("|",$strMName);
$datLst=explode("|",$strValue);
$clrLsts =explode("|",$strColor);

*/





header("Content-type: image/png");
imagepng($image);
imagedestroy($image);   //释放资源

?>