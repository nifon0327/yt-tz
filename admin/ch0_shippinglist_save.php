<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="模拟出货资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$ALType="CompanyId=$CompanyId";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	if($checkid[$i]!=""){
		$TEMP=explode("^^",$checkid[$i]);
		$Id=$TEMP[0];$Type=$TEMP[1];
		if($Type==1){
			$Ids1=$Ids1==""?$Id:($Ids1.",".$Id);
			}
		else{
			$Ids2=$Ids2==""?$Id:($Ids2.",".$Id);
			}
		}
	}
$Ids1=$Ids1==""?0:$Ids1;
$Ids2=$Ids2==""?0:$Ids2;
//锁定表
//$LockSql=" LOCK TABLES $DataIn.ch0_shipmain WRITE"; $LockRes=@mysql_query($LockSql);

//保存主单资料
$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch0_shipmain",$link_id));
$Number=$checkNumber["Number"]+1;
//银行帐号
/*
	switch($CompanyId){
		case 1002://ECHO
		case 1013://VOG
		case 1050://PGD
			$BankId=2;break;
		case 1024:
			$BankId=3;break;
		default:
			$BankId=1;break;
		}
*/		
/*
$mainInSql="INSERT INTO $DataIn.ch0_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,PreSymbol,Date,Estate,Locks,Sign,cwSign,Operator) 
VALUES (NULL,'$CompanyId','$ModelId','$BankId','$Number','$InvoiceNO','1','$Wise','','$ShipDate','1','1','1','1','$Operator')";
*/
$mainInSql="INSERT INTO $DataIn.ch0_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,cwSign,Operator) 
VALUES (NULL,'$CompanyId','$ModelId','$BankId','$Number','$InvoiceNO','1','$Wise','$Notes','$Terms','$PaymentTerm','','$ShipDate','1','1','1','1','$Operator')";

$mainInAction=@mysql_query($mainInSql);
$Mid=mysql_insert_id();
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if($mainInAction){
	$Log.="出货主单($Mid)创建成功.<br>";
	//出货明细入库:产品和样品INSERT INTO ch0_shipsheet 
	if ($DataIn=='ac'){
		 $sheetInSql="INSERT INTO $DataIn.ch0_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,Qty,Price,'1','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.yw1_ordersheet WHERE Id IN ($Ids1)";
	}
	else{
		  $sheetInSql="INSERT INTO $DataIn.ch0_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,Qty,Price,'1','1','1' FROM $DataIn.yw1_ordersheet WHERE Id IN ($Ids1)";
	}
	$sheetInAction=@mysql_query($sheetInSql);
	if($sheetInAction && mysql_affected_rows()>0){
		$Log.="模拟出货的订单($Ids1)加入出货明细表成功.<br>";
		}
	else{
		$Log.="<div class='redB'>模拟出货的订单($Ids1)加入出货明细表失败. $sheetInSql </div><br>";
		$OperationResult="N";
		}
	//生成Invoice
	$Id=$Mid;
	//include "ch0_shippinglist_toinvoice.php";
	include "ch0_shippinglistBlue_toinvoice.php";
	}
else{
	$Log.="<div class='redB'>出货主单($Mid)创建失败.</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>