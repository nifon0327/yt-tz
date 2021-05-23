<?php 

	include "../../basic/parameter.inc";
	
	$Id = $_POST["Id"];
	$Number = $_POST["number"];
	$Type = $_POST["type"];
	$Date=date("Y-m-d");
	
	$tResult=mysql_query("SELECT Id FROM $DataPublic.rs_sbtype Where Name = '$Type'",$link_id);
	$tRow = mysql_fetch_assoc($tResult);
	$Type = $tRow["Id"];
	
	$Note = $_POST["note"];
	$sMonth = $_POST["smonth"];
	$eMonth = $_POST["emonth"];
	$Operator = $_POST["operator"];
	
	//$succeeArray = array();
	$err = "no";
	$payResult =mysql_query("
			SELECT MIN(minM) AS minTemp,MAX(minT) AS maxTemp FROM(
			SELECT MIN(Month) AS minM,MAX(Month) AS minT FROM $DataIn.sbpaysheet WHERE 1 AND Number=$Number
			",$link_id);
			
		$minMonth=mysql_result($payResult,0,"minTemp");
		$maxMonth=mysql_result($payResult,0,"maxTemp");
		//检查起始月份
		if($minMonth!="" && $sMonth>$minMonth){
			$Log="起始月份不允许大于初始缴费的月份($minMonth)!";
			$err = "yes";
			$OperationResult="N";
			}
		else{
			$sMonthSTR=",sMonth='$sMonth'";
			$eMonthSTR=",eMonth='',Estate='1'";
			//检查结束月份
			if($eMonth!=""){
				if($maxMonth!="" && $eMonth<$maxMonth){
					$Log="结束月份不允许少于最后缴费的月份($maxMonth)!";
					$err = "yes";
					$OperationResult="N";
					}
				else{
					$eMonthSTR=",eMonth='$eMonth',Estate='0'";
					}
				}			
			}
		if($Log==""){
			$Sql = "UPDATE $DataPublic.sbdata SET Type='$Type',Locks='0',Note='$Note',Date='$Date',Operator='$Operator' $eMonthSTR  $sMonthSTR WHERE Id='$Id' and Number= '$Number' LIMIT 1";
			
			$Result = mysql_query($Sql);
			
			if ($Result){
				$Log.="更新成功!";
				}
			else{
				$Log.="更新失败!";
				$err = "yes";
				$OperationResult="N";
				}
			}
		
		$succeeArray = array("$err",$Log);
		
		echo json_encode($succeeArray);
		
?>