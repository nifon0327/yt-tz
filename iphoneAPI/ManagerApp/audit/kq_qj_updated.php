<?
    $Log_Item="请假审核"; 
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
            $AuditStr="Estate=0,Locks=0,Checker='$Operator'";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr="Estate=2,Locks=1,Checker='$Operator'";
            break;      
    }
    
    if ($AuditStr!="")
    {
    $updateSql="UPDATE  $DataPublic.kqqjsheet SET $AuditStr WHERE Id=$Id";
    $UpdateResult = mysql_query($updateSql,$link_id);
    if($UpdateResult)
     {
	     
	     $insql="INSERT INTO  $DataPublic.kqqj_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate')";
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