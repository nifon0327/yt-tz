<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$Log_Item="生产记录";
$Log_Funtion="更新";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$upDataSheet="$DataIn.sc1_cjtj";
$checkSql=mysql_query("SELECT POrderId FROM $upDataSheet WHERE Id='$Id'",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	$POrderId=$checkRow["POrderId"];
	if($Qty>0){//更新
		$upSql = "UPDATE $upDataSheet SET Qty='$Qty' WHERE Id=$Id";
		$upResult = mysql_query($upSql);
		$UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId') A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				   SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				   FROM $DataIn.cg1_stocksheet G 
				   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				   LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				  WHERE G.POrderId='$POrderId' AND T.mainType=3) B ON B.POrderId=Y.POrderId 
			      SET Y.scFrom=0 
			WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty" ;//该订单中，如果生产的数量和需求的数量一致，则生产状态为0，出货状态是不变的1，由品检审核后出货状态才改为待出2
		    $UpdateResult = mysql_query($UpdateSql);
		    $Log="ID为 $Id 的生产记录更新成功!";
           if(!$UpdateResult){
                      $upSql1="Update $DataIn.yw1_ordersheet Y SET Y.scFrom=2 AND Y.Estate=1 WHERE Y.POrderId='$POrderId'";//产量不够，回到生产状态中
                      $UpdateResult=mysql_query($upSql1);
                    }
		}
	else{//删除
		    $delSql = "DELETE FROM $upDataSheet WHERE Id='$Id'"; 
		    $delRresult = mysql_query($delSql);
		    if ($delRresult && mysql_affected_rows()>0){
			//更新状态
			$UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId') A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE G.POrderId='$POrderId' AND T.mainType=3) B ON B.POrderId=Y.POrderId 
			SET Y.scFrom=0 
			WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty" ;//该订单中，如果生产的数量和需求的数量一致，则生产状态为0，出货状态是不变的1，由品检审核后出货状态才改为待出2
			$UpdateResult = mysql_query($UpdateSql);
			$Log="ID为 $Id 的生产记录删除成功!";
               if(!$UpdateResult){
                      $upSql1="Update $DataIn.yw1_ordersheet Y SET Y.scFrom=2 AND Y.Estate=1 WHERE Y.POrderId='$POrderId'";//产量不够，回到生产状态中
                      $UpdateResult=mysql_query($upSql1);
                    }
			}
		else{//删除失败.
			$Log="<div class='redB'>ID为 $Id 的生产记录删除失败!</div>";
			$OperationResult="N";
			}
		}
	}
else{
	$Log="<div class='redB'>读取ID为 $Id 的订单资料失败!更新不成功!</div>";
	$OperationResult="N";
	}
echo $OperationResult;
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>