<?php   
//独立已更新电信---yang 20120801
include "../basic/chksession.php";
//立体柱形图
include "parameter.inc";
$ToDate=date("Y-m-d");
//出货或下单最高金额
$StartDay="2008-10-01";		//开始计算的起始日期
$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Amount) AS MaxAmont FROM ( 
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0 GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
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
$imW=12*$MonthStep+60;											//图像宽度
$imH=$MaxAmount+100;											//图像高度
$imH=$imH<500?500:$imH;
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
$T1001=imagecolorallocate($image,0,102,255);	$F1001=imagecolorallocate($image,0,102,204); 	$R1001=imagecolorallocate($image,0,51,102); 
$T1002=imagecolorallocate($image,255,105,180); 	$F1002=imagecolorallocate($image,255,20,147); 	$R1002=imagecolorallocate($image,199,21,133);
$T1003=imagecolorallocate($image,238,130,238); 	$F1003=imagecolorallocate($image,255,0,255); 	$R1003=imagecolorallocate($image,128,0,128);
$T1004=imagecolorallocate($image,153,50,204); 	$F1004=imagecolorallocate($image,148,0,211); 	$R1004=imagecolorallocate($image,75,0,130);
$T1005=imagecolorallocate($image,123,104,238); 	$F1005=imagecolorallocate($image,106,90,205); 	$R1005=imagecolorallocate($image,72,61,139);

$T1017=imagecolorallocate($image,135,206,235); 	$F1017=imagecolorallocate($image,0,191,255); 	$R1017=imagecolorallocate($image,95,158,160);
$T1018=imagecolorallocate($image,127,255,212); 	$F1018=imagecolorallocate($image,64,224,208); 	$R1018=imagecolorallocate($image,32,178,170);
$T1020=imagecolorallocate($image,0,255,127); 	$F1020=imagecolorallocate($image,60,179,113); 	$R1020=imagecolorallocate($image,46,139,87);
$T1022=imagecolorallocate($image,0,255,0); 		$F1022=imagecolorallocate($image,50,205,50); 	$R1022=imagecolorallocate($image,34,139,34);
$T1023=imagecolorallocate($image,173,255,47); 	$F1023=imagecolorallocate($image,154,205,50); 	$R1023=imagecolorallocate($image,107,142,35);
$T1024=imagecolorallocate($image,255,215,0); 	$F1024=imagecolorallocate($image,218,165,32); 	$R1024=imagecolorallocate($image,184,134,11);
$T1028=imagecolorallocate($image,244,164,96); 	$F1028=imagecolorallocate($image,210,105,30); 	$R1028=imagecolorallocate($image,139,69,19);
$T1029=imagecolorallocate($image,255,160,122); 	$F1029=imagecolorallocate($image,255,127,80); 	$R1029=imagecolorallocate($image,233,150,122);
$T1031=imagecolorallocate($image,255,69,0); 	$F1031=imagecolorallocate($image,255,0,0); 		$R1031=imagecolorallocate($image,178,34,34);
$T1032=imagecolorallocate($image,169,169,169); 	$F1032=imagecolorallocate($image,105,105,105); 	$R1032=imagecolorallocate($image,0,0,0);
$T1034=imagecolorallocate($image,0,0,255); 		$F1034=imagecolorallocate($image,0,0,205); 		$R1034=imagecolorallocate($image,0,0,139);
$T1036=imagecolorallocate($image,255,215,0); 	$F1036=imagecolorallocate($image,218,165,32); 	$R1036=imagecolorallocate($image,184,134,11);
$Tother=imagecolorallocate($image,255,105,180); $Fother=imagecolorallocate($image,255,20,147); 	$Rother=imagecolorallocate($image,199,21,133);


$Tile="客户月下单、出货总额变化趋势图";
$titleX=$imW/2-130;												//标题输出的起始X位置
imagettftext($image,15,0,$titleX,20,$TextBlack,$UseFont,$Tile);	//输出标题

$imL=50;														//顶部X:左边距
$imT=50;														//顶部Y:上边距
$imR=$imW-10;													//底部X:
$imB=$imH-50;													//底部Y:
imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
$MoveLeft=0;													//上下偏移量，柱体厚度

imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imR-$MoveLeft,$imB+$MoveLeft,$TextBlack);	//左移线
imageline($image,$imL-$MoveLeft,$imB+$MoveLeft,$imL-$MoveLeft,$imT+$MoveLeft,$TextBlack);	//下移线

$MonthTemp=8;
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
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imB+35+$MoveLeft,$TextRed,$UseFont,"2009年");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-20,$imT-20,$TextRed,$UseFont,"2009年");
			}
		else{
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imB+20+$MoveLeft,$TextBlack,$UseFont,$MonthTemp."月");
			imagettftext($image,10,0,($imL+$i*$MonthStep)-$MonthStep/2-10,$imT-5,$TextBlack,$UseFont,$MonthTemp."月");
			}
		}
	}

//金额间隔线
$countY=$MaxAmount/50*2;
$AmountStep=25;
for($i=0;$i<=$countY;$i++){
	$TempAmount=$AmountStep*$i;
	if($i==0 || $i==$countY){
		imageline($image,$imL,$imB-$i*$AmountStep,$imL-$MoveLeft,$imB-$i*$AmountStep+$MoveLeft,$TextBlack);		//斜线
		imageline($image,$imL-$MoveLeft,$imB-$i*$AmountStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$AmountStep+$MoveLeft,$TextBlack);//短线
		}
	else{
		imageline($image,$imL,$imB-$i*$AmountStep,$imR,$imB-$i*$AmountStep,$Gridcolor);			//间隔线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL-$MoveLeft,$imB-$i*$AmountStep+$MoveLeft,$Gridcolor);		//斜线
		imageline($image,$imL-$MoveLeft,$imB-$i*$AmountStep+$MoveLeft,$imL-$MoveLeft-5,$imB-$i*$AmountStep+$MoveLeft,$Gridcolor);//短线
		//输出金额
		$TempAmountX=$TempAmount<100?30:($TempAmount<1000?24:18);
 		imagestring($image,3,$TempAmountX-$MoveLeft,($imB-$AmountStep*$i)+$MoveLeft-7,$TempAmount,$TextBlack);
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
							WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.CompanyId 
						) A WHERE Amount>=200000 GROUP BY CompanyId ORDER BY Amount DESC",$link_id);
						if($clientRow=mysql_fetch_array($clientSql)){
							//坐标计算：X轴坐标每月只变化一次
							$YB=$imB+$MoveLeft;
							$YT=$imB;
							$XL=$imL+$MonthStep*($i-1)+$jCube+$MonthStep/2;
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
								$Fcolor="F".$CompanyId;$Rcolor="R".$CompanyId;$Tcolor="T".$CompanyId;
								//画柱形图正面
								imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Fcolor); //画填充矩形
								
								//画柱形图侧面
								$TempPoints[0]=$XR;$TempPoints[1]=$YB;
								$TempPoints[2]=$XR;$TempPoints[3]=$YT;
								$TempPoints[4]=$XR+$MoveLeft;$TempPoints[5]=$YT-$MoveLeft;
								$TempPoints[6]=$XR+$MoveLeft;$TempPoints[7]=$YB-$MoveLeft;
								imagefilledpolygon($image,$TempPoints,4,$$Rcolor);
								$Forshort=$Forshort=="MCA包装"?"包装":$Forshort;
								imagettftext($image,12,1,$XL,$YB,$TextWhite,$UseFont,$Forshort);
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
									$Fcolor="Fother";
									$Rcolor="Rother";
									$Tcolor="Tother";
									//画柱形图正面
									imagefilledrectangle($image,$XL,$YB,$XR,$YT,$Fother); //画填充矩形
									imagettftext($image,12,1,$XL,$YB,$TextWhite,$UseFont,"其它");
									
									//画柱形图侧面
									$TempPoints[0]=$XR;$TempPoints[1]=$YB;
									$TempPoints[2]=$XR;$TempPoints[3]=$YT;
									$TempPoints[4]=$XR+$MoveLeft;$TempPoints[5]=$YT-$MoveLeft;
									$TempPoints[6]=$XR+$MoveLeft;$TempPoints[7]=$YB-$MoveLeft;
									imagefilledpolygon($image,$TempPoints,4,$$Rcolor);
									//Y坐标重设
									$YB=$YT;
								}
							//顶部
							$TempPoints[0]=$XL;$TempPoints[1]=$YT;
							$TempPoints[6]=$XL+$MoveLeft;$TempPoints[7]=$YT-$MoveLeft;
							imagefilledpolygon($image,$TempPoints,4,$$Tcolor);
							//总金额
							$SumAmount=sprintf("%.0f",$SumAmount/10000);
							//imagestring($image,3,$TempPoints[6],$TempPoints[7],$SumAmount,$TextBlack);
							imagettftext($image,12,0,$TempPoints[6],$TempPoints[7],$TextBlack,$UseFont,$SumAmount."万");
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
								$YB=$imB+$MoveLeft;
								$YT=$imB;
								$XL=$imL+$MonthStep*($i-1)+$jCube+$MonthStep/2;
								$XR=$XL+30;
								$SumAmount=0;
								$Fcolor="Fother";$Rcolor="Rother";$Tcolor="Tother";
								$Amount=$allRow["Amount"];
								$SumAmount=$SumAmount+$Amount;
								$shipAmount=intval(sprintf("%.0f",$Amount/10000));											//月出货总额
								$YT =$YB-$shipAmount;
								//总金额
								if($SumAmount>=10000){$SumAmount=sprintf("%.0f",$SumAmount/10000)."万";}
								else{$SumAmount=sprintf("%.0f",$SumAmount)."元";}
								if($SumAmount>0){
									//画柱形图正面
									imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Fcolor); //画填充矩形
									
									//画柱形图侧面
									$TempPoints[0]=$XR;$TempPoints[1]=$YB;
									$TempPoints[2]=$XR;$TempPoints[3]=$YT;
									$TempPoints[4]=$XR+$MoveLeft;$TempPoints[5]=$YT-$MoveLeft;
									$TempPoints[6]=$XR+$MoveLeft;$TempPoints[7]=$YB-$MoveLeft;
									//imagefilledpolygon($image,$TempPoints,4,$$Rcolor);
									//顶部
									$TempPoints[0]=$XL;$TempPoints[1]=$YT;
									$TempPoints[6]=$XL+$MoveLeft;$TempPoints[7]=$YT-$MoveLeft;
									//imagefilledpolygon($image,$TempPoints,4,$$Tcolor);
									imagettftext($image,12,0,$TempPoints[6],$TempPoints[7],$TextBlack,$UseFont,$SumAmount);}
								}
							}
							
		//***********当月下单输出
		/////////////////////////////////////////////////
		$clientSql=mysql_query("
			SELECT Amount,CompanyId,Forshort FROM (
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,M.CompanyId,D.Forshort 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' GROUP BY M.CompanyId ) A 
			WHERE Amount>=200000 GROUP BY CompanyId ORDER BY Amount DESC
			",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){
			//坐标计算：X轴坐标每月只变化一次
			$YB=$imB+$MoveLeft;
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
				$Fcolor="F".$CompanyId;
				$Rcolor="R".$CompanyId;
				$Tcolor="T".$CompanyId;
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Fcolor); //画填充矩形
				//imagestring($image,3,$XR+200,$YT,$YT,$TextBlack);
				
				//画柱形图侧面
				$TempPoints[0]=$XR;$TempPoints[1]=$YB;
				$TempPoints[2]=$XR;$TempPoints[3]=$YT;
				$TempPoints[4]=$XR+$MoveLeft;$TempPoints[5]=$YT-$MoveLeft;
				$TempPoints[6]=$XR+$MoveLeft;$TempPoints[7]=$YB-$MoveLeft;
				imagefilledpolygon($image,$TempPoints,4,$$Rcolor);
				$Forshort=$Forshort=="MCA包装"?"包装":$Forshort;
				imagettftext($image,12,1,$XL,$YB,$TextWhite,$UseFont,$Forshort);
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
					$Fcolor="Fother";
					$Rcolor="Rother";
					$Tcolor="Tother";
					//画柱形图正面
					imagefilledrectangle($image,$XL,$YB,$XR,$YT,$Fother); //画填充矩形
					imagettftext($image,12,1,$XL,$YB,$TextWhite,$UseFont,"其它");
					
					//画柱形图侧面
					$TempPoints[0]=$XR;$TempPoints[1]=$YB;
					$TempPoints[2]=$XR;$TempPoints[3]=$YT;
					$TempPoints[4]=$XR+$MoveLeft;$TempPoints[5]=$YT-$MoveLeft;
					$TempPoints[6]=$XR+$MoveLeft;$TempPoints[7]=$YB-$MoveLeft;
					imagefilledpolygon($image,$TempPoints,4,$$Rcolor);
					//Y坐标重设
					$YB=$YT;
				}
			//顶部
			$TempPoints[0]=$XL;$TempPoints[1]=$YT;
			$TempPoints[6]=$XL+$MoveLeft;$TempPoints[7]=$YT-$MoveLeft;
			imagefilledpolygon($image,$TempPoints,4,$$Tcolor);
			//总金额
			if($SumAmount>=10000){$SumAmount=sprintf("%.0f",$SumAmount/10000)."万";}
								else{$SumAmount=sprintf("%.0f",$SumAmount)."元";}
			imagettftext($image,12,0,$TempPoints[6]-3,$TempPoints[7],$TextBlack,$UseFont,$SumAmount);
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
				$Fcolor="Fother";$Rcolor="Rother";$Tcolor="Tother";
				$Amount=$allRow["Amount"];
				$SumAmount=$SumAmount+$Amount;
				$shipAmount=intval(sprintf("%.0f",$Amount/10000));											//月出货总额
				$YT =$YB-$shipAmount;
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Fcolor); //画填充矩形
				//imagestring($image,3,$XR+200,$YT,$YT,$TextBlack);
				
				//画柱形图侧面
				$TempPoints[0]=$XR;$TempPoints[1]=$YB;
				$TempPoints[2]=$XR;$TempPoints[3]=$YT;
				$TempPoints[4]=$XR+$MoveLeft;$TempPoints[5]=$YT-$MoveLeft;
				$TempPoints[6]=$XR+$MoveLeft;$TempPoints[7]=$YB-$MoveLeft;
				imagefilledpolygon($image,$TempPoints,4,$$Rcolor);
				//imagettftext($image,12,1,$XL+5,$YB,$TextWhite,$UseFont,"ALL");客户标记
				//顶部
				$TempPoints[0]=$XL;$TempPoints[1]=$YT;
				$TempPoints[6]=$XL+$MoveLeft;$TempPoints[7]=$YT-$MoveLeft;
				imagefilledpolygon($image,$TempPoints,4,$$Tcolor);
				//总金额
				if($SumAmount>=10000){$SumAmount=sprintf("%.0f",$SumAmount/10000)."万";}
				else{$SumAmount=sprintf("%.0f",$SumAmount)."元";}
				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7],$TextBlack,$UseFont,$SumAmount);
				}
			//////////////////////
			}
		
		//**********************
		$i++;
		}while ($MonthRow=mysql_fetch_array($MonthSql));
	}
	
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);              //释放资源
?>