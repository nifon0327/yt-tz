<?php 
        $Log_Item="出差登记";
        switch($ActionId){
            case "SAVE"://新增记录
                $Log_Funtion="保存";
                $StartTime = $info[0];
                $EndTime = $info[1];
                $CarId = $info[2];
                $Driver = $info[3];
                $Remark = $info[4];
                
                $Driver=$Driver==0?1:$Driver;
                $Operator1=$Operator;
                include '../../model/subprogram/staffname.php';
                
            $inRecode="INSERT INTO $DataPublic.info1_business (Id,Businesser,StartTime,EndTime,CarId,Drivers,Remark,sCourses,eCourses,Estate,Date,Operator) VALUES 
        (NULL,'$Operator','$StartTime','$EndTime','$CarId ','$Driver','$Remark','0','0','1','$DateTime','$Operator1')";
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
                    $delSql = "DELETE FROM $DataPublic.info1_business  WHERE Id ='$Id'"; 
                    $delRresult = mysql_query($delSql);
                    if($delRresult && mysql_affected_rows()>0){
                           $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                           $infoSTR=$Log_Funtion ."数据成功";
                            }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion 失败!</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
                    //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.info1_business");
                    break;
               
               /*
                case "EDIT":
                     $Id= $info[0];
                     $Log_Funtion=$Info ."更新";
                      //更新数据库记录
                    $updateSql = "UPDATE $DataPublic.info1_business  SET EndTime='$DateTime' WHERE Id =$Id"; 
                    $updateRresult = mysql_query($updateSql);
                    if($updateRresult && mysql_affected_rows()>0){
                           $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                           $infoSTR=$Log_Funtion ."数据成功";
                            }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
                      */
                    break;
           }

?>