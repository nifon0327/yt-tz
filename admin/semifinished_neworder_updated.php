<?php
    $MyPDOEnabled=1;
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="半成品新品订单";		//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$SaveOperationSign=1;
	$ALType="From=$From&Pagination=$Pagination&Page=$Page";
	//步骤3：需处理，更新操作
	$x=1;
	switch($ActionId){
		
	 case 26://重置记录
	     $mySql="SELECT COUNT(G.Id) AS Counts,S.StockId,IFNULL(V.ReduceWeeks,0) AS ReduceWeeks
	             FROM $DataIn.cg1_stocksheet S  
	             LEFT JOIN $DataIn.cg1_semifinished G ON G.mStockId=S.StockId 
	             LEFT JOIN $DataIn.semifinished_deliverydate V ON V.mStuffId=S.StuffId 
	             WHERE S.Id='$Id'";
	     $myResult=$myPDO->query($mySql); 
	     $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	     $ReduceWeeks=$myRow['ReduceWeeks'];
	     $mStockId=$myRow['StockId'];
	     if ($myRow['Counts']>0){
		     $Log.="<div class=redB>半成品为 $mStockId 需求BOM已存在,不能重置记录.</div><br>"; 
	     }
	     else{
	       $setResult=$myPDO->query("CALL proc_cg1_stocksheet_reset('$mStockId',$Operator);");
	       $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
	       
	       if ($setRow['OperationResult']=="Y"){
		        $Log.="半成品为 $mStockId 重置记录成功.<br>"; 
		        
		           $setResult=null; 
		           $setRow=null;
		           $setResult=$myPDO->query("CALL proc_cg1_semifinished_setdeliverydate('$mStockId',NULL,$ReduceWeeks,$Operator);");
			       $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
			       if ($setRow['OperationResult']=="Y"){
				        $Log.="半成品为 $mStockId 更新采购单交货周期成功.<br>"; 
			       }
			       else{
				        $Log.="<div class=redB>半成品为 $mStockId 更新采购单交货周期失败.</div><br>"; 
			       }
			       
			        $setResult=null; 
		            $setRow=null;
		           
			        $checkResult=$myPDO->query("CALL proc_cg1_stocksheet_check($mStockId,$Operator);");
		            $checkRow = $checkResult->fetch(PDO::FETCH_ASSOC);
		            
			        $Log.=$checkRow['OperationResult']=="Y"?$checkRow['OperationLog'] . "<br>":"<div class=redB>" .$checkRow['OperationLog'] . "</div><br>";         
			        $checkResult=null;
	       }
	       else{
		        $Log.="<div class=redB>半成品为 $mStockId 重置记录失败.</div><br>"; 
	       }
		   $setResult=null;  
	     } 
	     $SaveOperationSign=1;
	    break;	
	    
	    case "ResetRemark":
	        $checkStuffResult = $myPDO->query("SELECT Id FROM $DataIn.cg1_reset_remark WHERE StockId = $StockId");
	        $checkStuffRow = $checkStuffResult->fetch(PDO::FETCH_ASSOC);
		    $Id = $checkStuffRow["Id"];
		    $checkStuffResult = null;
		    $checkStuffRow = null;
		    if($Id>0){
			    $sql = "UPDATE $DataIn.cg1_reset_remark SET Remark='$NewRemark',modifier='$Operator',modified='$DateTime' 
			    WHERE StockId='$StockId'";
			    $result = $myPDO->exec($sql);  
		    }else{
			    $sql = "INSERT INTO $DataIn.cg1_reset_remark(Id,StockId,Remark,Estate,Locks,Date,Operator,PLocks,creator,
			    created,modifier,modified) VALUES(NULL,'$StockId','$NewRemark','1','0','$Date','$Operator','0','$Operator',
			    '$DateTime','$Operator','$DateTime')";
			    $result = $myPDO->exec($sql);
				echo $sql;
		    }
	    break; 
	    
    }

if ($SaveOperationSign==1){
	  $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$myPDO->exec($IN_recode);
}

include "../model/logpage.php";

?>