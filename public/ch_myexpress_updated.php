<?php 
//电信-zxq 2012-08-01
//步骤1 $DataPublic.my3_express 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="我的快递单";		//需处理
$upDataSheet="$DataPublic.my3_express";	//需处理
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
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		$SetStr="CompanyId='0',ShipType='0',BillNumber='',Length='0',Width='0',Height='0',dWeight='0',cWeight='0',Amount='0',CFSAmount='0',SendDate='0000-00-00',Estate='1',Locks='1',HandledBy='0'";
		include "../model/subprogram/updated_model_3d.php";			break;
	case 42://寄出
		$SetStr="CompanyId='$CompanyId',ShipType='$ShipType',BillNumber='$BillNumber',Length='$Length',Width='$Width',Height='$Height',dWeight='$dWeight',cWeight='$cWeight',Amount='$Amount',CFSAmount='$CFSAmount',Remark='$Remark',SendDate='$SendDate',Estate='0',HandledBy='$HandledBy'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>