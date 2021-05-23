<?php 
/*电信---yang 20120801
$DataIn.sc1_cjtj
二合一已更新
*/
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车间生产记录";//需处理
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
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		//取相关的POrderId号
		$checkPOrderId=mysql_fetch_array(mysql_query("SELECT POrderId FROM $DataIn.sc1_cjtj WHERE Id='$Id'",$link_id));
		$POrderId=$checkPOrderId["POrderId"];
		$DelSql = "DELETE FROM $DataIn.sc1_cjtj WHERE Id='$Id'";
		$DelResult = mysql_query($DelSql);
		if($DelResult && mysql_affected_rows()>0){
			$Log.="ID号为 $Id 的 $Log_Item 删除操作成功.<br>";
			//重新检查相应订单的状态，取消待出 S.scFrom=2,S.Estate=1
			//检查该订单是否已经生产完，是则更新订单状态Y.Estate=2,
			$UpdateSql="Update $DataIn.yw1_ordersheet Y
				LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
				LEFT JOIN (
					SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
					FROM $DataIn.cg1_stocksheet G 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY POrderId) B ON B.POrderId=Y.POrderId 
				SET Y.scFrom=0 
				WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";
			$UpdateResult = mysql_query($UpdateSql);
			//主动删除登记记录时，如果生产总数少于订单总数
			$UpdateSql="Update $DataIn.yw1_ordersheet Y
				LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
				LEFT JOIN (
					SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
					FROM $DataIn.cg1_stocksheet G 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId  
					WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
				SET Y.Estate=1,Y.scFrom=2 
				WHERE Y.POrderId='$POrderId' AND A.Qty<B.Qty AND Y.Estate>0 AND Y.Estate<4";
			$UpdateResult = mysql_query($UpdateSql);
			}
		else{
			$Log.="<div class='redB'>ID号为 $Id 的 $Log_Item 删除操作失败. </div><br>";
			$OperationResult="N";
			}		
		}
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.sc1_cjtj");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&Ptype=$Ptype";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>