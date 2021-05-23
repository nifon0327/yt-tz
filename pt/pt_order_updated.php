<?php
    $MyPDOEnabled=1;
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="半成品订单";		//需处理
	$upDataSheet="$DataIn.yw1_scsheet";	//需处理
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
	  case 23:		//新加需求单,需更新订单的状态
		    include "../model/subprogram/FireFox_Safari_PassVar.php";
		    $Level=$Level==""?2:$Level+1;   
		    for ($i=0;$i<count($StuffId);$i++){
			     
			   $_StuffId=$StuffId[$i];
			   $_Relation=$pandsQty[$i];
			   $_Unite=$Unite[$i];
			    //echo "$POrderId,'$_StuffId','0','$_Relation','$Level',$Operator";
			    $myResult=$myPDO->query("CALL proc_cg1_stocksheet_add($POrderId,'$_StuffId','0','$_Relation','$Level',$Operator);");
			
		        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		    
			    $OperationResult = $myRow['OperationResult'];
			    
			    $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>"; 
	            
	            $NewStockId=$myRow['NewStockId'];
	            
	            $myResult=null;
	            
	           if ($OperationResult=="Y"){
	           
	                $inSql="INSERT INTO cg1_semifinished(POrderId,mStockId,mStuffId,StockId,StuffId,Relation,OrderQty,Date,Operator) 
	                SELECT $POrderId,$mStockId,$mStuffId,StockId,StuffId,$_Relation,OrderQty,CURDATE(),$Operator 
	                FROM cg1_stocksheet WHERE StockId=$NewStockId ";
	                $myPDO->exec($inSql);
	            
		            if ($OperationResult=="Y" && strlen($_Unite)>0){
			            
			             $uniteResult=$myPDO->query("CALL proc_cg1_stuffunite_add($POrderId,'$_StuffId','$_Unite',$Operator);");
			             $uniteRow = $uniteResult->fetch(PDO::FETCH_ASSOC);
				         $uOperationResult = $uniteRow['OperationResult'];
				         $Log.=$uOperationResult=="Y"?"$_StuffId -配件的关联表保存成功!<br>":"<div class=redB>" .$_StuffId . " -配件的关联表保存失败!</div><br>";
				         $uOperationResult=null;
		            } 
	            } 
	  	  }
	  	  //旧bom新设的关联
	  	  for ($i=0;$i<count($ExStuffId);$i++){
	  	     $_Unite=$Unites[$i];
	  	     $_oldUnite=$oldUnites[$i];
	  	     
	  	     if ($_oldUnite!=$_Unite){
	  	         $_StuffId=$ExStuffId[$i];
	  	         if ($_oldUnite!=""){
		  	        $delSql="DELETE FROM $DataIn.cg1_stuffunite WHERE POrderId=$POrderId AND StuffId=$_StuffId";
	  	            $myPDO->exec($delSql); 
	  	         }
	  	         
	  	         $UniteArray=explode(",", $_Unite);
	  	         $IN_recode="";
	  	         for ($n=0;$n<count($UniteArray);$n++){
	  	               $UniteId=$UniteArray[$n];
	  	               
					   if ($UniteId>0){
					      $IN_recode.=$IN_recode==""?"":",";
						  $IN_recode.="(NULL,'$POrderId','$ProductId','$_StuffId','$UniteId',0,'$Date','$Operator')";
						     
					   }     
	  	         }
	  	         if ($IN_recode!="") {
	  	              $IN_recode="INSERT INTO $DataIn.cg1_stuffunite (Id,POrderId,ProductId,StuffId,uStuffId,Relation,Date,Operator) VALUES $IN_recode";
	  	              //echo $IN_recode;
	  	              $myPDO->exec($IN_recode);
	  	         }
	  	         $Log.="已更新 $_StuffId 的关联配件<br>";
	  	     }
	  	  }
	  	  $SaveOperationSign=0;
	 break;
	 case "DeliveryDate"://ajax
		     //设置订单采购交期
	       if (strlen($ReduceWeeks)==0) $ReduceWeeks=-1;
	       
	       $setResult=$myPDO->query("CALL proc_cg1_semifinished_setdeliverydate('$mStockId',NULL,$ReduceWeeks,$Operator);");
	       $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
	       
	       if ($setRow['OperationResult']=="Y"){
		        $Log.="半成品为 $mStockId 更新采购单交货周期成功.<br>"; 
	       }
	       else{
		        $Log.="<div class=redB>半成品为 $mStockId 更新采购单交货周期失败.</div><br>"; 
	       }
	        $setResult=null; 
		    $SaveOperationSign=1;
		break;
	 case 26://重置记录
	     $mySql="SELECT COUNT(*) AS Counts,S.StockId FROM $DataIn.cg1_stocksheet S  
	            LEFT JOIN $DataIn.cg1_semifinished G ON G.mStockId=S.StockId WHERE S.Id='$Id'";
	     $myResult=$myPDO->query($mySql); 
	     $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	     
	     $mStockId=$myRow['StockId'];
	     if ($myRow['Counts']>0){
		     $Log.="<div class=redB>半成品为 $mStockId 需求BOM已存在,不能重置记录.</div><br>"; 
	     }
	     else{
	       $setResult=$myPDO->query("CALL proc_cg1_stocksheet_reset('$mStockId',$Operator);");
	       $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
	       
	       if ($setRow['OperationResult']=="Y"){
		        $Log.="半成品为 $mStockId 重置记录成功.<br>"; 
	       }
	       else{
		        $Log.="<div class=redB>半成品为 $mStockId 重置记录失败.</div><br>"; 
	       }
		   $setResult=null;  
	     }
	     
	     $SaveOperationSign=1;
	    break;	
    }

if ($SaveOperationSign==1){
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$myPDO->exec($IN_recode);
}

include "../model/logpage.php";

?>