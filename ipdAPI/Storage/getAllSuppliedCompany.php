<?php
	
	include_once "../../basic/parameter.inc";
	
	$forSupplied = $_GET["forSupplied"];
	
	if($forSupplied != "yes")
	{
		$type = $_POST["type"];
		//$type = "1"; //1为全检  0为抽检 , 抽检不分退换和未退换
	
		$backType = $_POST["back"];
		//$backType = "0"; //1为未退换 0为已退换 
	
		$searchRow = "";
		if($type == "1")
		{
			$searchRow .= " And S.AQL = ''";
			if($backType == "0")
			{
				$searchRow .= " And S.Estate = 0";
			}
			else if($backType == "1")
			{
				$searchRow .= " And S.Estate = 1";
			}
		}
		else if($type == "0")
		{
			$searchRow .= " And S.AQL <> ''";
		}
	
		$gys = array();
		$gys_Sql = "SELECT M.CompanyId,P.Forshort, P.Letter 
        			FROM $DataIn.qc_badrecord S 
        			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        			LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid
        			LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
        			LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
        			WHERE 1
        			$searchRow 
        			AND K.tStockQty!=0
        			AND M.CompanyId>0 
        			GROUP BY M.CompanyId 
        			ORDER BY M.CompanyId";
    	
        			$gysResult = mysql_query($gys_Sql);
        			while($gysRow = mysql_fetch_assoc($gysResult))
        			{
	        			$gysLetter = $gysRow["Letter"];
	        			$gysShort = $gysRow["Forshort"];
	        			$gysId = $gysRow["CompanyId"];
		
	        			if(($type == "1" && $backType == "0") || $type == "0")
	        			{
		        			//获取以退换的日期
		        			$searchBackDate = "SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Date 
							   				   FROM $DataIn.qc_badrecord S 
							   				   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
							   				   LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid
							   				   LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
							   				   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
							   				   WHERE 1 
							   				   $searchRow
							   				   AND M.CompanyId='$gysId'
							   				   GROUP BY DATE_FORMAT(S.Date,'%Y-%m') 
							   				   ORDER BY DATE_FORMAT(S.Date,'%Y-%m') DESC";
			
						   $backDate = array();
						   $searchBackDateResult = mysql_query($searchBackDate);
						   while($serachBackRows = mysql_fetch_assoc($searchBackDateResult))
						   {
							   $backDate[] = $serachBackRows["Date"];
						   }
			
					}
		
					if(($type == "1" && $backType == "0") || $type == "0")
					{
						$gys[] = array($gysShort, $gysId, $backDate);
					}
					else
					{
						$gys[] = array($gysShort, $gysId);
					}
		
		}
        echo json_encode($gys);
	}
?>