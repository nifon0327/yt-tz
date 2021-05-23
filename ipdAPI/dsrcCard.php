<?php
	
	include_once "../basic/parameter.inc";
	$dsrcCardSql = "Select CardNumber From $DataIn.dsrc_list";
	$dsrcCardResult = mysql_query($dsrcCardSql);
	while($dsrcRows = mysql_fetch_assoc($dsrcCardResult))
	{
		$cardNumber = $dsrcRows["CardNumber"];
		if(strlen($cardNumber) == 16)
		{
			echo $cardNumber."<br>";
		}
	}

	
?>