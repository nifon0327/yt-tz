<?php 
	
	include "../../basic/parameter.inc";
	
	$function = $_POST["funcion"];
	$number = $_POST["number"];
	$typeName = $_POST["type"];
	$outDate = $_POST["outDate"];
	$reason = $_POST["reason"];
	$operator = $_POST["operator"]; 
	$id = $_POST["id"];
	$Date=date("Y-m-d");
	
	$typeCheck = mysql_query("Select Id From $DataPublic.dimissiontype Where Name = '$typeName'");
	$typeReslut = mysql_fetch_assoc($typeCheck);
	$type = $typeReslut["Id"];
	
	$resultFlag = "Y";
	
	if($function == "insert")
	{
		//1.首先将staffmain里员工状态改变
		$staffStateUpdate = "Update $DataPublic.staffmain Set Estate=0,Locks=0 Where Number = '$number'";
		if(mysql_query($staffStateUpdate))
		{
			//2.将信息写入dimisson表
        if($DataIn=="ac"){
		         $inRrecode="INSERT INTO $DataPublic.dimissiondata SELECT NULL,Number,'$outDate','1','','$type','0','$Date','$operator','1','0',null,null, null,null
                                             FROM $DataPublic.staffmain WHERE Number = $number";
            }else{
		         $inRrecode="INSERT INTO $DataPublic.dimissiondata SELECT NULL,Number,'$outDate','1','','$type','0','$Date','$operator' 
                                           FROM $DataPublic.staffmain WHERE Number = $number";
            }
			if(!mysql_query($inRrecode))
			{
				$resultFlag = "N";
			}
		}
		else
		{
			$resultFlag = "N";
		}
	}
	else if($function == "update")
	{
		$staffOutUpdate = "Update $DataPublic.dimissiondata Type='$type',outDate='$outDate',Reason='$reason',Date='$Date',Operator='$Operator',Locks='0' Where Id='$id'";
		if(!mysql_query($staffOutUpdate))
		{
			$resultFlag = "N";
		}
	}
	
	echo json_encode(array($resultFlag));
	
?>