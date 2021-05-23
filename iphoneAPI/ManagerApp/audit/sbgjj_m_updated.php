<?
    $Log_Item="保险审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
 $curEstate = 0;
 
    $AuditStr="";
    switch($ActionId)
    {
    case "PASS":
    $curEstate = 1;
    $ReturnReasons = '';
    
            $AuditStr="Estate=3,Locks=0";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr="Estate=1,Locks=1 ";
						$returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$DataIn.sbpaysheet','$ReturnReasons', '$DateTime')";
		mysql_query($returnReasonSql); 
            break;      
    }
    
    if ($AuditStr!="")
    {
		$Multi = explode("-",$Id);
		$Multi0 = $Multi[0];
		
		
		$allids = array();
		
		
		if ($Multi0 == 'multi') {
			$typeCondi = "typeId=".$Multi[1];
			
			$findIds = mysql_query("select Id from  $DataIn.sbpaysheet  $typeCondi and Estate=2");
			while ($aRow = mysql_fetch_array($findIds)) {
				
				$allids[]=$aRow['Id'];
			}
			
			$updateSql="UPDATE $DataIn.sbpaysheet SET $AuditStr WHERE $typeCondi and Estate=2";
		} else {
	    	$updateSql="UPDATE $DataIn.sbpaysheet SET $AuditStr WHERE Id=$Id";
		}
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
		     
		     if (count($allids) > 0) {
			     foreach ($allids as $Sid) {
				     
				     $insql="INSERT INTO  $DataIn.sbpay_audit   (Sid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Sid,$curEstate,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
					mysql_query($insql,$link_id);

				     
			     }
			     
			     
		     } else {
			     $insql="INSERT INTO  $DataIn.sbpay_audit   (Sid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
			     mysql_query($insql,$link_id);
		     }
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>