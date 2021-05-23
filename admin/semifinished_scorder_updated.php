<?php
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="工单";		//需处理
	$upDataSheet="$DataIn.yw1_scsheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$ALType="From=$From&Pagination=$Pagination&Page=$Page";
	//步骤3：需处理，更新操作
	$x=1;
	switch($ActionId){

		case "Remark":
			$Log_Funtion="更新Remark";
			$sql = "UPDATE $upDataSheet SET Remark='$tempRemark' WHERE sPOrderId='$sPOrderId'";
			$result = mysql_query($sql);
			if ($result){
				$Log="工单流水号为 $sPOrderId Remark更新成功. $sql <br>";
				}
			else{
				$Log="<div class=redB>工单流水号为 $sPOrderId Remark更新失败. $sql </div><br>";
				$OperationResult="N";
				}
				
		 break;
		 
		case "WorkShopId":
			$Log_Funtion="更新WorkShopId";
			$sql = "UPDATE $upDataSheet SET WorkShopId='$tempWorkShopId' WHERE sPOrderId='$sPOrderId'";
			$result = mysql_query($sql);
			if ($result){
				$Log="工单流水号为 $sPOrderId WorkShopId更新成功. $sql <br>";
				}
			else{
				$Log="<div class=redB>工单流水号为 $sPOrderId WorkShopId 更新失败. $sql </div><br>";
				$OperationResult="N";
				}
	
	
	     case "176":
	        
	        $myLock = 0;
			$Log_Funtion="工单锁定";
			$count_Temp=mysql_query("SELECT count( * ) AS counts FROM $DataIn.yw1_sclock WHERE sPOrderId='$sPOrderId' ",$link_id);  
			$counts=mysql_result($count_Temp,0,"counts");
			if ($counts<1){ 
				$inRecode="INSERT INTO $DataIn.yw1_sclock (Id,sPOrderId,Estate,Locks,Remark,Date,Operator) VALUES (NULL,'$sPOrderId','1','$myLock','$LockRemark','$Date','$Operator') ";
				$inResult=mysql_query($inRecode);
	
			}
			else{
			    $LockRemarkSTR=$LockRemark!=""?",Remark='$LockRemark'":"";
				$inRecode = "UPDATE $DataIn.yw1_sclock  SET Locks='$myLock',Estate=1,Date='$Date',Operator='$Operator' $LockRemarkSTR WHERE sPOrderId='$sPOrderId'";
				$inResult = mysql_query($inRecode);
			
			}
			$Log="$sPOrderId 锁定！";
				
				
		 break;
		 
		  case "177":
	        
	        $myLock = 0;
			$Log_Funtion="工单解锁";
			
			$Lens=count($checkid);
            for($i=0;$i<$Lens;$i++){
	            $Id = $checkid[$i];
	            
	            
				$count_Row=mysql_fetch_array(mysql_query("SELECT count( * ) AS counts,Y.sPOrderId FROM $DataIn.yw1_sclock L 
				LEFT JOIN $DataIn.yw1_scsheet Y ON Y.sPOrderId = L.sPOrderId 
				WHERE Y.Id='$Id' ",$link_id)); 
				
			    $counts=$count_Row["counts"];
			    $sPOrderId=$count_Row["sPOrderId"];
				if ($counts>0){ 
					$inRecode = "UPDATE $DataIn.yw1_sclock  SET Locks='1',Estate=1,Date='$Date',Operator='$Operator', Remark='' WHERE sPOrderId='$sPOrderId'";
					$inResult = mysql_query($inRecode);
		           echo $inRecode;
		           $Log="$sPOrderId 解锁成功！";
				}
			
				
			}
		 break;
		 
		 
    }
    $ALType="From=$From&Pagination=$Pagination&Page=$Page&OrderAction=$OrderAction";
    
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";

?>