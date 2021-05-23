<?php 
//读取公司工作地点
	include "../../basic/parameter.inc";
                $mySql="SELECT A.Id,A.Name  FROM $DataPublic.staffworkadd A WHERE A.Estate=1 ORDER BY A.Id";
	$jsonArray = array();
	$myResult = mysql_query($mySql);
	if($myRow = mysql_fetch_assoc($myResult))
	{
		do 
		{	
		    $Id=$myRow["Id"];
		    $Name = $myRow["Name"];		
			
		$jsonArray[] = array( "$Id","$Name");
		}
		while($myRow = mysql_fetch_assoc($myResult));
                echo json_encode($jsonArray);
	}
?>