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
	     $mySql="SELECT COUNT(G.Id) AS Counts,S.StockId FROM $DataIn.cg1_stocksheet S  
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