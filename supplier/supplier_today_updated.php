<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="采购单";		//需处理
$upDataSheet="$DataIn.cg1_stockreview";	//需处理
$Log_Funtion="确认";
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
		//echo $Ids;
	    $InsertSql="INSERT INTO $DataIn.cg1_stockreview SELECT NULL,Id,PurchaseId,'1','0','$Date','$Operator','0','$Operator','$DateTime',null,null  FROM  $DataIn.cg1_stockmain  WHERE Id IN ($Ids) ";
	    
		$InsertResult = mysql_query($InsertSql);
		if($InsertResult && mysql_affected_rows()>0){
			$Log.="新采购单 $Ids 供应商已确认!<br>"; 
			}
		else{		
			$Log.="<div class='redB'>新采购单  $Ids 供应商确认失败! $InsertSql </div><br>"; 
			$OperationResult="N";
			}
		
		break;	
	default:

		break;
	

}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";
?>