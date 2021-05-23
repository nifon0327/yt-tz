<?
 $Log_Item="订单备注"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 switch($ActionId){
	   case "Remark":
	              $POrderId=$info[0];  $PackRemark=$info[1];  $enRemark=$info[2];
	              if ($PackRemark!=""){
		              $Log_Funtion="更新包装说明";
					  $upSql = "UPDATE $DataIn.yw1_ordersheet  SET PackRemark='$PackRemark' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
					  $upResult = mysql_query($upSql,$link_id);
					 if ($upResult){
					     $inSql = "INSERT INTO $DataIn.yw2_orderremark(Id,POrderId,Type,Remark,Date,Operator) Values (NULL,'$POrderId','2','$PackRemark','$DateTime','$Operator')";
						  $inResult = mysql_query($inSql,$link_id);
						  
						$Log="订单流水号为 $POrderId 的包装方式更新成功. <br>";
						 $OperationResult="Y";
						 $infoSTR="更新成功";
						}
					else{
						$Log="<div class=redB>订单流水号为 $POrderId 的包装方式更新失败.  </div><br>";
						$OperationResult="N";
						$infoSTR="更新失败";
						}
	              }
	              
	            if ($enRemark!=""){
		                 $Log_Funtion="更新英文备注";
					     $inSql = "INSERT INTO $DataIn.yw2_orderremark(Id,POrderId,Type,Remark,Date,Operator) Values (NULL,'$POrderId','1','$enRemark','$DateTime','$Operator')";
						  $inResult = mysql_query($inSql,$link_id);
						  if ($inResult){
							$Log.="订单流水号为 $POrderId 的英文备注更新成功.<br>";
						    $OperationResult="Y";
							$infoSTR="更新成功";
							}
						else{
							$Log.="<div class=redB>订单流水号为 $POrderId 的英文备注更新失败.</div> $inSql<br>";
							$OperationResult="N";
							$infoSTR="更新失败";
							}
	              }  
	              
	            break;
 }
 
  if ($Log_Funtion!="")
{
		         $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
                 $IN_res=@mysql_query($IN_recode);
                 $jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
 }

 
?>