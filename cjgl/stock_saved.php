<?php
	
	include_once "../../basic/parameter.inc";
	
	$Operator = $_POST["operator"];
	$stockIds = $_POST["stockIds"];
	$qtys = $_POST["qtys"];
	
	$Log_Item="订单领料";			//需处理
	$Log_Funtion="数据更新";
	$Date=date("Y-m-d");
	$DateTime=date("Y-m-d H:i:s");
	$OperationResult="Y";
	
	$ArrId=explode("|",$stockIds);
	$ArrQty=explode("|",$qtys);
	$sLen=count($ArrId); 
	if(count($ArrQty)==$sLen && $sLen>0)
	{
		// //$LockSql="LOCK TABLES $DataIn.ck9_stocksheet  WRITE";
		// $LockRes=@mysql_query($LockSql);
		//生成主备料单
		$blinRecode="INSERT INTO $DataIn.yw9_blmain (Id,Estate,Locks,Date,Operator) VALUES (NULL,'1','0','$DateTime','$Operator')";
		$blinAction=@mysql_query($blinRecode);
		$Pid=mysql_insert_id();
			
		if($Pid!=0 && $Pid!="")
		{
			$faildStuff = array();
			for ($i=0;$i<$sLen;$i++)
			{
				//取得配件ID号
				$checkResult = mysql_query("SELECT StuffId FROM $DataIn.cg1_stocksheet WHERE StockId='$ArrId[$i]' LIMIT 1",$link_id); 
				$StuffId=mysql_result($checkResult,0,"StuffId");
				//生成领料明细数据 
				$llInSql="INSERT INTO $DataIn.ck5_llsheet (Id,Pid,Mid,StockId,StuffId,Qty,Locks,Estate) VALUES  (NULL,'$Pid','0','$ArrId[$i]','$StuffId','$ArrQty[$i]','0','1')";
			    $llInAction=@mysql_query($llInSql);
				//更新在库
				$signUpSql="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$ArrQty[$i]  WHERE StuffId='$StuffId' AND tStockQty>=$ArrQty[$i]";
				$signUpResult = mysql_query($signUpSql);
				if ($signUpResult)
				{
					$Log.="更新配件($StuffId)在库数量(-$ArrQty[$i])成功!";
				}
				else
				{
					$Log.="更新配件($StuffId)在库数量(-$ArrQty[$i])失败!";
					$faildStuff[] = $StuffId;
				}
			}	
			$Log.="生成领料单成功!";
			   //解锁
             //  $unLockSql="UNLOCK TABLES"; $unLockRes=mysql_query($unLockSql);
	   }
	   else
	   {
	       $Log.="生成领料单失败!";
	       $OperationResult="N";
	   }
    }
    else
    {
		$Log.="生成领料单失败!";
		$OperationResult="N";	
	}
	
	$faildLine = implode("|", $faildStuff);
	echo json_encode(array($OperationResult, $Log, $faildLine));

	//步骤4：
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	
?>