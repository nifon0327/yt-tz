<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="送货单明细";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
//$sql=" LOCK TABLES $DataIn.gys_shmain WRITE,$DataIn.gys_shsheet WRITE";$res=@mysql_query($sql);
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//读取送货资料
		$CheckSql= mysql_query("SELECT S.Mid,S.StockId,S.StuffId FROM $DataIn.gys_shsheet  S WHERE S.Id='$Id' and  NOT EXISTS(select K.gys_Id FROM $DataIn.ck1_rksheet K WHERE K.gys_Id=S.Id)",$link_id);
		//$CheckSql= mysql_query("SELECT Mid,StockId,StuffId FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Locks=1",$link_id);
		if($CheckRow = mysql_fetch_array($CheckSql)){
			$Mid=$CheckRow["Mid"];
			$StockId=$CheckRow["StockId"];
			$StuffId=$CheckRow["StuffId"];
			$delSql = "DELETE FROM $DataIn.gys_shsheet WHERE Id='$Id'";
			$delRresult = mysql_query($delSql);
			if($delRresult && mysql_affected_rows()>0){
				$Log.="配件 $StuffId 的需求单 $StockId 待送货记录删除成功!<br>";
				//主入库单
				$delMainSql = "DELETE FROM $DataIn.gys_shmain WHERE Id=$Mid AND Id NOT IN (SELECT Mid FROM $DataIn.gys_shsheet WHERE Mid=$Mid)"; 
				$delMianRresult = mysql_query($delMainSql);
				if($delMianRresult && mysql_affected_rows()>0){
					$Log.="主入库单已经没有内容，清除成功!<br>";
					
					//删除送货信息内容
					$delSendSql = "DELETE FROM $DataPublic.come_data WHERE Mid=$Mid AND AND cSign=7 AND TypeId=1 AND CompanyId='$myCompanyId'"; 
					//echo $delSendSql ;
					$delSendRresult = mysql_query($delSendSql);
					}
				}
			}//end if($CheckRow = mysql_fetch_array($CheckSql))
		}//end if($Id!="")	
	}//end for($i=1;$i<$IdCount;$i++)
//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
//操作日志
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE gys_shmain");
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE gys_shsheet");
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";
?>