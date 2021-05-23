<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$rkHolder = array();
	$getAllCompanySql = "SELECT DISTINCT DATE_FORMAT(DATE,'%Y-%m') as rkTime 
						 FROM $DataIn.ck1_rkmain 
						 Where DATE_FORMAT(DATE,'%Y-%m') <> '0000-00'
						 Order By DATE Desc";
				 
	$allCompanyResult = mysql_query($getAllCompanySql);
	while($allCompanyRow = mysql_fetch_assoc($allCompanyResult))
	{
		$rkDate = $allCompanyRow["rkTime"];
		$StartDate=$rkDate."-01";
		$EndDate=date("Y-m-t",strtotime($rkDate));
		
		$companies = array();
		$getCompanyMonthSql = "SELECT M.CompanyId,P.Forshort,P.Letter 
							   FROM $DataIn.ck1_rkmain M,$DataIn.trade_object P 
							   WHERE M.CompanyId=P.CompanyId 
							   And ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate') 
							   GROUP BY M.CompanyId ORDER BY P.Letter";
		$companyMonthResult = mysql_query($getCompanyMonthSql);
		if(mysql_num_rows($companyMonthResult) == 0)
		{
			continue;
		}
		while($companyMontRow = mysql_fetch_assoc($companyMonthResult))
		{
			$companyId = $companyMontRow["CompanyId"];
			$companyName = $companyMontRow["Letter"]."-".$companyMontRow["Forshort"];
			
			$companies[] = array("companyId"=>"$companyId", "companyName"=>"$companyName");
		}
		
		$rkHolder[] = array("$rkDate", $companies);
	}
	
	echo json_encode($rkHolder);
	
?>