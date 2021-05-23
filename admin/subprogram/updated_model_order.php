<?php   
/*
订单数量减少电信---yang 20120801
1、需求单未下单
	A:全部使用库存：退回多出的库存
	B:部分使用库存
	C:未使用库存
	计算:
		新的需求数=新的订单数-原已用库存
		if(新的需求数>=0){
			使用库存数、增购不变
			}
		else{
			新的需求数=0
			使用库存数=新的订单数
			增购=0
			}
2、需求单已下单：增购处理
	B:部分使用库存
	C:未使用库存
$DataIn.yw1_ordersheet
$DataIn.yw1_ordersheet
二合一已更新
*/
$tlMid="";
$x=1;
$newEstate=2;
$OldQtyTemp=mysql_query("SELECT Qty,POrderId,ProductId FROM $DataIn.yw1_ordersheet WHERE Id='$Id' ORDER BY Id LIMIT 1",$link_id);
if($OldRow = mysql_fetch_array($OldQtyTemp)){	//读取订单流水号和订单数量
	$OldQty=$OldRow["Qty"];
	$POrderId=$OldRow["POrderId"];
	$ProductId=$OldRow["ProductId"];
	if($OldQty!=$Qty){//数量发生变化
		if($OldQty>$Qty){	//订单数量减少：
			include "subprogram/updated_model_order_1.php";
			}//if($OldQty>$Qty){	//订单数量减少：
		else{//订单数量增加
			include "subprogram/updated_model_order_2.php";
			}
		}//end if($OldQty!=$Qty){
	}//end if($OldRow = mysql_fetch_array($OldQtyTemp))	

//3明细信息更新
$DeliveryDate=$DeliveryDate==""?"0000-00-00":$DeliveryDate;
$sheetSql = "UPDATE $DataIn.yw1_ordersheet SET Qty='$Qty',Price='$Price',PackRemark='$PackRemark',ShipType='$ShipType',DeliveryDate='$DeliveryDate',Estate='1' WHERE Id='$Id'";//无论生产记录如何变化，出货状态均重置为1
$sheetResult = mysql_query($sheetSql);

if($sheetResult){
	$Log.="Id为 $Id 的产品订单资料更新成功.<br>";
	}
else{
	$Log.="<div class=redB>Id为 $Id 的产品订单资料更新失败. $sheetSql </div><br>";
	$OperationResult="N";
	}
?>