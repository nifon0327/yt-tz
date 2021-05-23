<?php   
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
     case "billnum": //发票删除
         $UpdateSql = " UPDATE $DataIn.ch1_shipfile  F 
         LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = F.ShipId 
         SET  F.BillNum = ''  WHERE M.CompanyId = '$CompanyId' AND F.BillNum='$BillNum'";
         $UpdateResult = mysql_query($UpdateSql);
         if($UpdateResult && mysql_affected_rows()>0){
             echo "Y";
	         $filePath = "../download/billback/".$CompanyId."_".$BillNum.".pdf";
	         if(file_exists($filePath)){
		         unlink($filePath);
	         }
	         
         }
     break;
}