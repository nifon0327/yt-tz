<?php 
 $Log_Item="补休申请";
        switch($ActionId){
            case "SAVE"://新增记录
                $Log_Funtion="保存";
                $StartTime = $info[0];$EndTime = $info[1];$Hours = $info[2]; $Remark = $info[3];
                
                  $inRecode="INSERT INTO $DataPublic.bxSheet (Id, Number, StartDate, EndDate, hours, Note, Date, type, Estate, Operator) Values (NULL,'$LoginNumber','$StartTime','$EndTime', $Hours, '$Remark','$Date', '0', '1','$LoginNumber')";
	             $inAction=@mysql_query($inRecode);
	                if ($inAction){ 
	                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                        $OperationResult="Y";
	                        $infoSTR=$Log_Funtion ."数据成功";
	                } 
	                else{
	                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	                        $infoSTR=$Log_Funtion ."数据失败";
	                        }
                    break;
                    
                     case "DEL":
                     $Log_Funtion="删除";
                     $Id= $info[0];
                      //删除数据库记录
                    $delSql = "DELETE FROM $DataPublic.bxSheet  WHERE Id ='$Id'  and Estate=1"; 
                    $delRresult = mysql_query($delSql);
                    if($delRresult && mysql_affected_rows()>0){
                           $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                           $info=$Log_Funtion ."数据成功";
                         }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
                  break;
        }

?>