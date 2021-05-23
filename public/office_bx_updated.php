<?php 
	/*
		$DataPublic.kqqjsheet
		$DataIn.kqdata
		二合一已更新
		电信-joseph
		*/
	include "../model/modelhead.php";
	$fromWebPage=$funFrom."_$From";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="补休记录";		//需处理
	$upDataSheet="$DataPublic.kqqjsheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	//步骤3：需处理，更新操作
	
	$x=1;
	switch($ActionId)
	{
		case 15:
		case 17:
		{
			$Ids = implode(",", $checkid);
			$fromWebPage="office_bx_m";
			$checkState = ($ActionId == 17)?0:2;
			$updateBxCheckSql = ($ActionId == 17)?"Update $DataPublic.bxsheet Set Estate = '$checkState', Checker='$Login_P_Number'":"Update $DataPublic.bxsheet Set Estate = '$checkState', Checker='$Login_P_Number', Reason = '$ReturnReasons'";
			$updateBxCheckSql .= " Where Id in ($Ids)";
						
			if(mysql_query($updateBxCheckSql))
			{
				$Log="$TitleSTR 操作成功!(如果记录未保存则输入的记录无效)<br>";
			}
			else
			{
				$Log="<div class=redB>$TitleSTR 操作失败! $inRecode </div><br>";
				$OperationResult="N";
			}
			
		}
		break;
		default:
		{
			$StartDate=$StartDate." ".$StartTime.":00";
			$EndDate=$EndDate." ".$EndTime.":00";
			$note = $Reason;
			$updateBxSql = "Update $DataPublic.bxsheet Set StartDate = '$StartDate', EndDate = '$EndDate', Note='$note', Checker = NULL, Estate='1',type = '$CalculateType' Where Id = '$Id'";
			
			if(mysql_query($updateBxSql))
			{
				$Log="$TitleSTR 操作成功!(如果记录未保存则输入的记录无效)<br>";
			}
			else
			{
				$Log="<div class=redB>$TitleSTR 操作失败! $inRecode </div><br>";
				$OperationResult="N";
			}
			
		}
		break;
	}
	
	if($fromWebPage=="")
	{
		$fromWebPage=$funFrom."_read";
	}
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
?>
