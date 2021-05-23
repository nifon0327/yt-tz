<?php 
 $Log_Item="备料信息";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 switch($ActionId){
          case "Lock":
               $WeekName=$info[0];   $Locks=$info[1];
               $Log_Funtion=$Locks==0?"锁定":"解锁";
                $inRecode="REPLACE INTO $DataIn.yw9_blunlock (Id,WeekName,Locks,Date,Operator) VALUES (NULL,'$WeekName','$Locks','$DateTime','$Operator')";
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
        case "Remark":
                  $POrderId=$info[0];  $sgRemark=$info[1];  
	              if ($sgRemark!=""){
		              $Log_Funtion="生管备注";
					  $upSql = "UPDATE $DataIn.yw1_ordersheet  SET sgRemark='$sgRemark' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
					  $upResult = mysql_query($upSql,$link_id);
					 if ($upResult){
					     $Log="订单流水号为 $POrderId 的生管备注更新成功. <br>";
						 $OperationResult="Y";
						 $infoSTR="更新成功";
						}
					else{
						$Log="<div class=redB>订单流水号为 $POrderId 的生管备注更新失败.  </div><br>";
						$OperationResult="N";
						$infoSTR="更新失败";
						}
	             }
          break;
 }
  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>