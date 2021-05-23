<?php 
//$DataPublic.staffmain
//二合一已更新电信---yang 20120801
	$pResult = mysql_query("SELECT Name,Estate FROM $DataPublic.staffmain WHERE Number='$Operator'  LIMIT 1",$link_id);
	if($pRow = mysql_fetch_array($pResult)){
		   //$Operator=$pRow["Estate"]==0?"<span class='redB'>" . $pRow["Name"] .  "</span>":$pRow["Name"];
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
?>