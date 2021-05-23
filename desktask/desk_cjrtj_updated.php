<?php   
//已更新电信---yang 20120801
include "../model/modelhead.php";
if($Day<10){
	$Date=$Month."-0".$Day;
	}
else{
	$Date=$Month."-".$Day;
	}
//否则做新增
$checkRemarkSql=mysql_query("SELECT Id FROM $DataIn.sc1_remark WHERE GroupId='$GroupId' AND Date='$Date'",$link_id);
if($checkRemarkRow=mysql_fetch_array($checkRemarkSql)){
	$UpdateSql = "UPDATE $DataIn.sc1_remark SET Remark='$Remark',Operator='$Login_P_Number' WHERE GroupId='$GroupId' AND Date='$Date'";echo $UpdateSql;
	$UpdateResult = mysql_query($UpdateSql);
	}
else{
	$InsertSql="INSERT INTO $DataIn.sc1_remark (Id,GroupId,Remark,Date,Operator) VALUES (NULL,'$GroupId','$Remark','$Date','$Login_P_Number')";echo $InsertSql;
	$InsertRow=@mysql_query($InsertSql);	
	}
?>