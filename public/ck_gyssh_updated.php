<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="送货单";		//需处理
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作

switch ($ActionId){
	case 17://到货审核
		$Log_Funtion="到货审核";//到货和到货数量确定
		$upDataSheet="$DataIn.gys_shsheet";	//需处理
		$SetStr="Estate=2,Locks=0";
		include "../model/subprogram/updated_model_3d.php";
		break;
	}
$ALType="From=$From&CompanyId=$CompanyId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>