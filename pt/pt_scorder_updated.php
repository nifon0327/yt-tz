<?php
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="工单";		//需处理
	$upDataSheet="$DataIn.yw1_scsheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$ALType="From=$From&Pagination=$Pagination&Page=$Page";
	//步骤3：需处理，更新操作
	$x=1;
	switch($ActionId){

		case "Remark":
			$Log_Funtion="更新Remark";
			$sql = "UPDATE $upDataSheet SET Remark='$tempRemark' WHERE sPOrderId='$sPOrderId'";
			$result = mysql_query($sql);
			if ($result){
				$Log="工单流水号为 $sPOrderId Remark更新成功. $sql <br>";
				}
			else{
				$Log="<div class=redB>工单流水号为 $sPOrderId Remark更新失败. $sql </div><br>";
				$OperationResult="N";
				}
				
		 break;
		 
		case "WorkShopId":
			$Log_Funtion="更新WorkShopId";
			$sql = "UPDATE $upDataSheet SET WorkShopId='$tempWorkShopId' WHERE sPOrderId='$sPOrderId'";
			$result = mysql_query($sql);
			if ($result){
				$Log="工单流水号为 $sPOrderId WorkShopId更新成功. $sql <br>";
				}
			else{
				$Log="<div class=redB>工单流水号为 $sPOrderId WorkShopId 更新失败. $sql </div><br>";
				$OperationResult="N";
				}
				
		 break;
    }
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";

?>