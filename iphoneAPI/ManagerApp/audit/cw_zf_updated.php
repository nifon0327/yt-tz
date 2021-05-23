<?
    $Log_Item="杂费审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 $ReturnReasons;
    $AuditStr="";
    switch($ActionId)
    {
    case "PASS":
            $AuditStr="Estate=3,Locks=0";
            break;
    case "BACK":
            $AuditStr="ReturnReasons='$ReturnReasons',Estate=1,Locks=1";	  
		 break;   
    }
    
    if ($AuditStr!="")
    {
    $updateSql="UPDATE  $DataIn.ch3_forward SET $AuditStr WHERE Id=$Id";
    $UpdateResult = mysql_query($updateSql,$link_id);
    if($UpdateResult)
     {
         $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
         $OperationResult="Y";
         $infoSTR=$Log_Item . "成功";
        } 
    else{
         $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>"; 
          $infoSTR=$Log_Item . "失败";
        }
    }
?>