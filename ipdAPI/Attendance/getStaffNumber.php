<?php
	
	function getStaffNumber($cardNumber, $DataPublic)
	{
		$catchNumber = $cardNumber;
		if(strlen($catchNumber) > 8){
			$catchNumber = substr($cardNumber, 10, 8);
		}
		$catchNumberSql = "Select Number From $DataPublic.staffmain Where IdNum = '$catchNumber'";
		$catchNumberResult = mysql_query($catchNumberSql);
		$catchNumberRow = mysql_fetch_assoc($catchNumberResult);
		
		return $catchNumberRow["Number"];
		
	}
	
?>