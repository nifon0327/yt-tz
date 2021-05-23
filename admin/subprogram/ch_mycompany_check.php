<?php   
//临时区分ECHO公司的出货信息
$tempCheck_Sign="";
/*
if ($CompanyId==1065){
    
       $checkResult = mysql_query("SELECT O.OrderPO  
         FROM `$DataIn`.`ch1_shipsheet` S 
         LEFT JOIN `$DataIn`.`yw1_ordersheet` O ON O.POrderId=S.POrderId 
         WHERE  S.Mid='$check_Id'  LIMIT 1",$link_id);  

       if($checkRows = mysql_fetch_array($checkResult)){
	    $tmpOrderPO=$checkRows["OrderPO"]; 
            //以下PO用鼠宝公司出货
            if ($tmpOrderPO=='24.318' || $tmpOrderPO=='24.385' || $tmpOrderPO=='24.417' || $tmpOrderPO=='24.448'){
                $tempCheck_Sign=2;
            }
       }
       

}
*/
?>