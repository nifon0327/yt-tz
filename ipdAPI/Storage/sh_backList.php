<?php
	
	include_once "../../basic/parameter.inc";
	
	$companyId = $_POST["companyId"];
	$floor = $_POST["floor"];
	
	if($startPosition == "")
	{
		$startPosition = "0";
	}
	
	$backList = array();
	
	$backListSql = "Select A.Id, A.StockId, A.SendDate, A.StuffId, A.Qty, A.remark, A.BillNumber, B.StuffCname
				    From $DataIn.gys_shback A
				    Left Join $DataIn.stuffdata B On A.StuffId = B.StuffId
				    Where CompanyId = $companyId
				    And B.SendFloor = '$floor'
				    Order By A.Id Desc";
				    
	$backListResult = mysql_query($backListSql);
	while($backRows = mysql_fetch_assoc($backListResult))
	{
		$stockId = $backRows["StockId"];
		$stuffId = $backRows["StuffId"];
		$cName = $backRows["StuffCname"];
		$qty = $backRows["Qty"];
		$remark = $backRows["remark"];
		
		$id = $backRows["Id"];
		$sendDate = $backRows["SendDate"];
		$billNumber = $backRows["BillNumber"];
		
		$backList[] = array("$stockId", "$stuffId", "$cName", "$qty", "$remark", "$id", "$sendDate", "$billNumber");
	}
	
	echo json_encode($backList);
	
?>