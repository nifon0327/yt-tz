<?php
	
	include_once "../../basic/parameter.inc";
	
	$Id = $_POST["Id"];
	$Remark = $_POST["remark"];
	$Login_P_Number = $_POST["operator"];
	$result = "N";
	
	$Date=date("Y-m-d");
    $checkSql=mysql_query("SELECT Id FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
	if($checkRow=mysql_fetch_array($checkSql))
	{//更新
    	$updateSQL = "UPDATE $DataIn.ck6_shremark SET Remark='$Remark',Date='$Date',Operator='$Login_P_Number' WHERE ShId='$Id'";
	    $updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0)
		{ 
			$result = "Y";
		}
    }
    else
    {
    	$addRecodes="INSERT INTO $DataIn.ck6_shremark (Id, ShId, Remark, Date, Operator) VALUES (NULL, '$Id', '$Remark', '$Date', '$Login_P_Number')";
	    $addAction=@mysql_query($addRecodes);
	    if($addAction)
	    {
		    $result = "Y";
	    }
    }
    
    echo $result;
	
?>