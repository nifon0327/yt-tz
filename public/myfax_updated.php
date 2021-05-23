<?php 
//电信-yang 20120801
//代码、数据库共享-EWEN (传真文件分开存放)
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 传真资料操作");
$nowWebPage="myfax_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="传真资料";
switch($Action){
	case "Del":
		$ChooseType=2;
		for($i=0;$i<count($checkid);$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Log_Funtion="删除";
		$delSql = "DELETE FROM $DataPublic.faxdata WHERE Id IN ($Ids)";
		$delRresult = mysql_query($delSql);
		if ($delRresult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp;ID号在 $Ids 的传真删除成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>&nbsp;&nbsp;$x-ID号在 $Ids 的传真删除失败!</div></br>";
			}
		//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.faxdata");
		break;
	case "Claim":
		$ChooseType=1;
		for($i=0;$i<count($checkid);$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Log_Funtion="读取";
		$ClaimDate=date("Y-m-d H:i:s");
		$Sql = "UPDATE $DataPublic.faxdata SET Sign='0' WHERE Id IN ($Ids)";
		$Result = mysql_query($Sql);
		if ($Result){
			$Log.="&nbsp;&nbsp;ID号在 $Ids  的传真读取标记成功! $Sql</br>";
			
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>&nbsp;&nbsp;ID号在 $Ids  的传真读取标记失败! $Sql </div></br>";
			}
		break;
	default:
		$Log_Funtion="更新主题";
		$Sql = "UPDATE $DataPublic.faxdata SET Title='$TitleTemp' WHERE Id='$Id'";
		$Result = mysql_query($Sql);
		if ($Result){
			$Log.="&nbsp;&nbsp;ID号在 $Ids  的传真主题更新成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>&nbsp;&nbsp;ID号在 $Ids  的传真主题更新失败!</div></br>";
			}
		break;			
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="ChooseType=$ChooseType";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>