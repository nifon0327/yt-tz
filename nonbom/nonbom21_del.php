<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件领用退回仓库记录";//需处理
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
//还原成领用状态
$UpdateSql="UPDATE  $DataIn.nonbom7_code C 
LEFT JOIN nonbom8_rebackfixed F ON F.BarCode=C.BarCode
LEFT JOIN nonbom8_reback B ON B.Id=F.BackId
SET C.Estate=2,C.Number=0
WHERE  B.Id IN ($Ids) AND B.Estate=1";
$UpdateResult=@mysql_query($UpdateSql);

$DelSql = "DELETE   B,F
FROM $DataIn.nonbom8_reback  B
LEFT JOIN $DataIn.nonbom8_rebackfixed F ON F.BackId=B.Id
WHERE  B.Id IN ($Ids) AND B.Estate=1";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败$DelSql</div><br>";
	$OperationResult="N";
	}





$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>