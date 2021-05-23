<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rkmain
$DataIn.ck1_rksheet
$DataIn.ck9_stocksheet
$DataIn.cg1_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="入库";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Log.=$x.":<br>";
		//读取此ID的数据:配件ID，入库数量，
		$CheckSql= mysql_query("SELECT 
		R.StuffId,R.StockId,R.Qty,R.Mid,R.llQty,K.tStockQty
		FROM $DataIn.ck1_rksheet  R 
		LEFT JOIN $DataIn.ck9_stocksheet  K  ON R.StuffId=K.StuffId
		WHERE R.Id='$Id' AND K.tStockQty>=R.Qty",$link_id);
		if($CheckRow = mysql_fetch_array($CheckSql)){				//可删除
			$StockId=$CheckRow["StockId"];				//需求单
			$Mid=$CheckRow["Mid"];						//入库主单
			$StuffId=$CheckRow["StuffId"];				//配件ID			
			$Qty=$CheckRow["Qty"];	//入库数量
			$llQty	= $CheckRow["llQty"];	//领料数量
			if($llQty>0){
				 $Log.="<div class='redB'>&nbsp;&nbsp;该条入库记录已经出库，不能删除!</div><br>";
			     $OperationResult="N"; 	  
			}else{
			    $delSql = "DELETE FROM $DataIn.ck1_rksheet WHERE Id='$Id'"; //删除些入库记录
			    $delRresult = mysql_query($delSql);
			    if($delRresult && mysql_affected_rows()>0){
					    $Log.="&nbsp;&nbsp;1.配件 $StuffId 的需求单 $StockId 入库记录删除成功!<br>";
						//2.更新需求单的收货状态
						$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE WHEN (SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId = '$StockId')>0 THEN 2 ELSE 1 END) WHERE StockId='$StockId'";
						$upRkAction=mysql_query($uprkSign);	
						if($upRkAction){$Log.="&nbsp;&nbsp;2.需求单 $StockId 的入库标记更新成功.<br>";}
						else{$Log.="<div class='redB'>&nbsp;&nbsp;2.需求单 $StockId 的入库标记更新失败. $uprkSign </div><br>";$OperationResult="N";}
							
						//3.更新在库 通过触发器修改库存
						/*$Stockinsq = "UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty-$Qty WHERE StuffId='$StuffId' AND tStockQty>=$Qty LIMIT 1";
						$Stockinresult = mysql_query($Stockinsq);
						if($Stockinresult){$Log.="&nbsp;&nbsp;3.配件 $StuffId 的在库扣除成功!<br>";}
						else{$Log.="<div class='redB'>&nbsp;&nbsp;3.配件 $StuffId 的在库扣除失败! $Stockinsq </div><br>";$OperationResult="N";}*/
							
						//4.主入库单
						$delMainSql = "DELETE FROM $DataIn.ck1_rkmain WHERE Id=$Mid and Id NOT IN (SELECT Mid FROM $DataIn.ck1_rksheet WHERE Mid=$Mid)"; 
						$delMianRresult = mysql_query($delMainSql);
						if($delMianRresult && mysql_affected_rows()>0){$Log.="&nbsp;&nbsp;4.主入库单已经没有内容，清除成功!<br>";}
						else{$Log.="&nbsp;&nbsp;4.主入库单还有内容，不做处理! $delMainSql <br>";$OperationResult="N";}

					}
				else{//删除操作失败
					$Log.="<div class='redB'>&nbsp;&nbsp;1.配件 $StuffId 的需求单 $StockId 入库资料删除失败! $delSql </div><br>";
					$OperationResult="N";
					}
			  }
			}//end if($CheckRow = mysql_fetch_array($CheckSql))
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;配件的在库不足或其它原因，删除失败!</div><br>";
			$OperationResult="N";
			}
		$x++;
		}//end if($Id!="")	
	}//end for($i=1;$i<$IdCount;$i++)
//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
//操作日志
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ck1_rkmain");
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ck1_rksheet");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>