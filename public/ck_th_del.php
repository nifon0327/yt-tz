<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="退换";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;

$Lens=count($checkid);
$Ids="";
/*for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
///////////////////////////////////////////////////////////////////////////////////////////
		//删除的条件：除了本次的数量，总退货数量>=总补仓数量，则可以删除
		$upSql="
		UPDATE $DataIn.ck2_thsheet S
				LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.ck9_stocksheet K ON S.StuffId=K.StuffId 
				LEFT JOIN 
				(
					SELECT SUM(Qty) AS unQty,StuffId,CompanyId FROM (
						SELECT SUM(S.Qty) AS Qty,S.StuffId,M.CompanyId FROM $DataIn.ck2_thsheet S,$DataIn.ck2_thmain M WHERE M.Id=S.Mid GROUP BY S.StuffId,M.CompanyId
						UNION ALL
						SELECT IFNULL(SUM(-S.Qty),0) AS Qty,S.StuffId,M.CompanyId FROM $DataIn.ck3_bcsheet S,$DataIn.ck3_bcmain M WHERE M.Id=S.Mid GROUP BY S.StuffId,M.CompanyId 
					) A GROUP BY StuffId,CompanyId ORDER BY StuffId,CompanyId  
				) B ON B.StuffId=S.StuffId 
		SET K.tStockQty=K.tStockQty+S.Qty,S.StuffId='0'
		WHERE S.Id=$Id AND S.Qty<=B.unQty AND B.CompanyId=M.CompanyId";
				
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			//删除此退换记录
			$delSql = "DELETE $DataIn.ck2_thsheet S FROM $DataIn.ck2_thsheet S WHERE S.Id=$Id AND S.StuffId=0"; 
			$delRresult = mysql_query($delSql);
			$Log.="&nbsp;&nbsp;记录 $Id 退换资料删除操作成功!<br>";
			}
		else{//不能删除，原因有补仓记录
			   $checkThSql=mysql_query("SELECT R.Id  FROM $DataIn.ck2_threview R WHERE R.Mid='$Id' AND R.Estate=2 LIMIT 1",$link_id);
				if($checkThRows = mysql_fetch_array($checkThSql)){
				      $RId=$checkThRows["Id"];
				      $updateSql="UPDATE  $DataIn.ck2_threview  SET Estate=0 WHERE Id='$RId' ";
				      $delRresult = mysql_query($updateSql);
				      $Log.="&nbsp;&nbsp;记录 $Id 退换资料审核状态更新成功!<br>";
				}
				else{
				$Log.="<div class='redB'>&nbsp;&nbsp;记录 $Id 退换资料删除操作失败!</div><br>";
				$OperationResult="N";
				}
		}
	}//end if($Id!="")	
}//end for($i=1;$i<$IdCount;$i++)		
*/


$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){			//先更新后删除
			
		$thStateSql = "SELECT T.Estate,D.StuffCname FROM $DataIn.ck2_thsheet T 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = T.StuffId 
		WHERE T.Id = $Id";
		$thStateResult = mysql_query($thStateSql);
		$thStateRows = mysql_fetch_assoc($thStateResult);
		$thState = $thStateRows['Estate'];
		$StuffCname= $thStateRows['StuffCname'];	
		if($thState == '0'){
		
		    $OperationResult="N";
			$Log.="<div class='redB'>配件名为: $StuffCname 的退货记录已审核，不能删除 </div><br>";
		}else if ($thState>0){
			//删除记录
			$delSql = "DELETE FROM $DataIn.ck2_thsheet WHERE Id='$Id' AND Estate>0"; 
			$delResult = mysql_query($delSql);
			if($delResult && mysql_affected_rows()>0){
				$Log.="$x -配件名为: $StuffCname 的退货记录删除成功.<br>";
				$y++;
				}
			else{
				$Log.="<div class='redB'>$x -配件名为: $StuffCname 的退货记录删除失败. $delSql </div><br>";
				$OperationResult="N";
				}
			}
	     $x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//删除全部没有明细的主单
$delMainSql = "DELETE $DataIn.ck2_thmain M FROM $DataIn.ck2_thmain M 
	LEFT JOIN $DataIn.ck2_thsheet S ON M.Id=S.Mid
	WHERE S.Id IS NULL"; 
$delMianRresult = mysql_query($delMainSql);

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>