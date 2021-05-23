<?php 
 $Log_Item="备品";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 $Log = "";
 
 switch($ActionId){
          case "Modify":
	           {
		           $Id=$info[0];$Number = $info[1]; $Remark=$info[2];
	             $Log_Funtion="备品修改"; 
	             
	            
	            $updateSql="UPDATE $DataIn.ck7_bprk S 
																						SET S.Qty=$Number,S.Remark='$Remark' ,S.Estate=1 
											WHERE S.Id='$Id' and S.Estate=2";
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
         case "DEL":
         {
	         $Id=$info[0];
	             $Log_Funtion="备品删除"; 
	             
	            
	            $updateSql="delete from  $DataIn.ck7_bprk 
											WHERE Id='$Id' and Estate=2";
	           $updateResult = mysql_query($updateSql,$link_id);
               if (mysql_affected_rows($link_id)>0){
	                 $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                 $OperationResult="Y";
	                  mysql_query("delete from returnreason where targetTable='ac.ck7_bprk' and tableId=$Id",$link_id);
	                 
	                 $infoSTR=$Log_Funtion ."成功";
               }
               else {
	               $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                    $infoSTR=$Log_Funtion ."失败";
               }


	         
         }
                        break;
         default : break;
     }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>