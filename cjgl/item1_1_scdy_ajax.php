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
$Log_Item="打印任务";			//需处理
$Log_Funtion="保存";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$inRecode="INSERT INTO $DataIn.sc3_printtasks (Id,CodeType,POrderId,sPOrderId,Qty,Estate,Date,Operator) VALUES";
if($Qty1!="" && $Qty1>0){
	$inRecode1=" (NULL,'1','$POrderId','$sPOrderId','$Qty1','1','$DateTime','$Operator')";
	}
if($Qty2!="" && $Qty2>0){
	$inRecode1.=$inRecode1==""?"(NULL,'2','$POrderId','$sPOrderId','$Qty2','1','$DateTime','$Operator')":",(NULL,'2','$POrderId','$Qty2','1','$DateTime','$Operator')";
	}
if($Qty3!="" && $Qty3>0){
	$inRecode1.=$inRecode1==""?"(NULL,'3','$POrderId','$sPOrderId','$Qty3','1','$DateTime','$Operator')":",(NULL,'3','$POrderId','$Qty3','1','$DateTime','$Operator')";
	}
if($Qty4!="" && $Qty4>0){
	$inRecode1.=$inRecode1==""?"(NULL,'4','$POrderId','$sPOrderId','$Qty4','1','$DateTime','$Operator')":",(NULL,'4','$POrderId','$Qty4','1','$DateTime','$Operator')";
	}	
//步骤3：需处理
$inAction=@mysql_query($inRecode.$inRecode1);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>
