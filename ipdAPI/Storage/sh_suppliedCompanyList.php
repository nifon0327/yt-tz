<?php
	
	include_once "../../basic/parameter.inc";
	
	$floor = $_GET["floor"];	
	
	if($floor == "3")
	{
		$searchRow = "And (M.Floor = '$floor' or M.Floor = 0)";
	}
	else
	{
		$searchRow = "And M.Floor = '$floor'";
	}
	
	$GysResult= mysql_query("SELECT M.CompanyId,P.Forshort, P.Letter,SUM(S.Qty) as Count 
							 FROM $DataIn.gys_shsheet S
							 LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
							 LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
							 WHERE 1 
							 $searchRow
							 AND S.Estate=1
							 GROUP BY M.CompanyId 
							 ORDER BY P.Letter",$link_id);
	$gysCompany = array();
	while($GysRows = mysql_fetch_assoc($GysResult))
	{
		$CompanyName = $GysRows["Letter"]."-".$GysRows["Forshort"];
		$CompnayId = $GysRows["CompanyId"];
		$gysCompany[] = array($CompanyName, $CompnayId);
	}
	
	//print_r($gysCompany);
	echo json_encode($gysCompany);
	
?>