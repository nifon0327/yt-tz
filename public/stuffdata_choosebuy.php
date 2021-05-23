<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$Log_Item="购买选择";			//需处理
$Log_Funtion="保存";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$CheckStuffBuyResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.stuffbuy WHERE StuffId=$StuffId",$link_id));
$CheckStuffBuyId=$CheckStuffBuyResult["Id"];
		if($CheckStuffBuyId!=""){
       $DelSql="DELETE  FROM $DataIn.stuffbuy WHERE StuffId=$StuffId";
        $DelResult=@mysql_query($DelSql);
       if($DelResult && mysql_affected_rows()>0)echo "D";
         }
else{
       $IN_recode="INSERT INTO $DataIn.stuffbuy(`Id`, `StuffId`, `Date`, `Operator`)VALUES(NULL,'$StuffId','$DateTime','$Operator')";
       $IN_res=@mysql_query($IN_recode);
       if($IN_res && mysql_affected_rows()>0)echo "Y";
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>