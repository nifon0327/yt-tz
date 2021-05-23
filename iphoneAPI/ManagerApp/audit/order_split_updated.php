<?
 $Log_Item="拆分订单审核"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 
 
 switch($ActionId){
	   case "PASS":
	   		$insql="INSERT INTO  $DataIn.yw10_ordersplit_audit   (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
		    mysql_query($insql,$link_id);
	              $OperationResult="Y";
	              include "../../admin/subprogram/yw_order_split.php";
	            break;
	            
	    case "BACK":
	    	
		    
	            $DelResult="DELETE FROM $DataIn.yw10_ordersplit WHERE Id='$Id'";
		         $DelSql=mysql_query($DelResult);
			    if($DelSql){
				    $insql="INSERT INTO  $DataIn.yw10_ordersplit_audit   (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate')";
				    mysql_query($insql,$link_id);
			       $Log="<div class=greenB>$Id-$Log_Item 退回成功!</div><br>";
	              $OperationResult="Y";
			   }
				else{
				     $Log="<div class=redB>$Id-$Log_Item 退回失败! </div><br>$DelResult</br>";   
					 $OperationResult="N";
				}
	            break; 
 }
 
?>