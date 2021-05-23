<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$y=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];		//行ID
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
switch($Type){
	case 2:$LogSTR="客户";
		$DelSQL = "DELETE A,B,C,D FROM $DataIn.linkmandata A LEFT JOIN $DataIn.usertable B ON B.Number=A.Id LEFT JOIN $DataIn.online C ON C.uId=B.Id LEFT JOIN $DataIn.sys_clientfunpower D ON D.UserId=B.Id WHERE A.Id IN ($Ids) AND A.Type='$Type'";
		break;
	case 3:
		$LogSTR="供应商";
		$DelSQL = "DELETE A,B,C,D  FROM $DataIn.linkmandata A LEFT JOIN $DataIn.usertable B ON B.Number=A.Id LEFT JOIN $DataIn.online C ON C.uId=B.Id LEFT JOIN $DataIn.sys4_gysfunpower D ON D.UserId=B.Id	 WHERE A.Id IN ($Ids) AND A.Type='$Type'";
		break;
	case 4:
		$LogSTR="Forward";
		$DelSQL = "DELETE FROM $DataIn.linkmandata WHERE Id IN ($Ids) AND Type='$Type'";
		break;
	case 5:
		$LogSTR="快递公司";
		$DelSQL = "DELETE FROM $DataIn.linkmandata WHERE Id IN ($Ids) AND Type='$Type'";
		break;
	}
$Log_Item=$LogSTR."联系人资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
//删除联系人时，同时权限
$DelResult= mysql_query($DelSQL);
if($DelResult){
	$Log.="&nbsp;&nbsp;$x -ID号为 $Id 的联系人资料删除操作成功！<br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp; $y -ID号为 $Id 的联系人资料删除操作失败!</div></br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.linkmandata,$DataIn.usertable,$DataIn.online,$DataIn.sys_clientfunpower,$DataIn.sys4_gysfunpower");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&ComeFrom=$ComeFrom&Type=$Type";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>