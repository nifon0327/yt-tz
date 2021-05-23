<?php
//独立已更新**********电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";

//出货或下单最高金额
$StartDay="2009-01-01";		//开始计算的起始日期
$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Amount) AS MaxAmont FROM ( 
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 AND M.Date>='$StartDay' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
	UNION ALL 
	SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.OrderDate>='$StartDay' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
	)A",$link_id));

$MaxAmount=ceil(intval($MaxResult["MaxAmont"]/10000)/50)*50;	//范围月份内最高总额
$MonthStep=100;													//月份间隔步长
$imW=12*$MonthStep+100;											//图像宽度
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
	WHERE M.Estate=0 AND M.Date>='$StartDay' AND D.Estate='1' AND D.cSign='7' GROUP BY M.CompanyId
UNION ALL 
	SELECT '0' AS OutAmount,SUM(S.Qty*S.Price*C.Rate) AS InAmount,M.CompanyId,D.ForShort
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.OrderDate>='$StartDay' AND D.Estate='1' AND D.cSign='7' GROUP BY M.CompanyId
)A GROUP BY CompanyId ORDER BY InAmount DESC
	",$link_id);
$clientNum= mysql_num_rows($CheckAllSql);

$clientNumH=$clientNum*50+50;
$CimW=$imW-50;
$image = imagecreate ($CimW,$imH+$clientNumH);								//输出空白图像

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
$T1002in=imagecolorallocate($image,250,126,84); $T1002out=imagecolorallocate($image,255,70,4);//ECHO
$T1024in=imagecolorallocate($image,200,161,127);$T1024out=imagecolorallocate($image,172,112,61);//KON

$Totherin=imagecolorallocate($image,186,87,136);$Totherout=imagecolorallocate($image,150,0,75);//20万以下

$T1036in=imagecolorallocate($image,255,138,201);$T1036out=imagecolorallocate($image,255,20,147);//其它(美元)


$T1003in=imagecolorallocate($image,206,231,154); $T1003out=imagecolorallocate($image,156,206,52);//LAZ
$T1004in=imagecolorallocate($image,202,128,233); $T1004out=imagecolorallocate($image,148,0,211);//CEL
$T1018in=imagecolorallocate($image,127,255,212); $T1018out=imagecolorallocate($image,64,224,208);//EUR
$T1020in=imagecolorallocate($image,158,217,184); 	$T1020out=imagecolorallocate($image,60,179,113); //IMA
$T1031in=imagecolorallocate($image,255,69,0); 	$T1031out=imagecolorallocate($image,255,0,0); 	//Elite
$T1032in=imagecolorallocate($image,169,169,169); $T1032out=imagecolorallocate($image,105,105,105);//PMD


$Tile="研砼客户月下单、出货总额条形图";
$titleX=$imW/2-130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出标题

$imL=50;														//顶部X:左边距
$imT=50;														//顶部Y:上边距
$imR=$imW-50;													//底部X:
$imB=$imH-100;													//底部Y:
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
$MonthTemp=11;
for($i=0;$i<13;$i++){
	$MonthTemp++;
	$MonthTemp=$MonthTemp>12?1:$MonthTemp;
	if($i%2!=0){
		//矩形
		imagefilledrectangle($image,($imL+$i*$MonthStep),$imB-1,($imL+$i*$MonthStep)+100,$imT+1,$Gridbgcolor); //画填充矩形
		}

	if($i==0 || $i==12){
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
		if($MonthTemp==1){
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextRed,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imB+35,$TextRed,$UseFont,"2009年");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imT-20,$TextRed,$UseFont,"2009年");
			}
		else{
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20,$TextBlack,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextBlack,$UseFont,$MonthTemp."月");
			}
		}
	}

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
		$TempAmountX=$TempAmount<100?30:($TempAmount<1000?24:18);
 		imagestring($image,3,$TempAmountX,($imB-$AmountStep*$i)-7,$TempAmount,$TextBlack);
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
		SELECT Date FROM $DataIn.ch1_shipmain WHERE Date>='$StartDay' AND Date>DATE_SUB('$ToDay',INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(Date,'%Y-%m')
		UNION ALL 
		SELECT OrderDate AS Date FROM $DataIn.yw1_ordermain WHERE OrderDate >='$StartDay' AND OrderDate >DATE_SUB('$ToDay',INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(OrderDate ,'%Y-%m')
	)A GROUP BY DATE_FORMAT(Date,'%Y-%m')
	",$link_id);
if($MonthRow=mysql_fetch_array($MonthSql)){
	$i=1;
	do{
		$theMonth=date("Y-m",strtotime($MonthRow["Date"]));
			////////////////A 当月出货总量有超过 20万的/////////////////
						$clientSql=mysql_query("
						SELECT Amount,CompanyId,Forshort FROM (
							SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,M.CompanyId,D.Forshort 
							FROM $DataIn.ch1_shipsheet S 
							LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
							LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
							LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
							WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' 
							GROUP BY M.CompanyId 
						) A WHERE Amount>=150000 GROUP BY CompanyId ORDER BY Amount DESC",$link_id);
						if($clientRow=mysql_fetch_array($clientSql)){
							//坐标计算：X轴坐标每月只变化一次
							$YB=$imB;
							$YT=$imB;
							$XL=$imL+$MonthStep*($i-1)+$jCube*2+$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
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
							$allSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
							FROM $DataIn.ch1_shipsheet S 
							LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
							LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
							LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
							WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'",$link_id);
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
		//当月供应商货款输出
		$gysSql=mysql_query("
			SELECT SUM(G.OrderQty*G.Price*C.Rate) AS Amount 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.trade_object P ON G.CompanyId=P.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
			WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' 
			AND S.Type='1' AND P.Currency='2'
			",$link_id);
		if($gysRow=mysql_fetch_array($gysSql)){
			$gysAmount=$gysRow["Amount"];
			if($gysAmount>0){
				$YB=$imB;
				$YT=$imB;
				$XL=$imL+$MonthStep*$i-$jCube-$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
				$XR=$XL+$wCube/2;
				$TempHight=intval($gysAmount/10000);
				$YT =$YT-$TempHight;
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$TextRed); //画填充矩形
				//顶部
				$TempPoints[6]=$XL+2;$TempPoints[7]=$YT;
				//总金额
				$SumAmount=sprintf("%.0f",$gysAmount/10000);
				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextRed,$UseFont,$SumAmount);
				}
			}
		//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
		//##################################################
		//当月车间薪资
		//
		$hzSql=mysql_query("SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(S.Amount+S.Sb+S.Jz+S.RandP+S.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet S,$DataPublic.staffmain M  WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			UNION ALL
			SELECT SUM(S.cAmount) AS Amount 
			FROM $DataIn.sbpaysheet S,$DataPublic.staffmain M  WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			UNION ALL
			SELECT SUM(S.Amount) AS Amount 
			FROM $DataIn.hdjbsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
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
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,M.CompanyId,D.Forshort 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' GROUP BY M.CompanyId ) A WHERE Amount>=150000 GROUP BY CompanyId ORDER BY Amount DESC
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
			$allSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth'",$link_id);
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
		$i++;
		}while ($MonthRow=mysql_fetch_array($MonthSql));
	}
//色块说明
$MovePoint=-20;

//输出说明
imagettftext($image,10,0,45,$imB+50,$TextBlack,$UseFont,"第一柱:当月下单金额");
imagettftext($image,10,0,45,$imB+65,$TextBlack,$UseFont,"第二柱:当月出货总额");
imagettftext($image,10,0,45,$imB+80,$TextBlack,$UseFont,"第三柱:出货订单中采购单下单金额为美金的需求单总额(换算成RMB)");
imagettftext($image,10,0,45,$imB+95,$TextBlack,$UseFont,"第四柱:当月车间人工薪资");

//总出货下单图例
//出货下单总额图
$AllimL=$imL+100;
$AllimT=$imH+10;
$AllimR=$imR+100;
$AllimB=$imH+$clientNumH-40;
imagerectangle($image,$AllimL,$AllimT,$AllimR,$AllimB,$TextBlack); 			//画矩形：曲线图范围
//画隔线
imagefilledrectangle($image,900,$AllimT,$AllimR,$AllimT+100,$Gridbgcolor);
imagefilledrectangle($image,910,$AllimT+10,940,$AllimT+40,$Tallout);
imagefilledrectangle($image,910,$AllimT+60,940,$AllimT+90,$Tallin);

for($k=0;$k<=$clientNum;$k++){
	imageline($image,$imL,$AllimB-$k*50,$AllimR,$AllimB-$k*50,$TextBlack);
	}
//出货总额

if($CheckAllRow=mysql_fetch_array($CheckAllSql)){
	$CimL=$AllimL+1;			//固定值
	$CimB=$AllimB-1;		//初始化位置为底部上升5
	$i=1;
	do{
		$CimT=$CimB-20;
		$ForShort=$CheckAllRow["ForShort"];
		$InAmount=sprintf("%.0f",$CheckAllRow["InAmount"]);
		$OutAmount=sprintf("%.0f",$CheckAllRow["OutAmount"]);
		$CimR1=intval(sprintf("%.0f",$InAmount/10000));
		//画下单条形图 500万
		if($CimR1>0){
			imagefilledrectangle($image,$CimL,$CimB,$CimL+$CimR1,$CimT,$Tallin);

			}
		if($InAmount>0){
			imagettftext($image,12,0,$CimL+$CimR1+10,$CimB,$TextBlack,$UseFont,number_format($InAmount));}
		imagettftext($image,10,0,$imL,$CimB-5,$TextRed,$UseFont,$i."-".$ForShort);
		//画出货条形图
		$CimR2=intval(sprintf("%.0f",$OutAmount/10000));
		$CimB2=$CimT;
		$CimT2=$CimB2-20;
		if($CimR2>0){
			imagefilledrectangle($image,$CimL,$CimB2,$CimL+$CimR2,$CimT2,$Tallout);
			}
		if($OutAmount>0){
			imagettftext($image,12,0,$CimL+$CimR2+10,$CimB2,$TextBlack,$UseFont,number_format($OutAmount));
			}
		//Y轴累加
		$CimB=$CimT2-10;
		$i++;
		}while ($CheckAllRow=mysql_fetch_array($CheckAllSql));
	}
//总数输出
$CheckSumSql= mysql_query("
SELECT SUM(InAmount) AS SumInAmount,SUM(OutAmount) AS SumOutAmount FROM(
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS OutAmount,'0' AS InAmount
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 AND M.Date>='$StartDay' AND D.cSign='7'
UNION ALL 
	SELECT '0' AS OutAmount,SUM(S.Qty*S.Price*C.Rate) AS InAmount
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.OrderDate>='$StartDay' AND D.cSign='7'
)A
	",$link_id);
if($CheckSumRow=mysql_fetch_array($CheckSumSql)){
	//输出总额
	$SumInAmount=$CheckSumRow["SumInAmount"];
	$SumOutAmount=$CheckSumRow["SumOutAmount"];
	}
//输出图像
$SumInAmount=intval(sprintf("%.0f",$SumInAmount/10000));
$SumOutAmount=intval(sprintf("%.0f",$SumOutAmount/10000));

imagettftext($image,14,0,950,$AllimT+30,$TextBlack,$UseFont,"2009年出货总额：".number_format($SumOutAmount)." 万元");
imagettftext($image,14,0,950,$AllimT+80,$TextBlack,$UseFont,"2009年接单总额：".number_format($SumInAmount)." 万元");
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);   //释放资源
?>