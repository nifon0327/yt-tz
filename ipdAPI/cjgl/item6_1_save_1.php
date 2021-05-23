<?php 
//include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
$Log_Item="出货";
$Operator=$Login_P_Number;
$checkid=explode("^^",$IdArray);
$Type=1;//产品(鼠宝皮套没有样品随货出)
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	if($checkid[$i]!=""){
	    $Id=$checkid[$i];
		if($Type==1){
			$Ids1=$Ids1==""?$Id:($Ids1.",".$Id);
			}
		else{//样品
			$Ids2=$Ids2==""?$Id:($Ids2.",".$Id);
			}
		}
	}
$Ids1=$Ids1==""?0:$Ids1;
$Ids2=$Ids2==""?0:$Ids2;
//保存主单资料
$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch1_shipmain",$link_id));
$Number=$checkNumber["Number"]+1;
if($ShipType) {$ShipType='replen';} //新增补货标识
   else{$ShipType='';}	
$mainInSql="INSERT INTO $DataIn.ch1_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,Ship,ShipType,cwSign,Remark,Operator)VALUES(NULL,'$CompanyId','$ModelId','$BankId','$Number',
'$InvoiceNO','1','$Wise','$Notes','$Terms','$PaymentTerm','','$ShipDate','1','1','1','-1','$ShipType','1','','$Operator')";
$mainInAction=@mysql_query($mainInSql);
$Mid=mysql_insert_id();
if($mainInAction){
	$Log.="出货主单($Mid)创建成功.<br>";
	//出货明细入库:产品和样品 
	if ($DataIn=='ac'){
	    $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,Qty,Price,'1','1','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.yw1_ordersheet WHERE Id IN ($Ids1)";
	}
	else{
		$sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,Qty,Price,'1','1','1' FROM $DataIn.yw1_ordersheet WHERE Id IN ($Ids1)";
	}
	if($Ids2!=""){
		  if ($DataIn=='ac'){
				$sheetInSql.=" UNION SELECT NULL,'$Mid',SampId,'0',Qty,Price,'2','1','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ch5_sampsheet WHERE Id IN ($Ids2)";
			}
			else{
				$sheetInSql.=" UNION SELECT NULL,'$Mid',SampId,'0',Qty,Price,'2','1','1' FROM $DataIn.ch5_sampsheet WHERE Id IN ($Ids2)";
			}
		}
	$sheetInAction=@mysql_query($sheetInSql);
	if($sheetInAction && mysql_affected_rows()>0){
		$Log.="出货的订单($Ids1)和随货项目($Ids2)加入出货明细表成功.<br>";
		
		}
	else{
		$Log.="<div class='redB'>出货的订单($Ids1)和随货项目($Ids2)加入出货明细表失败.</div><br>";
		}
	//更新状态
	$pUpSql="UPDATE $DataIn.yw1_ordersheet SET Estate='4' WHERE Id IN ($Ids1)";
	$pUpResult=@mysql_query($pUpSql);
	if($pUpResult && mysql_affected_rows()>0){
		$Log.="订单($Ids1)的状态更新成功.<br>";
		$OperationResult="Y";
	    $alertLog="出货成功";
		}
	else{
		$Log.="<div class='redB'>订单($Ids1)的将出状态更新失败. </div><br>";
		}
	$sUpSql="UPDATE $DataIn.ch5_sampsheet SET Estate='2' WHERE Id IN ($Ids2)";
	$sUpResult=mysql_query($sUpSql);
	if($sUpResult && mysql_affected_rows()>0){
		$Log.="随货项目($Ids2)的状态更新成功.<br>";
		}
	else{
		$Log.="<div class='redB'>随货项目($Ids2)的将出状态更新失败.</div><br>";
		}
	//生成Invoice
	
	$Id=$Mid;
	 //include "../admin/ch_shippinglist_toinvoice.php";
	$alertLog=$Mid;
	
	}
else{
	$Log.="<div class='redB'>出货主单($Mid)创建失败. $mainInSql </div><br>";
	$OperationResult="N";
	$alertErrLog="出货失败 ";
	}

if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(4,4);</script>";
   }
?>