<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="固定资产";//需处理
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
		}
	}
$DelSql = "DELETE   A,B
FROM $DataIn.nonbom7_code  A
LEFT JOIN $DataIn.nonbom7_fixedassets  B ON B.BarCode=A.BarCode
WHERE  A.Id IN ($Ids)  AND A.rkId=0  AND B.Estate!=1 AND NOT EXISTS(SELECT C.BarCode FROM $DataIn.nonbom7_depreciationcharge C WHERE C.BarCode=A.BarCode)";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败,只能删除手动新增的固定资产且没有折旧记录</div><br>";
	$OperationResult="N";
	}
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>