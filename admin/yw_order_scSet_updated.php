<?PHP
 $MyPDOEnabled=1;
	include "../model/modelhead.php";
	
	$fromWebPage=$funFrom."_m";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Log_Item="工单拆分";		
	$Log_Funtion="审核";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$ALType="From=$From&Pagination=$Pagination&Page=$Page";
	$SaveOprationlog=0;
	
	switch($ActionId){
	    case 15:
	    $Log_Funtion="退回";	
	    $Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
		   $Id=$checkid[$i];
				if($Id!=""){
		         $checkEstateSql = "DELETE FROM  $DataIn.yw1_ordersplit    WHERE Id ='$Id' AND Estate=1 ";
	             $checkEstateResult = $myPDO->exec($checkEstateSql);
				 if($checkEstateResult){
					$Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;Id为 $Id 的工单拆分退回成功.<br>";
				 }else{
					$OperationResult ="N";
					$Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;Id为 $Id 的工单拆分退回失败.$checkEstateSql<br>";
				}
			}
		}
	    break;
		case 17://审核通过
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
		   $Id=$checkid[$i];
			if($Id!=""){
				 $checkIdSql = "SELECT Id,sPOrderId,splitQty,wsId,LockSign,POrderId,Operator 
				 FROM $DataIn.yw1_ordersplit  WHERE Id ='$Id' ";
	             $checkIdResult = $myPDO->query($checkIdSql);
	             $checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC);
	             $POrderId= $checkIdRow["POrderId"];
	             $_sPOrderId= $checkIdRow["sPOrderId"];
	             $_sQty= $checkIdRow["splitQty"];
	             $_wsId= $checkIdRow["wsId"];
	             $_Operator = $checkIdRow["Operator"];
	             $_LockSign = $checkIdRow["LockSign"];
				 $Operator = $_Operator==""?$Operator:$_Operator;
				 
			     if ($POrderId!="" && $_sPOrderId!="" && $_sQty!=""){
			            echo "'$POrderId','$_sPOrderId','$_sQty','$_wsId','$_LockSign',$Operator";
				        $myResult=$myPDO->query("CALL proc_yw1_scsheet_updated('$POrderId','$_sPOrderId','$_sQty','$_wsId','$_LockSign',$Operator);");
				        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
					    $OperationResult = $myRow['OperationResult'];
					    $Log=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>"; 
					    $myResult = null;
			     }else{
				     $OperationResult = "N";
				     $Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;Id为 $Id 的审核记录不存在.<br>";
			     }
			     if($OperationResult=="Y"){
					 $checkEstateSql = "UPDATE $DataIn.yw1_ordersplit  SET Estate=0 WHERE Id ='$Id' ";
		             $checkEstateResult = $myPDO->exec($checkEstateSql);
					 if($checkEstateResult){
						 $Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;POrderId为 $POrderId 的工单拆分审核成功.<br>";
					 }
					 //工单解锁
					   /*$_sPOrderIdArray = explode("|",$_sPOrderId);
					   $_sPOrderIdCount = count($_sPOrderIdArray);
					   for($k=0;$k<$_sPOrderIdCount;$k++){
						   $tempsPOrderId= $_sPOrderIdArray[$k];
						   if($tempsPOrderId>0){
							    $count_TempResult=$myPDO->query("SELECT count( * ) AS counts FROM $DataIn.yw1_sclock WHERE sPOrderId='$tempsPOrderId'");  
							    $count_TempRow =$count_TempResult->fetch(PDO::FETCH_ASSOC);
								$counts=$count_TempRow["counts"];
								$count_TempResult = null;
								if ($counts>0){ 
									$inRecode = "UPDATE $DataIn.yw1_sclock  SET Locks='1',Remark='$LockRemark',modifier='$Operator',modified='$DateTime'  WHERE sPOrderId='$tempsPOrderId'";
									$inResult = $myPDO->exec($inRecode);
								}
						   } 
					   }*/
					 
				 }
			}
		}			
		break;
	}
	
include "../model/logpage.php";	
?>