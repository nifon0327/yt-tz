<?php
//采购交货准时率统计
$Punc_Percent=" ";$Punc_Value=0; $Punc_RIcon="";$Punc_Color="#0050FF";

$Punc_FilterTerms="";
switch($PuncSelectType){
	case 1:
	        $Punc_FilterTerms=" DATE_FORMAT(M.rkDate,'%Y-%m')='$checkMonth' ";
	       break;
    case 2:
          $Punc_FilterTerms=" M.CompanyId='$CompanyId'  and DATE_FORMAT(M.rkDate,'%Y-%m')='$checkMonth' ";
           break;
}
if ($Punc_FilterTerms!=""){
		$NumsResult=mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty   
		FROM $DataIn.ck1_rksheet S
		LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
		LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId=S.StockId  
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		WHERE  $Punc_FilterTerms ",$link_id);
			if($NumsRow = mysql_fetch_array($NumsResult)){
				  $P_Qty=$NumsRow["Qty"];
				  if ($P_Qty>0){
						  $P_OverQty=$NumsRow["OverQty"];
						  $Punc_Value=($P_Qty-$P_OverQty)/$P_Qty*100;
						  $Punc_Color=$Punc_Value<80?"#66B3FF":$Punc_Color;
						  $Punc_RIcon=$Punc_Value<80?"ipunctuality_r":"ipunctuality1_r";
						  $Punc_Value=round($Punc_Value);
						  $Punc_Percent=$Punc_Value>=0?"    " . $Punc_Value ."%":" "; 
				  }
			}
}
?>