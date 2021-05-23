<?php 
//电信---yang 20120801
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="退回金额";			//需处理
$Log_Funtion="更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
switch($ActionId){
   case 1:
           case 1:
            $UpdateSql="UPDATE  $DataIn.cwdyfsheet SET OutAmount='$OutAmount' where Id='$Id'";
            $UpdateResult=mysql_query($UpdateSql);
            if($UpdateResult)  echo "Y"; 
             else  echo "N";
            break;
case 2:
          $modelfeeSql="INSERT INTO $DataIn.cw16_modelfee(Id, Mid,BankId, Moq, OutAmount, ItemName,  Remark,Bill,Estate, Locks, Date, Operator) values(NULL,'$Id','0','0','$OutAmount','$Content','','0','1','0','$Date','$Operator')";
          $modelfeeReuslt=mysql_query($modelfeeSql);
          if($modelfeeReuslt)echo "Y";
            else echo "N";
         break;
 }
?>