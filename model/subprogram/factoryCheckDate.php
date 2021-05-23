<?php
	
	function newDateTime($Date, $link)
	{	
		//echo $Date;
		$path = $_SERVER["DOCUMENT_ROOT"];
		include_once "$path/basic/parameter.inc";
		$newDate = $Date;
		$weekDay=date("w",strtotime($Date));	
		if($weekDay == "6" || $weekDay == "0")
		{
			$overTimehourSql = mysql_query("Select * From $DataIn.kqovertime Where otDate = '$Date'");
			$overTimehourResult = mysql_fetch_assoc($overTimehourSql);
			if($overTimehourResult["weekday"] == 0)
			{
				if($weekDay == "6")
				{
					$newDate = date("Y-m-d",strtotime("$Date - 1 days"));
				}
				else if($weekDay == "0")
				{
					$newDate = date("Y-m-d",strtotime("$Date + 1 days"));
				}
			}
		}
			
		return $newDate;
	}
	
	function skipStaff($Number)
	{
		//$target = array("10744", "11923", "11924", "11976", "11977", "11978", "11979", "11983", "11903");
		$target = array();
		return in_array($Number, $target);
		
	}
	
?>