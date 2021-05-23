<?php
	
	include_once "../../basic/parameter.inc";
	
	$state = $_GET["state"];
	
	$gys = array();

	$gysResultSql = "SELECT S.CompanyId,P.Forshort,P.Letter 
							FROM $DataIn.cg1_stocksheet S 
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 			
							LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2		
							WHERE  1  AND ( S.Mid>0   OR   (S.Mid=0  AND  OP.Property=2 AND (S.FactualQty >0  OR S.AddQty >0))) Group by P.CompanyId Order by P.Letter";
	//AND S.rkSign>0
	//echo $gysResultSql;
	$GysResult= mysql_query($gysResultSql,$link_id);
		
	while($gysRows = mysql_fetch_assoc($GysResult))
	{
		$company = $gysRows["Letter"]."-".$gysRows["Forshort"];
		$companyId = $gysRows["CompanyId"];
		$gys[] = array($company, $companyId);
	}
	
	echo json_encode($gys);
	
?>