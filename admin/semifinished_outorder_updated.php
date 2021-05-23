<?php
    $MyPDOEnabled=1;
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="外发半成品需求单";		//需处理
	$upDataSheet="$DataIn.yw1_scsheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$SaveOperationSign=1;
	$ALType="From=$From&Pagination=$Pagination&Page=$Page&OrderAction=$OrderAction";
	//步骤3：需处理，更新操作
	$x=1;
	switch($ActionId){
		case "delStuff":
			$Log_Funtion="标记删除需求单";//如果有领料，先删除领料退料记录
			$SetStr="Estate=4,StockRemark='$StockRemark'";
			
			$sql = "UPDATE $DataIn.cg1_stocksheet SET $SetStr WHERE StockId='$StockId'";
			$count = $myPDO->exec($sql);
			if($count>0){
			    $Log="需求单 $StockId 标记删除成功.</br>";
				}
			else{
				$Log="需求单 $StockId 标记删除失败! $sql</br>";
				$OperationResult="N";
				}
		    break;		
	  case 23:		//新加需求单，需要主管审核，审核走yw1_order_updated /m3
		    include "../model/subprogram/FireFox_Safari_PassVar.php";
		    
		    $Level=$Level==""?2:$Level+1;     
		    for ($i=0;$i<count($StuffId);$i++){
               $_StuffId=$StuffId[$i];
			   $_Relation=$pandsQty[$i];
			   $_Unite=$Unite[$i]==""?0:$Unite[$i];
			   $DelSql="DELETE FROM $DataIn.cg1_addstuff WHERE POrderId=$POrderId AND StuffId=$_StuffId AND Estate>0 AND Level =1";
	  	       $myPDO->exec($DelSql); 
	  	       $In_Sql="INSERT INTO $DataIn.cg1_addstuff (Id,Level,POrderId,mStockId,mStuffId,StuffId,uStuffId,Relation,
	  	       Estate,Locks,Date,Operator,PLocks,creator,created,modifier,modified)VALUES(NULL,'$Level','$POrderId','$mStockId',
	  	       '$mStuffId','$_StuffId','$_Unite','$_Relation','1','0','$Date','$Operator','0','$Operator','$DateTime',
	  	       '$Operator','$DateTime')";
	  	       $In_Result = $myPDO->exec($In_Sql);
               $addId = $myPDO->lastInsertId();
               if($addId>0){
	               $Log.="<div class='greenB'>配件ID为:$_StuffId 需求单异动增加成功，请通知主管审核有效！<br></div>";
	               
               }else{
	               $Log.="<div class=redB>配件ID为:$_StuffId 需求单异动添加失败！$In_Sql<br></div>";
	               $OperationResult = "N";
               }
          }
          
         /* $DelSql1="DELETE FROM $DataIn.cg1_addstuff_unite WHERE POrderId=$POrderId  AND Level =1";
	  	  $myPDO->exec($DelSql1);     
          if(count($ExStuffId)>0){
	          $ExStuffIdStr = implode(",", $ExStuffId);
	          $UniteStr = count($Unites)>0?implode(",", $Unites):"";
	          $OldUniteStr = count($oldUnites)>0?implode(",", $oldUnites):"";
	          $In_Sql1="INSERT INTO $DataIn.cg1_addstuff_unite (Id,Level,POrderId,ExStuffIdStr,UniteStr,OldUniteStr,Estate,Locks,
	          Date,Operator, PLocks,creator,created,modifier,modified)VALUES(NULL,'$Level','$POrderId','$ExStuffIdStr',
	          '$UniteStr','$OldUniteStr','1','0','$Date','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
	  	       $myPDO->exec($In_Sql1);
          }*/
          
	  	  $SaveOperationSign=0;
	 break;
	  case "DeliveryDate"://ajax
	     //设置订单采购交期
	   $Log_Funtion.= "采购交期";
       if (strlen($ReduceWeeks)==0) $ReduceWeeks=-1;
       
       $setResult=$myPDO->query("CALL proc_cg1_semifinished_setdeliverydate('$mStockId',NULL,$ReduceWeeks,$Operator);");
       $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
       
       if ($setRow['OperationResult']=="Y"){
	        $Log.="半成品为 $mStockId 更新采购单交货周期成功.<br>"; 
       }
       else{
	        $Log.="<div class=redB>半成品为 $mStockId 更新采购单交货周期失败.</div><br>"; 
       }
	    $SaveOperationSign=1;
	    break;
	


	      
		case 131: //生产类配件置换 需审核
		    
		    $checkResult = $myPDO->query("SELECT Id FROM $DataIn.yw1_stuffchange WHERE StockId = $StockId AND Estate>0 ");
		    $checkRow = $checkResult->fetch(PDO::FETCH_ASSOC);
		    $ChangeId = $checkRow["Id"];
		    if($ChangeId>0){
			    $Log="<div class=redB>流水号为 $StockId 的配件置换失败，有未审核的置换记录.</div><br>";
				$OperationResult="N";
			    
		    }else{
		       
		        $checkResult = null;
			    $InsertStuffSql = "INSERT INTO $DataIn.yw1_stuffchange(Id,POrderId,StockId,OldStuffId,NewStuffId,NewRelation,Remark,
			    Date,Operator, Estate,Locks,PLocks,creator,created,modifier,modified) VALUES(NULL,'$POrderId','$StockId','$OldStuffId',
			    '$ChangeStuffId','$NewRelation','$Remark','$Date','$Operator','1','0','0','$Operator','$DateTime','$Operator','$DateTime')";
			    $InsertStuffResult = $myPDO->exec($InsertStuffSql);
				if ($InsertStuffResult){
					$Log="<div class=greenB>流水号为 $StockId 的配件置换为 $ChangeStuffId  成功，请通知主管审核</div><br>";
					}
				else{
					$Log="<div class=redB>流水号为 $StockId 的配件置换为 $ChangeStuffId 失败.$InsertStuffSql</div><br>";
					$OperationResult="N";
				 }
		    }
		
		  break;	
	      
    }
    
if ($SaveOperationSign==1){
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$myPDO->exec($IN_recode);
}

include "../model/logpage.php";

?>