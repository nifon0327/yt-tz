<?php
if (!isset($ipadTag)) $ipadTag="no";
if($ipadTag != "yes"){
    include "../basic/chksession.php";
}
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";

$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");

//echo $sgIds;

//sgIds="+sgIds+"&sgCompanyId="+sgCompanyId+"&sgNumber="+sgNumber+"&sgPrice="+sgPrice
$updateSql = "update $DataIn.cg1_stocksheet
set BuyerId=$sgNumber,
    CompanyId=$sgCompanyId,
    Price=$sgPrice
where Id in ($sgIds)";

$result = mysql_query($updateSql);
if($result && mysql_affected_rows()>0){
    
} else {
    echo "设置采购信息失败";
}

?>