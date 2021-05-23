<?
     $Log_Item="异常采单审核"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
   switch($ActionId){
   case "PASS":
            $updateSql="UPDATE $DataIn.cg1_stocksheet SET Estate=0 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	                $Log="<div class=greenB>采购需求单更新($Id)审核成功!</div><br>";
	                $OperationResult="Y";
	                
	                 $insql="INSERT INTO  $DataIn.cg1_abnormal_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
            mysql_query($insql,$link_id);
	                /*
	                if ($DataIn=="d7"){
		                 include "../../admin/swapdata/cg_updatePrice_topt.php";
	                }
	                */
                } 
            else{
                $Log="<div class=redB>采购需求单更新($Id)审核失败! </div><br>$updateSql</br>";   
                }
              break;
              
              
        case "BACK": 
        {
	      
	      
	      
        }
    }
 
?>