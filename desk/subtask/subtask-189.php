<?php   
//ewen-2012-12-29
$myResult=mysql_query("SELECT IFNULL(SUM(A.Amount),0) AS Amount,A.Currency FROM $DataIn.cw_advanced A WHERE A.Estate=1 GROUP BY A.Currency",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$ResultSTR="AdvancedAmount".strval($myRow["Currency"]); 
		$$ResultSTR=sprintf("%.2f",$myRow["Amount"]);
		}while ($myRow = mysql_fetch_array($myResult));
	}
$AdvancedAmount=$AdvancedAmount1+$AdvancedAmount2*$USDRate+$AdvancedAmount3*$HKDRate;
$contentSTR="<li class=TitleBL>预结付金额</li><li class=TitleBR><a href='$Extra' target='_blank'>¥".number_format($AdvancedAmount)."</a></li>
<li class=DataA>$".number_format($AdvancedAmount2)."</li> 
<li class=DataA>HKD".number_format($AdvancedAmount3)."</li> 
<li class=DataA>¥".number_format($AdvancedAmount1)."</li>";?> 