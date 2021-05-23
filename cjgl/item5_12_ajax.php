<?php   
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="禁用类库存配件";			
$Log_Funtion="报废";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
if($Reason!=""){
   if($Reason=='0')$Reason=$otherCause;
$BfResult="INSERT INTO $DataIn.ck8_bfsheet (Id, ProposerId, StuffId, Qty, Remark, Type, Date, Estate, Locks, Operator)VALUES (NULL,'$Operator','$StuffId','$Qty','$Reason','0','$Date','1','0','$Operator')";
//echo $BfResult;
	 $BfAction=@mysql_query($BfResult);
     if ($BfAction && mysql_affected_rows()>0){
	    $Log="$TitleSTR 成功!<br>";
		//$UpStock=mysql_query("UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$Qty,oStockQty=oStockQty-$Qty WHERE  StuffId='$StuffId'",$link_id);
		echo "Y";
	    }
	else{
	   $Log="<div class=redB>$TitleSTR 失败(库存不足或其它)!</div> $inRecode <br>";
	   $OperationResult="N";
	   echo "N";
	   } 
    }
else{
     echo "N";
	 }

//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

?>