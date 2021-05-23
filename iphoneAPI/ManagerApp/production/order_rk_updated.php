<?php
	$POrderId = $info[0];
	$Operator = $LoginNumber;
	
		
	$Log_Funtion="数据更新";
	$DateTime=date("Y-m-d H:i:s");
	$Time = date("H:i:s");
	$Date=date("Y-m-d");
	$OperationResult="Y";

	$log = "";
	switch($ActionId)
	{
		case "RK": {
			$Log_Item="组装入库";
		 	$sql = "UPDATE $DataIn.yw1_ordersheet A Left Join $DataIn.sc1_mission B On B.POrderId=A.POrderId Set A.scFrom=0, A.Estate=2,B.Estate=0,B.FinishTime='$DateTime' Where A.POrderId='$POrderId' ";
			$rs = mysql_query($sql);
			if ($rs && mysql_affected_rows()>0) {
				$log = "组装入库确认成功(POrderId:$POrderId)";
				$OperationResult="Y";
				
				$upSql = "UPDATE  $DataIn.yw1_productinspection SET  Inspection=0 WHERE  POrderId='$POrderId' ";
			    $upResult = mysql_query($upSql);
			} else {
				$log = "组装入库确认失败(POrderId:$POrderId)";
				$OperationResult="N";
			}
		}
		break;
	   case "Inspection":
	   {
		   $Log_Item="重检";
		 	$upSql = "UPDATE  $DataIn.yw1_productinspection SET  Inspection=0 WHERE  POrderId='$POrderId' ";
			$rs = mysql_query($upSql);
			if ($upResult && mysql_affected_rows()>0) {
				$log = "订单重检状态设置成功(POrderId:$POrderId)";
				$OperationResult="Y";
			} else {
				$log = "订单重检状态设置失败(POrderId:$POrderId)";
				$OperationResult="N";
			}
	   }
	   break;
		default: {
			$OperationResult="N";
		}
		break;
	}


	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
	$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$log");
	
	
?>