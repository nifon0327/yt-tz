<?php 
 $Log_Item="业务处理-";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 switch($ActionId){
          case "Lock":
               $POrderId=$info[0];   $Locks=$info[1];
               $Log_Funtion=$Locks==0?"订单锁定":"订单解锁";
                if ($Locks==1){
if($DataIn =="ac"){
                    $inRecode="INSERT INTO $DataIn.yw2_orderexpress_log  SELECT NULL,POrderId,Type,Remark,Date,Operator ,'1','0','0','$Operator','$DateTime','$Operator','$DateTime' 
                FROM $DataIn.yw2_orderexpress WHERE  POrderId='$POrderId' AND Type='2'";
}else{
                    $inRecode="INSERT INTO $DataIn.yw2_orderexpress_log  SELECT NULL,POrderId,Type,Remark,Date,Operator  FROM $DataIn.yw2_orderexpress WHERE  POrderId='$POrderId' AND Type='2'";
}
                    $inAction=@mysql_query($inRecode);
                
					$delSql="DELETE FROM $DataIn.yw2_orderexpress WHERE  POrderId='$POrderId' AND Type='2'";
					$delResult=mysql_query($delSql);
					if($delResult && mysql_affected_rows()>0){
					   //更新未下采单时间
					   $upcgSql="UPDATE $DataIn.cg1_stocksheet S 
					           LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	                           LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					           SET S.ywOrderDTime=NOW() 
					           WHERE S.POrderId='$POrderId' AND T.mainType<2 AND S.Mid=0";
					    $upResult=mysql_query($upcgSql);     
						$Log=$Log_Item .$Log_Funtion . "成功!($POrderId)<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
						}
					else{
						 $Log="<div class=redB>$Log_Item $Log_Funtion 失败! ($POrderId) </div><br>";
                         $infoSTR=$Log_Funtion ."失败";
						}
					}
            break;
           case "cgLock":
               $StockId=$info[0];   $Locks=$info[1];
               $Log_Funtion=$Locks==0?"采购单锁定":"采购单解锁";
                if ($Locks==1){
if($DataIn =="ac"){
                     $inRecode="INSERT INTO $DataIn.cg1_lockstock_log  SELECT NULL,StockId,Estate,Locks,Remark,Date,Operator,'0','$Operator','$DateTime','$Operator','$DateTime'  
                FROM $DataIn.cg1_lockstock WHERE  StockId='$StockId' AND Locks='0' ";
}else{
                     $inRecode="INSERT INTO $DataIn.cg1_lockstock_log  SELECT NULL,StockId,Estate,Locks,Remark,Date,Operator  FROM $DataIn.cg1_lockstock WHERE  StockId='$StockId' AND Locks='0' ";
}
                    $inAction=@mysql_query($inRecode);
                    $upSql = "UPDATE $DataIn.cg1_lockstock  SET Locks='1',Date='$curDate',Operator='$Operator'  WHERE StockId='$StockId'";
					$upResult = mysql_query($upSql);
					if($upResult && mysql_affected_rows()>0){	
						//更新未下采单时间
					    $upcgSql="UPDATE $DataIn.cg1_stocksheet  SET ywOrderDTime=NOW()  WHERE StockId='$StockId'  AND Mid=0";
					    $upResult=mysql_query($upcgSql); 
					    
					    $Log=$Log_Item .$Log_Funtion . "成功!($StockId)<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
					}
					else{
						 $Log="<div class=redB>$Log_Item $Log_Funtion 失败! ($StockId) </div><br>";
                         $infoSTR=$Log_Funtion ."失败";
					}
			  }
            break;
        case "Ts"://标准图审核
                 $Log_Funtion="产品标准图审核";
                 $ProductId=$info[0];   $Locks=$info[1];
                 $upSql = "UPDATE $DataIn.productdata SET TestStandard=1 WHERE ProductId=$ProductId";
				 $upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
				        $Log=$Log_Item .$Log_Funtion . "成功!($ProductId)<br>";
				         $OperationResult="Y";
						include "../../model/subprogram/delete_orderTfile.php";   //删除订单为重新上传标准图的标记
						$delSql="DELETE FROM $DataIn.test_remark WHERE ProductId=$ProductId";//删除标准图备注纪录
						$delResult = mysql_query($delSql);
						if($delResult && mysql_affected_rows()>0){
							$Log=$Log."且该产品的备注纪录删除成功";
						}
				   }
			else{
				$Log="<div class=redB>$Log_Item $Log_Funtion 失败! ($ProductId) </div><br>";
				$OperationResult="N";
				}

                  break;
        case "ShipType"://出货方式
                   $Log_Funtion="设置出货方式";
                   $POrderId=$info[0];   $ShipType=$info[1];
				  $upSql = "UPDATE $DataIn.yw1_ordersheet SET ShipType='$ShipType' WHERE POrderId='$POrderId'";
				  $upResult = mysql_query($upSql);
				  if($upResult && mysql_affected_rows()>0){
						$Log=$Log_Item .$Log_Funtion . "成功!($POrderId)<br>";
				         $OperationResult="Y";
						}
					else{
					$Log="<div class=redB>$Log_Item $Log_Funtion 失败! ($POrderId) </div><br>";
						$OperationResult="N";
						}
                 break;
 }
  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>