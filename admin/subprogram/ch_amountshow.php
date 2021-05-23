<?php   
$AmountSQL=" (M.CompanyId=1004 OR M.CompanyId=1059 OR M.CompanyId=1064 OR M.CompanyId='1071')  AND (M.Bankid=7 OR M.Bankid=5) ";	

if($CompanyId==1004 || $CompanyId==1059 || $CompanyId==1064 || $CompanyId==1071)   //只对Cel设置，而且是国内对公账号
{
	//CELL 国内对公账号出货总金额
	$YearMonth=date("Y-m");  //把当前月的统计出来
	$checkAmount=mysql_fetch_array(mysql_query("SELECT  IFNULL( SUM( S.Qty * S.Price ) , 0 ) AS Amount 
		FROM $DataIn.ch1_shipmain M 
		LEFT JOIN $DataIn.ch1_shipsheet S ON S.MId=M.ID  
		LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
		WHERE $AmountSQL  AND T.Type=1 AND  DATE_FORMAT(M.Date,'%Y-%m')='$YearMonth'",$link_id));
	
	$LastAmount=sprintf("%.2f",$checkAmount["Amount"]);  //已出货的金额
	
	$checkusdFl=mysql_fetch_array(mysql_query("SELECT  IFNULL(Rate, 1) AS usdRate 
		FROM $DataPublic.currencydata  WHERE Symbol='USD' ",$link_id));
	$usdRate=$checkusdFl["usdRate"];  //当前美元费率
	$LastAmount=sprintf("%.2f",$usdRate*$LastAmount); 

	$checkAmount=mysql_fetch_array(mysql_query("SELECT  IFNULL( sum( Amount ) , 0 ) AS Amount
		FROM $DataIn.ch11_shipamount M 
		WHERE M.Month='$YearMonth'",$link_id));
	$MaxAmount=$checkAmount["Amount"];  //当月最高金额
	if($MaxAmount>0)
	{
		$SubAmout=$MaxAmount-$LastAmount;
		if ($SubAmout<10000) //变色
		{
			$SubNumber="<div class='redB'>$SubAmout</div>";
		}
		else
		{
			$SubNumber=$SubAmout;
		}
		$MaxStr="本月最高金额：$MaxAmount: 剩余金额: $SubNumber";
	}
}
?>