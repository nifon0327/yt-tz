<?php   
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache"); 
$Date=date("Y-m-d");
switch($ActionId){
 case 1:
    if($POrderId!=""){
          $CheckResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw1_orderclient WHERE POrderId='$POrderId'",$link_id));
          $CheckId=$CheckResult["Id"];
          if($CheckId!=""){
                      $upSql="UPDATE $DataIn.yw1_orderclient SET CLnotes='$notes' WHERE POrderId='$POrderId'";
                      $upResult=@mysql_query( $upSql);
                      if($upResult && mysql_affected_rows()>0)echo "Y";
                      else  echo "N";
                   }
           else{
                     $InSql="INSERT INTO $DataIn.yw1_orderclient(Id, POrderId,CLnotes,ACnotes,Date) VALUES(NULL,'$POrderId','$notes','','$Date')";
                     $InResult=@mysql_query( $InSql);
                     if($InResult) echo "Y";
                     else echo "N";
                  }
        }
     break;

 case 2:
    if($POrderId!=""){
    $notes=FormatSTR($notes);
          $CheckResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw1_orderclient WHERE POrderId='$POrderId'",$link_id));
          $CheckId=$CheckResult["Id"];
          if($CheckId!=""){
                      $upSql="UPDATE $DataIn.yw1_orderclient SET ACnotes='$notes' WHERE POrderId='$POrderId'";
                      $upResult=@mysql_query( $upSql);
                      if($upResult && mysql_affected_rows()>0)echo "Y";
                      else  echo "N";
                   }
           else{
                     $InSql="INSERT INTO $DataIn.yw1_orderclient(Id, POrderId,CLnotes,ACnotes, Date) VALUES(NULL,'$POrderId','','$notes','$Date')";
                     $InResult=@mysql_query( $InSql);
                     if($InResult) echo "Y";
                     else echo "N";
                  }
        }
     break;

 case 3://加急订单
    if($POrderId!=""){
          if($Estate==1){
                      $DelSql="DELETE FROM  $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type='8'";
                      $DelResult=@mysql_query( $DelSql);
                      if($DelResult && mysql_affected_rows()>0)echo "Y";
                      else  echo "N";
                   }
           else{
                     $InSql="INSERT INTO $DataIn.yw2_orderexpress(Id, POrderId, Type, Date, Operator) VALUES(NULL,'$POrderId','8','$Date',0)";
                     $InResult=@mysql_query( $InSql);
                     if($InResult) echo "Y";
                     else echo "N$InSql";
                  }
        }
     break;
}
?>