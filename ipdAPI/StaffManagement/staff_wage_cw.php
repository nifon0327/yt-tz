<?php 

	//获取指定月份的薪资表，如果有生成确认数据，无则还没指定月份数据
	
	include "../../basic/parameter.inc";
	
	$Month = $_POST["month"];
	$Operator = $_POST["operator"];
	//$Month = "2012-04";
	$hasMonth = "no";
	$needReload = "no";
	$Date = date("Y-m-d");
	
	$mysql = "Select M.Name,M.Number,M.Estate From $DataIn.cwxzsheet C
					Left Join $DataPublic.staffmain M On M.Number = C.Number
					Where C.Month = '$Month' AND M.Estate =1 AND M.Number != '10001' Order By  M.Estate DESC,M.BranchId,M.Number ";
					
	$wageResult = mysql_query($mysql);
	
	if(mysql_num_rows($wageResult) == 0)
	{
		$info = "指定月份薪资还未生成";
	}
	else
	{
		$hasMonthList = "Select * From $DataIn.wage_list Where Month = '$Month'";
		$hasMonthLIstResult = mysql_query($hasMonthList);
		if(mysql_num_rows($hasMonthLIstResult) == 0)
		{
			$insertMonthList = mysql_query("Insert Into $DataIn.wage_list Values (Null,'$Month','','$Date','0','$Operator')");
			$needReload = "yes";
		}
	
		$hasMonth = "yes";
		$info = array();
		while($wageRow = mysql_fetch_assoc($wageResult))
		{
			$Name = $wageRow["Name"];
			$Number = $wageRow["Number"];
			$Estate = $wageRow["Estate"];
			$info[] = array("$Name","$Number","$Estate");			
			
		}
		
	}
	
	echo json_encode(array("$hasMonth",$info,"$needReload"));

?>