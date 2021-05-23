<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_cpdata
$DataPublic.net_cpsfdata
$DataPublic.net_cpcheckdiary
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="电脑清单";//需处理
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
/*
$DelSql = "DELETE $DataPublic.net_cpdata,$DataPublic.net_cpsfdata,$DataPublic.net_cpcheckdiary 
FROM $DataPublic.net_cpdata 
LEFT JOIN $DataPublic.net_cpsfdata ON $DataPublic.net_cpsfdata.hdId=$DataPublic.net_cpdata.Id
LEFT JOIN $DataPublic.net_cpcheckdiary ON $DataPublic.net_cpcheckdiary.hdId=$DataPublic.net_cpdata.Id
WHERE $DataPublic.net_cpdata.Id IN ($Ids)";
*/

$DelSql = "DELETE C,S,H 
FROM $DataPublic.net_cpdata C
LEFT JOIN $DataPublic.net_cpsfdata S ON S.hdId=C.Id
LEFT JOIN $DataPublic.net_cpcheckdiary H ON H.hdId=C.Id
WHERE C.Id IN ($Ids)";


$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql </div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.net_cpdata,$DataPublic.net_cpsfdata,$DataPublic.net_cpcheckdiary");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>