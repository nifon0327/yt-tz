<?php 
$OId=$Id;$aGroupId ="";
$orderResult=mysql_query("SELECT O.POrderId,O.Qty,O.Qty1,O.Qty2 FROM $DataIn.yw10_ordersplit O WHERE O.Id='$OId'",$link_id);
if($orderRow=mysql_fetch_array($orderResult)){
     $POrderId=$orderRow["POrderId"];
	 $Qty=$orderRow["Qty"];
     $Qty1=$orderRow["Qty1"];
     $Qty2=$orderRow["Qty2"];
}			 

if ($POrderId!=""){		
   
	    $MyPDOEnabled=1;  //启用PDO连接数据库
	    include "../basic/parameter.inc";
	    $myResult=$myPDO->query("CALL proc_yw1_ordersheet_split('','$POrderId','$Qty1','$Qty2','$Operator');");
		$myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		$OperationResult = $myRow['OperationResult'];
		$myResult=null;
		 
		if ($OperationResult=="Y"){
		    $Log.="<div class=greenB>&nbsp;&nbsp;ID:$POrderId 订单数量($Qty)拆分($Qty1/$Qty2)成功</div>"; 
		    
		    $UpdateSql="UPDATE $DataIn.yw10_ordersplit SET Estate=1 WHERE Id='$Id'";
            $UpdateResult=mysql_query($UpdateSql);
	    }else{
		    $Log.="<div class=redB>&nbsp;&nbsp;ID:$POrderId 订单数量($Qty)拆分($Qty1/$Qty2)失败！</div>"; 
		    
	    } 
}
?>