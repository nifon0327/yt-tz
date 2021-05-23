<?php 
//电信-EWEN
$TimeType=array();			//时间段类型：时间段1/时间段2/加班4/夜班0
$kqDefaultTime=array();		//预设签卡时间
$TimeRecord=array();		//隶属时间段的记录数;
$RestTimeRecord=array();	//中途休息时间段的记录数;
//检查是否有临时排班
$BCType="default";
$lspb_Result = mysql_query("SELECT * FROM $DataIn.kqlspbb WHERE 1 and Number=$DefaultNumber and DATE_FORMAT(InTime,'%Y-%m-%d')='$toDay' LIMIT 1",$link_id);
if($lspb_Row = mysql_fetch_array($lspb_Result)) {
	$InTime=$lspb_Row["InTime"];		//上班时间
	$OutTime=$lspb_Row["OutTime"];		//下班时间
	$InLate=$lspb_Row["InLate"];		//非0则计算迟到
	$OutEarly=$lspb_Row["OutEarly"];	//非0计算早退
	$TimeType=$lspb_Row["TimeType"];	
	$RestTime=$lspb_Row["RestTime"];	//中途休息日期
	$BCType="";
	}
else{
	$kqDefaultTime[0]="08:00:00";
	$kqDefaultTime[1]="12:00:00";
	$kqDefaultTime[2]="13:00:00";
	$kqDefaultTime[3]="17:00:00";
	$kqDefaultTime[4]="18:00:00";
	$kqDefaultTime[5]="21:00:00";
	$today_d_time[0]=$toDay." ".$kqDefaultTime[0];
	$today_d_time[1]=$toDay." ".$kqDefaultTime[1];
	$today_d_time[2]=$toDay." ".$kqDefaultTime[2];
	$today_d_time[3]=$toDay." ".$kqDefaultTime[3];
	$today_d_time[4]=$toDay." ".$kqDefaultTime[4];
	$today_d_time[5]=$toDay." ".$kqDefaultTime[5];
	}
$bc_length=count($TimeType)-1;//时间段数
$kqDefaultTime_length=count($kqDefaultTime);//时间段上下班签卡次数
?>