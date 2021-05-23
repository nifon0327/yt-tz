<?
    $Log_Item="订单锁定审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
    $AuditStr="";
    switch($ActionId)
    {
    case "PASS":
            $AuditStr="Estate=0 ";
            break;
    case "BACK":
            $AuditStr="Estate=2,ReturnReasons='$ReturnReasons' ";
            break;      
    }
    
    if ($AuditStr!="")
    {
	    $updateSql="UPDATE $DataIn.yw2_orderexpress SET $AuditStr WHERE Id=$Id";
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>