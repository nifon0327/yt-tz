<?php   
//电信---yang 20120801
$checkSMS= mysql_query("SELECT Number,Rsign FROM $DataIn.smsuse WHERE ItemId=$smsfunId",$link_id);
if($checkRow = mysql_fetch_array($checkSMS)){
	do{
		$toNumber=$checkRow["Number"];
		$Rsign=$checkRow["Rsign"];
		$smsInSql = "INSERT INTO $DataIn.smsdata (Id,Number,Date,Note,Estate,Rsign,Operator) VALUES (NULL,'$toNumber','$DateTime','$smsNote','1','$Rsign','0')";
		$smsInResult = @mysql_query($smsInSql);
		}while($checkRow = mysql_fetch_array($checkSMS));
	}
?>