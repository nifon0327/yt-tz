<?php 
 $Log_Item="人员资料";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 switch($ActionId){ 
         case "UPDATE":
             $Number=$info[0];$Address=$info[1];$Mobile=$info[2];$Dh=$info[3];$ExtNo=$info[4];$Mail=$info[5];$LinkedIn=$info[6];$Weixin=$info[7];
             $Log_Funtion="更新";
             if ($Number>0){
                  $upRecode="UPDATE $DataPublic.staffmain  M,$DataPublic.staffsheet S 
                   SET M.ExtNo='$ExtNo',M.Mail='$Mail',S.Address='$Address',S.Mobile='$Mobile',S.Dh='$Dh',S.Weixin='$Weixin',S.LinkedIn='$LinkedIn' 
                   WHERE M.Number=S.Number AND  M.Number='$Number'";
                   //echo $upRecode;
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
            break;
        case "Performance":
             $Log_Item="在职表现";
             $Log_Funtion="保存";
            $Number=$info[0];$Description=$info[2];
            $IN_recode="INSERT INTO $DataPublic.staff_performance (Id,Number,Description,Date,Estate,Locks,Operator) VALUES (NULL,'$Number','$Description','$DateTime','1','0','$Operator')";
            $IN_Action=@mysql_query($IN_recode);
            if ($IN_Action){ 
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>$upRecode ";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                 } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $upRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
           break;
          case "Duty":
              $Log_Item="工作职责";
             $Log_Funtion="保存";
            $Number=$info[0];$Description=$info[1];
            $IN_recode="REPLACE INTO $DataPublic.staff_jobduties (Id,Number,Description,Date,Estate,Locks,Operator) VALUES (NULL,'$Number','$Description','$DateTime','1','0','$Operator') ";
            $IN_Action=@mysql_query($IN_recode);
            if ($IN_Action){ 
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>$upRecode ";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                 } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $upRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
           break;
   }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>