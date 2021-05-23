<?php
	
	include_once "../../basic/parameter.inc";
	
	$suppliedList = array();
	$GysResult= mysql_query("SELECT M.CompanyId,P.Forshort 
							 FROM $DataIn.gys_shsheet S
							 LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
							 LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
							 WHERE 1 
							 And S.Estate=1 
							 GROUP BY M.CompanyId 
							 ORDER BY M.CompanyId",$link_id);

	while($GysRows = mysql_fetch_assoc($GysResult))
	{
		$tmpSignSupplied = array();
		$theGysId = $GysRows["CompanyId"];
		$tmpSignSupplied[] = $GysRows["Forshort"]."|".$theGysId;
		$checkNumSql = mysql_query("SELECT M.BillNumber,M.Date FROM $DataIn.gys_shmain M 
									LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
									WHERE 1 
									And M.CompanyId = '$theGysId' 
									AND S.Estate = 1
									GROUP BY S.Mid 
									ORDER BY M.BillNumber 
									DESC",$link_id);
		$tmpLists = array();	
		$number = 1;						
		while($checkNumRows = mysql_fetch_row($checkNumSql))
		{
			$BillNumber = $checkNumRows[0];
			$Date = $checkNumRows[1];
			$tmpLists[] = array("$number", "$BillNumber", "$Date");
			$number++;
		}
		
		$tmpSignSupplied[] = $tmpLists;							
		$suppliedList[] = $tmpSignSupplied;
	}
	
	echo json_encode($suppliedList);
	
?>