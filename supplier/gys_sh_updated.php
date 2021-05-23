<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="送货资料";		//需处理
$upDataSheet="$DataIn.gys_shsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 933:
		$Remark=FormatSTR($Remark);
		$mainSql = "UPDATE $DataIn.gys_shmain SET Remark='$Remark' WHERE Id='$Mid'";
		$mainResult = mysql_query($mainSql);
		if($mainResult && mysql_affected_rows()>0){
			$Log.="采购单 $Mid 主单信息更新成功!<br>"; 
			}
		else{		
			$Log.="<div class='redB'>采购单 $Mid 主单信息更新失败! $mainSql </div><br>"; 
			$OperationResult="N";
			}
		break;	
	default:	//更新:单价、数量
		$SetStr="Qty='$Qty'";
		include "../admin/subprogram/updated_model_3a.php";
		break;
	

}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";
?>