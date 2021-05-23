<?
    $Log_Item="车辆费用审核"; 
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
            $AuditStr=" ReturnReasons='',Estate=3,Auditor='$Operator',Locks=0 ";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr="ReturnReasons='$ReturnReasons',Auditor='$Operator',Estate=1,Locks=1";
            break;      
  
         
    }
    
    if ($AuditStr!="")
    {
	    $updateSql="UPDATE $DataIn.carfee SET $AuditStr WHERE Id=$Id";
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
		      $insql="INSERT INTO  $DataIn.carfee_audit  (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate')";
		         mysql_query($insql,$link_id);
		         
		         
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>