<?php 
 $Log_Item="点餐登记";
        switch($ActionId){
            case "SAVE"://新增记录
                $Log_Funtion="保存";
                $CtId = $info[1];$MenuId = $info[2];$Price = $info[3];$Qty = $info[4]; $Amount = $info[5]; 
               
               $CheckmainResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.ct_myordermain WHERE Date='$Date'",$link_id));
                $CheckId=$CheckmainResult["Id"];
				if($CheckId==""){
				      $IN_main="INSERT INTO $DataPublic.ct_myordermain(Id, Date, Bill, Remark)VALUES(NULL,'$Date','','')";
				      $In_Result=@mysql_query($IN_main);
				     $Mid=mysql_insert_id();
				}
				else $Mid=$CheckId;
              
               $inRecode="INSERT INTO $DataPublic.ct_myorder (Id,Mid,CtId, MenuId, Price, Qty, Amount, Estate, Locks, Date, Operator) VALUES (NULL,'$Mid','$CtId','$MenuId','$Price','$Qty','$Amount','1','1','$DateTime','$LoginNumber')";

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
                    $delSql = "DELETE FROM $DataPublic.ct_myorder WHERE Id ='$Id' AND Locks=1"; 
                    $delRresult = mysql_query($delSql);
                    if($delRresult && mysql_affected_rows()>0){
                            $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                            $infoSTR=$Log_Funtion ."数据成功";
                         }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
                  break;
        }

?>