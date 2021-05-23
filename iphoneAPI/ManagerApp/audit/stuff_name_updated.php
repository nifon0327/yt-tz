<?
 $Log_Item="配件名称审核"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 $AuditStr="";
 switch($ActionId){
    case "PASS":
            $updateSql="UPDATE $DataIn.stuffdata SET Estate=1 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	            
	            $insql="INSERT INTO  $DataIn.stuffdata_audit   (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
			    mysql_query($insql,$link_id);
			    
			    
	                $Log="<div class=greenB>配件名称($Id)审核成功!</div><br>";
	                $OperationResult="Y";
                } 
            else{
                $Log="<div class=redB>配件名称($Id)审核失败! </div><br>$updateSql</br>";   
                }
              break;
    case "BACK":
	    //先改变配件状态
		$updateSql = "update $DataIn.stuffdata set Estate = '3' Where Id = '$Id'";
		$UpdateResult = mysql_query($updateSql,$link_id);
		
		 if($UpdateResult){
			 
			 
			$insql="INSERT INTO  $DataIn.stuffdata_audit   (Sid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
		    mysql_query($insql,$link_id);
			    
			    
	                $Log="<div class=greenB>配件名称($Id)退回成功!</div><br>";
	                $OperationResult="Y";
	                //再作废更改记录
					$upSql = "UPDATE $DataIn.stuffchange C SET C.Estate=0 WHERE NOT EXISTS (SELECT StuffId FROM $DataIn.stuffdata S WHERE C.StuffId=S.StuffId AND S.Estate=2)";
					$upResult = mysql_query($upSql);
					
					//写入退回原因
					$returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$DataIn.stuffdata','$ReturnReasons', '$DateTime')";
					mysql_query($returnReasonSql);
                } 
            else{
                $Log="<div class=redB>配件名称($Id)退回失败! </div><br>$updateSql</br>";   
                }
		
		
        break;
}
?>