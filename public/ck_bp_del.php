<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck7_bprk
$DataIn.ck9_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="备品转入记录";//需处理
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
	if ($Id!=""){			//先更新后删除
		$Log.=$x.":<br>";
		$bpStateSql = "SELECT B.Estate,D.StuffCname FROM $DataIn.ck7_bprk B 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = B.StuffId 
		WHERE B.Id = $Id";
		$bpStateResult = mysql_query($bpStateSql);
		$bpStateRows = mysql_fetch_assoc($bpStateResult);
		$bpState = $bpStateRows['Estate'];
		$StuffCname= $bpStateRows['StuffCname'];

		if($bpState == '0'){
			$Log.="<div class='redB'>配件名为: $StuffCname 的备品入库记录已审核，不能删除 </div><br>";
		}else{
			$delSql = "DELETE FROM $DataIn.ck7_bprk WHERE Id='$Id' AND Estate>0";
			$delResult = mysql_query($delSql);
			if($delResult && mysql_affected_rows()>0){
				$Log.="配件名为: $StuffCname 的备品入库记录删除成功.<br>";
				$y++;
			}
			else{
				$Log.="<div class='redB'>配件名为: $StuffCname 的备品入库记录删除失败. $delSql </div><br>";
				$OperationResult="N";
			}
		}
		$x++;	
	}//end if ($Id!="")
}//end for($i=1;$i<$IdCount;$i++)

if($y==$IdCount){
	$chooseDate="";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>