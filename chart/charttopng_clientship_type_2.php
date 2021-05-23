<?php   
//**********电信---yang 20120801
$TotalSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE  S.Estate>0 AND  T.Estate ='1' $ClientStr $TjIn",$link_id));
$SumNotAmount=$TotalSql["Amount"];

$NotQtySql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE  S.Estate>0 AND  T.Estate ='1' $ClientStr $TjIn",$link_id));
$SumNotQty=$NotQtySql["Qty"];

//取最大值
$CheckMaxRow= mysql_fetch_array(mysql_query("
SELECT MAX(MAXClientAmount) AS MAXClientAmount FROM(
    SELECT SUM(S.Qty*S.Price*C.Rate) AS MAXClientAmount
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency  
	WHERE S.Estate>0 AND D.Estate='1' $ClientStr $TjIn GROUP BY PT.mainType
	)A",$link_id));
$MAXClientAmount=$CheckMaxRow["MAXClientAmount"];
//点值=最大值/宽度
$onepointV=intval($MAXClientAmount/($TypeimR-200));
$YvlaueLenght=strlen($onepointV)-1;
$Ystep=pow(10,$YvlaueLenght);
$xValue=$Ystep*ceil($onepointV/$Ystep);	//1X像素点的金额,自动调整(当值越来越大时,调整改值)
$xStep=50;
$Num_W2=($TypeimR-200)/50;

imagefilledrectangle($image,$TypeimL_X,$TypeimB,$TypeimR_X,$TypeimB+50,$JgColor);
for($k=0;$k<=$TypeNum+1;$k++){//Y轴间隔线
	imageline($image,$TypeimL_X,$TypeimB-$k*50,$TypeimR_X,$TypeimB-$k*50,$TextBlack);
	}
for($k=1;$k<=$Num_W2;$k++){//X轴间隔线
	imageline($image,$TypeimL_X+$k*$xStep+50,$TypeimT,$TypeimL_X+$k*$xStep+50,$TypeimB,$Gridcolor);//间隔线
	if($k<$Num_W2){
		$m=$xValue*$xStep*$k/10000;
		imagettftext($image,12,0,$TypeimL_X+$k*$xStep-15+100,$TypeimB+25,$TextBlack,$UseFont,$m);//间隔值
		}
	}
	imagettftext($image,12,0,$TypeimL_X,$TypeimB+25,$TextBlack,$UseFont,"(单位:万元) 0");

$NotShipResult = mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,R.Name,R.Id
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE S.Estate>0 AND T.Estate ='1' $ClientStr $TjIn GROUP BY T.mainType ORDER BY Amount DESC
			",$link_id);
if($CheckAllRow=mysql_fetch_array($NotShipResult)){
	$CimL=$TypeimL_X+101;		//条形图起始左坐标
	$CimB=$TypeimB-1;		//条形图起始底坐标
	$i=1;
	$SumGrossAmount=0;
	do{
		$CimT=$CimB-20;		//条形图顶坐标，即条形图高度20
		$MName=$CheckAllRow["Name"];
		$NotAmount=sprintf("%.0f",$CheckAllRow["Amount"]);
		$Id=$CheckAllRow["Id"];
		$mtColor="mtColor".strval($Id);
		imagettftext($image,12,0,$TypeimL_X+2,$CimB-5,$$mtColor,$UseFont,$i."-".$MName);
		//画出货条形图
		//****************************************************各类产品数量
	    $eachQtySql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE  S.Estate>0 AND  T.Estate ='1' AND R.Id='$Id'  $ClientStr $TjIn ",$link_id));
        $eachNotQty=$eachQtySql["Qty"];
		$NotPC=sprintf("%.1f",$NotAmount*100/$SumNotAmount);
		$CimR=intval(sprintf("%.0f",$NotAmount/$xValue));
		
		if($CimR>0){
			imagefilledrectangle($image,$CimL,$CimT,$CimL+$CimR,$CimB,$$mtColor);
			}
		if($NotAmount>0){
			imagettftext($image,11,0,$CimL+$CimR+10,$CimB,$TextBlack,$UseFont,number_format($NotAmount)."(".$NotPC."%)(".$eachNotQty." PCS)");
			}
		//****************************************************各类产品毛利分析
		$TypecostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS TypeCost
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain  M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>0 AND R.Id='$Id' $ClientStr $TjIn",$link_id);
		if($TypecostRow=mysql_fetch_array($TypecostResult)){
		  $TypecostAmount=$TypecostRow["TypeCost"];
		  }
		$GrossTypeAmount=$NotAmount-$TypecostAmount;
		$GrossTypePC=sprintf("%.1f",$GrossTypeAmount*100/$SumNotAmount);
		$CimR=intval(sprintf("%.0f",$GrossTypeAmount/$xValue));
		if($CimR>0){
			imagefilledrectangle($image,$CimL,$CimT-22,$CimL+$CimR,$CimB-20.5,$$mtColor);
			imagefilledrectangle($image,$CimL,$CimT-22,$CimL+$CimR,$CimB-20.5,$alpha_white); //画透明
			}
		if($GrossTypeAmount>0){
			imagettftext($image,11,0,$CimL+$CimR+10,$CimB-25,$TextBlack,$UseFont,number_format($GrossTypeAmount)."(".$GrossTypePC."%)");
			}
		$CimB=$CimB-$xStep;//Y轴上移 1 个行高
		$i++;
		$SumGrossAmount+=$GrossTypeAmount;
		}while ($CheckAllRow=mysql_fetch_array($NotShipResult));
	}
$SumNotAmount=number_format(sprintf("%.0f",$SumNotAmount));
$SumNotQty=number_format($SumNotQty);
$SumGrossAmount=number_format($SumGrossAmount);
//图例说明
imagefilledrectangle($image,$TypeimR_X-274,$TypeimT+1,$TypeimR_X-1,$TypeimT+99,$Gridbgcolor);
//imagettftext($image,12,0,$TypeimR_X-265,$TypeimT+55,$TextBlack,$UseFont,"主分类未出订单总额/毛利统计图");
imagettftext($image,12,0,$TypeimR_X-265,$TypeimT+30,$TextBlack,$UseFont,"未出订单毛利(上)：".$SumGrossAmount);
imagettftext($image,12,0,$TypeimR_X-265,$TypeimT+80,$TextBlack,$UseFont,"未出订单总额(下)：".$SumNotAmount."(". $SumNotQty." PCS)");
?>