<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployk
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="考勤状态调动资料";		//需处理
$upDataSheet="$DataPublic.redeployk";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$Date=date("Y-m-d");
		$Remark=FormatSTR($Remark);
		//更新
		$UpSql="UPDATE $upDataSheet B,$DataPublic.staffmain M,
			(SELECT MAX(Month) MaxMonth FROM $DataPublic.redeployk WHERE Number='$Number' AND Id!='$Id') C,
			(SELECT MAX(Month) AS MaxMonth FROM $DataIn.cwxzsheet WHERE Number='$Number' AND Estate='0') D
			SET 
			B.Month='$Month',B.ActionIn='$ActionIn',B.ActionOut='$ActionOut',B.Remark='$Remark',B.Date='$Date',B.Operator='$Operator',B.Locks='0',
			M.KqSign='$ActionIn'
			WHERE B.Id='$Id' AND M.Number=B.Number AND (C.MaxMonth IS NULL OR C.MaxMonth<='$Month') AND (D.MaxMonth IS NULL OR D.MaxMonth<'$Month')";
		$UpResult=mysql_query($UpSql);
		if($UpResult && mysql_affected_rows()>0){
			$Log="员工 $Number 的 $Log_Item$Log_Funtion更新成功.";
			}
		else{
			$Log="<div class='redB'>员工 $Number 的 $Log_Item$Log_Funtion更新失败,检查月份是否符合条件1.不能少于前一次的生效月份;2.生效月份的薪资表没有结付. $UpSql </div>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>