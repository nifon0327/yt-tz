<?php

	include_once "../../basic/parameter.inc";

	$listLocation = $_POST["type"];
	//$listLocation = 0;
	$listDate = $_POST["date"];
	//$listDate = "2012-09";
	$operator = $_POST["operator"];
	$operator = "10082";
	$date = date("Y-m-d");
	$payDate = substr($date, 0, 7);

	$valueInserts = "";

	if($listLocation == "0" || $listLocation == "1")
	{
		$mcWageSql = "SELECT M.Name, M.Number FROM $DataIn.cwxzsheet S 
					  LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
					  WHERE S.Month='$listDate' 
					  and (S.Estate = '0' or S.Estate = '3')
					  and M.Estate = '1'
					  and M.Number != '10001'";

	    $mcWageResult = mysql_query($mcWageSql);
	    if(mysql_num_rows($mcWageResult) > 0)
	    {
		    $valueInserts = "Insert Into $DataPublic.wage_list (Id, Month, FileName, cSign, Date, Estate, Operator) Values (NULL,'$listDate',NULL,'7','$date','0','$operator')";
		    $mcFailed = "no";
		    if(!mysql_query($valueInserts))
		    {
			    $mcFailed = "研砼".$listDate."月薪资已经生成!\n";
		    }
	    }
	    else
	    {
		    $mcFailed = "研砼".$listDate."月薪资还未生成!\n";
	    }
	}

	if($listLocation == "0" || $listLocation == "2")
	{
		$ptWageSql = "SELECT M.Name, M.Number FROM $DataOut.cwxzsheet S 
					  LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
					  WHERE S.Month='$listDate' 
					  and (S.Estate = '0' or S.Estate = '3')
					  and M.Estate = '1'
					  and M.Number != '10001'";

		$ptWageResult = mysql_query($ptWageSql);
		if(mysql_num_rows($ptWageResult) > 0)
		{
			$ptValueInsert = "Insert Into $DataPublic.wage_list (Id, Month, FileName, cSign, Date, Estate, Operator) Values (NULL,'$listDate',NULL,'3','$date','0','$operator')";
			$ptFailed = "no";

			if(!mysql_query($ptValueInsert))
		    {
			    $ptFailed = "皮套".$listDate."月薪资已经生成!\n";
		    }
		}
		else
		{
			$ptFailed = "皮套".$listDate."月薪资单还未生成!\n";
		}
	}

	$mcResult = ($mcFailed == "no")?"研砼".$listDate."月薪资单生成成功!\n":$mcFailed;
	$ptResult = ($ptFailed == "no")?"皮套".$listDate."月薪资单生成成功!\n":$ptFailed;
	$wageResult = $mcResult.$ptResult;

	//echo json_encode(array($wageResult));
	echo $wageResult;

?>