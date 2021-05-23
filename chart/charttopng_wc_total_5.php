<?php   
//未出订单总额*********电信---yang 20120801
$CheckwcSumSql= mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,D.Forshort
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE 1 AND S.Estate>0 GROUP BY D.CompanyId",$link_id);
if($CheckwcSumRow=mysql_fetch_array($CheckwcSumSql)){//总额
   do{
	$SumwcAmount+=$CheckwcSumRow["Amount"];
	}while($CheckwcSumRow=mysql_fetch_array($CheckwcSumSql));
  }
 //未出订单毛利总额
 $GrossProfit=0;
 $CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS TheCost
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>0 GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$TempTheCost=sprintf("%.3f",$CostRow["TheCost"]);
				$SumwcCost+=$TempTheCost;
				}while($CostRow= mysql_fetch_array($CostResult));
			}
$GrossProfit=$SumwcAmount-$SumwcCost;

//取最大值
$CheckMaxRow= mysql_fetch_array(mysql_query("
SELECT MAX(MAXClientAmount) AS MAXClientAmount FROM(
	SELECT SUM(S.Qty*S.Price*C.Rate) AS MAXClientAmount
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1  AND D.Estate='1' AND S.Estate>0 GROUP BY M.CompanyId
)A",$link_id));
$MAXClientAmount=$CheckMaxRow["MAXClientAmount"];
//点值=最大值/宽度
$onepointV=intval($MAXClientAmount/($AllimR-200));
$YvlaueLenght=strlen($onepointV)-1;
$Ystep=pow(10,$YvlaueLenght);
//$xValue=$Ystep*ceil($onepointV/$Ystep)*1.8;	//1X像素点的金额,自动调整
//$xValue=ceil($MAXClientAmount/1000000)*1000;//1X像素点的金额
$xValue=20000;//固定为100万一格
$xStep=50;//每隔像素值
//Y轴间隔线
for($k=0;$k<$clientNum;$k++){
	imageline($image,$imL+$MoveY,$AllimB-$k*50,$AllimR+$MoveY,$AllimB-$k*50,$TextBlack);
	}

$CheckAllSql= mysql_query("SELECT Qty,Amount,CompanyId,ForShort FROM(
    SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*C.Rate) AS Amount,M.CompanyId,D.ForShort
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	WHERE 1 AND D.Estate='1' AND S.Estate>0 GROUP BY D.CompanyId ORDER BY Amount DESC) A
	WHERE 1",$link_id);
if($CheckAllRow=mysql_fetch_array($CheckAllSql)){
	$CimL=$AllimL+101;			//固定值
	$CimB=$AllimB-1;		//初始化位置为底部上升5
	$i=1;
	do{
		$CimT=$CimB-15;
		$CompanyId=$CheckAllRow["CompanyId"];
		$ForShort=$CheckAllRow["ForShort"];
        $wcQty=sprintf("%.0f",$CheckAllRow["Qty"]/1000);
		$wcAmount=sprintf("%.0f",$CheckAllRow["Amount"]);
		$CimR1=intval(sprintf("%.0f",$wcAmount/$xValue));
		$wcBL=sprintf("%.1f",$wcAmount*100/$SumwcAmount);
		//未出订单总额
	  if($wcAmount>0){
		if($CimR1>0){
			imagefilledrectangle($image,$CimL+$MoveY,$CimB,$CimL+$CimR1+$MoveY,$CimT,$Tallout);
			}
		imagettftext($image,11,0,$CimL+$MoveY+5,$CimB,$TextWhite,$UseFont,$wcBL."%");
		imagettftext($image,11,0,$CimL+$CimR1+$MoveY+10,$CimB,$TextBlack,$UseFont,number_format($wcAmount)."(".number_format($wcQty)."K PCS)");
		imagettftext($image,10,0,$imL+$MoveY+2,$CimB-5,$TextRed,$UseFont,$i."-".$ForShort);
		//**************************各客户各类产品未出订单分类
		$WcTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,T.mainType
	          FROM $DataIn.yw1_ordersheet S 
	          LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			  LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	          LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
	          LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
	          WHERE 1 AND D.Estate='1' AND S.Estate>0 AND M.CompanyId='$CompanyId' AND T.mainType IS NOT NULL 
			  GROUP BY T.mainType ORDER BY Amount DESC",$link_id);
		if($WcTypeRow=mysql_fetch_array($WcTypeSql)){
		    $CimB3=$CimT;
		    $CimT3=$CimB3-5;
			$XR=0;
		   do{
		     $mainType=$WcTypeRow["mainType"];
		     $WcTypeAmount=sprintf("%.0f",$WcTypeRow["Amount"]);
		     $CimR3=intval(sprintf("%.0f",$WcTypeAmount/$xValue));
			 $mainTypeC="mtColor".strval($mainType);
			 $mainTypeC=$$mainTypeC;
		     imagefilledrectangle($image,$CimL+$MoveY+$XR,$CimB3,$CimL+$MoveY+$XR+$CimR3,$CimT3,$mainTypeC); //画矩形
			 $XR =$XR+$CimR3;
		     }while($WcTypeRow=mysql_fetch_array($WcTypeSql));
		  }
		//**************************各个客户未出订单毛利总额图
		$ThecostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS TheCost
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain  M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>0 AND M.CompanyId='$CompanyId'",$link_id);
		if($ThecostRow=mysql_fetch_array($ThecostResult)){
		  $ThecostAmount=$ThecostRow["TheCost"];
		  }
		$GrossAmount=$wcAmount-$ThecostAmount;
		$GrossPC=sprintf("%.1f",$GrossAmount*100/$wcAmount);
		$CimR2=intval(sprintf("%.0f",$GrossAmount/$xValue));
		$CimB2=$CimT3-1;
		$CimT2=$CimB2-15;
		if($CimR2>0){
			imagefilledrectangle($image,$CimL+$MoveY,$CimB2,$CimL+$CimR2+$MoveY,$CimT2+3,$Tallout);
			imagefilledrectangle($image,$CimL+$MoveY,$CimB2,$CimL+$CimR2+$MoveY,$CimT2+3,$alpha_white);
			}
		///////////////////////////////////////////////////////////////////////////////
		if($GrossAmount>0){
			//imagettftext($image,11,0,$CimL+5,$CimB2,$TextWhite,$UseFont,$GrossPC."%");
			//输出比率+$CimR2-40
	     imagettftext($image,11,0,$CimL+$CimR2+$MoveY+10,$CimB2,$TextRed,$UseFont,number_format($GrossAmount)."(".$GrossPC."%)");
			}
		
		  //**********************************************************
	   
		//Y轴累加
		  $CimB=$CimB-$xStep;
		  $i++;
		  }
		  if($i>$clientNum)break;
		}while ($CheckAllRow=mysql_fetch_array($CheckAllSql));
	}

//X轴间隔线
$Num_W2=($AllimR-100)/50;
for($k=1;$k<=$Num_W2;$k++){
	imageline($image,$AllimL+$k*$xStep+$MoveY+50,$AllimT,$AllimL+$k*$xStep+$MoveY+50,$AllimB+10,$Gridcolor);//间隔线
	if($k<$Num_W2){
		$m=$xValue*$xStep*$k/10000;
		imagettftext($image,12,0,$AllimL+$k*$xStep+$MoveY-15+100,$AllimB+26,$TextBlack,$UseFont,$m);//间隔值
		}
	}
imagettftext($image,12,0,$MoveY+50,$AllimB+25,$TextBlack,$UseFont,"(单位:万元) 0");
//输出图像
//$AvgInMonth=$SumInAmount/($CheckMonths+1);
//$AvgOutMonth=$SumOutAmount/($CheckMonths+1);

//客户图例说明
imagefilledrectangle($image,$AllimR+$MoveY-349,$AllimT+1,$AllimR+$MoveY-1,$AllimT+99,$Gridbgcolor);
imagefilledrectangle($image,$AllimR+$MoveY-340,$AllimT+10,$AllimR+$MoveY-310,$AllimT+40,$Tallout);
imagefilledrectangle($image,$AllimR+$MoveY-340,$AllimT+60,$AllimR+$MoveY-310,$AllimT+90,$Tallout);
imagefilledrectangle($image,$AllimR+$MoveY-340,$AllimT+60,$AllimR+$MoveY-310,$AllimT+90,$alpha_white);
//$GrossProfit=intval(sprintf("%.0f",$GrossProfit/10000));
//$SumwcAmount=intval(sprintf("%.0f",$SumwcAmount/10000));  
imagettftext($image,14,0,$AllimR+$MoveY-300,$AllimT+30,$TextBlack,$UseFont,"订单金额(比例):".number_format($SumwcAmount)."元");
imagettftext($image,14,0,$AllimR+$MoveY-300,$AllimT+80,$TextBlack,$UseFont,"毛利总额(毛利率):".number_format($GrossProfit)."元");
imagesetthickness ($image,1);
?>
