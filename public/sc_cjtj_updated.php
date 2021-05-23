<?php 
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车间生产资料";		//需处理
$upDataSheet="$DataIn.sc1_cjtj";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$Remark=FormatSTR($Remark);
		$SetStr="Qty='$NowQty',Remark='$Remark',Date='$Date',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		//更新订单状态,如果生产的数量和需求的数量一致，则生产状态为0，出货状态不变,如果已经是已出，则还是为已出
		$UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
			SET Y.scFrom=0,Y.Estate=2 
			WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";
		$UpdateResult = mysql_query($UpdateSql);
	    if($UpdateResult && mysql_affected_rows()>0){
			$Log="$Operator"."生产登记".$POrderId."数量".$Qty.";订单生产完毕,生产状态更新成功.";
			}
		else{
			$Log="$Operator"."生产登记".$POrderId."数量".$Qty.";订单生产未完成.</br>";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Tid=$Tid&chooseDay=$chooseDay";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>