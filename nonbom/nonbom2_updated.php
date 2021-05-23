<?php 
//非BOM配件子分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件子分类";		//需处理
$upDataSheet="$DataPublic.nonbom2_subtype";	//需处理
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
		$TypeName=FormatSTR($TypeName);
		$Name=FormatSTR($Name);
		$NameRule=FormatSTR($NameRule);
		//初审人检查
		$checkBuyerSql=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE Name='$Name' LIMIT 1",$link_id);
		if($chekBuyerRow=mysql_fetch_array($checkBuyerSql)){
			$Number=$chekBuyerRow["Number"];
			$NumberSTR=",CheckerId='$Number'";
			}
		else{
			$Log.="<span class='redB'>采购更新失败</span><br>";
			}
$GetSign=$GetSign[0];
$SetStr="SendFloor='$SendFloor',TypeName='$TypeName',BuyerId='$BuyerId',mainType='$mainType',FirstId='$FirstId',NameRule='$NameRule',Remark='$Remark',Date='$DateTime',GetSign='$GetSign',Operator='$Operator',Locks='0' $NumberSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>