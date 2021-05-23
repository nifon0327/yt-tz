<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";

	$ClientResult= mysql_query("SELECT W.CompanyId,C.Forshort 
					 FROM $DataIn.yw9_blmain M 
					 LEFT JOIN $DataIn.ck5_llsheet S ON S.Pid=M.Id 
					 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
					 LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId  
					 LEFT JOIN $DataIn.yw1_ordermain W  ON W.OrderNumber=Y.OrderNumber 
					 LEFT JOIN $DataIn.trade_object C ON W.CompanyId=C.CompanyId
					 WHERE 1
					 And S.Estate = 1  
					 GROUP BY W.CompanyId order by W.CompanyId",$link_id);
			 					 
	$clientHolder = array();
	while($ClientRow = mysql_fetch_assoc($ClientResult))
	{
		$theCompanyId=$ClientRow["CompanyId"];
		$theForshort=$ClientRow["Forshort"];
		if(!$theCompanyId)
		{
			continue;
		}
		
		$TypeResult= mysql_query("SELECT P.TypeId,T.TypeName 
                   				  FROM $DataIn.yw9_blmain M 
                   				  LEFT JOIN $DataIn.ck5_llsheet S ON S.Pid=M.Id 
                   				  LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
                   				  LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId    
                   				  LEFT JOIN $DataIn.yw1_ordermain W  ON W.OrderNumber=Y.OrderNumber 
                   				  LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId 
                   				  LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
                   				  WHERE 1 
                   				  and S.Estate = 1  
                   				  and W.CompanyId='$theCompanyId'
                   				  GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId",$link_id);
        $typeInClient = array();
        while($typeRow = mysql_fetch_assoc($TypeResult))
        {
        	$typeId = $typeRow["TypeId"];
        	$typeName = $typeRow["TypeName"];
	        $typeInClient[] = array("$typeName" , "$typeId");
        }
        
        $clientHolder[] = array("$theCompanyId", "$theForshort", $typeInClient);
		
	}
	
	echo json_encode($clientHolder);
	
?>