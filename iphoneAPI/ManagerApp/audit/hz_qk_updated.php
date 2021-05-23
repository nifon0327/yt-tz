<?
    $Log_Item="行政费用审核"; 
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
            $AuditStr="Estate=3,Locks=0,Auditor='$Operator'";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr="Estate=1,Locks=1,ReturnReasons='$ReturnReasons',Auditor='$Operator'";
            break;      
    }
    
    if ($AuditStr!="")
    {
	    $updateSql="UPDATE $DataIn.hzqksheet SET $AuditStr WHERE Id=$Id";
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
		     
		     $insql="INSERT INTO  $DataIn.hzqk_audit (Sid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
         mysql_query($insql,$link_id);
         
         
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>