<?php   
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$OperationResult="N";

switch($ActionId){
	case "1":
		$Log_Funtion="生产生产记录";
		$DeleteSql="DELETE FROM  $DataIn.sc1_cjtj  WHERE Id = $Id AND sPOrderId=$sPOrderId";
		$DeleteResult=@mysql_query($DeleteSql);
		if($DeleteResult && mysql_affected_rows()>0){
		     $OperationResult="Y";
		}
		else{
		     $OperationResult="N";
		}
	break;
	case "2":
	   if($delQty ==0 || $delQty == ""){
		   $OperationResult="N";
	   }else if($delQty == $scQty){ //做删除动作
			$DeleteSql="DELETE FROM  $DataIn.sc1_cjtj  WHERE Id = $Id AND sPOrderId=$sPOrderId";
			$DeleteResult=@mysql_query($DeleteSql);
			if($DeleteResult && mysql_affected_rows()>0){
			     $OperationResult="Y";
			}
			else{
			     $OperationResult="N";
			}
	   }else{ //做更新动作
			$updateSql="UPDATE $DataIn.sc1_cjtj SET Qty = Qty - $delQty WHERE Id = $Id AND sPOrderId=$sPOrderId";
			$updateResult=@mysql_query($updateSql);
			if($updateResult && mysql_affected_rows()>0){
			     $OperationResult="Y";
			}
			else{
			     $OperationResult="N";
			}
	   }
	break;
}



//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
 ?>