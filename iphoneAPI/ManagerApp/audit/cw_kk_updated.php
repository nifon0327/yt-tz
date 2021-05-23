<?
    $Log_Item="扣款审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 $ReturnReasons;
    $AuditStr="";
    
    $curEstate = 0;
    switch($ActionId)
    {
    case "PASS":
    $curEstate = 1;
    $ReturnReasons = '';
            $AuditStr="Estate=0,Locks=0";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr="Estate=1,Locks=1";
           
			$returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$DataIn.cw15_gyskkmain','$ReturnReasons', '$DateTime')";
		mysql_query($returnReasonSql);   
		 break;   
    }
    
    if ($AuditStr!="")
    {
    $updateSql="UPDATE  $DataIn.cw15_gyskkmain SET $AuditStr WHERE Id=$Id";
    $UpdateResult = mysql_query($updateSql,$link_id);
    if($UpdateResult)
     {
	     $insql="INSERT INTO  $DataIn.cw15_gyskk_audit (Mid,Estate,Checker,creator,created,Date,Reason) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate','$ReturnReasons')";
		         mysql_query($insql,$link_id);
		         
		         
		         
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