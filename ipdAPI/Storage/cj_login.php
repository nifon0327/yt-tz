<?php
	
	include_once "../../basic/parameter.inc";
	
	$UserName = $_POST["username"];
	//$UserName = "joseph_mc";
	//$UserName = "Admin";
	$Password = $_POST["password"];
	//$Password = "Admin@12345";
	$Password = md5($Password);
	
	$permission = array();
	
	$mySql="SELECT U.uType,U.Id,U.uName,U.Number,U.Estate,M.Name,M.GroupId,M.JobId,G.GroupName,G.TypeId
			FROM $DataIn.UserTable U 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number
			LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
			WHERE 1 
			AND U.uName='$UserName' 
			AND U.uPwd='$Password' 
			AND U.uType=1 
			ORDER BY U.Id 
			LIMIT 1";
	
	$loginResult = mysql_query($mySql);
	if($loginRow = mysql_fetch_assoc($loginResult))
	{
		$number = $loginRow["Number"];
		$name = $loginRow["Name"];
		$jobId = $loginRow["JobId"];
		
		$permission[] = $number;
		$permission[] = $name;
		$permission[] = array();
		$permission[] = $jobId;
		
		$isStockLeader = ($number == "10214" || $number == "11008" || $number == "10341")?"yes":"no";
		$isAssembleLeader = ($number == "10200" || $number == "11008" || $number == "10341" || $number == '10782')?"yes":"no";
		
		$permission[] = $isStockLeader;
		$permission[] = $isAssembleLeader;
		
		$curDate=date("Y-m-d");
		$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7day"));
		$nextNextWeekDate = date("Y-m-d",strtotime("$curDate  +14day"));
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek, YEARWEEK('$curDate',1) AS ThisWeek, YEARWEEK('$nextNextWeekDate',1) AS nextNextWeek",$link_id));
		$permission[]=$dateResult["ThisWeek"]."";
		$permission[] = $dateResult["NextWeek"]."";
		$permission[] = $dateResult["nextNextWeek"]."";
		
		//检测权限
		$checkMenuResult=mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId
								   FROM $DataIn.sc4_upopedom P
								   LEFT JOIN $DataPublic.sc4_modulenexus M ON M.dModuleId=P.ModuleId 
								   LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=M.ModuleId
								   LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
								   WHERE U.Number='$number' 
								   AND F.Place='1' 
								   AND P.Action>0 
								   GROUP BY F.ModuleId 
								   ORDER BY F.OrderId DESC");
								   
		while($checkMenuRows = mysql_fetch_assoc($checkMenuResult))
		{
			$ModuleId = $checkMenuRows["ModuleId"];		//功能ID
			//$ModuleName = $checkMenuRows["ModuleName"];	//功能名称
			//$Parameter = $checkMenuRows["Parameter"];		//连接参数
			//$OrderId = $checkMenuRows["OrderId"];			//排序ID
			
			$subModuleResult = mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId,P.Action
											FROM $DataPublic.sc4_modulenexus M
											LEFT JOIN $DataIn.sc4_upopedom P ON M.dModuleId=P.ModuleId 
											LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=P.ModuleId 
											LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
											WHERE M.ModuleId='$ModuleId' 
											AND U.Number='$number' 
											AND P.Action>0 
											AND F.Estate=1 
											GROUP BY F.ModuleId 
											ORDER BY M.OrderId");
												
			while($subModuleRows = mysql_fetch_assoc($subModuleResult))
			{
				 //$SubModuleId = $subModuleRows["ModuleId"];		//功能ID
				 //$SubParameter = $subModuleRows["Parameter"];	//连接参数
				 //$SubOrderId = $subModuleRows["OrderId"];
				 $SubModuleName = $subModuleRows["ModuleName"];	//功能名称
				 $Action = $subModuleRows["Action"];
				 
				 $permission[2][$SubModuleName] = $Action; 
				 
			}
			
		}
		
	}
	else
	{
		$permission[] = "error";
		$permission[] = "登录失败,帐号或密码错误!";
	}
	
	echo json_encode($permission);
	
?>