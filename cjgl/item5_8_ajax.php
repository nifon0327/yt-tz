<?php   
session_start();
$MyPDOEnabled=1;
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="物料补仓";			//需处理
$Log_Funtion="数据更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
	case 1:
	$BillNumber=$BillNumber==""?"":$BillNumber;
	$Lens=count($thQTY);
	for($i=0;$i<$Lens;$i++){
		if($thQTY[$i]!="" && $CompanyId>0){
			$StuffId=$thStuffId[$i];
			$Qty=$thQTY[$i];
			$Remark=$thRemark[$i];
		    $myResult=$myPDO->query("CALL proc_ck3_bcsheet_save('$BillNumber','$CompanyId','$StuffId','$Qty','$Remark',$Operator);");
		    $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		    $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
		    $myResult=null;
		    $myRow=null;   
        }
    }

  break;


	
  case 20:
  		$Log_Funtion="主补仓单更新";
		$upSql = "UPDATE $DataIn.ck3_bcmain SET Date='$bcDate',BillNumber='$BillNumber' 
		         WHERE Id='$Mid'";
		$upResult = $myPDO->exec($upSql);		
		if($upResult){
			$Log="补仓主单资料更新成功.<br>";
			}
		else{
			$Log="<div class='redB'>补仓主单资料更新失败!</div><br>";
			$OperationResult="N";
			}
	 break;
}

$alertLog=$Log_Item . "数据更新成功";
$alertErrLog=$Log_Item . "数据更新失败";	
if ($OperationResult=="N"){
       echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertErrLog');</script>";
      }
   else{
	  echo "<SCRIPT LANGUAGE=JavaScript>alert('$alertLog');parent.closeWinDialog();parent.ResetPage(1,5);</script>";
   }

?>