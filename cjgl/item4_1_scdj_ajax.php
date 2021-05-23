<?php
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="本次预计生产数量";			//需处理
$Log_Funtion="保存";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$MaxResult=mysql_fetch_array(mysql_query("SELECT MAX(Num) AS MaxNum FROM $DataIn.yw1_scsheet WHERE POrderId=$POrderId",$link_id));
$MaxNum=$MaxResult["MaxNum"];
if($MaxNum!="")$MaxNum=$MaxNum+1;
else $MaxNum=1;
$scSheetResult=mysql_fetch_array(mysql_query("SELECT Id ,Qty FROM $DataIn.yw1_scsheet WHERE POrderId=$POrderId AND Estate=1",$link_id));
$thisScId=$scSheetResult["Id"];
if($thisScId!=""){
           $Update_Sql="UPDATE $DataIn.yw1_scsheet SET Qty='$thisQty' WHERE POrderId=$POrderId AND Estate=1";
           $Update_Result=@mysql_query($Update_Sql);
            if($Update_Result && mysql_affected_rows()>0){
                      echo "Y";
                      }
           else echo "N";
          }
else{
           $In_Sql="INSERT INTO $DataIn.yw1_scsheet(Id,Num,POrderId,Qty,Estate,Date,Operator)VALUES(NULL,'$MaxNum','$POrderId','$thisQty','1','$Date','$Operator')";
           $In_Result=@mysql_query($In_Sql);
           if($In_Result) echo"Y";
           else echo "N";
          }
?>