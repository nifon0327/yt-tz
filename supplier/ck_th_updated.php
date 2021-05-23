<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="物料退换";		//需处理
$upDataSheet="$DataIn.ck2_threview";	//需处理
$Log_Funtion="审核";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 17:
	   $Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
	    }
		
	    $InsertSql="INSERT INTO $DataIn.ck2_threview SELECT NULL,Id,BillNumber,'$Remark',1','0','$Date','$Operator','$DateTime','0','$Operator','$DateTime',null,null  FROM  $DataIn.ck2_thmain WHERE Id IN ($Ids) ";
		$InsertResult = mysql_query($InsertSql);
		if($InsertResult && mysql_affected_rows()>0){
			$Log.="退换单 $Ids 供应商审核退过!<br>"; 
			}
		else{		
			$Log.="<div class='redB'>退换单 $Ids 供应商审核更新失败! $InsertResult </div><br>"; 
			$OperationResult="N";
			}
		break;	
	default:	//更新:单价、数量

		break;
	

}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";
?>