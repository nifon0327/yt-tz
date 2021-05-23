<?php 
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
session_start();
$OperationResult="N";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

switch($Action){
	case 1:
	case 2:
	     $thTableReview=$Sign==1?"ck12_threview":"ck2_threview";
	     $thTableSheet=$Sign==1?"ck12_thsheet":"ck2_thsheet";
		 $InsertSql="INSERT INTO $DataIn.$thTableReview SELECT NULL,Id,StuffId,'$Remark','$Action','0','$Date','$Operator','$DateTime','$Operator','$DateTime',null,null  FROM  $DataIn.$thTableSheet  WHERE Id='$Id' ";
		$InsertResult = mysql_query($InsertSql,$link_id);
		if($InsertResult && mysql_affected_rows()>0){
			$OperationResult="Y";
		}
		break;
}
echo $OperationResult;
?>