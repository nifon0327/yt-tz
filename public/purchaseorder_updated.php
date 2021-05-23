<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
switch($ActionId){
   case "jq":
	if($DeliveryDate==""){
	$DeliveryDate="0000-00-00";
	}

	$checkSql =mysql_fetch_array(mysql_query("SELECT DeliveryDate FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId'  LIMIT 1",$link_id));
	$oldDeliveryDate= $checkSql['DeliveryDate'];
	
	$sql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate='$DeliveryDate' WHERE StockId='$StockId'";
	$result = mysql_query($sql);
	break;
	
	case "jqdd":
	       
	       $UpdateSql = "UPDATE $DataIn.cg1_stocksheet  SET DeliveryDate='0000-00-00',DeliveryWeek=0 WHERE StockId='$StockId'";
	       $UpdateResult = mysql_query($UpdateSql);
	       if($UpdateResult){
		        $UpdateSubSql = "UPDATE $DataIn.cg1_stocksheet  G 
		        LEFT JOIN $DataIn.cg1_semifinished SM ON SM.StockId = G.StockId 
		        SET G.DeliveryDate='0000-00-00',G.DeliveryWeek=0
		        WHERE SM.mStockId ='$StockId' AND G.rkSign>0 AND (G.AddQty+G.FactualQty)>0  ";
		       $UpdateSubResult = mysql_query($UpdateSubSql);
	       }
	
	break;
}



//将修改记录添加到交期表


$IN_recode="INSERT INTO $DataIn.cg1_deliverydate (Id,StockId,DeliveryDate,Remark,Estate,Locks,Date,Operator) VALUES (Null,'$StockId','$oldDeliveryDate','$updateWeekRemark','1','0','$Date','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>