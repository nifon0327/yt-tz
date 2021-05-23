<?php 
//电信-ZX  2012-08-01
/*
$DataIn.staffrandp
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工奖惩记录";//需处理
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
$OtherWhere="AND Number NOT IN (SELECT Number FROM $DataIn.cwxzsheet WHERE Month=left($DataIn.staffrandp.Date,7) ORDER BY Number)";
$DelSql = "DELETE FROM $DataIn.staffrandp WHERE Id IN ($Ids) $OtherWhere"; 
*/
$OtherWhere="AND Number NOT IN (
								SELECT Number FROM $DataIn.cwxzsheet 
								WHERE Month=left($DataIn.staffrandp.Date,7) ORDER BY Number
								)";
$DelSql = "DELETE FROM $DataIn.staffrandp WHERE Id IN ($Ids) $OtherWhere"; 


$DelResult = mysql_query($DelSql);
if($DelResult){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功(如果记录仍在，则职位已使用不能删除).<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql </div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.staffrandp");
$Page=$IdCount==$y?1:$Page;
$chooseMonth=$IdCount==$y?"":$chooseMonth;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>