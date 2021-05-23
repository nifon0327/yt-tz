<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="模拟装箱数据";			//需处理
$nowWebPage=$funFrom."_packingsave";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&CompanyId=$CompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

//先删除原有数据
$delOld = mysql_query("DELETE FROM $DataIn.ch0_packinglist WHERE Mid='$Id'",$link_id); 
$delOldSS = mysql_query("DELETE FROM $DataIn.ch0_packpoecodebar WHERE Pid='$Id'",$link_id); 

//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch0_packinglist");
//行数|订单或样品ID|装箱数量|箱数|总数|毛重|外箱尺寸
$Field=explode(",",$PackingList);
$Count=count($Field);
$PEBinRecode="";
for($i=1;$i<$Count;$i++){
	$RowData=explode("^^",$Field[$i]);
	$BoxRow=$RowData[0]==""?'0':$RowData[0];
	$POrderId=$RowData[1];	
	$BoxPcs=$RowData[2];
	$BoxQty=$RowData[3];
	$FullQty=$RowData[4]==""?'0':$RowData[4];
	$WG=$RowData[5]==""?'0':$RowData[5];
	$BoxSpec=$RowData[6];
	
	$POEcodeBarCode=$RowData[7];
	//echo "$POEcodeBarCode <br>";
	$POEcodeBarArr=explode("|",$POEcodeBarCode);
	$PEBCount=count($POEcodeBarArr);
	$newPo="";
	$newEcode="";
	$newBarCode="";
	$newOtherEcode="";
	
	if($PEBCount>0){
		if(($POEcodeBarArr[0]!="*") && ($POEcodeBarArr[0]!="")){
			$newPo=$POEcodeBarArr[0];
		}
	}
	
	if($PEBCount>1){
		if(($POEcodeBarArr[1]!="*") && ($POEcodeBarArr[1]!="")){
			$newEcode=$POEcodeBarArr[1];
		}
	}
	
	if($PEBCount>2){
		if(($POEcodeBarArr[2]!="*") && ($POEcodeBarArr[2]!="")){
			$newBarCode=$POEcodeBarArr[2];
		}
	}
	
	if($PEBCount>3){
		if(($POEcodeBarArr[3]!="*") && ($POEcodeBarArr[3]!="")){
			$newOtherEcode=$POEcodeBarArr[3];
		}
	}
	
	if($BoxQty==""){
		$BoxQty=$oldBoxQty;
		}
	else{
		$oldBoxQty=$BoxQty;
		}
	/*	
	$inRecode=$inRecode==""?"INSERT INTO $DataIn.ch0_packinglist (Id,Mid,POrderId,BoxRow,BoxPcs,BoxQty,WG,FullQty,BoxSpec,Locks) VALUES (NULL,'$Id','$POrderId','$BoxRow','$BoxPcs','$BoxQty','$WG','$FullQty','$BoxSpec','1')":$inRecode.",(NULL,'$Id','$POrderId','$BoxRow','$BoxPcs','$BoxQty','$WG','$FullQty','$BoxSpec','1')";
	*/
	/*
	if($PEBCount>1 && ($newPo!="" || $newEcode!="" || $newBarCode!="")){
		$PEBinRecode=$PEBinRecode==""?"INSERT INTO $DataIn.ch0_packpoecodebar (Id,Mid,POrderId,OrderPO,eCode,BoxCode,Estate,Locks,Date,Operator) 
	                                 VALUES (NULL,'$Mid','$POrderId','$newPo','$newEcode','$newBarCode',1,0,'$Date','$Operator')":$PEBinRecode.",(NULL,'$Mid','$POrderId','$newPo','$newEcode','$newBarCode',1,0,'$Date','$Operator')";
	}
	*/
	$inRecode="INSERT INTO $DataIn.ch0_packinglist (Id,Mid,POrderId,BoxRow,BoxPcs,BoxQty,WG,FullQty,BoxSpec,Locks) VALUES (NULL,'$Id','$POrderId','$BoxRow','$BoxPcs','$BoxQty','$WG','$FullQty','$BoxSpec','1')";
	$inAction=mysql_query($inRecode,$link_id);
	$Mid=mysql_insert_id();
	if ($inAction){
		//include "ch0_shippinglist_toinvoice.php";
		$Log="装箱数据写入数据库成功!";
		
		//add by zx 2015-11-13 加入PO，ecode, barcode
		if($PEBCount>1){
			$PEBinRecode="INSERT INTO $DataIn.ch0_packpoecodebar (Id,Mid,Pid,POrderId,OrderPO,eCode,BoxCode,OtherEcode,Estate,Locks,Date,Operator) 
	                                 VALUES (NULL,'$Mid','$Id','$POrderId','$newPo','$newEcode','$newBarCode','$newOtherEcode',1,0,'$Date','$Operator')";
		
			//echo "$PEBinRecode";
			$PinAction=mysql_query($PEBinRecode,$link_id);
		}
		
		}
	else{
		$Log="<div class='redB'>装箱数据写入数据库失败! $inRecode</div>";
		$OperationResult="N";
		}	
	
}
include "ch0_shippinglist_toinvoice.php";
/*
$inAction=mysql_query($inRecode,$link_id);
$Mid=mysql_insert_id();
if ($inAction){
	include "ch0_shippinglist_toinvoice.php";
	$Log="装箱数据写入数据库成功!";
	
	//add by zx 2015-11-13 加入PO，ecode, barcode
	
	$PEBinRecode="INSERT INTO $DataIn.ch0_packpoecodebar (Id,Mid,POrderId,OrderPO,eCode,BoxCode,Estate,Locks,Date,Operator) 
	                                 VALUES (NULL,'$Mid','$POrderId','$newPo','$newEcode','$newBarCode',1,0,'$Date','$Operator')";
	
	echo "$PEBinRecode";
	$PinAction=mysql_query($PEBinRecode,$link_id);
	
	}
else{
	$Log="<div class='redB'>装箱数据写入数据库失败! $inRecode</div>";
	$OperationResult="N";
	}
*/	
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode,$link_id);
include "../model/logpage.php";
?>