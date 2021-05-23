<?php
//订单准时率统计
$Punc_Percent=" ";$Punc_Value=0;  $Punc_RIcon="";$Punc_Color="#0050FF";

$Punc_FilterTerms="";
switch($PuncSelectType){
	case 1:
	        $Punc_FilterTerms=" M.Estate='0' and  DATE_FORMAT(M.Date,'%Y-%m')='$Month' ";
	       break;
    case 2:
          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$CompanyId'  and DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth' ";
           break;
    case 3:
              $Punc_FilterTerms=" M.Id='$Id' ";
		 break;
    case 4:
	        $Punc_FilterTerms=" M.Estate='0' and  M.Date='$checkDate' ";
	       break;
    case 5:
          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$CompanyId'  and M.Date='$checkDate'  ";
           break;
   case 6:
          $Punc_FilterTerms=" M.Estate='0' and M.CompanyId='$CompanyId' AND YEAR(M.Date)=YEAR(CURDATE()) ";
           break;
}

if ($Punc_FilterTerms!=""){
     $Punc_FilterTerms.="  AND M.ShipType<>'credit' AND M.ShipType<>'debit' ";
	$NumsResult=mysql_query("SELECT SUM(A.Qty) AS Nums,SUM(IF(A.Leadtime<scDate,A.Qty,0)) AS OverNums 
			             FROM (
							SELECT YEARWEEK(substring(PI.Leadtime,1,10),1) AS Leadtime, YEARWEEK(MAX(C.Date),1) AS scDate,S.Qty   
									FROM $DataIn.ch1_shipmain M
									LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
								    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
								    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id 
								    LEFT JOIN $DataIn.sc1_cjtj C ON C.POrderId=Y.POrderId 
									WHERE  $Punc_FilterTerms  GROUP BY S.Id 
							)A ",$link_id);
			if($NumsRow = mysql_fetch_array($NumsResult)){
				  $P_Nums=$NumsRow["Nums"];
				  if ($P_Nums>0){
						  $P_OverNums=$NumsRow["OverNums"];
						  $Punc_Value=($P_Nums-$P_OverNums)/$P_Nums*100;
						  $Punc_Color=$Punc_Value<80?"#66B3FF":$Punc_Color;
						  $Punc_RIcon=$Punc_Value<80?"ipunctuality_r":"ipunctuality1_r";
						  $Punc_Value=round($Punc_Value);
						  $Punc_Percent=$Punc_Value>=0?"   " . $Punc_Value ."%":" "; 
				  }
			}
}
?>