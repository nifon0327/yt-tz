<?
    $Log_Item="备品转入审核"; 
    $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
     $ReturnReasons;
    $AuditStr="";
    
    switch($ActionId)
    {
    case "PASS":
            $AuditStr="Estate=0";
            break;
    case "BACK":
            $AuditStr="Estate=2";
           
			$returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime,Operator) Values (NULL, '$Id', '$DataIn.ck7_bprk','$ReturnReasons', '$DateTime','$Operator')";
		mysql_query($returnReasonSql);   
		 break;   
    }
    /*
    if ($AuditStr!="")
    {
    $updateSql="UPDATE  $DataIn.ck7_bprk SET $AuditStr WHERE Id=$Id";
    $UpdateResult = mysql_query($updateSql,$link_id);
    if($UpdateResult)
     {
         $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
         $UpSql = "UPDATE   $DataIn.ck7_bprk B LEFT JOIN $DataIn.ck9_stocksheet S ON S.StuffId=B.StuffId SET S.tStockQty=S.tStockQty+B.Qty,S.oStockQty=S.oStockQty+B.Qty WHERE B.Id='$Id' AND B.Estate=0 ";
		$UpResult = mysql_query($UpSql);
			
         $OperationResult="Y";
         $infoSTR=$Log_Item . "成功";
        } 
    else{
         $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>"; 
          $infoSTR=$Log_Item . "失败";
        }
    }
    */
?>