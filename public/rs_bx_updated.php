<?php 
//电信-EWEN
	include "../model/modelhead.php";
	include "../model/kq_YearHolday.php";
	$fromWebPage=$funFrom."_$From";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="补休记录";		//需处理
	$upDataSheet="$DataPublic.bxSheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	//步骤3：需处理，更新操作
	$x=1;

	switch($ActionId){
		case 15:
		case 17:
		{
			$Ids = implode(",", $checkid);
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
			$MonthTemp=substr($StartDate,0,7);
			$StartDate=$StartDate." ".$StartTime.":00";
			$EndDate=$EndDate." ".$EndTime.":00";
			$Date=date("Y-m-d");
			//条件//该月的考勤统计未生成
			
			if($Attached!=""){//有上传文件
				$FileType=".jpg";
				$OldFile=$Attached;
				$FilePath="../download/staffbx/";
				if(!file_exists($FilePath)){
					makedir($FilePath);
					}
				$PreFileName=date("YmdHis").$FileType;
				$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
				$AttachedStr = $Attached==""?"":",Attached='$Attached'";
				
			}
	
			$Number = "";
			$oldBxInfoSql = "Select * From $DataPublic.bxSheet Where Id = '$Id'";
			$oldBxResult = mysql_query($oldBxInfoSql);
			if($oldBxRow = mysql_fetch_assoc($oldBxResult))
			{
				$Number = $oldBxRow["Number"];
				$oldStartDate = $oldBxRow["StartDate"];
				$oldEndDate = $oldBxRow["EndDate"];
				$oldCalculateType = $oldBxRow["type"];
			}
	
			$newHours = ($CalculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
			
			$newHours += $zlHours;
			$SetStr="StartDate='$StartDate',EndDate='$EndDate',
			Operator='$Operator', Note='$note', hours='$newHours' $AttachedStr";
			$upDateBxSql = "Update $upDataSheet Set $SetStr Where Id = '$Id'";
		
			if(mysql_query($upDateBxSql) && $Number != "")
			{
				$Log.= "$TitleSTR 更新成功";
			}
			else
			{
				$Log.= "$TitleSTR 更新失败";
				$OperationResult = "N";
			}
			
		break;
	}
	
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";