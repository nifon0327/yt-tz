<?
     $Log_Item="采购请款审核"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
    switch($ActionId)
    {
   case "PASS":
            $updateSql="UPDATE $DataIn.cw1_fkoutsheet SET Estate=3 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	            $insql="INSERT INTO  $DataIn.cw1_fkout_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
         mysql_query($insql,$link_id);
                $Log="<div class=greenB>采购单请款($Id)审核成功!</div><br>";
                $OperationResult="Y";
                } 
            else{
                $Log="<div class=redB>采购单请款($Id)审核失败! </div><br>$updateSql</br>";   
                }
              break;
    case "BACK":
    //
    
     
            $delRecode="DELETE FROM $DataIn.cw1_fkoutsheet WHERE Id=$Id";
           $delAction = mysql_query($delRecode);
            if($delAction && mysql_affected_rows()>0){
	            
	            
	            $insql="INSERT INTO  $DataIn.cw1_fkout_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate')";
         mysql_query($insql,$link_id);
                    $Log="<div class=greenB>请款ID在($Id)的需求单请款退回成功.</div><br>";
                     $OperationResult="Y";
                    //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw1_fkoutsheet");
                    }
            else{
                    $Log="<div class='redB'>请款ID在($Id)的需求单请款退回失败.</div>";
                    $OperationResult="N";
                    }
            break;         
    }
 
?>