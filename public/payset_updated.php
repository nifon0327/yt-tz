<?php 
//步骤1 $DataPublic.paybase 二合一已更新电信---yang 20120801
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
$x=1;
$Date=date("Y-m-d");
if ($Currency==1){
	$Jtbz=0;  $Sbkk=0;  $Taxkk=0;$Dx=0;
}
//如果已经存在，则更新，否则新增
$InSql="INSERT INTO $DataPublic.paybase (Id,Number,Dx,Jj,Jtbz,Sbkk,Taxkk,Locks,Date,Operator) VALUES (NULL,'$Id','$Dx','$Jj','$Jtbz','$Sbkk','$Taxkk','0','$Date','$Operator')";
$InRes=@mysql_query($InSql);
if ($InRes && mysql_affected_rows()>0){ 
	 $Log="&nbsp;&nbsp;员工".$Number."的预设奖金新增成功!<br>";
	} 
else {
	$UpSql = "UPDATE $DataPublic.paybase SET Dx='$Dx',Jj='$Jj',Jtbz='$Jtbz',Sbkk='$Sbkk',Taxkk='$Taxkk',Locks='0',Date='$Date',Operator='$Operator' WHERE Number='$Id'";
	$UpResult = mysql_query($UpSql);
	if($UpResult){
		$Log="员工".$Id."的".$TitleSTR."更新成功！";
		}
	else{
		$Log="<div class=redB>员工".$Id."的".$TitleSTR."更新失败! $UpSql</div>";
		$OperationResult="N";
		}
	}

if ($OperationResult=="Y"){
	$UpSql2 = "UPDATE $DataPublic.staffmain SET Currency='$Currency' WHERE Number='$Id'";
	$UpResult = mysql_query($UpSql2);
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
