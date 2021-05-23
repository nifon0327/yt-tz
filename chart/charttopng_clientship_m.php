<?php   	
//独立已更新**********电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
if($CID==""){
	$image = imagecreate (600,200);								//输出空白图像
	imagecolorallocate($image,255,255,255);							//图像背景色
	
	$Tile="请选择客户...";
	$TextBlack = imagecolorallocate($image,0,0,0);
	$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
	imagettftext($image,15,0,250,100,$TextBlack,$UseFont,$Tile);	//输出标题
	}
else{
	//出货最高金额
	$NowY=$Y==""?date("Y"):$Y;
	$PreY=$NowY-1;
	
	//之前的均线
	$PreAmount=0;
	for($TempY=2008;$TempY<$NowY;$TempY++){
		//上一年度出货平均值：从有数据那个月开始
		$checkPreSql=mysql_fetch_array(mysql_query("
				SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign)/(13-DATE_FORMAT(Min(M.Date),'%c')) AS AvgQty
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
				WHERE M.Estate=0 AND M.CompanyId='$CID' AND DATE_FORMAT(M.Date,'%Y')='$TempY'",$link_id));
		$TempPreAmount=intval($checkPreSql["AvgQty"]);
		$TempAmonutSTR="PreAmount".strval($TempY); 
		$$TempAmonutSTR=$TempPreAmount;
		$PreAmount=$PreAmount<$TempPreAmount?$TempPreAmount:$PreAmount;
		}
	/*上一年度平均出货值：有出货的月份
	$checkPreA=mysql_fetch_array(mysql_query("
		SELECT AVG(PreAmount) AS PreAmount FROM(
			SELECT SUM(PreAmount) AS PreAmount FROM(
				SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS PreAmount,DATE_FORMAT(M.Date,'%Y-%m') AS Month
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
				WHERE M.Estate=0 AND M.CompanyId='$CID' AND DATE_FORMAT(M.Date,'%Y')='$PreY' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
			)N GROUP BY Month
		) M
	",$link_id));
	$PreAmount=intval($checkPreA["PreAmount"]);
	*/
	
	$NowResult = mysql_fetch_array(mysql_query("
		SELECT MAX(Amount) AS MaxAmont FROM ( 
		SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE M.Estate=0 AND M.CompanyId='$CID' AND DATE_FORMAT(M.Date,'%Y')='$NowY' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		UNION ALL
		SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 AND M.CompanyId='$CID' AND DATE_FORMAT(M.OrderDate,'%Y')='$NowY' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
		)A 
		",$link_id));
	$NowAmount=intval($NowResult["MaxAmont"]);	//范围月份内最高总额
	if($NowAmount>0){
	$MaxAmount=$PreAmount>$NowAmount?$PreAmount:$NowAmount;

	//间隔值计算
	$AmountLenght=strlen($MaxAmount)-2;
	$AmountStep=pow(10,$AmountLenght);
	$AmountStep=$AmountStep*intval($MaxAmount/($AmountStep*10));
	
	$MonthStep=50;													//月份间隔步长
	$imW=12*$MonthStep+300;											//图像宽度
	//图像高度计算(预加100)
	//Y轴间隔数
	$Ys=ceil($MaxAmount/$AmountStep)+1;
	$imH=$Ys*25+100;
	$Diameter=5;													//圆点直径
	$image = imagecreate ($imW,$imH+100);								//输出空白图像
	imagecolorallocate($image,255,255,255);							//图像背景色
	////设置字体颜色
	$Tallin=imagecolorallocate($image,191,208,234); $Tallout=imagecolorallocate($image,0,55,152);
	$Gridcolor=imagecolorallocate($image,153,153,153);
	$Gridbgcolor=imagecolorallocate($image,221,221,221);
	$TextRed= imagecolorallocate($image,255,0,0);
	$TextLess= imagecolorallocate($image,250,130,130);//出货少于平均线
	$TextGreen= imagecolorallocate($image,120,220,120);
	$TextWhite= imagecolorallocate($image,255,255,255);
	$TextBlack = imagecolorallocate($image,0,0,0);
	$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
$T1001=imagecolorallocate($image,0,60,121); 	$F1001=imagecolorallocate($image,0,60,121); 	$R1001=imagecolorallocate($image,0,60,121);
$T1002=imagecolorallocate($image,111,141,185);		$F1002=imagecolorallocate($image,111,141,185); 	$R1002=imagecolorallocate($image,111,141,185); 

	$Tile=$NowY."年 $Forshort 月出货总额条形图";
	$titleX=$imW/2-130;												//标题输出的起始X位置
	imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出标题
	
	$imL=100;														//顶部X:左边距
	$imT=50;														//顶部Y:上边距
	$imR=$imW-200;													//底部X:
	$imB=$imH-50;													//底部Y:
	$MoveLeft=0;													//上下偏移量，柱体厚度
	
	imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imR-$MoveLeft,$imB+$MoveLeft,$TextBlack);	//左移线
	imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imL-$MoveLeft,$imT+$MoveLeft,$TextBlack);	//下移线
///////////////////输出分类图块//////////////////////////////////
imagesetthickness ($image,3);
$RemarkSql=mysql_query("SELECT * FROM $DataIn.productmaintype",$link_id);
if($RemarkRow= mysql_fetch_array($RemarkSql)){
	$i=1;
	do{
		$Id=$RemarkRow["Id"];
		$Name=$RemarkRow["Name"];
		$R=$RemarkRow["rColor"];
		$G=$RemarkRow["gColor"];
		$B=$RemarkRow["bColor"];
		$mtColor="mtColor".strval($Id);
		$$mtColor=imagecolorallocate($image,$R,$G,$B);
		imagerectangle($image,5+$i*100,$imH-22,25+$i*100,$imH-2,$$mtColor);
		//imagefilledrectangle($image,5+$i*100,$imH-5,25+$i*100,$imH-25,$$mtColor);
		imagettftext($image,12,0,30+$i*100,$imH-5,$TextBlack,$UseFont,$Name);
		$i++;
		}while($RemarkRow= mysql_fetch_array($RemarkSql));
	}
imagesetthickness ($image,1);
//////////////////////////////////////////////////////////////////

	$MonthTemp=11;
	for($i=0;$i<13;$i++){
		$MonthTemp++;
		$MonthTemp=$MonthTemp>12?1:$MonthTemp;
		if($i%2!=0){
			//矩形
			imagefilledrectangle($image,($imL+$i*$MonthStep),$imB-1,($imL+$i*$MonthStep)+$MonthStep,$imT+1,$Gridbgcolor); //画填充矩形
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
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20+$MoveLeft,$TextBlack,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextBlack,$UseFont,$MonthTemp."月");
			}
		}
	
	//金额间隔线
	$YStep=25;							//每间隔的像素值
	for($i=0;$i<=$Ys;$i++){				//$Ys间隔的数量
		$TempAmount=$AmountStep*$i;		//间隔金额值
		if($i==0 || $i==$Ys){
			imageline($image,$imL,$imB-$i*$YStep,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$TextBlack);		//斜线
			imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$TextBlack);//短线
			}
		else{
			imageline($image,$imL,$imB-$i*$YStep,$imR,$imB-$i*$YStep,$Gridcolor);			//间隔线
			imageline($image,$imL,$imB-$i*$YStep,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$Gridcolor);		//斜线
			imageline($image,$imL-$MoveLeft,$imB-$i*$YStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$YStep+$MoveLeft,$Gridcolor);//短线
			//输出金额的位置计算
			
			$TempAmountX=strlen($TempAmount)*10;
			imagestring($image,3,95-$TempAmountX+strlen($TempAmount),($imB-$YStep*$i)+$MoveLeft-7,number_format($TempAmount),$TextBlack);
			}
		}
	imagettftext($image,11,0,36,$imT+4,$TextBlack,$UseFont,"金额(元)");
	
	//初始化XL,XR,YB,YT
	$wCube=20;		//柱体宽度
	$jCube=10;		//柱体间隔
	$ToDay=date("Y-m-d");
	$SumCurYearOAmount=0;   //modify by zx  2010-11-19
	$SumMonth=0;            //modify by zx  2010-11-19
	//月份检查:包括出货或下单的月份
	$MonthSql=mysql_query("
	SELECT Date FROM (
	SELECT Date FROM $DataIn.ch1_shipmain WHERE CompanyId='$CID' AND DATE_FORMAT(Date,'%Y')='$NowY' GROUP BY DATE_FORMAT(Date,'%Y-%m')
	UNION ALL
	SELECT OrderDate AS Date FROM $DataIn.yw1_ordermain WHERE CompanyId='$CID' AND DATE_FORMAT(OrderDate,'%Y')='$NowY' GROUP BY DATE_FORMAT(OrderDate,'%Y-%m')
	) A WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date
	",$link_id);
	if($MonthRow=mysql_fetch_array($MonthSql)){
		$i=1;
		$YB=$imB+$MoveLeft;
		$YT=$imB;
		$SumMonth=13-date('m',strtotime($MonthRow["Date"]));   //获取开始的月份到12月份的总月数   modify by zx  2010-11-19
		do{
			$theMonth=date("Y-m",strtotime($MonthRow["Date"]));
			$Month=date("m",strtotime($MonthRow["Date"]));
								//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
		//各产品主分类出货金额
imagesetthickness ($image,3);
$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND M.CompanyId='$CID' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$tYB=$imB+$MoveLeft;
			$tYT=$imB;
			$tXL=$imL+$MonthStep*$Month-6;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$tXR=$tXL+4;
			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){
					//条形图颜色
					$Id=$mainTypeRow["mainType"];
					$mainTypeC="mtColor".strval($Id);
					$mainTypeC=$$mainTypeC;
					$TempHight=round($typeAmount/($AmountStep/25));
					$tYT =$tYB-$TempHight;//条形图新的Top座标
					if($tYB-$tYT<5){
						imagerectangle($image,$tXL,$tYT,$tXR,$tYB,$mainTypeC); //画矩形
						}
					else{
						//imagefilledrectangle($image,$tXL,$tYB-2,$tXR,$tYT+2,$TextWhite); //画矩形
						imagerectangle($image,$tXL,$tYT+3,$tXR,$tYB,$mainTypeC); //画矩形
						}
					$tYB=$tYT;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}
$mainTypeSql=mysql_query("
SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,T.mainType
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' AND M.CompanyID='$CID' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC		
		",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$tYB=$imB+$MoveLeft-1;
			$tYT=$imB;
			$tXL=$imL+$MonthStep*($Month-1)+$wCube+5;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$tXR=$tXL+4;
			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){
					//条形图颜色
					$Id=$mainTypeRow["mainType"];
					$mainTypeC="mtColor".strval($Id);
					$mainTypeC=$$mainTypeC;
					$TempHight=round($typeAmount/($AmountStep/25));
					$tYT =$tYT-$TempHight;//条形图新的Top座标
					if($tYB-$tYT<5){
						imagerectangle($image,$tXL,$tYT,$tXR,$tYB,$mainTypeC); //画矩形
						}
					else{
						imagefilledrectangle($image,$tXL,$tYT+2,$tXR,$tYB-2,$TextWhite); //画矩形
						imagerectangle($image,$tXL,$tYT+3,$tXR,$tYB,$mainTypeC); //画矩形
						}
					$tYB=$tYT;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}
		imagesetthickness ($image,1);
		//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

			$clientSql=mysql_query("
				SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
				WHERE M.Estate=0 AND M.CompanyId='$CID' AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'",$link_id);
			if($clientRow=mysql_fetch_array($clientSql)){
				$XL=$imL+$MonthStep*$Month-$wCube*1;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
				$XR=$XL+$wCube-7;
				$Amount=round($clientRow["Amount"]);				//实际出货金额
				$shipY=round($Amount/($AmountStep/25));				//出货总额高度
				$YT =$YB-$shipY;
				//$TempColor=$TextLess;
				$TempColor=$TextGreen;  //全部用绿色,不再分高于还是低于的色 modify by zx  2010-11-19
				if($Amount>=$PreAmount){
					$TempColor=$TextGreen;
					}
				$SumCurYearOAmount=$SumCurYearOAmount+$Amount; //统计当年出货	 modify by zx  2010-11-19
				
				imagefilledrectangle($image,$XL,$imB,$XR,$YT,$TempColor); //画填充矩形
				imagettftext($image,10,90,$XL+12,$imB-2,$TextBlack,$UseFont,number_format($Amount));
				}
			////////////////////////////////////////////////////////
			//当月下单全部
			$AmountTemp=0;
			$AllSql=mysql_query("
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 AND M.CompanyId='$CID' AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' 
			",$link_id);
			if($AllRow=mysql_fetch_array($AllSql)){
				$XL=$imL+$MonthStep*$Month-$wCube*2;
				$XR=$XL+$wCube;
				$Amount=round($AllRow["Amount"]);				//实际出货金额
				$shipY=round($Amount/($AmountStep/25));				//出货总额高度
				$YT =$YB-$shipY;
				imagefilledrectangle($image,$XL,$imB,$XR-7,$YT,$T1002); //画填充矩形
				$AmountTemp=$Amount;
				$XLtemp=$XL+2;
				$YTtemp=$YT;
				
				}
			imagettftext($image,10,20,$XLtemp,$YTtemp,$T1002,$UseFont,number_format($AmountTemp));//总下单金额
			////////////////////////////////////////////////////////
			
			
			$i++;
			}while ($MonthRow=mysql_fetch_array($MonthSql));
		}
	imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
	
	if($SumMonth>0){ //把今年的平均值也加进去  modify by zx  2010-11-19
		$TempAmonutSTR="PreAmount".strval($NowY);  //把今年的平均值也加进去
		$$TempAmonutSTR=intval($SumCurYearOAmount/$SumMonth);
	}
	
	for($TempY=2008;$TempY<=$NowY;$TempY++){
		$TempAmonutSTR="PreAmount".strval($TempY); 
		//上年平均线=平均值/（间隔值/25）
		$PreAmount=$$TempAmonutSTR;
		$Ytemp=round($PreAmount/($AmountStep/25));						//平均线的Y坐标
		imageline($image,$imL,$imB-$Ytemp,$imR+5,$imB-$Ytemp,$TextRed);	//画平均线
		//imagestring($image,3,$imR+10,$imB-$Ytemp-7,,$TextRed);//输出平均值
		imagettftext($image,9,0,$imR+10,$imB-$Ytemp+5,$TextRed,$UseFont,$TempY."年出货均线:".number_format($PreAmount));
		}
	//说明

imagefilledrectangle($image,$imW-190,$imT,$imW-170,$imT+20,$T1002);
imagefilledrectangle($image,$imW-190,$imT+30,$imW-170,$imT+50,$TextGreen);
//imagefilledrectangle($image,$imW-190,$imT+60,$imW-170,$imT+80,$TextLess);
imagettftext($image,10,0,$imW-160,$imT+15,$T1002,$UseFont,"产品下单金额");
//imagettftext($image,10,0,$imW-160,$imT+45,$TextGreen,$UseFont,"出货金额大于上年均值");
imagettftext($image,10,0,$imW-160,$imT+45,$TextGreen,$UseFont,"产品出货金额");

//imagettftext($image,10,0,$imW-160,$imT+75,$TextRed,$UseFont,"出货金额少于上年均值");


//************************************************************************************
//输出年统计图:宽度600

//当年下单、出货总额
$CheckMaxRow= mysql_fetch_array(mysql_query("
SELECT SUM(AmountIn) AS AmountIn,SUM(AmountOut) AS AmountOut FROM(
	SELECT '0' AS AmountIn,SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS AmountOut
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 AND M.CompanyId='$CID' AND DATE_FORMAT(M.Date,'%Y')='$NowY'
UNION ALL 
	SELECT SUM(S.Qty*S.Price*C.Rate) AS AmountIn,'0' AS AmountOut
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 AND M.CompanyId='$CID' AND DATE_FORMAT(M.OrderDate,'%Y')='$NowY'
)A",$link_id));
$AmountIn=sprintf("%.0f",$CheckMaxRow["AmountIn"]);		//下单总额
$AmountOut=sprintf("%.0f",$CheckMaxRow["AmountOut"]);	//出货总额
$MAXClientAmount=$AmountIn>$AmountOut?$AmountIn:$AmountOut;
$Len=strlen(sprintf("%.0f",$MAXClientAmount))-1;//长度

$gValue=ceil($MAXClientAmount/pow(10,$Len))*pow(10,$Len)/(10000*10);//1隔代表的金额
$xStep=50;//每隔像素值
$xValue=ceil($MAXClientAmount/pow(10,$Len))*pow(10,$Len)/500;
//出货总额
$TempWidth=intval($AmountOut/$xValue);
imagefilledrectangle($image,101,$imB+80,100+$TempWidth,$imB+100,$TextGreen);
imagettftext($image,12,0,100+$TempWidth+2,$imB+97,$TextBlack,$UseFont,number_format($AmountOut));//输出总金额
//下单总额
$TempWidth=intval($AmountIn/$xValue);
imagefilledrectangle($image,101,$imB+100,100+$TempWidth,$imB+119,$T1002);
imagettftext($image,12,0,100+$TempWidth+2,$imB+116,$TextBlack,$UseFont,number_format($AmountIn));//输出总金额

//出货-分类
imagesetthickness ($image,3);
$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,T.mainType
	FROM $DataIn.yw1_ordersheet S  
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 AND M.CompanyId='$CID' AND DATE_FORMAT(M.OrderDate,'%Y')='$NowY' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC
	",$link_id);
if($mainTypeRow=mysql_fetch_array($mainTypeSql)){	
	$XL=100;
	$XR=$XL;
	
	do{
		$mainType=$mainTypeRow["mainType"];
		$typeAmount=$mainTypeRow["Amount"];
		//画图
		if($typeAmount>0){//条形图颜色
			$mainTypeC="mtColor".strval($mainType);
			$mainTypeC=$$mainTypeC;
			$TempWidth=intval($typeAmount/$xValue);
			$XR =$XR+$TempWidth;//条形图新的X座标
			imagefilledrectangle($image,$XL,$imB+114,$XR,$imB+120,$mainTypeC); //画矩形

			$XL=$XR;

			}
		}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
	}
//下单-分类	
$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 AND M.Estate=0 AND M.CompanyId='$CID' AND DATE_FORMAT(M.Date,'%Y')='$NowY' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC
	",$link_id);
if($mainTypeRow=mysql_fetch_array($mainTypeSql)){	
	$XL=100;
	$XR=$XL;
	$TXL=$XL;
	do{
		$mainType=$mainTypeRow["mainType"];
		$typeAmount=$mainTypeRow["Amount"];
		//画图
		if($typeAmount>0){//条形图颜色
			$mainTypeC="mtColor".strval($mainType);
			$mainTypeC=$$mainTypeC;
			$TempWidth=intval($typeAmount/$xValue);
			$XR =$XR+$TempWidth;//条形图新的X座标
			imagefilledrectangle($image,$XL,$imB+80,$XR,$imB+86,$mainTypeC); //画矩形
			$typeAmount=sprintf("%01.2f",$typeAmount);
			imagettftext($image,10,0,$TXL,$imB+80,$mainTypeC,$UseFont,"$typeAmount");
			$XL=$XR;
			$TXL=$TXL+100;
			}
		}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
	}
imagesetthickness ($image,1);
//范围框
imagerectangle($image,100,$imB+70,700,$imB+120,$TextBlack);
//X轴间隔线
for($k=1;$k<11;$k++){
	imageline($image,100+$k*$xStep,$imB+70,100+$k*$xStep,$imB+130,$Gridcolor);//间隔线
	$m=$gValue*$k;
	imagettftext($image,12,0,105+$k*$xStep-15,$imB+140,$TextBlack,$UseFont,$m);//间隔值
	}
imagettftext($image,12,0,105+$k*$xStep-15,$imB+140,$TextBlack,$UseFont,"(万)");
imagettftext($image,10,0,70,$imB+93,$TextBlack,$UseFont,"出货");
imagettftext($image,10,0,70,$imB+120,$TextBlack,$UseFont,"下单");
//*******************************************************************************
		}
	else{
	$image = imagecreate (1,1);								//输出空白图像
	imagecolorallocate($image,255,255,255);							//图像背景色
	}
	}
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);   //释放资源
?>