<?php   
//Y轴间隔线*********电信---yang 20120801
for($k=0;$k<$TypeNum;$k++){
	imageline($image,$imL+$MoveY,$TypeimB-$k*50,$TypeimR+$MoveY,$TypeimB-$k*50,$TextBlack);
	}
for($k=1;$k<=$Num_W2;$k++){
	imageline($image,$TypeimL+$k*$xStep+$MoveY+50,$TypeimT-10,$TypeimL+$k*$xStep+$MoveY+50,$TypeimB,$Gridcolor);//间隔线
	}

$TypeResult = mysql_query("SELECT SUM(Qty) AS Qty,SUM(S.Qty*S.Price*C.Rate) AS Amount,R.Name,R.Id
	        FROM $DataIn.yw1_ordersheet S 
	        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE S.Estate>0 GROUP BY T.mainType ORDER BY Amount DESC
			",$link_id);
if($CheckAllRow=mysql_fetch_array($TypeResult)){
	$CimL=$TypeimL+101;		//条形图起始左坐标
	$CimB=$TypeimB-1;		//条形图起始底坐标
	$i=1;
	do{
		$CimT=$CimB-25;		//条形图顶坐标，即条形图高度20
		$MName=$CheckAllRow["Name"];
		$wcTypeAmount=sprintf("%.0f",$CheckAllRow["Amount"]);
		$Id=$CheckAllRow["Id"];
                $wcQty=sprintf("%.0f",$CheckAllRow["Qty"]/1000);
		$mtColor="mtColor".strval($Id);
		imagettftext($image,12,0,$imL+$MoveY+2,$CimB-5,$$mtColor,$UseFont,$i."-".$MName);
		//画出货条形图
		$wcBL=sprintf("%.1f",$wcTypeAmount*100/$SumwcAmount);
		$CimR=intval(sprintf("%.0f",$wcTypeAmount/$xValue));
         if($CimR>$Num_W2*50)$Temp_Y=$CimL+$MoveY+600;//600为第二个图的宽度，除去第一格。12*50=600
        else $Temp_Y=$CimL+$MoveY+$CimR;
		if($CimR>0){
			imagefilledrectangle($image,$CimL+$MoveY,$CimT,$Temp_Y,$CimB,$$mtColor);
			}
		if($wcAmount>0){
		    imagettftext($image,11,0,$CimL+$MoveY+5,$CimB,$TextWhite,$UseFont,$wcBL."%");
			imagettftext($image,11,0,$Temp_Y+10,$CimB,$TextBlack,$UseFont,number_format($wcTypeAmount) ."(".number_format($wcQty)."K PCS)");
			}
		//各类产品毛利分析
		$TypecostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS TypeCost
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain  M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>0 AND T.mainType='$Id'",$link_id);
		if($TypecostRow=mysql_fetch_array($TypecostResult)){
		  $TypecostAmount=$TypecostRow["TypeCost"];
		  }
		  
		$GrossTypeAmount=$wcTypeAmount-$TypecostAmount;
		$GrossTypePC=sprintf("%.1f",$GrossTypeAmount*100/$wcTypeAmount);
		$CimR2=intval(sprintf("%.0f",$GrossTypeAmount/$xValue));
		$CimB2=$CimT;
		$CimT2=$CimB2-25;
		if($CimR2>0){    
			imagefilledrectangle($image,$CimL+$MoveY,$CimB2,$CimL+$CimR2+$MoveY,$CimT2+3,$$mtColor);//画填充矩形
			imagefilledrectangle($image,$CimL+$MoveY,$CimB2-1,$CimL+$CimR2+$MoveY,$CimT2+2,$alpha_white); //画透明
			}
		///////////////////////////////////////////////////////////////////////////////
		if($GrossTypeAmount>0){
			//imagettftext($image,11,0,$CimL+5,$CimB2,$TextWhite,$UseFont,$GrossPC."%");
			//输出比率+$CimR2-40
              imagettftext($image,11,0,$CimL+$CimR2+$MoveY+10,$CimB2,$TextRed,$UseFont,number_format($GrossTypeAmount)."(".$GrossTypePC."%)");
			}
		else{
              imagettftext($image,11,0,$CimL+$CimR2+$MoveY+10,$CimB2,$TextRed,$UseFont,number_format($GrossTypeAmount)."(".$GrossTypePC."%)");
		 }
		  
		 //Y轴累加
		$CimB=$CimB-$xStep;//Y轴上移 1 个行高
		$i++;
		}while ($CheckAllRow=mysql_fetch_array($TypeResult));
	}
//图例说明
imagefilledrectangle($image,$TypeimR+$MoveY-349,$TypeimT+1,$TypeimR+$MoveY-1,$TypeimT+99,$Gridbgcolor);
imagettftext($image,14,0,$TypeimR+$MoveY-305,$TypeimT+55,$TextBlack,$UseFont,"主分类未出订单总额/毛利统计图");
?>