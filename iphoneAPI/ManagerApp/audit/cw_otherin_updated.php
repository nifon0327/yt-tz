<?
    $Log_Item="其他收入审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
    $AuditStr="";
    $curEstate = 0;
    switch($ActionId)
    {
    case "PASS":
    $ReturnReasons = '';
    $curEstate = 1;
            $AuditStr="Estate=3,Locks=0,Auditor='$Operator'";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr="Estate=2,Locks=1,ReturnReasons='$ReturnReasons',Auditor='$Operator'";
            break;      
    }
    
    if ($AuditStr!="")
    {
	    $updateSql="UPDATE $DataIn.cw4_otherin SET $AuditStr WHERE Id=$Id";
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
		     
		     
		     $insql="INSERT INTO  $DataIn.cw4_otherin_audit (Mid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
         mysql_query($insql,$link_id);
	     
	     
	     
	     
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>