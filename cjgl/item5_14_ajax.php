<?php   
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="外发领料";			//需处理
$Log_Funtion="数据更新";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){
	case 31://新增领料数据
	     
	    $tempCG_mStockId = array();
	    $ArrId=explode("|",$Id);
	    $ArrQty=explode("|",$Qty);
	    $sLen=count($ArrId); 
	    
	    if (count($ArrQty)==$sLen && $sLen>0){
	   
			for ($i=0;$i<$sLen;$i++){
				//取得ID号
                $tempArray  =  explode("@", $ArrId[$i]);
				$POrderId   =  $tempArray[0];
				$StockId    =  $tempArray[1];
				$mStockId   =  $tempArray[2];
				$StuffId    =  $tempArray[3];
				$llQty      =  $ArrQty[$i];
				
				$checkSResult=$myPDO->query("SELECT sPOrderId  FROM $DataIn.yw1_scsheet WHERE POrderId = '$POrderId' AND Level = 1 ");
                $checkSRow  = $checkSResult->fetch(PDO::FETCH_ASSOC);
                $sPOrderId = $checkSRow["sPOrderId"];
                $checkSResult  = null;
                $checkSRow = null;
				
		        $myResult=$myPDO->query("CALL proc_ck5_llsheet_save('$POrderId',$sPOrderId,'$StockId','$StuffId','$llQty',$Operator,'$fromPage');");
		        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		        $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
			    $myResult=null;
			    $myRow=null;  
			    $tempCG_mStockId[]    = $mStockId;     
		    }	
		     $tempCG_mStockId    = array_unique($tempCG_mStockId); 
		     $cgCount = count($tempCG_mStockId);
		    
		    
	    }
        else{
			$OperationResult="N";	
	    }
	break;
}
include "../basic/quit_pdo.php";

echo $OperationResult;
?>