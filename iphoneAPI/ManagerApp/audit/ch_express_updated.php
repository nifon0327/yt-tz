<?
    $Log_Item="快递费审核"; 
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
            $AuditStr=" Estate=3,Locks=0 ";
            break;
    case "BACK":
    $curEstate = 2;
            $AuditStr=" Estate=1,Locks=1";
			

            break;      
  
         
    }
    
    if ($AuditStr!="")
    {
		$Multi = explode("-",$Id);
		$Multi0 = $Multi[0];
		$allIds = array();
		if ($Multi0 == 'mult') {
			$typeCondi = "CompanyId=".$Multi[1];
			
			$sqlAll = mysql_query("select Id from $DataIn.ch9_expsheet WHERE $typeCondi and Estate=2");
			
			while ($sRow = mysql_fetch_array($sqlAll)) {
				
				$allIds[]=$sRow['Id'];
			}
			
			$updateSql="UPDATE $DataIn.ch9_expsheet SET $AuditStr WHERE $typeCondi and Estate=2";
		} else {
	    	$updateSql="UPDATE $DataIn.ch9_expsheet SET $AuditStr WHERE Id=$Id";
		}
	   
	    $UpdateResult = mysql_query($updateSql,$link_id);
	    if($UpdateResult)
	     {
		     
		     if (count($allIds) > 0) {
			     
			     foreach ($allIds as $Sid) {
				     $insql="INSERT INTO  $DataIn.ch9_exp_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Sid,$curEstate,$Operator,$Operator,'$DateTime','$curDate')";
	            mysql_query($insql,$link_id);

			     }
		     } else {
			     $insql="INSERT INTO  $DataIn.ch9_exp_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,$curEstate,$Operator,$Operator,'$DateTime','$curDate')";
	            mysql_query($insql,$link_id);

		     }
		     
	        $Log="<div class=greenB>$Id-$Log_Item 成功!</div><br>";
	        $OperationResult="Y";
	        } 
	    else{
	        $Log="<div class=redB>$Id-$Log_Item 失败! </div><br>$updateSql</br>";   
	        }
    }
?>