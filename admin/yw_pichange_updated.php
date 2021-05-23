<?php   
$MyPDOEnabled=1;
 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="PI交期更改审核";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
switch($ActionId){
	case 17:
		  $Log_Funtion="审核";		
		  $Lens=count($checkid);
		  for($i=0;$i<$Lens;$i++){
			 $Id=$checkid[$i];
			 //需清除上次查询，否则执行下面sql会报错
	          $myResult=null;$myRow=null; 
	          
	          $mySql="SELECT C.POrderId,C.UpdateLeadtime,C.ReduceWeeks,PI.oId,PI.Leadtime AS PILeadtime,CG.ReduceWeeks AS OldReduceWeeks,Lead.Leadtime AS NOPILeadtime,C.Operator 
                      FROM $DataIn.yw3_pileadtimechange  C 
                     INNER JOIN  $DataIn.yw1_ordersheet  S  ON S.POrderId=C.POrderId
                     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
                     LEFT JOIN $DataIn.yw3_pileadtime Lead ON Lead.POrderId=C.POrderId
                     LEFT JOIN $DataIn.yw2_cgdeliverydate CG  ON CG.POrderId=C.POrderId
                     WHERE C.Id=$Id LIMIT 1";   
			  $myResult=$myPDO->query($mySql);
		      $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		      
		      $POrderId=$myRow["POrderId"];
              $UpdateLeadtime=$myRow["UpdateLeadtime"];
              $PIoId=$CheckResult["oId"];
              $PIOperator=$CheckResult["Operator"];
              
              if($PIoId==""){//未生成PI，取表yw3_pileadtime中的Leadtime
                   $oldLeadtime=$myRow["NOPILeadtime"];
               }
              else{
                   $oldLeadtime=$myRow["PILeadtime"];
               }
               
               $ReduceWeeks=$myRow["ReduceWeeks"];
               $OldReduceWeeks=$myRow["OldReduceWeeks"];
               $PIDate = $UpdateLeadtime;
                 
               //1主单信息更新
			   $UpdateSql="UPDATE $DataIn.yw3_pileadtimechange  SET Estate=0 ,OldLeadtime='$oldLeadtime',OldReduceWeeks='$OldReduceWeeks' WHERE Id=$Id";
		       $count = $myPDO->exec($UpdateSql); 
		       //$count=1;
		       if ($count>0){
			       $Log="订单流水号为 $POrderId 的PI交期更改审核成功.<br>";
			       
	                   $Log.="订单流水号为 $POrderId 的PI交期更新成功.<br>";
	                   //设置订单采购交期
		  		       if (strlen($ReduceWeeks)==0) $ReduceWeeks=-1;
		  		       
		  		       $PIOperator=$PIOperator==""?$Operator:$PIOperator;
		  		       $setResult=$myPDO->query("CALL proc_yw1_ordersheet_setdeliverydate('$POrderId','$PIDate',$ReduceWeeks,'1','$PIOperator');");
		  		       $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
		  		       
		  		       
		  		       if ($setRow['OperationResult']=="Y"){
			  		        $Log.="订单流水号为 $POrderId 更新采购单交货周期成功.<br>"; 
		  		       }
		  		       else{
			  		        $Log.="<div class=redB>订单流水号为 $POrderId 更新采购单交货周期失败.</div><br>"; 
			  		        echo $UpdateSql2;
		  		       }
				       
			           $setResult=null;$setRow=null;
                   }
		          else{
				      $OperationResult="N";
				      $Log.="<div class=redB>订单流水号为 $POrderId 的PI交期更改审核失败.</div><br>";
                  }
		  	}
          $fromWebPage=$funFrom."_m";
		break;

	case 15:
		 $Log_Funtion="退回";
		  $Lens=count($checkid);
		  for($i=0;$i<$Lens;$i++){
			         $Id=$checkid[$i];
                            $DeleteSql = "DELETE FROM  $DataIn.yw3_pileadtimechange  WHERE Id=$Id";
                            $count = $myPDO->exec($DeleteSql);
                             if($count>0){
				                 $Log.="订单流水号为 $POrderId 的PI交期更改退回成功.<br>";
                              }
                              else{
					             $Log.="<div class=redB>订单流水号为 $POrderId 的PI交期更改退回失败.</div>$DeleteSql<br>";
				                 $OperationResult="N";
                             }
              }
         $fromWebPage=$funFrom."_m";
		break;
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$myPDO->exec($IN_recode);

include "../model/logpage.php";
?>