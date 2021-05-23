<?
     $Log_Item="助学补助审核"; 
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
   
            $AuditStr="Estate=3,Locks=0";
            break;
    case "BACK":
    $curEstate  = 2;
    
            $AuditStr="Estate=1,Locks=1";
            break;        
    }
    
    if ($AuditStr!="")
    {
    $updateSql="UPDATE $DataIn.cw19_studyfeesheet SET $AuditStr WHERE Id IN ($Id) AND Estate=2";
    $UpdateResult = mysql_query($updateSql,$link_id);
    if($UpdateResult)
     {
	     
	     $insql="INSERT INTO  $DataIn.cw19_studyfee_audit   (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate')";
	     mysql_query($insql,$link_id);
	     
	     
        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
        $OperationResult="Y";
        } 
    else{
        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
        }
    }
?>	