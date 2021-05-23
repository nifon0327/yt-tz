<?php 
 $Log_Item="配件禁用";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 switch($ActionId){
          case "ADD":
               $Log_Funtion="备注保存";
               $StuffId=$info[0];   $Remark=$info[1];
                 $inRecode="INSERT INTO $DataIn.stuffremark (Id,StuffId,Type,Remark,Date,Operator) VALUES (NULL,'$StuffId','1','$Remark','$curDate','$Operator')";
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
			case "BFUPDATE": {
			
			$Id=$info[0];
			$BfType= $info[1];
			$bfReason = $info[2];
			$bfNum = $info[3];
			
			$BfType = (int)$BfType;
			$Reason='禁用/报废:'.$bfReason;
			$bfNum = (int)$bfNum;
			
			$Log_Funtion="保存";
			$InRecode=0;
			if ($BfType == 6) {//禁用
				$Delstuff="DELETE FROM $DataIn.stuffdisable WHERE StuffId='$Id'";
				$DelResult=@mysql_query($Delstuff);
				$InSql="INSERT INTO $DataIn.stuffdisable  (Id,StuffId,Reason,Date,Operator) 
						 VALUES  (NULL,'$Id','$Reason','$curDate','$Operator')";
	        	$InRecode=mysql_query($InSql);
			} else {
				$InRecode = 1;
			}
			$InSql="INSERT INTO $DataIn.ck8_bfsheet
			  		(Id,StuffId,ProposerId,Bill,Operator,Qty,Remark,Date,Type,Estate,Locks,dealResult,OPdatetime) 
					VALUES
			(NULL,'$Id','$Operator',0,'$Operator',$bfNum,'$bfReason','$curDate','$BfType',1,0,'','$DateTime')";
			$InRecode2=mysql_query($InSql);
			 
			if ($InRecode && $InRecode2){
				$upAction = 0;
				if ($BfType == 6) {//禁用
					$upRecode="UPDATE $DataIn.stuffdata  SET Estate=0,Locks=0 WHERE  StuffId='$Id'";
					$upAction=@mysql_query($upRecode);
				} else {
					$upAction = 1;
				}
				 /*
				 $upRecode="UPDATE $DataIn.ck9_stocksheet  SET oStockQty=(oStockQty-$bfNum),tStockQty=(tStockQty-$bfNum) WHERE  StuffId='$Id'";
                 $upAction2=@mysql_query($upRecode);
				 */
				if ($upAction){ 
					$Log=$Log_Item .$Log_Funtion . "成功!<br>$upRecode ";
					$OperationResult="Y";
					$infoSTR=$Log_Funtion ."成功";
				} else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $upRecode </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
              }
			} else{
				$Log="<div class=redB>$Log_Item $Log_Funtion 失败!</div><br>";
				$infoSTR=$Log_Funtion ."失败";
			}
			}
			break;
     }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>