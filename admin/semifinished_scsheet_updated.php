<?php

$MyPDOEnabled=1;
include "../model/modelhead.php";

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="生产工单设置";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId&OrderAction=$OrderAction";

switch($ActionId){
   case 72: //生产工单设置
      $_sPOrderId=$IdList;
      $_sQty=$QtyList;
      $_wsId=$WsList;
      $_LockSign = $LockSignList;
      if ($POrderId!="" && $_sPOrderId!="" && $_sQty!=""){
            //echo "'$POrderId','$_sPOrderId','$_sQty','$_wsId',$Operator";
	        $myResult=$myPDO->query("CALL proc_yw1_scsheet_updated('$POrderId','$_sPOrderId','$_sQty','$_wsId','$_LockSign',$Operator);");
		
	        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	    
		    $OperationResult = $myRow['OperationResult'];
		    $Log=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>"; 
      }
      
      if ($funFrom=="pt_order"){
	      $fromWebPage="../pt/" . $fromWebPage;
      }
     // echo $fromWebPage;
      break;
        
}
	
include "../model/logpage.php";
?>