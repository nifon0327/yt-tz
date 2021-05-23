<?php   
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

ChangeWtitle("$SubCompany 生产工单");
$Log_Funtion="更新";
$Login_help="yw_order_ajax_updated";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="工单锁定";

$ALType="";
$x=1;
$y=1;
$OperationResult="N";
switch ($Action){
	case "Lock":
		$count_Temp=mysql_query("SELECT count( * ) AS counts FROM $DataIn.yw1_sclock WHERE sPOrderId='$sPOrderId' ",$link_id);  
		$counts=mysql_result($count_Temp,0,"counts");
		if ($counts<1){ 
			$inRecode="INSERT INTO $DataIn.yw1_sclock (Id,sPOrderId,Estate,Locks,Remark,Date,Operator,creator,created,modifier,modified) VALUES (NULL,'$sPOrderId','1','$myLock','$LockRemark','$Date','$Operator','$Operator','$DateTime','$Operator','$DateTime') ";
				$inResult=mysql_query($inRecode);
		}
		else{
		    $LockRemarkSTR=$LockRemark!=""?",Remark='$LockRemark'":"";
			$inRecode = "UPDATE $DataIn.yw1_sclock  SET Locks='$myLock',Estate=1,modifier='$Operator',modified='$DateTime' $LockRemarkSTR WHERE sPOrderId='$sPOrderId'";
			$inResult = mysql_query($inRecode);
		}
		$Log=$myLock==1?"$StockId 解锁！":"$StockId 锁定！";
	break;
	
	case "delProcessId":
	    $DelProcessSql = "DELETE FROM $DataIn.cg1_processsheet WHERE  StockId ='$StockId' AND ProcessId='$ProcessId'";
		$DelProcessResult = mysql_query($DelProcessSql);
	    if($DelProcessResult){
		    echo "Y";
	    }else{
		    echo $DelProcessSql;
	    }
	break;
	
	
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode);	
?>