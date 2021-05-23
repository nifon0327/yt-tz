<?php 
//$DataIn.电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

switch($ActionId){
    case 41:  //取消订单后，删除无订单已领料数据
       $StockId=$Id;
       include "../admin/subprogram/del_model_llqty.php";
       if (strpos($Log,'成功删除')>0){
           echo "Y";
       }else{
           echo "N";
       }
    break;
}    
    //步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>
