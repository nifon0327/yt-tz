<?php
	
	include_once "../../basic/parameter.inc";
	
	$CheckSign = $_POST["CheckSign"];
	//$CheckSign = "1";
	
	$GysCompany = array();
	$GysResult= mysql_query("SELECT M.CompanyId,P.Forshort, P.Letter,SUM(S.Qty) as Count 
							 FROM $DataIn.gys_shsheet S
							 LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
							 LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
							 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
							 WHERE 1 
							 AND S.Estate = 2
							 And D.CheckSign = '$CheckSign'
							 GROUP BY M.CompanyId 
							 ORDER BY P.Letter",$link_id);
	
	while($GysRows = mysql_fetch_assoc($GysResult))
	{
		$companyName = $GysRows["Letter"]."-".$GysRows["Forshort"];
		$companyId = $GysRows["CompanyId"];
		$companyCount = $GysRows["Count"];
		$GysCompany[] = array($companyName, $companyId, $companyCount);
	}
	
	echo json_encode($GysCompany);
	
?>