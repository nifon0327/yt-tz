<?
     $Log_Item="客户退款审核"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
    switch($ActionId)
    {
   case "PASS":
            $updateSql="UPDATE $DataIn.cw1_tkoutsheet SET Estate=3 WHERE Id='$Id'";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	            $insql="INSERT INTO  $DataIn.cw1_tkout_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
	            mysql_query($insql,$link_id);

	            
                $Log="<div class=greenB>客户退款($Id)审核成功!</div><br>";
                $OperationResult="Y";
                } 
            else{
                $Log="<div class=redB>客户退款($Id)审核失败! </div><br>$updateSql</br>";   
                }
              break;
    case "BACK":
            $delRecode="DELETE FROM $DataIn.cw1_tkoutsheet WHERE Id='$Id'";
           $delAction = mysql_query($delRecode);
            if($delAction && mysql_affected_rows()>0){
	            
	            $insql="INSERT INTO  $DataIn.cw1_tkout_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate')";
	            mysql_query($insql,$link_id);

                    $Log="<div class=greenB>请款ID在($Id)的客户退款退回成功.</div><br>";
                     $OperationResult="Y";
                    }
            else{
                    $Log="<div class='redB'>请款ID在($Id)的客户退款退回失败.</div>";
                    $OperationResult="N";
                    }
            break;         
    }
 
?>