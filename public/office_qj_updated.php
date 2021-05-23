<?php 
/*
$DataPublic.kqqjsheet
$DataIn.kqdata
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="请假记录";		//需处理
$upDataSheet="$DataPublic.kqqjsheet";	//需处理
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
	case 17:
		$fromWebPage="office_qj_m";
		$Log_Funtion="审核";		$SetStr="Estate=0,Locks=0,Checker='$Operator'";				include "../model/subprogram/updated_model_3d.php";		break;
	case 162:
	{
		$fromWebPage="office_qj_m";
		$Log_Funtion="审核";	
		
		$returnQjSql = "update $upDataSheet set Estate = '2' Where Id = '$Id'";
		mysql_query($returnQjSql);
		
		$returnReasonSql = "Insert Into $DataPublic.returnreason (Id, tableId, targetTable, Reason, DateTime) Values (NULL, '$Id', '$upDataSheet','$ReturnReasons', '$DateTime')";
		mysql_query($returnReasonSql);
		
	}
	break;
	default:
		$FileType=".jpg";
		$FilePath="../download/bjproof/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="proof".$Id.$FileType;
		if($Proof!=""){//有上传文件
			$OldFile=$Proof;
			$Proof=UploadFiles($OldFile,$PreFileName,$FilePath);
			$ProofSTR=$Proof==""?"":",Proof='1'";
			}
		if($oldProof==1 && $ProofSTR==""){
			$FilePath1=FilePath.$PreFileName;
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				$ProofSTR=",Proof='0'";
				}			
			}
		$MonthTemp=substr($StartDate,0,7);
		$StartDate=$StartDate." ".$StartTime.":00";
		$EndDate=$EndDate." ".$EndTime.":00";
		$Date=date("Y-m-d");
		$Reason=FormatSTR($Reason);
		//条件//该月的考勤统计未生成
		$OtherWhere="AND Number NOT IN (SELECT Number From $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Number)";
		$SetStr="StartDate='$StartDate',EndDate='$EndDate',Reason='$Reason',Type='$Type',bcType='$bcType',Date='$Date', Estate = '1',Locks='0' $ProofSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
