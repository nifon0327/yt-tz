<?
    $Log_Item="配件锁定审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
    $AuditStr="";
    $curEstate = 0;
    switch($ActionId)
    {
    case "PASS":
    $curEstate = 1;
    $ReturnReasons = '';
            $AuditStr="Estate=0 ";
            break;
    case "BACK":
    $curEstate = 2;
    
            $AuditStr="Estate=2,ReturnReasons='$ReturnReasons' ";
            break;      
    }
    
    if ($AuditStr!="")
    {
	    $updateSql="UPDATE $DataIn.cg1_lockstock SET $AuditStr WHERE Id=$Id";
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
		     
		     $insql="INSERT INTO  $DataIn.cg1_lockstock_audit    (Sid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
		    mysql_query($insql,$link_id);
		    
		    
		     
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>