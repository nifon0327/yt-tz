<?php   
//电信-zxq 2012-08-01
//订单的状态更新-2		随货项目的状态更新-1	扣款资料的状态更新-1
//读取Invocie文件，备删
$CheckPdf=mysql_fetch_array(mysql_query("SELECT M.InvoiceNO,M.Sign 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
WHERE M.Id='$Id' GROUP BY M.Id",$link_id));
$InvoiceFile=$CheckPdf["InvoiceNO"].".pdf";
$Sign=$CheckPdf["Sign"];//可以分出货单和扣款单处理??????


$UpSql="UPDATE $DataIn.ch1_shipsheet C 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=C.POrderId 
LEFT JOIN $DataIn.ch1_shipsplit  SP ON SP.ShipId=C.Id
LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId 
LEFT JOIN $DataIn.ch6_creditnote N ON N.Number=C.POrderId 
SET Y.Estate='2',S.Estate='1',N.Estate='1',SP.ShipId=0,SP.Estate=1 WHERE C.Mid='$Id'";
$UpResult = mysql_query($UpSql);
if($UpResult && mysql_affected_rows()>0){
	$Log="相关出货资料状态恢复成功.<br>";
	//删除主单、明细、装箱、运费(条件未结付)、杂费(条件未结付)	
	$DelSql="DELETE  M,C,P,FA,FR 
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shipsheet C ON C.Mid=M.Id 
	LEFT JOIN $DataIn.ch2_packinglist P ON P.Mid=M.Id	
	LEFT JOIN $DataIn.ch3_forward FA ON FA.Mid=M.Id 
	LEFT JOIN $DataIn.ch4_freight_declaration FR ON FR.Mid=M.Id 
	WHERE M.Id='$Id'";
	$delRresult = mysql_query($DelSql);
	if ($delRresult && mysql_affected_rows()>0){
		$Log.="出货单取消成功.<br>";
		$FilePath="../download/Invoice/$InvoiceFile";
		if(file_exists($FilePath)){
			unlink($FilePath);
			}
		}
	else{	//删除失败，状态维持已出货状态
		$UpSql="UPDATE $DataIn.ch1_shipsheet C 
        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=C.POrderId 
       LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId 
        LEFT JOIN $DataIn.ch1_shipsplit  SP ON SP.ShipId=C.Id
        LEFT JOIN $DataIn.ch6_creditnote N ON N.Number=C.POrderId 
        SET Y.Estate='0',S.Estate='0',N.Estate='0'  ,SP.ShipId='$Id',SP.Estate=0  WHERE C.Mid='$Id'";
		$UpResult = mysql_query($UpSql);
		$Log.="<div class='redB'>2出货单取消失败.</div><br>";
		$OperationResult="N";
		}
	}
else{
	$Log="<div class='redB'>1出货资料取消失败. $UpSql</div>";
	$OperationResult="N";
	}

?>
