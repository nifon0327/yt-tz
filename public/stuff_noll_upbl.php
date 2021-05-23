<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="已出订单未领料配件自动领料";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	
	case 90:	
	    $StuffId = $Id;
	    $checkResult  = mysql_query("SELECT * FROM (
		SELECT Y.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,K.tStockQty  
		FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
		LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = G.StockId 
		LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
		WHERE G.StuffId = '$StuffId' AND G.OrderQty>0 AND Y.Estate=0 GROUP BY G.StockId 
		UNION ALL
		SELECT Y.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,K.tStockQty   
		FROM $DataIn.cg1_stuffcombox G 
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId 
		LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId = G.StockId 
		LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
		WHERE G.StuffId = '$StuffId' AND G.OrderQty>0 AND Y.Estate=0 GROUP BY G.StockId ) A 
		WHERE A.llQty<A.OrderQty ORDER BY A.OrderQty ASC",$link_id);
		$tempK = 1;  $tempQty = 0 ;  $lastOrderQty = 0;
		while($checkRow = mysql_fetch_array($checkResult)){
		    $POrderId=$checkRow["POrderId"];
			$tStockQty=$checkRow["tStockQty"];
			$StockId=$checkRow["StockId"];
			$OrderQty=$checkRow["OrderQty"];
			$llQty=$checkRow["llQty"];
			if($tempK==1){
				$tempQty = $tStockQty;
			}else{
				$tempQty = $tempQty-$lastOrderQty;
			}
		   $thisllQty = 0 ;
		   if($tempQty>0){
		     if($tempQty>=$OrderQty){
			     $thisllQty = $OrderQty;
		     }else{
			     $thisllQty = $tempQty;
		     }  
		  }else{
			  $thisllQty = 0 ;
			  break;
		  }
		  
		  if($thisllQty>0){
			  
			   $CheckPidRow = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw9_blmain WHERE DATE_FORMAT(Date,'%Y-%m-%d')='$Date'",$link_id));
			   $Pid  = $CheckPidRow["Id"];
			   if($Pid==""){
				   $blinRecode="INSERT INTO $DataIn.yw9_blmain (Id,Estate,Locks,Date,Operator) VALUES (NULL,'1','0','$DateTime','$Operator')";
				   $blinAction=@mysql_query($blinRecode);
				   $Pid=mysql_insert_id();
				}
			 
	           if($Pid!=0 && $Pid!=""){
					  //生成领料明细数据 
					 $llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,POrderId,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','0','$POrderId','$StockId','$StuffId','$thisllQty','0','1')";
				     $llInAction=@mysql_query($llInSql);
					 $signUpSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$thisllQty  WHERE StuffId='$StuffId' AND tStockQty>=$thisllQty";
					 $signUpResult = mysql_query($signUpSql);
				     $Log.="$StockId 订单数量：$OrderQty 补料数量：$thisllQty 成功!<br>";
		           }
			   else{
					   $Log.="<div class='redB'>生成领料单失败!</div><br>";
					   $OperationResult="N";
			      }
			    $lastOrderQty = $thisllQty;
			    $tempK++;
		   } 
       }
	break;

	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>