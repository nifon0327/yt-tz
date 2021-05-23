<?php   
//Y轴间隔线*********电信---yang 20120801
for($k=0;$k<$TypeNum;$k++){
	imageline($image,$imL,$TypeimB-$k*50,$TypeimR,$TypeimB-$k*50,$TextBlack);
	}
for($k=1;$k<=$Num_W2;$k++){
	imageline($image,$TypeimL+$k*$xStep+50,$TypeimT-10,$TypeimL+$k*$xStep+50,$TypeimB,$Gridcolor);//间隔线
	}

$ShipResult = mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,R.Name,R.Id
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN  $DataIn.productmaintype R ON R.Id=T.mainType 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 $TJA  GROUP BY T.mainType ORDER BY Amount DESC
			",$link_id);
if($CheckAllRow=mysql_fetch_array($ShipResult)){
	$CimL=$TypeimL+101;		//条形图起始左坐标
	$CimB=$TypeimB-1;		//条形图起始底坐标
	$i=1;
	do{
		$CimT=$CimB-30;		//条形图顶坐标，即条形图高度20
		$MName=$CheckAllRow["Name"];
		$OutAmount=sprintf("%.0f",$CheckAllRow["Amount"]);
		$Id=$CheckAllRow["Id"];
		$mtColor="mtColor".strval($Id);
		imagettftext($image,12,0,$imL+2,$CimB-5,$$mtColor,$UseFont,$i."-".$MName);
		//画出货条形图
		$OutPC=sprintf("%.1f",$OutAmount*100/$SumOutAmount);
		$CimR=intval(sprintf("%.0f",$OutAmount/$xValue));
        if($CimR>$Num_W2*50)$Temp_Y=$CimL+$Num_W2*50+50;
        else $Temp_Y=$CimL+$CimR;
		if($CimR>0){
			imagefilledrectangle($image,$CimL,$CimT,$Temp_Y,$CimB,$$mtColor);
			}
		if($OutAmount>0){
			imagettftext($image,11,0,$Temp_Y+10,$CimB,$TextBlack,$UseFont,number_format($OutAmount)."(".$OutPC."%)");
			}
		$CimB=$CimB-$xStep;//Y轴上移 1 个行高
		$i++;
		}while ($CheckAllRow=mysql_fetch_array($ShipResult));
	}
//图例说明
imagefilledrectangle($image,$TypeimR-299,$TypeimT+1,$TypeimR-1,$TypeimT+99,$Gridbgcolor);
imagettftext($image,14,0,$TypeimR-225,$TypeimT+55,$TextBlack,$UseFont,"主分类出货统计图");
?>