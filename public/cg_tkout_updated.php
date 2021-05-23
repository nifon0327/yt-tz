<?php 
//电信-zxq 2012-08-01
/*$DataIn.cw1_tkoutsheet 二合一已更新*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="需求单";		//需处理
$Log_Funtion="请款";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
$ALType="From=$From";
switch ($ActionId){
	case 14:
		$Log_Funtion="客户退款配件请款";
		//将记录复制到请款明细表(客户退款类配件只按订单数计算)	
		$inRecode=$DataIn!=='ac' ? "INSERT INTO $DataIn.cw1_tkoutsheet SELECT NULL,'0',StockId,POrderId,StuffId,OrderQty,Price,OrderQty,'0','0',FactualQty,CompanyId,BuyerId,OrderQty*Price,'$Month','2','1','$DateTime' FROM $DataIn.cg1_stocksheet WHERE Id IN ($Ids)" : 
		                           "INSERT INTO $DataIn.cw1_tkoutsheet SELECT NULL,'0',StockId,POrderId,StuffId,OrderQty,Price,OrderQty,'0','0',FactualQty,CompanyId,BuyerId,OrderQty*Price,'$Month','2','1','$DateTime',0,'$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator' FROM $DataIn.cg1_stocksheet WHERE Id IN ($Ids)";
		$inAction=@mysql_query($inRecode);
		if ($inAction){ 
			$Log="&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."成功!<br>";
			} 
		else{ 
			$Log="<div class=redB>&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."失败! $inRecode</div><br>";
			$OperationResult="N";
			}
	     	break;
	case 15://退回修改
		//删除记录
		$Log_Funtion="退回修改";
		$delRecode="DELETE FROM $DataIn.cw1_tkoutsheet WHERE Id IN ($Ids)";
		$delAction = mysql_query($delRecode);
		if($delAction && mysql_affected_rows()>0){
			$Log="请款ID在($Ids)的需求单请款退回成功.";
			$ALType="From=$From&CompanyId=$CompanyId";
			}
		else{
			$Log="<div class='redB'>请款ID在($Ids)的需求单请款退回失败.</div>";
			$OperationResult="N";
			}
		break;
	case 17://审核通过
		$Log_Funtion="审核通过";
		$updateRecode="UPDATE $DataIn.cw1_tkoutsheet SET Estate='3' WHERE Id IN ($Ids)";
		$updateAction = mysql_query($updateRecode);
		if($updateAction && mysql_affected_rows()>0){
			$Log="请款ID在($Ids)的需求单审核成功.";
			$ALType="From=$From&CompanyId=$CompanyId";
			}
		else{
			$Log="<div class='redB'>请款ID在($Ids)的需求单审核失败.</div>";
			$OperationResult="N";
			}
		break;
	}
//返回参数

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>