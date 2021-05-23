<?php 
//$DataPublic.staffmain
//二合一已更新电信---yang 20120801
$SYS_ReadSign=1;
if (is_array($SYS_OperatorArray)){
	if ($SYS_OperatorArray["$Operator"]!=""){
		$Operator=$SYS_OperatorArray["$Operator"];
		$SYS_ReadSign=0;
	}
}

if ($SYS_ReadSign==1 && $Operator>0){
    $_tempOperator=$Operator;
	$pResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Operator'  LIMIT 1",$link_id);
	if($pRow = mysql_fetch_array($pResult)){
		   $Operator=$pRow["Name"];
	}
	else
	{
	   //外部人员资料
	   $otResult = mysql_query("SELECT Name FROM $DataIn.ot_staff WHERE Number='$Operator' LIMIT 1",$link_id);
	   if($otRow = mysql_fetch_array($otResult)){
		     $Operator=$otRow["Name"];
	     } 
    }
    $SYS_OperatorArray["$_tempOperator"]=$Operator;
}
?>