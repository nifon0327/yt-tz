<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品单位";//需处理
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
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$y++;
		}
	}
//删除数据库记录
$DelSql = "DELETE Ａ 
	FROM $DataPublic.productunit Ａ
	LEFT JOIN (
		SELECT * FROM (
			SELECT Unit FROM $DataIn.productdata GROUP BY Unit
			) Y
		) Z ON Z.Unit=A.Id
	WHERE A.Id IN ($Ids) AND Z.Unit IS NULL"; 
$DelResult = mysql_query($DelSql);
if ($DelResult){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功(如果记录仍在，则已有使用不能删除).<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql </div><br>";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.productunit");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>