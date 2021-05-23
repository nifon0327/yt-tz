<?php

	include "../../basic/parameter.inc";

	$checkMenuSql=mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId
			FROM $DataIn.sc4_upopedom P
			LEFT JOIN $DataPublic.sc4_modulenexus M ON M.dModuleId=P.ModuleId 
			LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=M.ModuleId
			LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
			WHERE U.Number='$Number' AND F.Place='1' AND P.Action>0 GROUP BY F.ModuleId ORDER BY F.OrderId DESC
			",$link_id);

	$Item_info = array();
	$warehouse = array();
	while($checkMenuRow=mysql_fetch_array($checkMenuSql))
	{
		$ModuleId=$checkMenuRow["ModuleId"];		//功能ID
		$ModuleName=$checkMenuRow["ModuleName"];	//功能名称
		//$Parameter=$checkMenuRow["Parameter"];		//连接参数
		$OrderId=$checkMenuRow["OrderId"];			//排序ID

		$checkSubSql=mysql_query("SELECT F.ModuleId,F.ModuleName,F.Parameter,F.OrderId,P.Action
					FROM $DataPublic.sc4_modulenexus M
					LEFT JOIN $DataIn.sc4_upopedom P ON M.dModuleId=P.ModuleId 
					LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=P.ModuleId 
					LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
					WHERE M.ModuleId='$ModuleId' AND U.Number='$Number' AND P.Action>0 AND F.Estate=1 GROUP BY F.ModuleId ORDER BY M.OrderId",$link_id);

		while($checkSubRow=mysql_fetch_array($checkSubSql))
		{
			$SubModuleId=$checkSubRow["ModuleId"];		//功能ID
			$SubModuleName=$checkSubRow["ModuleName"];	//功能名称
			$SubParameter=$checkSubRow["Parameter"];	//连接参数
			$SubOrderId=$checkSubRow["OrderId"];		//排序ID

			if($SubParameter == "item6_1.php" || $SubParameter == "item6_2.php")
			{
				$warehouse[] = $SubModuleName."|".$SubParameter;
			}
			else
			{
				$Item_info[$ModuleName][] = $SubModuleName."|".$SubParameter;
			}
		}

	}

	$Item_info["出货"] = $warehouse;

?>