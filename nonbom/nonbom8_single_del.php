<?php 
//EWEN 2013-03-01 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件个人申领记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);

//步骤3：需处理，执行动作
$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$checkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(Qty,0) AS Qty,GoodsId,Locks,Estate FROM $DataIn.nonbom8_outsheet WHERE Id='$Id'",$link_id));
		$llQty=$checkQty["Qty"];
		$GoodsId=$checkQty["GoodsId"];
		if($checkQty["Estate"]==2){
			$DelSql = "DELETE FROM $DataIn.nonbom8_outsheet WHERE Id='$Id'";
			$DelResult = mysql_query($DelSql);
			if($DelResult && mysql_affected_rows()>0){
				$Log.="ID/编号为 $Id/$GoodsId 的 $Log_Item 删除操作成功.<br>";
				//更新库存
				  $DelSQL1 = "Delete  from $DataIn.nonbom8_outfixed WHERE OutId='$Id'";
				  $DelResult1 = mysql_query($DelSQL1);
				}
			else{
				$Log.="<div class='redB'>ID/编号为 $Id/$GoodsId 的 $Log_Item 删除操作失败! $DelSql</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class='redB'>ID/编号为 $Id/$GoodsId 的 $Log_Item 已发放或者已审核，不能做删除! $DelSql</div><br>";
			$OperationResult="N";
			}
		}
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&OperatorSign=$OperatorSign";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>