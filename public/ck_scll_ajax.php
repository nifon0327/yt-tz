<?php   

session_start();
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$DateTemp = date("Ymd");
$Operator=$Login_P_Number;
$OperationResult = "N";

/*if($diffQty>0){  //补料
	$NextllQty = $diffQty ;
	$CheckResult = mysql_query("SELECT  Id ,(Qty-llQty) AS lastQty,Price FROM ck1_rksheet  WHERE  StuffId = $StuffId AND Qty>llQty ORDER BY Id ASC",$link_id);
	while($CheckRow = mysql_fetch_array($CheckResult)){
		
		$RkId = $CheckRow["Id"];
		$lastQty = $CheckRow["lastQty"];
		$Price = $CheckRow["Price"];
		if($NextllQty<=0){
		     break;
	     }
	    if($lastQty>$diffQty){
		     $llQty  = $diffQty;
		     $llSign = 2;
		     $NextllQty = 0 ;
		    
	     }else{   
		     $llQty = $lastQty;
		     $llSign = 0 ;
		     $NextllQty = $diffQty - $llQty;
	     }
	     
	    
	     
	     $InSql = "INSERT INTO ck5_llsheet (POrderId, sPOrderId, StockId, StuffId, Price, Qty, ComboxSign,Type, FromId, FromFunction, RkId, Locks, Estate, Date, Operator, Receiver, Received, PLocks, creator, created) 
                     VALUES ('$POrderId','$sPOrderId','$StockId','$StuffId','$Price','$llQty',0,'1','0','工单领料','$RkId','0',0,'$DateTime','$Operator','0','0000-00-00','0',$Operator,'$DateTime')";
         $InResult = mysql_query($InSql);
         if($InResult && mysql_affected_rows()>0){
             $OperationResult="Y";
	         $UpdateSql = "UPDATE  ck1_rksheet  SET llQty = llQty + $llQty ,llSign = $llSign  WHERE Id = $RkId";
	         $UpdateResult = mysql_query($UpdateSql);
         }        
		
	}
	echo $OperationResult;
	
}else{ //多领
	
	$updateQty = -$diffQty;
	
	$CheckRow = mysql_fetch_array(mysql_query("SELECT Id,RkId FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Qty= $updateQty LIMIT 1  ",$link_id));
	$llId = $CheckRow["Id"];
	$RkId = $CheckRow["RkId"];
	if($llId>0){
	         $DelSql = "DELETE FROM  $DataIn.ck5_llsheet  WHERE Id = '$llId'";
			 $DelResult = mysql_query($DelSql);
			 if($DelResult && mysql_affected_rows()>0){
			      echo "Y";
			      $updateSql = "UPDATE ck1_rksheet SET llQty =llQty -$updateQty ,llSign='2'  WHERE Id = '$RkId'";
			      $updateResult = mysql_query($updateSql);
			  }
		
	}else{
	
		$CheckRow = mysql_fetch_array(mysql_query("SELECT Id,RkId FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Qty> $updateQty LIMIT 1  ",$link_id));
		 $llId = $CheckRow["Id"];
		 $RkId = $CheckRow["RkId"];
		 if($llId>0){
			 $UpdateSql = "UPDATE $DataIn.ck5_llsheet SET Qty = Qty-$updateQty WHERE Id = '$llId'";
			 $UpdateResult = mysql_query($UpdateSql);
			 if($UpdateResult && mysql_affected_rows()>0){
			      echo "Y";
			      $updateSql = "UPDATE ck1_rksheet SET llQty =llQty -$updateQty ,llSign='2'  WHERE Id = '$RkId'";
			      $updateResult = mysql_query($updateSql);
			  }
		   }
	}
	  
}*/






?>