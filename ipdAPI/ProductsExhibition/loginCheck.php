<?php
	
	include_once "../../basic/parameter.inc";
	$UserName = $_POST["username"];
	$UserName = "tw0219";
	$Password = $_POST["password"];
	$Password = "tw0219";
	$Password = md5($Password);
	
	$mySql="SELECT U.uType,U.Id,U.uName,U.Number,U.Estate,M.Name,M.GroupId,G.GroupName,G.TypeId
			FROM $DataIn.UserTable U 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number
			LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
			WHERE 1 
			AND U.uName='$UserName' 
			AND U.uPwd='$Password' 
			AND U.uType=1 
			ORDER BY U.Id 
			LIMIT 1";
	
	$loginQueue = array();
	$loginTag = "N";
	$loginResult = mysql_query($mySql);
	if($loginRow = mysql_fetch_assoc($loginResult))
	{
		$loginTag = "Y";
		$number = $loginRow["Number"];
		$name = $loginRow["Name"];
		
		$subModuleResult = mysql_query("SELECT P.Action
											From $DataIn.upopedom P 
											LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=P.ModuleId 
											LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
											WHERE P.ModuleId='1213' 
											AND U.Number='$number' 
											AND P.Action>0 
											AND F.Estate=1 
											GROUP BY F.ModuleId Limit 1");
		$loginInfomation = array("number"=>"$number", "action"=>"$action");
	
		$subModuleRow = mysql_fetch_assoc($subModuleResult);
		$action = $subModuleRow["Action"];
		
		$productTypeQueue = array();
		$productTypeReader = "SELECT P.TypeId,T.TypeName,T.Letter 
						  	  FROM $DataIn.productdata P
						  	  LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
						  	  LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
						  	  WHERE T.Estate=1
						  	  GROUP BY P.TypeId";
						  	  
		$productTypeResult = mysql_query($productTypeReader);
		while($productTypeRow = mysql_fetch_assoc($productTypeResult))
		{
			$productType = $productTypeRow["Letter"]."-".$productTypeRow["TypeName"];
			$productTypeId = $productTypeRow["TypeId"];
			$productTypeQueue[] = array("$productType", "$productTypeId");
		}
											
	}
	
	echo json_encode(array($loginTag, $loginInfomation, $productTypeQueue));
	
?>