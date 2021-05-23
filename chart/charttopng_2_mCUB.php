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
// add by zx 2011-01-27 end

/////////////////////////////////////////////////
//总数输出
$CheckSumSql= mysql_query("
SELECT SUM(InAmount) AS SumInAmount,SUM(OutAmount) AS SumOutAmount FROM(
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS OutAmount,'0' AS InAmount
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 $TJA
UNION ALL 
	SELECT '0' AS OutAmount,SUM(S.Qty*S.Price*C.Rate) AS InAmount
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 $TJB
)A
	",$link_id);
if($CheckSumRow=mysql_fetch_array($CheckSumSql)){
	//输出总额
	$SumInAmount=$CheckSumRow["SumInAmount"];
	$SumOutAmount=$CheckSumRow["SumOutAmount"];
	}


////////////////////////////////////////////////
//出货或下单最高金额
$StartDay=$CheckYear."-01-01";		//开始计算的起始日期
$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Amount) AS MaxAmont FROM ( 
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 $TJA GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
	UNION ALL 
	SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount FROM $DataIn.yw1_ordersheet S LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 $TJB GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
	)A",$link_id));

$MaxAmount=ceil(intval($MaxResult["MaxAmont"]/10000)/50)*50;	//范围月份内最高总额
$MonthStep=100;													//月份间隔步长
//$imW=12*$MonthStep+100;											//图像宽度
$imW=$TotalMonth*$MonthStep+100;	// modify by zx 2011-01-27 宽度要改变

$imH=$MaxAmount+100;											//图像高度
$imH=$imH<400?400:$imH;
$imH=$imH+50;
$Diameter=5;													//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
//客户数量
$CheckAllSql= mysql_query("
SELECT SUM(InAmount) AS InAmount,SUM(OutAmount) AS OutAmount,CompanyId,ForShort FROM(
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS OutAmount,'0' AS InAmount,M.CompanyId,D.ForShort
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 $TJA AND D.Estate='1'  GROUP BY M.CompanyId
UNION ALL 
	SELECT '0' AS OutAmount,SUM(S.Qty*S.Price*C.Rate) AS InAmount,M.CompanyId,D.ForShort
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 $TJB AND D.Estate='1' GROUP BY M.CompanyId
)A GROUP BY CompanyId ORDER BY OutAmount DESC
	",$link_id);
$clientNum= mysql_num_rows($CheckAllSql);

$clientNumH=$clientNum*50+50;
$CimW=$imW-50;
// 再加饼图的高度
$image = imagecreate ($CimW+150,$imH+$clientNumH);								//输出空白图像
//$image = imagecreate ($CimW+150,$imH+$clientNumH);								//输出空白图像

imagecolorallocate($image,255,255,255);							//图像背景色
////设置字体颜色
$TextBlack = imagecolorallocate($image,0,0,0);
$Gridcolor=imagecolorallocate($image,153,153,153);
$Gridbgcolor=imagecolorallocate($image,221,221,221);
$TextRed= imagecolorallocate($image,255,0,0);
$TextGreen= imagecolorallocate($image,0,204,0);
$TextWhite= imagecolorallocate($image,255,255,255);

//颜色列表
$Tallin=imagecolorallocate($image,191,208,234); $Tallout=imagecolorallocate($image,0,55,152);
$T1001in=imagecolorallocate($image,87,123,187); $T1001out=imagecolorallocate($image,0,55,152);//MCA
$T1003in=imagecolorallocate($image,206,231,154); $T1003out=imagecolorallocate($image,156,206,52);//LAZ
$T1004in=imagecolorallocate($image,202,128,233); $T1004out=imagecolorallocate($image,148,0,211);//CEL
$T1018in=imagecolorallocate($image,127,255,212); $T1018out=imagecolorallocate($image,64,224,208);//EUR
$T1002in=imagecolorallocate($image,250,126,84); $T1002out=imagecolorallocate($image,255,70,4);//ECHO
$T1020in=imagecolorallocate($image,158,217,184); 	$T1020out=imagecolorallocate($image,60,179,113); //IMA

$T1024in=imagecolorallocate($image,200,161,127);$T1024out=imagecolorallocate($image,172,112,61);//KON
$T1031in=imagecolorallocate($image,255,69,0); 	$T1031out=imagecolorallocate($image,255,0,0); 	//Elite
$T1032in=imagecolorallocate($image,169,169,169); $T1032out=imagecolorallocate($image,105,105,105);//PMD
$T1036in=imagecolorallocate($image,255,138,201);$T1036out=imagecolorallocate($image,255,20,147);//其它(美元)
$T1049in=imagecolorallocate($image,255,204,51); $T1049out=imagecolorallocate($image,255,204,51);//CG
$T1039in=imagecolorallocate($image,104,50,46); $T1039out=imagecolorallocate($image,229,191,199);//鼠宝
$Totherin=imagecolorallocate($image,186,87,136);$Totherout=imagecolorallocate($image,150,0,75);//20万以下


$Tile="研砼客户月下单、出货总额条形图";
$titleX=$imW/2-130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出标题




$imL=50;														//顶部X:左边距
$imT=50;														//顶部Y:上边距
$imR=$imW-50;													//底部X:
$imB=$imH-100;													//底部Y:
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围

//$MonthTemp=11;  // modify by zx 2011-01-27
//for($i=0;$i<13;$i++){  // modify  by zx 2011-01-27
$MonthTemp=-1;
for($i=0;$i<$TotalMonth+1;$i++){
	$MonthTemp++;
	//$MonthTemp=$MonthTemp>12?1:$MonthTemp;   // modify  by zx 2011-01-27  begin
	$addYear=0; //如果跨年,则要加1
	if($MonthTemp>12)
	{
		$addYear=1;
		$MonthTemp=1;
	}
	// modify  by zx 2011-01-27  end
	if($i%2!=0){
		//矩形
		imagefilledrectangle($image,($imL+$i*$MonthStep),$imB-1,($imL+$i*$MonthStep)+100,$imT+1,$Gridbgcolor); //画填充矩形
		}

	//if($i==0 || $i==12){
	if($i==0 || $i==$TotalMonth){
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imB,$TextBlack);
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imB+20,$TextBlack);
		}
	else{
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imT-15,$Gridcolor);
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imB,$Gridcolor);
		imageline($image,$imL+$i*$MonthStep,$imB,$imL+$i*$MonthStep,$imB+20,$Gridcolor);
		}
	//输出月份
	if($i>0){
		if($MonthTemp==1 ){
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imB+35,$TextRed,$UseFont,$CheckYear+$addYear."年");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imT-20,$TextRed,$UseFont,$CheckYear+$addYear."年");
			}
		else{
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20,$TextBlack,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextBlack,$UseFont,$MonthTemp."月");
			}
		}
	}
imagesetthickness ($image,3);
//输出分类图块
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
		imagerectangle($image,150+$i*100,$imB+60,170+$i*100,$imB+80,$$mtColor);
		imagettftext($image,12,0,175+$i*100,$imB+80,$TextBlack,$UseFont,$Name);
		$i++;
		}while($RemarkRow= mysql_fetch_array($RemarkSql));
	}

imagesetthickness ($image,1);


//金额间隔线
$countY=($imH-150)/25;
$AmountStep=25;
for($i=0;$i<=$countY;$i++){
	$TempAmount=$AmountStep*$i;
	if($i==0 || $i==$countY){
		imageline($image,$imL,$imB-$i*$AmountStep,$imL,$imB-$i*$AmountStep,$TextBlack);		//斜线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL-5,$imB-$i*$AmountStep,$TextBlack);//短线
		}
	else{
		imageline($image,$imL,$imB-$i*$AmountStep,$imR,$imB-$i*$AmountStep,$Gridcolor);			//间隔线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL,$imB-$i*$AmountStep,$Gridcolor);		//斜线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL-5,$imB-$i*$AmountStep,$Gridcolor);//短线
		//输出金额
		//$str="$imL,$imB-$i*$AmountStep,$imR,$imB-$i*$AmountStep";
		//imagestring($image,3,$imL,$imB-$i*$AmountStep,$str,$TextBlack);

		$TempAmountX=$TempAmount<100?30:($TempAmount<1000?24:18);
 		imagestring($image,3,$TempAmountX,($imB-$AmountStep*$i)-7,$TempAmount,$TextBlack);
		}
	}

//客户过滤:最高出货、或下单金额超过20万的客户

//初始化XL,XR,YB,YT
$wCube=20;		//柱体宽度
$jCube=10;		//柱体间隔
//月份检查:包括出货或下单的月份
$MonthSql=mysql_query("
	SELECT Date FROM ( 
		SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $TJA GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		UNION ALL 
		SELECT M.OrderDate AS Date FROM $DataIn.yw1_ordermain M WHERE 1 $TJB GROUP BY DATE_FORMAT(M.OrderDate ,'%Y-%m')
	)A GROUP BY DATE_FORMAT(Date,'%Y-%m')
	",$link_id);
if($MonthRow=mysql_fetch_array($MonthSql)){
	$i=1;
	$mNum=0;

	do{
		$theMonth=date("Y-m",strtotime($MonthRow["Date"]));

		//add by zx 2011-01-27  begin   因为换年月份要在第13列开始显示
		$theYear=date("Y",strtotime($MonthRow["Date"]));
		if($theYear>$CheckYear)
		{
			$i=date("m",strtotime($MonthRow["Date"]))*1+12;
		}
		else{
			$i=date("m",strtotime($MonthRow["Date"]))*1;
		}
		//$i=date("m",strtotime($MonthRow["Date"]))*1;
		//add by zx 2011-01-27 end

			////////////////A 当月出货总量有超过 20万的/////////////////
						$clientSql=mysql_query("
						SELECT Amount,CompanyId,Forshort FROM (
							SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,M.CompanyId,D.Forshort 
							FROM $DataIn.ch1_shipsheet S 
							LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
							LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
							LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
							 WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.CompanyId 
						) A WHERE Amount>=150000 GROUP BY CompanyId ORDER BY Amount DESC",$link_id);
						if($clientRow=mysql_fetch_array($clientSql)){
							//坐标计算：X轴坐标每月只变化一次
							$YB=$imB;
							$YT=$imB;
							$XL=$imL+$MonthStep*($i-1)+$jCube*1+$wCube*2;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
							$XR=$XL+$wCube;
							$ComPanyIdS="";
							$SumAmount=0;
							do{
								//颜色
								$CompanyId=$clientRow["CompanyId"];
								$ComPanyIdS=$ComPanyIdS==""?$CompanyId:($ComPanyIdS.",".$CompanyId);
								$Forshort=$clientRow["Forshort"];
								$Amount=$clientRow["Amount"];
								$SumAmount=$SumAmount+$Amount;
								$shipAmount=intval(sprintf("%.0f",$Amount/10000));											//月出货总额
								$YT =$YB-$shipAmount;
								$Tcolor="T".$CompanyId."out";
								//画柱形图正面
								imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$$Tcolor); //画填充矩形
								$Forshort=$Forshort=="MCA包装"?"包装":$Forshort;
								imagettftext($image,12,1,$XL,$YB,$TextBlack,$UseFont,$Forshort);
								//Y坐标重设
								$YB=$YT;
								}while($clientRow=mysql_fetch_array($clientSql));
							//其它没到20万的客户的金额总和
							$otherSql=mysql_query("
							SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount
							FROM $DataIn.ch1_shipsheet S 
							LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
							LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
							LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
							WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND M.CompanyId NOT IN ($ComPanyIdS)
							",$link_id);
							if($otherRow=mysql_fetch_array($otherSql)){
									$Amount=$otherRow["Amount"];
									$shipAmount=sprintf("%.2f",$Amount);											//月出货总额
									$SumAmount=$SumAmount+$Amount;
									$TempHight=intval($shipAmount/10000);
									$YT =$YT-$TempHight;
									//画柱形图正面
									imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$Totherout); //画填充矩形
									//Y坐标重设
									$YB=$YT;
								}
							//顶部
							$TempPoints[6]=$XL;$TempPoints[7]=$YT;
							//总金额
							$SumAmount=sprintf("%.0f",$SumAmount/10000);
							imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
							////////////////20万以上的用户/////////////////////
							}
						else{//没有达到20万的客户，则只计算该月总数

							/////////////////////////
							$allSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'",$link_id);
							if($allRow=mysql_fetch_array($allSql)){
								$YB=$imB;
								$YT=$imB;
								$XL=$imL+$MonthStep*($i-1)+$jCube*2+$wCube;
								$XR=$XL+$wCube;
								$SumAmount=0;
								$Fcolor="Totherin";$Rcolor="Totherin";$Tcolor="Totherin";
								$Amount=$allRow["Amount"];
								$SumAmount=$SumAmount+$Amount;
								$shipAmount=intval(sprintf("%.0f",$Amount/10000));											//月出货总额
								$YT =$YB-$shipAmount;
								//总金额
								$SumAmount=sprintf("%.0f",$SumAmount/10000);
								//if($SumAmount>=10000){$SumAmount=sprintf("%.0f",$SumAmount/10000)."万";}
								//else{$SumAmount=sprintf("%.0f",$SumAmount)."元";}
								if($SumAmount>0){
									//画柱形图正面
									imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$$Tcolor); //画填充矩形
									//顶部
									$TempPoints[6]=$XL;$TempPoints[7]=$YT;
									//imagefilledpolygon($image,$TempPoints,4,$$Tcolor);
									imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);}
								}
							}
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
		WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$YB=$imB-1;	//条形图起始Bottom座标
			$YT=$imB;	//条形图起始Top座标
			$XL=$imL+$MonthStep*$i-$jCube-$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$XR=$XL+4;
			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){
					//条形图颜色
					$Id=$mainTypeRow["mainType"];
					$mainTypeC="mtColor".strval($Id);
					$mainTypeC=$$mainTypeC;
					$TempHight=intval($typeAmount/10000);
					$YT =$YT-$TempHight;//条形图新的Top座标
					if($YB-$YT<5){
						imagerectangle($image,$XL,$YT,$XR,$YB,$mainTypeC); //画矩形
						}
					else{
						imagefilledrectangle($image,$XL,$YT+2,$XR,$YB-2,$TextWhite); //画矩形
						imagerectangle($image,$XL,$YT+3,$XR,$YB,$mainTypeC); //画矩形
						}
					$YB=$YT;
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
			WHERE 1 AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC		
		",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$YB=$imB-1;	//条形图起始Bottom座标
			$YT=$imB;	//条形图起始Top座标
			$XL=$imL+$MonthStep*($i-1)+$jCube+$wCube+2;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$XR=$XL+4;
			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){
					//条形图颜色
					$Id=$mainTypeRow["mainType"];
					$mainTypeC="mtColor".strval($Id);
					$mainTypeC=$$mainTypeC;
					$TempHight=intval($typeAmount/10000);
					$YT =$YT-$TempHight;//条形图新的Top座标
					if($YB-$YT<5){
						imagerectangle($image,$XL,$YT,$XR,$YB,$mainTypeC); //画矩形
						}
					else{
						imagefilledrectangle($image,$XL,$YT+2,$XR,$YB-2,$TextWhite); //画矩形
						imagerectangle($image,$XL,$YT+3,$XR,$YB,$mainTypeC); //画矩形
						}
					$YB=$YT;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}
		imagesetthickness ($image,1);
		//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
		//##################################################
		//当月车间薪资
		//
		$hzSql=mysql_query("SELECT SUM(Amount) AS Amount FROM(

			SELECT SUM(S.Amount+S.Sb+S.Jz+S.RandP+S.Otherkk) AS Amount FROM $DataIn.cwxzsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			UNION ALL
			SELECT SUM(S.cAmount) AS Amount FROM $DataIn.sbpaysheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			UNION ALL
			SELECT SUM(S.Amount) AS Amount FROM $DataIn.hdjbsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			)E
			",$link_id);
		if($hzRow=mysql_fetch_array($hzSql)){
			$hzAmount=$hzRow["Amount"];
			$SumAmount=sprintf("%.0f",$hzAmount/10000);
			if($SumAmount>0){
				$YB=$imB;
				$YT=$imB;
				$XL=$imL+$MonthStep*$i-$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
				$XR=$XL+$wCube/2;
				$TempHight=intval($hzAmount/10000);
				$YT =$YT-$TempHight;
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$TextGreen); //画填充矩形
				//顶部
				$TempPoints[6]=$XL+2;$TempPoints[7]=$YT;
				//总金额

				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
				}
			}
		//##################################################
		//***********当月下单输出
		/////////////////////////////////////////////////
		$clientSql=mysql_query("
			SELECT Amount,CompanyId,Forshort FROM (
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,M.CompanyId,D.Forshort FROM $DataIn.yw1_ordersheet S LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' GROUP BY M.CompanyId ) A WHERE Amount>=150000 GROUP BY CompanyId ORDER BY Amount DESC
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB-1;
			$YT=$imB;
			$XL=$imL+$MonthStep*($i-1)+$jCube;
			$XR=$XL+$wCube;
			$ComPanyIdS="";
			$SumAmount=0;
			do{
				//颜色
				$CompanyId=$clientRow["CompanyId"];
				$ComPanyIdS=$ComPanyIdS==""?$CompanyId:($ComPanyIdS.",".$CompanyId);
				$Forshort=$clientRow["Forshort"];
				$Amount=$clientRow["Amount"];
				$SumAmount=$SumAmount+$Amount;
				$shipAmount=intval(sprintf("%.0f",$Amount/10000));											//月出货总额
				$YT =$YB-$shipAmount;
				$Tcolor="T".$CompanyId."out";
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Tcolor); //画填充矩形
				$Forshort=$Forshort=="MCA包装"?"包装":$Forshort;
				imagettftext($image,12,1,$XL,$YB,$TextBlack,$UseFont,$Forshort);
				//Y坐标重设
				$YB=$YT;
				}while($clientRow=mysql_fetch_array($clientSql));
			//其它没到20万的客户的金额总和
			$otherSql=mysql_query("
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' AND M.CompanyId NOT IN ($ComPanyIdS)
			",$link_id);
			if($otherRow=mysql_fetch_array($otherSql)){
					$Amount=$otherRow["Amount"];
					$shipAmount=sprintf("%.2f",$Amount);											//月出货总额
					$SumAmount=$SumAmount+$Amount;
					$TempHight=intval($shipAmount/10000);
					$YT =$YT-$TempHight;
					//画柱形图正面
					imagefilledrectangle($image,$XL,$YB,$XR,$YT,$Totherout); //画填充矩形
					//imagettftext($image,12,1,$XL,$YB,$TextWhite,$UseFont,"其它");
					//Y坐标重设
					$YB=$YT;
				}
			//顶部
			$TempPoints[6]=$XL;$TempPoints[7]=$YT;
			//总金额
			$SumAmount=sprintf("%.0f",$SumAmount/10000);
			//if($SumAmount>=10000){$SumAmount=sprintf("%.0f",$SumAmount/10000);}
								//else{$SumAmount=sprintf("%.0f",$SumAmount)."元";}
			imagettftext($image,12,0,$TempPoints[6]-3,$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
			}
		//////////////////////////////////////////////////
		else{
			$allSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount FROM $DataIn.yw1_ordersheet S LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth'",$link_id);
			if($allRow=mysql_fetch_array($allSql)){
				$YB=$imB;
				$YT=$imB;
				$XL=$imL+$MonthStep*($i-1)+$jCube;
				$XR=$XL+$wCube;
				//$XL=$XL+$MonthStep;
				//$XR=$XR+$MonthStep;
				$SumAmount=0;
				$Fcolor="Totherout";$Rcolor="Totherout";$Tcolor="Totherout";
				$Amount=$allRow["Amount"];
				$SumAmount=$SumAmount+$Amount;
				$shipAmount=intval(sprintf("%.0f",$Amount/10000));											//月出货总额
				$YT =$YB-$shipAmount;
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Fcolor); //画填充矩形
				//顶部
				$TempPoints[6]=$XL;$TempPoints[7]=$YT;
				//总金额
				$SumAmount=sprintf("%.0f",$SumAmount/10000);
				//if($SumAmount>=10000){$SumAmount=sprintf("%.0f",$SumAmount/10000)."万";}
				//else{$SumAmount=sprintf("%.0f",$SumAmount)."元";}
				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7],$TextBlack,$UseFont,$SumAmount);
				}
			//////////////////////
			}

		//**********************
		//$i++;
		$mNum++;
		}while ($MonthRow=mysql_fetch_array($MonthSql));
	}
//色块说明
$MovePoint=-20;

//输出说明
imagettftext($image,10,0,45,$imB+50,$TextBlack,$UseFont,"第一柱:当月下单金额");
imagettftext($image,10,0,45,$imB+65,$TextBlack,$UseFont,"第二柱:当月出货总额");
imagettftext($image,10,0,45,$imB+80,$TextBlack,$UseFont,"第三柱:以主分类统计的金额");//出货订单中采购单下单金额为美金的需求单总额(换算成RMB)
imagettftext($image,10,0,45,$imB+95,$TextBlack,$UseFont,"第四柱:当月车间人工薪资");



//总出货下单图例
//出货下单总额图
$AllimL=$imL+99;
$AllimT=$imH+10;
$AllimR=$imR+100;
$AllimB=$imH+$clientNumH-40;
imagerectangle($image,$AllimL,$AllimT,$AllimR,$AllimB,$TextBlack); 			//画矩形：曲线图范围
//Y轴间隔线
for($k=0;$k<=$clientNum;$k++){
	imageline($image,$imL,$AllimB-$k*50,1250,$AllimB-$k*50,$TextBlack);
	}

//取最大值
$CheckMaxRow= mysql_fetch_array(mysql_query("
SELECT MAX(MAXClientAmount) AS MAXClientAmount FROM(
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS MAXClientAmount
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 $TJA AND D.Estate='1'  GROUP BY M.CompanyId
UNION ALL 
	SELECT SUM(S.Qty*S.Price*C.Rate) AS MAXClientAmount
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 $TJB AND D.Estate='1' GROUP BY M.CompanyId
)A",$link_id));
$MAXClientAmount=$CheckMaxRow["MAXClientAmount"];
$xValue=ceil($MAXClientAmount/1000000)*1000;//1X像素点的金额
$xStep=50;//每隔像素值
//出货总额
if($CheckAllRow=mysql_fetch_array($CheckAllSql)){
	$CimL=$AllimL+1;			//固定值
	$CimB=$AllimB-1;		//初始化位置为底部上升5
	$i=1;
	do{
		$CimT=$CimB-20;
		$CompanyId=$CheckAllRow["CompanyId"];
		$ForShort=$CheckAllRow["ForShort"];
		$InAmount=sprintf("%.0f",$CheckAllRow["InAmount"]);
		$OutAmount=sprintf("%.0f",$CheckAllRow["OutAmount"]);
		$CimR1=intval(sprintf("%.0f",$InAmount/$xValue));
		$OUTCRMB="";  //外购中的出货  add by zx 2010-12-15
		$OUTXRMB="";   //外购中的下单 add by zx 2010-12-15
		//画下单条形图 500万
		if($CimR1>0){
			imagefilledrectangle($image,$CimL,$CimB,$CimL+$CimR1,$CimT,$Tallin);

			}
		if($InAmount>0){
			imagettftext($image,11,0,$CimL+$CimR1+10,$CimB,$TextBlack,$UseFont,number_format($InAmount));}
		imagettftext($image,10,0,$imL,$CimB-5,$TextRed,$UseFont,$i."-".$ForShort);

		//画出货条形图

		$OutPC=sprintf("%.1f",$OutAmount*100/$SumOutAmount);
		$CimR2=intval(sprintf("%.0f",$OutAmount/$xValue));
		$CimB2=$CimT;
		$CimT2=$CimB2-20;
		if($CimR2>0){
			imagefilledrectangle($image,$CimL,$CimB2,$CimL+$CimR2,$CimT2+7,$Tallout);
			}
		//////////////////////////////主分类在出货中的比例////////////////////////////////////
		if($OutPC>1){
		imagesetthickness ($image,3);
		$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA AND M.CompanyId='$CompanyId' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$XL=$CimL;
			$XR=$CimL;

			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){//条形图颜色
					if($mainType==1){  //外购类
						$OUTCRMB="(外购:".number_format($typeAmount).")";
					}
					$mainTypeC="mtColor".strval($mainType);
					$mainTypeC=$$mainTypeC;
					$TempWidth=intval($typeAmount/$xValue);
					$XR =$XR+$TempWidth;//条形图新的X座标
					imagerectangle($image,$XL,$CimT2+2,$XR,$CimB2-15,$mainTypeC); //画矩形
					$XL=$XR;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}
		///////////////////////////////////////////////////////////////////////////////
		//////////////////////////////主分类在下单中所占比例///////////////////////////
		$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,T.mainType
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 $TJB AND M.CompanyId='$CompanyId' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC
			",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$XL=$CimL;
			$XR=$CimL;

			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];


				//画图
				if($typeAmount>0){//条形图颜色
					if($mainType==1){  //外购类
						$OUTXRMB="(外购:".number_format($typeAmount).")";
					}
					$mainTypeC="mtColor".strval($mainType);
					$mainTypeC=$$mainTypeC;
					$TempWidth=intval($typeAmount/$xValue);
					$XR =$XR+$TempWidth;//条形图新的X座标
					imagerectangle($image,$XL,$CimT+16,$XR,$CimB-1,$mainTypeC); //画矩形
					$XL=$XR;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}

		imagesetthickness ($image,1);
		}
		///////////////////////////////////////////////////////////////////////////////
		if($OutAmount>0){
			if($OutAmount>=500000)
			imagettftext($image,11,0,$CimL+5,$CimB2,$TextWhite,$UseFont,$OutPC."%");
			//输出比率+$CimR2-40
			imagettftext($image,11,0,$CimL+$CimR2+10,$CimB2,$TextBlack,$UseFont,number_format($OutAmount).$OUTCRMB);
			}
		//Y轴累加
		$CimB=$CimT2-10;
		$i++;
		}while ($CheckAllRow=mysql_fetch_array($CheckAllSql));
	}


//X轴间隔线
for($k=1;$k<23;$k++){
	imageline($image,$AllimL+$k*$xStep,$AllimT,$AllimL+$k*$xStep,$AllimB+10,$Gridcolor);//间隔线
	if($k<22){
		$m=$xValue*$xStep*$k/10000;
	imagettftext($image,12,0,$AllimL+$k*$xStep-15,$AllimB+25,$TextBlack,$UseFont,$m);//间隔值
	}
	}
imagettftext($image,12,0,60,$AllimB+25,$TextBlack,$UseFont,"(单位:万元)");

//图例说明
imagefilledrectangle($image,901,$AllimT+1,1248,$AllimT+99,$Gridbgcolor);
imagefilledrectangle($image,910,$AllimT+10,940,$AllimT+40,$Tallout);
imagefilledrectangle($image,910,$AllimT+60,940,$AllimT+90,$Tallin);


//输出图像
$AvgInMonth=$SumInAmount/$mNum;
$AvgOutMonth=$SumOutAmount/$mNum;

//画均线
	if($AvgInMonth>0){
		$tempY=round($AvgInMonth/10000);						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$TextRed);	//画平均线
		imagestring($image,3,$imR+70,$imB-$tempY-7,number_format($AvgInMonth),$TextRed);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+3,$TextRed,$UseFont,"接单均线:");
		}
if($AvgOutMonth>0){
		$tempY=round($AvgOutMonth/10000);						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$Tallout);	//画平均线
		imagestring($image,3,$imR+70,$imB-$tempY-7,number_format($AvgOutMonth),$Tallout);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+3,$Tallout,$UseFont,"出货均线:");
		}
$SumInAmount=intval(sprintf("%.0f",$SumInAmount/10000));
$SumOutAmount=intval(sprintf("%.0f",$SumOutAmount/10000));

if ($NowYear!=""){  //跨年显示
	imagettftext($image,14,0,950,$AllimT+30,$TextBlack,$UseFont,$CheckYear."-".$NowYear."年出货总额：".number_format($SumOutAmount)." 万元");
	imagettftext($image,14,0,950,$AllimT+80,$TextBlack,$UseFont,$CheckYear."-".$NowYear."年接单总额：".number_format($SumInAmount)." 万元");
}
else{
	imagettftext($image,14,0,950,$AllimT+30,$TextBlack,$UseFont,$CheckYear."年出货总额：".number_format($SumOutAmount)." 万元");
	imagettftext($image,14,0,950,$AllimT+80,$TextBlack,$UseFont,$CheckYear."年接单总额：".number_format($SumInAmount)." 万元");
}

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);   //释放资源
?>