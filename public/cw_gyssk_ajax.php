<?php 
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
session_start();
//步骤2：
$Log_Item="供应商税款记录";			//需处理
$upDataSheet="$DataIn.cw2_gyssksheet";	//需处理
$TitleSTR=$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch ($ActionId){
    case "801":
         $Log_Funtion="会计发票确认";
          $upsql = "UPDATE $upDataSheet SET InvoiceCollect=1 WHERE Id='$Id'";
		$result = mysql_query($upsql,$link_id);
		if($result){
			$Log="ID号为$Id 的记录 $Log_Funtion 成功. </br>";
			 echo "Y";
			}
		else{
			$Log="ID号为$Id 的记录 $Log_Funtion 失败! $upsql</br>";
			$OperationResult="N";
			}

      break;
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>
