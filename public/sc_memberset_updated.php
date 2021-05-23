<?php 
//电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车间每天组员";		//需处理
$upDataSheet="$DataIn.sc1_memberset";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$SetStr="GroupId='$newGroupId',Locks='0'";
		$NumberSTR="";
		$Counts=count($_POST["ListId"]);
		$Ids="";
		for($i=0;$i<$Counts;$i++){
			$thisId=$_POST["ListId"][$i];
			$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
			}
		$NumberSTR="AND Number IN ($Ids)";
		$updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE 1 $NumberSTR AND Date='$Date' AND GroupId='$GroupId'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log="&nbsp;&nbsp;员工编号在 ($Ids) 的 员工调动成功!<br>";
			}
		else{
			$Log="<div class='redB'>&nbsp;&nbsp;员工编号在 ($Ids) 的 员工调动失败! $updateSQL </div><br>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Date=$Date&GroupId=$newGroupId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>