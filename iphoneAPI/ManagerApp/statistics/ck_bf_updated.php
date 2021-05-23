<?php 
 $Log_Item="配件报废";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 switch($ActionId){
	 
	 	   case "DEL":
         {
	         $Id=$info[0];
	             $Log_Funtion="报废删除"; 
	             
	            
	            $updateSql="delete from  $DataIn.ck8_bfsheet 
											WHERE Id='$Id' and Estate=2";
	           $updateResult = mysql_query($updateSql,$link_id);
               if (mysql_affected_rows($link_id)>0){
	               
	               unlink("../../download/ckbf/B"."$Id".".jpg");
	               unlink("../../download/ckbf/B"."$Id"."_thumb.jpg");
	                 $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                 $OperationResult="Y";
	                  mysql_query("delete from returnreason where targetTable='ac.ck8_bfsheet' and tableId=$Id",$link_id);
	                 
	                 $infoSTR=$Log_Funtion ."成功";
               }
               else {
	               $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                    $infoSTR=$Log_Funtion ."失败";
               }


	         
         }
                        break;

          case "ESTATE":
                 $Log_Funtion="处理";
                 $Id=$info[0];  $StuffId=$info[1]; $Remark=$info[2];
                 $upRecode="UPDATE $DataIn.ck8_bfsheet  SET Estate=0,Locks=0 WHERE  Id='$Id'";
                 $upAction=@mysql_query($upRecode);
                 if ($upAction){ 
	                 $inRecode="INSERT INTO $DataIn.ck8_bfremark(Id,Mid,StuffId,Remark,Estate,Date,Operator) VALUES (NULL,'$Id','$StuffId','$Remark','0','$DateTime','$Operator')";
	                 $inAction=@mysql_query($inRecode);
	                 $Ids=$Id;
	                  include "../subpush/ck_bf_push.php";
                    $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                    $OperationResult="Y";
                    $infoSTR=$Log_Funtion ."成功";
	                } 
	                else{
	                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	                        $infoSTR=$Log_Funtion ."失败";
	           }
            break;  
         case "UPDATE":
             $Id=$info[0];$Reason=$info[1];
             $Log_Funtion="保存";
             $Delstuff="DELETE FROM $DataIn.stuffdisable WHERE StuffId='$Id'";
	         $DelResult=@mysql_query($Delstuff);
             $InSql="INSERT INTO $DataIn.stuffdisable  (Id,StuffId,Reason,Date,Operator) VALUES  (NULL,'$Id','$Reason','$curDate','$Operator')";
	        $InRecode=mysql_query($InSql);
	         if ($InRecode){
                 $upRecode="UPDATE $DataIn.stuffdata  SET Estate=0,Locks=0 WHERE  StuffId='$Id'";
                 $upAction=@mysql_query($upRecode);
                 if ($upAction){ 
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>$upRecode ";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $upRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
              }
              else{
	                $Log="<div class=redB>$Log_Item $Log_Funtion 失败!</div><br>";
                    $infoSTR=$Log_Funtion ."失败";
              }
            break;
     }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>