<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="预设奖金";		//需处理
$upDataSheet="$DataPublic.paybase";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$UpSql = "UPDATE $DataPublic.paybase A 
LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
SET A.Jj=A.Jj-'$ReducedValue',A.Locks='0',A.Date='$DateTime',A.Operator='$Operator' WHERE B.Estate=1 AND B.KqSign='3'";
$UpResult = mysql_query($UpSql);
if($UpResult){
	$Log="员工的".$TitleSTR."成功！";
	}
else{
	$Log="<div class=redB>员工的".$TitleSTR."失败! $UpSql</div>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
