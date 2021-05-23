<?php
	
	include_once "../../basic/parameter.inc";
	
	$backCompany = array();
	$backCompanySql = "Select B.CompanyId, B.Forshort, B.Letter 
        		FROM $DataIn.gys_shback A 
        		Left Join $DataIn.trade_object B On B.CompanyId = A.CompanyId
        		Where B.Estate = '1'
        		Group by B.CompanyId
        		Order by B.Forshort";

    $backCompanyResult = mysql_query($backCompanySql);
    while($backCompanyRow = mysql_fetch_assoc($backCompanyResult))
    {
	    $companyId = $backCompanyRow["CompanyId"];
	    $companyShort = $backCompanyRow["Forshort"];
	    $companyLetter = $backCompanyRow["Letter"];
	    $backCompany[] = array($companyLetter."-".$companyShort, "$companyId");
    }
    
    echo json_encode($backCompany);
    
?>