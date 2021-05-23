<?php   
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$OperationResult = "Y";
$Log_Funtion="变更生产单位";

$UpdateSql="UPDATE $DataIn.yw1_scsheet SET WorkShopId='$changeWorkShopId'  WHERE POrderId=$sPOrderId";
$UpdateResult=@mysql_query($UpdateSql);
if($UpdateResult && mysql_affected_rows()>0){
     $OperationResult="Y";
  }
 else{
     $OperationResult="N";
}


//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
 ?>