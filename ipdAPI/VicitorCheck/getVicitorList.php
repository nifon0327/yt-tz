<?php
	
	include_once "../../basic/parameter.inc";
	
	$typeId = $_POST["typeId"];
	//$typeId = "1";
	
	$today = date("Y-m-d");
	
	$list = array();
	$getTodyVicitorListSql = "Select * From $DataPublic.come_data 
							 Where 
							 TypeId = '$typeId' 
							 and (Estate in (1, 2) or (Estate = 0 and ComeDate = '$today'))
							 and Id not in (Select Id From $DataPublic.come_data 
							 Where 
							 TypeId = '$typeId' 
							 and InTime is NULL and Estate = '0')
							 Order By Estate Desc";
	//echo $getTodyVicitorListSql;				 
	$todayVicitorListResult = mysql_query($getTodyVicitorListSql);
	$badgeVaule = mysql_num_rows($todayVicitorListResult);
	
	while($todayVicitorRow = mysql_fetch_assoc($todayVicitorListResult))
	{
		$Id = $todayVicitorRow["Id"];
		$name = $todayVicitorRow["Name"];
		$person = $todayVicitorRow["Person"];
		$remark = $todayVicitorRow["Remark"];
		$inTime = $todayVicitorRow["InTime"];
		$inTime = substr($inTime, 11, 5);
		$inOperator = $todayVicitorRow["InOperator"];
		$outTime = $todayVicitorRow["OutTime"];
		$outTime = substr($outTime, 11, 5);
		$outOperator = $todayVicitorRow["OutOperator"];
		$companyId = $todayVicitorRow["CompanyId"];
		$cSign = $todayVicitorRow["cSign"];
		$mId = $todayVicitorRow["Mid"];
		
		if($typeId == 1)
		{
			$dataSource = ($cSign == 7)?$DataIn:$DataOut;
			$companyNameResult = mysql_query("Select Forshort From $dataSource.providerdata Where CompanyId = '$companyId'");
			$companyNameRow = mysql_fetch_assoc($companyNameResult);
			$companyName = $companyNameRow["Forshort"];
			
			if($mId != "")
			{
				$remark = $companyName."送货";
			}	
		}
		else
		{
			$operator = $todayVicitorRow["Operator"];
			$operatorNameResult = mysql_query("Select Name From $DataPublic.staffmain Where Number = '$operator'");
			$operatorRow = mysql_fetch_assoc($operatorNameResult);
			$operatorName = $operatorRow["Name"];
			
			$typeName = ($typeId == 2)?"访客":($typeId == 3)?"包裹":"";
			$remark = $operatorName.$typeName.$remark;
			
		}
		
		$estate = $todayVicitorRow["Estate"];
		$logDate = $todayVicitorRow["ComeDate"];
		$logDate = substr($logDate, 5,5);
		if($inTime == "")
		{
			$stage = "1";
		}
		else if($inTime != "" && $outTime == "")
		{
			$stage = "2";
		}
		else if($inTime != "" && $outTime != "")
		{
			$stage = "0";
			$badgeVaule--;
		}
		
		$list[] = array("Id"=>"$Id", "Name"=>"$name", "person"=>"$person", "remark"=>"$remark", "inTime"=>"$inTime", "inOperator"=>"$inOperator", "outTime"=>"$outTime", "outOperaotr"=>"$outOperator", "companyId"=>"$companyId", "companyName"=>"$companyName", "Estate"=>"$estate", "LogDate"=>"$logDate", "Stage"=>"$stage");
		
	}
	
	echo json_encode(array("$badgeVaule",$list));
	
?>