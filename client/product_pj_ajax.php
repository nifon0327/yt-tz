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
    if($ProductId!=""){
          $CheckResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.product_pj WHERE ProductId='$ProductId'",$link_id));
          $CheckId=$CheckResult["Id"];
          if($CheckId!=""){
                   if($pjtimes==1&&$times==1){
                             $DelSql="DELETE FROM  $DataIn.product_pj  WHERE ProductId='$ProductId'";
                             $DelResult=@mysql_query( $DelSql);
                             if($DelResult)echo "Y";
                             else  echo "N";
                              }
                       else{
                             $upSql="UPDATE $DataIn.product_pj SET pj_times='$times' WHERE ProductId='$ProductId'";
                             $upResult=@mysql_query( $upSql);
                             if($upResult && mysql_affected_rows()>0)echo "Y";
                             else  echo "N";
                          }
                   }
           else{
                  $InSql="INSERT INTO $DataIn.product_pj(Id, ProductId, pj_times, Date) VALUES(NULL,'$ProductId','$times','$Date')";
                  $InResult=@mysql_query( $InSql);
                  if($InResult) echo "Y";
                  else echo "N";
                  }
        //$mySql="OPTIMIZE TABLE  product_pj"; $myResult=@mysql_query($mySql);
        }
     break;
case 2:
      if($ProductId!=""){
          $CheckResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.product_estleadtime WHERE ProductId='$ProductId'",$link_id));
          $CheckId=$CheckResult["Id"];
          if($CheckId!=""){
                      $upSql="UPDATE $DataIn.product_estleadtime SET Estleadtime='$Estleadtime' WHERE ProductId='$ProductId'";
                      $upResult=@mysql_query( $upSql);
                      if($upResult && mysql_affected_rows()>0)echo "Y";
                      else  echo "N";
                    }
           else{
                  $InSql="INSERT INTO $DataIn.product_estleadtime(Id, ProductId,Estleadtime, Date) VALUES(NULL,'$ProductId','$Estleadtime','$Date')";
                  $InResult=@mysql_query( $InSql);
                  if($InResult) echo "Y";
                  else echo "N";
                  }
         }
    break;

case 5:
      if($ProductId!=""){
          $CheckResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.product_proinfo WHERE ProductId='$ProductId'",$link_id));
          $CheckId=$CheckResult["Id"];
          if($CheckId!=""){
                      $upSql="UPDATE $DataIn.product_proinfo SET ProInfo='$ProInfo' WHERE ProductId='$ProductId'";
                      $upResult=@mysql_query( $upSql);
                      if($upResult && mysql_affected_rows()>0)echo "Y";
                      else  echo "N";
                    }
           else{
                  $InSql="INSERT INTO $DataIn.product_proinfo(Id, ProductId,ProInfo) VALUES(NULL,'$ProductId','$ProInfo')";
                  $InResult=@mysql_query( $InSql);
                  if($InResult) echo "Y";
                  else echo "N";
                  }
         }
    break;
}
?>