<?php 
//电信---yang 20120801
$authorityResult = mysql_query("SELECT CompanyId FROM $DataIn.yw6_salesview 
    WHERE TypeId='2' and Estate='1' AND SalesId='$Login_P_Number'",$link_id);  
if($authorityRows = mysql_fetch_array($authorityResult)){

   do{
      $NewCompanyId=$authorityRows["CompanyId"];
      $ViewCompanyId=$ViewCompanyId==""?$NewCompanyId:($ViewCompanyId.",".$NewCompanyId);
     }while($authorityRows = mysql_fetch_array($authorityResult));
  }
if($ViewCompanyId!=""){$ClientStr=" AND M.CompanyId IN ($ViewCompanyId)";}
?>