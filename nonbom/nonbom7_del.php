<?php 
//ewen 2013-03-04 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件入库记录";//需处理
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
		$CheckSql= mysql_query("SELECT A.GoodsId,A.cgId,A.Qty,A.Mid,B.wStockQty FROM $DataIn.nonbom7_insheet A LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId WHERE A.Id='$Id' AND B.wStockQty>=A.Qty",$link_id);
		if($CheckRow = mysql_fetch_array($CheckSql)){				//可删除
			$cgId=$CheckRow["cgId"];					//需求单
			$Mid=$CheckRow["Mid"];						//入库主单
			$GoodsId=$CheckRow["GoodsId"];		//配件ID			
			$Qty=$CheckRow["Qty"];						//入库数量
			$delSql = "DELETE FROM $DataIn.nonbom7_insheet WHERE Id='$Id'"; //删除些入库记录
			$delRresult = mysql_query($delSql);
			if($delRresult && mysql_affected_rows()>0){
				$Log.="&nbsp;&nbsp;1.配件 $GoodsId 的需求单 $cgId 入库记录删除成功!<br>";
				////////////////////
					//2.更新需求单的收货状态
					$uprkSign="UPDATE $DataIn.nonbom6_cgsheet SET rkSign=(CASE WHEN (SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom7_insheet WHERE cgId = '$cgId')>0 THEN 2 ELSE 1 END) WHERE Id='$cgId'";
					$upRkAction=mysql_query($uprkSign);	
					if($upRkAction){$Log.="&nbsp;&nbsp;2.需求单 $cgId 的入库标记更新成功.<br>";}
					else{$Log.="<div class='redB'>&nbsp;&nbsp;2.需求单 $cgId 的入库标记更新失败. $uprkSign </div><br>";$OperationResult="N";}
						
					//3.更新在库
					$Stockinsq = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty-$Qty WHERE GoodsId='$GoodsId' AND wStockQty>=$Qty LIMIT 1";
					$Stockinresult = mysql_query($Stockinsq);
					if($Stockinresult){$Log.="&nbsp;&nbsp;3.配件 $GoodsId 的在库扣除成功!<br>";}
					else{$Log.="<div class='redB'>&nbsp;&nbsp;3.配件 $GoodsId 的在库扣除失败! $Stockinsq </div><br>";$OperationResult="N";}
						
					//4.主入库单
					$delMainSql = "DELETE FROM $DataIn.nonbom7_inmain WHERE Id='$Mid' AND Id NOT IN (SELECT Mid FROM $DataIn.nonbom7_insheet WHERE Mid=$Mid)"; 
					$delMianRresult = mysql_query($delMainSql);
					if($delMianRresult && mysql_affected_rows()>0){$Log.="&nbsp;&nbsp;4.主入库单已经没有内容，清除成功!<br>";}
					else{$Log.="&nbsp;&nbsp;4.主入库单还有内容，不做处理! $delMainSql <br>";$OperationResult="N";}

					//5，固定资产入库信息删除
					$delCodeSql = "DELETE FROM $DataIn.nonbom7_code WHERE rkId='$Id' AND GoodsId=$GoodsId AND TypeSign=1"; 
					$delCodeRresult = mysql_query($delCodeSql);
					if($delCodeRresult && mysql_affected_rows()>0){$Log.="&nbsp;&nbsp;5.固定资产入库信息没有内容，清除成功!<br>";}
					else{$Log.="&nbsp;&nbsp;4.固定资产入库信息清除失败! $delCodeSql <br>";}
                   
				////////////////////
				}
			else{//删除操作失败
				$Log.="<div class='redB'>&nbsp;&nbsp;1.配件 $GoodsId 的需求单 $cgId 入库资料删除失败! $delSql </div><br>";
				$OperationResult="N";
				}
			}//end if($CheckRow = mysql_fetch_array($CheckSql))
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;配件的在库不足或其它原因，删除失败!</div><br>";
			$OperationResult="N";
			}
		$x++;
		}//end if($Id!="")	
	}//end for($i=1;$i<$IdCount;$i++)
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>