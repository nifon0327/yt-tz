<?php 
 $Log_Item="补货记录";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 include "ck_function.php";
 
 switch($ActionId){
          case "BL":
               $Id=$info[0];
               $Log_Funtion="备料保存";
               
               $inSql = "INSERT INTO $DataIn.yw9_blmain (Id, Estate, Locks, Date, Operator) VALUES (NULL, '1', '0', '$DateTime', '$Operator')";
			   $inResult = mysql_query($inSql,$link_id);
			   $Pid=mysql_insert_id();
               if ($inResult && $Pid>0){
                   if($DataIn=="ac"){
                          $inSql2="INSERT INTO $DataIn.ck5_llsheet SELECT NULL,'$Pid','0',POrderId,StockId,StuffId,Qty,'1','0' ,'0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator'
                                          FROM $DataIn.ck13_replenish WHERE Id='$Id'";
                    }else{
                        $inSql2="INSERT INTO $DataIn.ck5_llsheet SELECT NULL,'$Pid','0',POrderId,StockId,StuffId,Qty,'1','0' FROM $DataIn.ck13_replenish WHERE Id='$Id'";
                     }
                        $inResult2 = mysql_query($inSql2,$link_id);
                        $Lid=mysql_insert_id();
                        if ($inResult2 && $Lid>0){
                             $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                             $OperationResult="Y";
                             $infoSTR=$Log_Funtion ."成功";
                             
                             $updateSql="UPDATE $DataIn.ck13_replenish S 
											LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
											SET S.Lid='$Lid',K.tStockQty=K.tStockQty-S.Qty  
											WHERE S.Id='$Id'";
	                         $updateResult = mysql_query($updateSql,$link_id);
                        }
                        
                        
                } 
              if ($OperationResult=="N"){
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;  
            
          case "LL":
               $Id=$info[0];
               $Log_Funtion="领料确认"; 
               $updateSql="UPDATE $DataIn.ck13_replenish S 
											LEFT JOIN $DataIn.ck5_llsheet  L ON L.Id=S.Lid 
											SET S.Estate=0,L.Estate=0  
											WHERE S.Id='$Id'";
	           $updateResult = mysql_query($updateSql,$link_id);
               if (mysql_affected_rows($link_id)>0){
	                 $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                 $OperationResult="Y";
	                 $infoSTR=$Log_Funtion ."成功";
               }
               else{
	               $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                    $infoSTR=$Log_Funtion ."失败";
               }
               break;
             
             
             
           case "Modify": {
	            $Id=$info[0];$Number = $info[1]; $Remark=$info[2];
	             $Log_Funtion="补料单修改"; 
	             
	            
	            $updateSql="UPDATE $DataIn.ck13_replenish S 
																						SET S.Qty=$Number,S.Remark='$Remark' ,S.Estate=2 
											WHERE S.Id='$Id' and S.Estate in (2,3)";
	           $updateResult = mysql_query($updateSql,$link_id);
               if (mysql_affected_rows($link_id)>0){
	                 $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                 $OperationResult="Y";
	                 $infoSTR=$Log_Funtion ."成功";
               }
               else {
	               $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                    $infoSTR=$Log_Funtion ."失败";
               }

	            
           }
           break;
           
           
           case "Delete": {
	            $Id=$info[0];
	             $Log_Funtion="补料单删除"; 
	            
	            $updateSql="delete from  $DataIn.ck13_replenish 	WHERE Id='$Id' and Estate in (2,3)";
	           $updateResult = mysql_query($updateSql,$link_id);
	           
               if (mysql_affected_rows($link_id)>0){
	                 $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                 $OperationResult="Y";
	                 $infoSTR=$Log_Funtion ."成功";
               }
               else{
                //$OperationResult="N";
	               $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                    $infoSTR=$Log_Funtion ."失败";
               }

	            

	           
           }
           break;
           
  }
  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>