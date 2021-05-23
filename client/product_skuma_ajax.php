<?php   
session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache"); 
$Date=date("Y-m-d");
if($ProductId!=""){
          $CheckResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.product_size WHERE ProductId='$ProductId'",$link_id));
          $CheckId=$CheckResult["Id"];
          if($CheckId!=""){
                    $upSql="UPDATE $DataIn.product_size SET width='$width',length='$length',height='$height' WHERE ProductId='$ProductId'";
                    $upResult=@mysql_query( $upSql);
                    if($upResult && mysql_affected_rows()>0)echo "Y";
                    else  echo "N<br>$upSql";
                   }
           else{
                    $InSql="INSERT INTO $DataIn.product_size(Id, ProductId,Width,length,height)VALUES(NULL,'$ProductId','$width','$length','$height')";
                    $InResult=@mysql_query( $InSql);
                    if($InResult) echo "Y";
                    else echo "N<br>$InSql";
                  }
    }
?>