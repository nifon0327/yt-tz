<?php 
//电信-EWEN
$TimeType=array();			//时间段类型：时间段1/时间段2/加班4/夜班0
$kqDefaultTime=array();		//预设签卡时间
$TimeRecord=array();		//隶属时间段的记录数;
$RestTimeRecord=array();	//中途休息时间段的记录数;
//检查是否有临时排班
$kqlspbResult = mysql_query("SELECT * FROM $DataIn.kqlspbb WHERE Number=$DefaultNumber and left(InTime,10)='$toDay' order by InTime",$link_id);
if($kqlspbTimeRow = mysql_fetch_array($kqlspbResult)){
	do{
		$kqDefaultTime[]=date("H:i",strtotime($kqlspbTimeRow["InTime"]));
		$RestTimeRecord[]=$kqlspbTimeRow["RestTime"];
		$kqDefaultTime[]=date("H:i",strtotime($kqlspbTimeRow["OutTime"]));
		$RestTimeRecord[]=$kqlspbTimeRow["RestTime"];
		$TimeType[]=$kqlspbTimeRow["TimeType"];
		$DateType="L";
		}while ($kqlspbTimeRow = mysql_fetch_array($kqlspbResult));
	}
else{
	//读取当天正常上班时间段
	$kqDefaultTimeResult = mysql_query("SELECT D.InTime,D.OutTime 
									   FROM $DataIn.kqpbb A
									   LEFT JOIN $DataIn.kqbcb B ON A.bcId=B.Id
									   LEFT JOIN $DataIn.kqbcsj C ON B.Id=C.bcId 
									   LEFT JOIN $DataIn.kqsjd D ON C.sjdId=D.Id 
									   WHERE 1 and A.Number=$DefaultNumber and B.StartDate<='$toDay' and B.EndDate>='$toDay' order by D.Id",$link_id);
	if($kqDefaultTimeRow = mysql_fetch_array($kqDefaultTimeResult)) {
		do{
			$kqDefaultTime[]=date("H:i",strtotime($kqDefaultTimeRow["InTime"]));
			$kqDefaultTime[]=date("H:i",strtotime($kqDefaultTimeRow["OutTime"]));
			$TimeType[]=1;
			}while ($kqDefaultTimeRow = mysql_fetch_array($kqDefaultTimeResult));
		}
	}
$bc_length=count($TimeType)-1;//时间段数
$kqDefaultTime_length=count($kqDefaultTime);//时间段上下班签卡次数
?>