<?php 
//电信-EWEN
//代码共享，ＭＣ未使用-EWEN 2012-08-19
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="部门小组-加工类配件关系";		//需处理
$upDataSheet="$DataIn.group_stuff";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$TypeIdSTR="and StuffId IN ($Ids)";
	$delSql="DELETE FROM $DataIn.group_stuff where 1 AND GroupId='$GroupId'";
	$delResult=mysql_query($delSql);
	$inRecode="INSERT INTO $DataIn.group_stuff SELECT NULL,'$GroupId',StuffId,'$Date','$Operator' FROM $DataIn.stuffdata WHERE 1 $TypeIdSTR";
	$inResult=@mysql_query($inRecode);
	if($inResult){
		$Log.="$Ids&nbsp;&nbsp;部门小组-加工类配件关系添加成功! </br>";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp;部门小组-加工类配件关系添加失败! $inRecode</div></br>";
		$OperationResult="N";
		}
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
