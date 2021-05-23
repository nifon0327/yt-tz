<?php   
//*****************************************************************出货下单金额**********电信---yang 20120801
$TotalSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 AND T.Estate ='1' $ClientStr $TjOut",$link_id));
$SumOutAmount=$TotalSql["Amount"];
//******************************************************************采购成本
include "../model/subprogram/sys_parameters.php";
$cbAmountUSD=0;$cbAmountRMB=0;
$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,SUM(A.OrderQty*A.Price*C.Rate) AS oTheCost2,C.Symbol,B.ProviderType
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.ch1_shipsheet H ON H.POrderId=A.POrderId
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=H.Mid 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1  AND M.Estate=0 $ClientStr $TjOut GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$cbAmount=sprintf("%.3f",$CostRow["oTheCost"]);
				$TempSymbol=$CostRow["Symbol"];
				$TempoTheCost=$CostRow["oTheCost"];
				$TempoTheCost2=$CostRow["oTheCost2"];
				$AmountTemp="cbAmount".strval($TempSymbol);
				$$AmountTemp=sprintf("%.0f",$TempoTheCost);//毛利成本
				$AmountTemp2="llcbAmount".strval($TempSymbol);
				$$AmountTemp2=sprintf("%.0f",$TempoTheCost2);//理论成本
				}while ($CostRow= mysql_fetch_array($CostResult));
			}
$SumGrossAmount=number_format($SumOutAmount-($cbAmountUSD+$cbAmountRMB));//毛利率
$profitRMB=number_format($SumOutAmount-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate);//理论利润


$TotalSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE  T.Estate ='1' $ClientStr $TjIn",$link_id));
$SumInAmount=$TotalSql["Amount"];
//*****************************************************************出货下单数量
$QtySql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 AND T.Estate ='1' $ClientStr $TjOut",$link_id));
$SumOutQty=$QtySql["Qty"];

$QtySql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE  T.Estate ='1' $ClientStr $TjIn",$link_id));
$SumInQty=$QtySql["Qty"];

//*****************************************************************取最大值
$CheckMaxRow= mysql_fetch_array(mysql_query("
SELECT MAX(MAXClientAmount) AS MAXClientAmount FROM(
	SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS MAXClientAmount
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE M.Estate=0  $ClientStr $TjOut  GROUP BY PT.mainType
	UNION ALL
	 SELECT SUM(S.Qty*S.Price*C.Rate) AS MAXClientAmount
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency  
	WHERE 1  AND D.Estate='1' $ClientStr $TjIn GROUP BY PT.mainType
)A",$link_id));
$MAXClientAmount=$CheckMaxRow["MAXClientAmount"];
//点值=最大值/宽度
$onepointV=intval($MAXClientAmount/($TypeimR-200));
$YvlaueLenght=strlen($onepointV)-1;
$Ystep=pow(10,$YvlaueLenght);
$xValue=$Ystep*ceil($onepointV/$Ystep);	//1X像素点的金额,自动调整(当值越来越大时,调整改值)
$xStep=50;
$Num_W2=($TypeimR-200)/50;

imagefilledrectangle($image,50,$TypeimB,$TypeimR,$TypeimB+50,$JgColor);
for($k=0;$k<=$TypeNum+1;$k++){//Y轴间隔线
	imageline($image,$TypeimL,$TypeimB-$k*50,$TypeimR,$TypeimB-$k*50,$TextBlack);
	}
for($k=1;$k<=$Num_W2;$k++){//X轴间隔线
	imageline($image,$TypeimL+$k*$xStep+50,$TypeimT,$TypeimL+$k*$xStep+50,$TypeimB,$Gridcolor);//间隔线
	if($k<$Num_W2){
		$m=$xValue*$xStep*$k/10000;
		imagettftext($image,12,0,$AllimL+$k*$xStep-15+150,$TypeimB+25,$TextBlack,$UseFont,$m);//间隔值
		}
	}
	imagettftext($image,12,0,50,$TypeimB+25,$TextBlack,$UseFont,"(单位:万元) 0");
	

$TypeResult = mysql_query("SELECT A.Name,A.Id,SUM(Amount) AS Amount FROM (
            SELECT R.Name,R.Id,S.Qty*S.Price*S.YandN*C.Rate*M.Sign AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
			WHERE M.Estate=0 AND T.Estate ='1' $ClientStr $TjOut  
			UNION ALL
			SELECT R.Name,R.Id,S.Qty*S.Price*C.Rate AS Amount
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
			WHERE  T.Estate ='1' $ClientStr $TjIn) A GROUP BY A.Id ORDER BY Amount DESC 
			",$link_id);
if($CheckAllRow=mysql_fetch_array($TypeResult)){
	$CimL=$TypeimL+101;		//条形图起始左坐标
	$CimB=$TypeimB-1;		//条形图起始底坐标
	$i=1;
	do{
		$Id=$CheckAllRow["Id"];
		$MName=$CheckAllRow["Name"];
		$mtColor="mtColor".strval($Id);
		$CimT=$CimB-20;		//条形图顶坐标，即条形图高度
		imagettftext($image,12,0,$TypeimL+2,$CimB-5,$$mtColor,$UseFont,$i."-".$MName);
		  $InRow=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE  T.Estate ='1' $ClientStr $TjIn AND R.Id='$Id' ",$link_id));
		$InAmount=sprintf("%.0f",$InRow["Amount"]);
		$InPC=sprintf("%.1f",$InAmount*100/$SumInAmount);
		//*****************画下单条形图
		$CimR=intval(sprintf("%.0f",$InAmount/$xValue));
		if($CimR>0){
			imagefilledrectangle($image,$CimL,$CimT,$CimL+$CimR,$CimB,$$mtColor);
			}
		if($InAmount>0){
			imagettftext($image,11,0,$CimL+$CimR+10,$CimB,$TextBlack,$UseFont,number_format($InAmount)."(".$InPC."%)");
			}
		//*****************画出货条形图	
		$ShipRow=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 AND T.Estate ='1' $ClientStr $TjOut  AND R.Id='$Id'",$link_id));
		$OutAmount=sprintf("%.0f",$ShipRow["Amount"]);
		$OutPC=sprintf("%.1f",$OutAmount*100/$SumOutAmount);
		$CimR=intval(sprintf("%.0f",$OutAmount/$xValue));
		if($CimR>0){
		    $alpha_white=imagecolorallocatealpha($image, 255, 255, 255,50);
			imagefilledrectangle($image,$CimL,$CimT-22,$CimL+$CimR,$CimB-20.5,$$mtColor);
			imagefilledrectangle($image,$CimL,$CimT-22,$CimL+$CimR,$CimB-20.5,$alpha_white); //画透明
			}
		if($OutAmount>0){
			imagettftext($image,11,0,$CimL+$CimR+10,$CimB-25,$TextBlack,$UseFont,number_format($OutAmount)."(".$OutPC."%)");
			}
			
		$CimB=$CimB-$xStep;//Y轴上移 1 个行高
		$i++;
		}while ($CheckAllRow=mysql_fetch_array($TypeResult));
	}
$SumOutAmount=number_format(sprintf("%.0f",$SumOutAmount));
$SumInAmount=number_format(sprintf("%.0f",$SumInAmount));
$SumOutQty=number_format($SumOutQty);
$SumInQty=number_format($SumInQty);

//图例说明
imagefilledrectangle($image,$TypeimR-309,$TypeimT+1,$TypeimR-1,$TypeimT+99,$Gridbgcolor);
//imagefilledrectangle($image,$TypeimR-250,$TypeimT+10,$TypeimR-220,$TypeimT+40,$T1001);
//imagefilledrectangle($image,$TypeimR-250,$TypeimT+60,$TypeimR-220,$TypeimT+90,$T1001);
imagettftext($image,12,0,$TypeimR-300,$TypeimT+30,$TextBlack,$UseFont,"出货总额(上)：".$SumOutAmount."(".$SumOutQty." PCS)");
imagettftext($image,12,0,$TypeimR-300,$TypeimT+80,$TextBlack,$UseFont,"下单总额(下)：".$SumInAmount."(". $SumInQty." PCS)");
//imagettftext($image,12,0,$TypeimR-300,$TypeimT+80,$TextBlack,$UseFont,"出货利润：".$SumGrossAmount);
?>