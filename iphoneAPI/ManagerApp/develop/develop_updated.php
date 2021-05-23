<?php 
 $Log_Item="开发进度";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 switch($ActionId){
          case "ADD":
               $Log_Funtion="日志保存";
               $MId=$info[0];   $Remark=$info[1];
                 $inRecode="INSERT INTO $DataIn.stuffdevelop_log (Id,Mid,Remark,Date,Operator) VALUES (NULL,'$MId','$Remark','$curDate','$Operator')";
                $inAction=@mysql_query($inRecode);
                 if ($inAction){ 
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
             $Id=$info[0];
             $Log_Funtion="状态更新";
             $upRecode="UPDATE $DataIn.stuffdevelop  SET Estate=0,Finishdate='$DateTime' WHERE  Id='$Id'";
             $upAction=@mysql_query($upRecode);
                 if ($upAction){ 
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>$upRecode ";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;
     }
  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>