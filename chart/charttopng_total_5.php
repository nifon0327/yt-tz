<?php   
//Y轴间隔线*********电信---yang 20120801
for($k=0;$k<$clientNum;$k++){
	imageline($image,$imL,$AllimB-$k*50,$AllimR,$AllimB-$k*50,$TextBlack);
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
//点值=最大值/宽度
$onepointV=intval($MAXClientAmount/($AllimR-200));
$YvlaueLenght=strlen($onepointV)-1;
$Ystep=pow(10,$YvlaueLenght);
//$xValue=$Ystep*ceil($onepointV/$Ystep)*1.5;	//1X像素点的金额,自动调整(当值越来越大时,调整改值)
$xValue=100000;//固定为500万一格

//$xValue=ceil($MAXClientAmount/1000000)*1000;//1X像素点的金额
$xStep=50;//每隔像素值

//出货总额
if($CheckAllRow=mysql_fetch_array($CheckAllSql)){
	$CimL=$AllimL+101;			//固定值
	$CimB=$AllimB-1;		//初始化位置为底部上升5
	$i=1;
	do{
		$CimT=$CimB-20;
		$CompanyId=$CheckAllRow["CompanyId"];
		$ForShort=$CheckAllRow["ForShort"];
		$InAmount=sprintf("%.0f",$CheckAllRow["InAmount"]);
		$OutAmount=sprintf("%.0f",$CheckAllRow["OutAmount"]);
		$CimR1=intval(sprintf("%.0f",$InAmount/$xValue));
		$OUTCRMB="";
		$OUTXRMB="";
		//画下单条形图 500万
		if($CimR1>0){
			imagefilledrectangle($image,$CimL,$CimB,$CimL+$CimR1,$CimT,$Tallout);
			imagefilledrectangle($image,$CimL,$CimB,$CimL+$CimR1,$CimT,$alpha_white);
			}
		if($InAmount>0){
			imagettftext($image,11,0,$CimL+$CimR1+10,$CimB,$TextBlack,$UseFont,number_format($InAmount));}
		imagettftext($image,10,0,$imL+2,$CimB-5,$TextRed,$UseFont,$i."-".$ForShort);
		
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
                                                $typeOutBL=sprintf("%.1f",$typeAmount*100/$OutAmount);
						$OUTCRMB="(外购:".$typeOutBL."%)";
                                              //  $OUTCRMB="(外购:".number_format($typeAmount).")";
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
                                                $typeInBL=sprintf("%.1f",$typeAmount*100/$InAmount);
						$OUTXRMB="(外购:".$typeInBL."%)";
					}
					$mainTypeC="mtColor".strval($mainType);
					$mainTypeC=$$mainTypeC;
					$TempWidth=intval($typeAmount/$xValue);
					$XR =$XR+$TempWidth;//条形图新的X座标
					imagerectangle($image,$XL,$CimT+16,$XR,$CimB-1,$mainTypeC); //画矩形
					imagerectangle($image,$XL,$CimT+16,$XR,$CimB-1,$alpha_white); //画透明矩形
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
$Num_W2=($AllimR-200)/50;
for($k=1;$k<=$Num_W2;$k++){
	imageline($image,$AllimL+$k*$xStep+50,$AllimT,$AllimL+$k*$xStep+50,$AllimB+10,$Gridcolor);//间隔线
	if($k<$Num_W2){
		$m=$xValue*$xStep*$k/10000;
		imagettftext($image,12,0,$TypeimL+$k*$xStep-15+100,$AllimB+26,$TextBlack,$UseFont,$m);//间隔值
		}
	}
imagettftext($image,12,0,50,$AllimB+25,$TextBlack,$UseFont,"(单位:万元) 0");
//输出图像
$AvgInMonth=$SumInAmount/($CheckMonths+1);
$AvgOutMonth=$SumOutAmount/($CheckMonths+1);
//画均线
if($AvgInMonth>0){
		$tempY=round($AvgInMonth/10000)*(25/$unitHeight);						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$TextRed);	//画平均线
		imagestring($image,3,$imR+70,$imB-$tempY-7,number_format($AvgInMonth),$TextRed);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+3,$TextRed,$UseFont,"接单均线:");
		}
if($AvgOutMonth>0){
		$tempY=round($AvgOutMonth/10000)*(25/$unitHeight);						//平均线的Y坐标
		imagesetthickness($image,2);
		imageline($image,$imL,$imB-$tempY,$imR+5,$imB-$tempY,$Tallout);	//画平均线
		imagestring($image,3,$imR+70,$imB-$tempY-7,number_format($AvgOutMonth),$Tallout);//输出平均值
		imagettftext($image,10,0,$imR+10,$imB-$tempY+3,$Tallout,$UseFont,"出货均线:");
		}

//客户图例说明
imagefilledrectangle($image,$AllimR-299,$AllimT+1,$AllimR-1,$AllimT+99,$Gridbgcolor);
imagefilledrectangle($image,$AllimR-290,$AllimT+10,$AllimR-260,$AllimT+40,$Tallout);
imagefilledrectangle($image,$AllimR-290,$AllimT+60,$AllimR-260,$AllimT+90,$Tallout);
imagefilledrectangle($image,$AllimR-290,$AllimT+60,$AllimR-260,$AllimT+90,$alpha_white);
//$SumInAmount=intval(sprintf("%.0f",$SumInAmount/10000));
//$SumOutAmount=intval(sprintf("%.0f",$SumOutAmount/10000));  
imagettftext($image,12,0,$AllimR-250,$AllimT+30,$TextBlack,$UseFont,$CheckYear."出货总额：".number_format($SumOutAmount)." 元");
imagettftext($image,12,0,$AllimR-250,$AllimT+80,$TextBlack,$UseFont,$CheckYear."接单总额：".number_format($SumInAmount)." 元");
//imagettftext($image,12,0,$AllimR-250,$AllimT+90,$TextBlack,$UseFont,$CheckYear."利润总额：".number_format($SumGrossAmount)." 元");
imagesetthickness ($image,1);
?>