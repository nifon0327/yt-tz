<?php 
//电信-zxq 2012-08-01
//步骤1 $DataIn.ch5_sampsheet 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="随货样品";		//需处理
$upDataSheet="$DataIn.ch5_sampsheet";	//需处理
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
		$Date=date("Y-m-d");
		$sql = "UPDATE $upDataSheet SET CompanyId='$CompanyId',TypeId='$TypeId',SampPO='$SampPO',SampName='$SampName',Description='$Description',Qty='$Qty',Price='$Price',Weight='$Weight',Type='$Type',Locks='0',Operator='$Operator' WHERE Id=$Id";
		$result = mysql_query($sql);
		if ($result){
			$Log="&nbsp;&nbsp;ID号为 $Id 的随货样品资料更新成功!<br>";
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;ID号为 $Id 的随货样品资料更新失败!</div><br>";
			$OperationResult="N";
			}
		break;
	}
$ALType="chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  