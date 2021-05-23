<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
//步骤2：
$Log_Item="补休记录";			//需处理
$fromWebPage=(strtolower($From) == "personal")?$funFrom."_personal":$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//锁定表?多表操作不能锁定
$StartDate=$StartDate." ".$StartTime.":00";
$EndDate=$EndDate." ".$EndTime.":00";
$Date=date("Y-m-d");
$Reason=FormatSTR($Reason);
$MonthTemp=substr($StartDate,0,7);

	//新加条件,加入的月份未生成
	if(count($ListId) == 0){
		$ListId[] = "$Login_P_Number";
	}
	
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/staffbx/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName=date("YmdHis").$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		
	}
	
	
	mysql_query("BEGIN");
	for($i=0; $i<count($ListId); $i++){
		$Number = $ListId[$i];
		
		$hours = ($CalculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
			if($hours == "")
			{
				$hours = "0";
			}
		$hours += $zlHours;
		
		$inRecode="INSERT INTO $DataPublic.bxSheet (Id,Number,StartDate,EndDate,hours,Note,Date,type,Attached,Estate, Operator) Values (NULL,'$Number','$StartDate','$EndDate','$hours','$note','$Date','$CalculateType', '$Attached','1','$Operator')";
		$inAction=@mysql_query($inRecode);
	}
	
	if(!mysql_errno())
	{ 
		mysql_query("commit");
		$Log.="$TitleSTR 操作成功!(如果记录未保存则输入的记录无效)<br>";			
	} 
	else
	{
		mysql_query("rollback");
		$Log.="<div class=redB>$TitleSTR 操作失败! $inRecode </div><br>";
		$OperationResult="N";
	}
	
	mysql_query("END");
	
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
