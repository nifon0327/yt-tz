<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 传真资料操作");
$nowWebPage="fax_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="传真资料";
switch($Action){
	case "Claim":
		for($i=0;$i<count($checkid);$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Log_Funtion="分配";
		$ClaimDate=date("Y-m-d H:i:s");
		$Sql = "UPDATE $DataPublic.faxdata SET Claimer='$Number',ClaimDate='$ClaimDate' WHERE Id IN ($Ids)";
		$Result = mysql_query($Sql);
		if ($Result){
			$Log.="&nbsp;&nbsp;ID号在 $Ids  的传真分配成功! $Sql";
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>&nbsp;&nbsp;ID号在 $Ids  的传真分配失败! $Sql </div>";
			}
		break;
	default:
		$Log_Funtion="更新主题";
		$Sql = "UPDATE $DataPublic.faxdata SET Title='$TitleTemp' WHERE Id='$Id'";
		$Result = mysql_query($Sql);
		if ($Result){
			$Log.="&nbsp;&nbsp;ID号在 $Ids  的传真主题更新成功!  </br>";
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
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>