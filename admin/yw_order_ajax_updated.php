<?php   
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

ChangeWtitle("$SubCompany 采购资料锁定保存");
$Log_Funtion="更新";
$Login_help="yw_order_ajax_updated";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="采购资料锁定";

$ALType="";
$x=1;
$y=1;
$OperationResult="N";
switch ($Action){
	case "Lock":
		$count_Temp=mysql_query("SELECT count( * ) AS counts FROM $DataIn.cg1_lockstock WHERE StockId='$StockId' ",$link_id);  //查找记录是否存在，不存在插入
		$counts=mysql_result($count_Temp,0,"counts");
		if ($counts<1){ 
			$inRecode="INSERT INTO $DataIn.cg1_lockstock (`Id`, `StockId`, `Estate`, `Locks`, `Remark`, `ReturnReasons`, `LockDate`, `Date`, `Operator`, `OPdatetime`) VALUES (NULL,'$StockId','1','$myLock','$LockRemark','','$Date','$Date','$Operator','$DateTime') ";
			$inResult=mysql_query($inRecode);

		}
		else{
		    $LockRemarkSTR=$LockRemark!=""?",Remark='$LockRemark'":"";
			$inRecode = "UPDATE $DataIn.cg1_lockstock  SET Locks='$myLock',Estate=1,Date='$Date',Operator='$Operator' $LockRemarkSTR WHERE StockId='$StockId'";
			$inResult = mysql_query($inRecode);
			//更新未下采单时间
		   $upcgSql="UPDATE $DataIn.cg1_stocksheet  SET ywOrderDTime=NOW()  WHERE StockId='$StockId'  AND Mid=0";
		   $upResult=mysql_query($upcgSql); 
		}
       //echo $inRecode;
		$Log=$myLock==1?"$StockId 解锁！":"$StockId 锁定！";
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode);	
?>