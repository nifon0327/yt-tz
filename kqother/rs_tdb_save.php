<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="部门调动资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$NumberSTR="AND Number IN ($Ids)";	//指定员工的编号字串
	}
$Remark=FormatSTR($Remark);
if($DataIn !== 'ac'){
	$inRecode = "INSERT INTO $DataPublic.redeployb SELECT NULL,Number,'$BranchId','$ActionIn','$Month','$Remark','$Date','0','$Operator' FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
}else{
	$inRecode = "INSERT INTO $DataPublic.redeployb SELECT NULL,Number,'$BranchId','$ActionIn','$Month','$Remark','$Date','0','$Operator', 1, 0, '$Operator', '$Date', '$Operator', '$Date' FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
}
$inResult=@mysql_query($inRecode);
if($inResult){	
	$Log="&nbsp;&nbsp; $TitleSTR 成功. ($Ids)</br>";//更新
	$upSql = "UPDATE $DataPublic.staffmain SET BranchId='$ActionIn' WHERE 1 $NumberSTR";
	$upResult = mysql_query($upSql);
	if($upResult){
		$Log.="员工($Ids)的部门更新成功!";
		//短消息通知
		//$smsNote="员工($Ids) 部门调动成功,薪资结算时将使用新部门的底薪,请核实.";	//短消息内容
		//$smsfunId=4;include "subprogram/tosmsdata.php";								//短消息通知编号：权限，经理
		}
	else{
		$Log.="<div class='redB'>员工($Ids)的部门更新失败! $upSql <div><br>";
		$OperationResult="N";
		}
	}
else{
	$Log="<div class='redB'>&nbsp;&nbsp; $TitleSTR 失败! ($Ids)</div></br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
