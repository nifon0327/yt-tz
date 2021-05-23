<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//步骤2：
$Log_Item="生产记录";			//需处理
$Log_Funtion="更新生管备注";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$sql = "UPDATE $DataIn.yw1_scsheet SET Remark='$sgRemark' WHERE sPOrderId='$sPOrderId' LIMIT 1";
$result = mysql_query($sql);
if ($result){
	$Log="工单流水号为 $sPOrderId 的生管备注更新成功.<br>";
	}
else{
	$Log="<div class=redB>工单流水号为 $sPOrderId 的生管备注更新失败.</div><br>";
	$OperationResult="N";
	}//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>
