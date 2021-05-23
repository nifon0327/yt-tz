<?php
$cSign = 7;
$cSignResult=mysql_query("SELECT cSign FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
  if  ($cSignRow = mysql_fetch_array($cSignResult)){
       $DataIn=$cSignRow["cSign"]==3?$DataOut:$DataIn;
       $cSign = $cSignRow["cSign"];
  }
			      
 $Log_Item="费用报销";
        switch($ActionId){
            case "SAVE"://新增记录
            
                $Log_Funtion="保存";
                $TypeId = $info[0];$Currency = $info[1];$Amount = $info[2]; $Remark = $info[3];
                
                $upFile=$_POST["Data"];

                 $inRecode="INSERT INTO $DataIn.hzqksheet (Id,Mid,cSign,Content,Amount,Currency,Bill,ReturnReasons,Date,Estate,TypeId,Locks,Operator) VALUES (NULL,'0','$cSign','$Remark', '$Amount','$Currency','0','','$Date','1','$TypeId','1','$LoginNumber')";
	             $inAction=@mysql_query($inRecode);
	              $Id=mysql_insert_id();
	                if ($inAction){ 
	                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                        $OperationResult="Y";
	                        $infoSTR=$Log_Funtion ."数据成功";
	                        
	                         if($upFile!=""){
                                $fileName="H".$Id.".jpg";
                                $path = "../../download/cwadminicost/".$fileName;
                                if(move_uploaded_file($_FILES['upFile']['tmp_name'],$path))
                                {
                                    //更新刚才的记录
                                    $upSql = "UPDATE $DataIn.hzqksheet SET Bill='1' WHERE Id='$Id'";
                                    $result = mysql_query($upSql);
                                    $infoSTR.= "上传文件成功！";			
                                }
                                else
                                {
                                    $infoSTR.= "上传文件失败！";
                                }
                          }
	                } 
	                else{
	                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	                        $infoSTR=$Log_Funtion ."数据失败";
	                        }
                    break;
                    case "ESTATE":
                             $Log_Funtion="请款";
                            $Id= $info[0];$Estate=$info[1];
                           $upSql = "UPDATE $DataIn.hzqksheet SET Estate='$Estate' WHERE Id='$Id'";
                           $upResult = mysql_query($upSql);
                            if($upResult && mysql_affected_rows()>0){
                                  $OperationResult="Y";
                                  $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                                  $infoSTR=$Log_Funtion ."成功";
                            }
                            else{
	                             $Log="<div class='redB'>$Log_Item $Log_Funtion 失败！</div><br>";
                                 $infoSTR=$Log_Funtion ."失败";
                            }
                      break;
                   case "UPDATE":
                          $Log_Funtion="更新";
                           $Id= $info[0];$TypeId = $info[1];$Currency = $info[2];$Amount = $info[3]; $Remark = $info[4]; 
                           $upFile=$_POST["Data"];
                           
                           $upBill="";
                            if($upFile!=""){
                                $fileName="H".$Id.".jpg";
                                $path = "../../download/cwadminicost/".$fileName;
                                if(move_uploaded_file($_FILES['upFile']['tmp_name'],$path))
                                {
                                    $upBill=",Bill='1'";
                                    $infoSTR.= "上传文件成功！";			
                                }
                                else
                                {
                                    $infoSTR= "上传文件失败！";
                                }
                          }
                          
                           $upSql = "UPDATE $DataIn.hzqksheet SET TypeId='$TypeId',Currency='$Currency',Amount='$Amount',Content='$Remark' $upBill WHERE Id='$Id'";
                           $upResult = mysql_query($upSql);
                            if($upResult && mysql_affected_rows()>0){
                                  $OperationResult="Y";
                                  $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                                  $infoSTR.=$Log_Funtion ."成功";
                            }
                            else{
	                             $Log="<div class='redB'>$Log_Item $Log_Funtion 失败！</div><br>";
                                 $infoSTR.=$Log_Funtion ."失败";
                            }     
                      break;
                     case "DEL":
                     $Log_Funtion="删除";
                     $Id= $info[0];
                      //删除数据库记录
                    $delSql = "DELETE FROM $DataIn.hzqksheet  WHERE Id ='$Id'  and Estate IN (1,2) and Mid=0"; 
                    $delRresult = mysql_query($delSql);
                    if($delRresult && mysql_affected_rows()>0){
                           $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                            $infoSTR=$Log_Funtion ."数据成功";
                           
                           $fileName="H".$Id.".jpg";
                           $path = "../../download/cwadminicost/".$fileName;
                           if(file_exists($path)){
                                unlink($path);
                                }
                         }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion 失败</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
                  break;
        }

?>