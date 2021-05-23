<?php   
//电信-zxq 2012-08-01
//点击计数
//include "chksession.php" ;
//include "basic/parameter.inc";
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$NowTime=date("Y-m-d H:i:s");
if($Login_P_Number!=10002 && $Login_P_Number!=10369 && $Login_P_Number!=10689 && $Login_P_Number!=10868 && $Login_P_Number!=10871){
	$inRecode="INSERT INTO $DataPublic.sys7_clicktotal (Id, ComeFrom, FunctionId, Operator, ClickDate) VALUES (NULL,'$ComeFrom','$FunctionId','$Login_P_Number','$NowTime')";
	$inAction=@mysql_query($inRecode);
	}
//读取点击总数并返回
$checkTotalRow=mysql_fetch_array(mysql_query("SELECT count(*) AS TotalNum FROM $DataPublic.sys7_clicktotal WHERE FunctionId='$FunctionId'",$link_id));
$TotalNum=$checkTotalRow["TotalNum"];
echo $TotalNum;
?>