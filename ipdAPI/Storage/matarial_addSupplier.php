<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$suppliers = array();
	$GYS_Sql = "SELECT S.CompanyId,P.Forshort,P.Letter 
				FROM $DataIn.cg1_stocksheet S 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
				LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2		
				WHERE  1 
				AND S.rkSign>0 
				AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) 
				Group by P.CompanyId";
				
	$gysResult = mysql_query($GYS_Sql);
	while($gysRow = mysql_fetch_assoc($gysResult))
	{
		$companyId = $gysRow["CompanyId"];
		$companyName = $gysRow["Letter"]."-".$gysRow["Forshort"];
		$suppliers[] = array($companyId, $companyName);
	}
	
	echo json_encode($suppliers);
?>