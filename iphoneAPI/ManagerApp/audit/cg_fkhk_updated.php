<?
     $Log_Item="货款返利审核"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
    switch($ActionId)
    {
   case "PASS":
            $updateSql="UPDATE $DataIn.cw2_hksheet SET Estate=3,Locks=0 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	            
	            $insql="INSERT INTO  $DataIn.cw2_hk_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
         mysql_query($insql,$link_id);
         
         
                $Log="<div class=greenB>$Log_Item($Id)审核成功!</div><br>";
                $OperationResult="Y";
                } 
            else{
                $Log="<div class=redB>$Log_Item($Id)审核失败! </div><br>$updateSql</br>";   
                }
              break;
    case "BACK":
             $updateSql="UPDATE $DataIn.cw2_hksheet SET Estate=1,Locks=1 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	             $insql="INSERT INTO  $DataIn.cw2_hk_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate')";
         mysql_query($insql,$link_id);
                $Log="<div class=greenB>$Log_Item($Id)退回成功!</div><br>";
                $OperationResult="Y";
                } 
            else{
                $Log="<div class=redB>$Log_Item($Id)退回失败! </div><br>$updateSql</br>";   
                }
              break;
            break;         
    }
 
?>