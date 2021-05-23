<?php 
//电信-ZX  2012-08-01
//第一步：计算要查询的月份及当月考勤天数
$nowMonth=date("Y-m");			//现在的月份，默认
$CheckMonth=$nowMonth;
$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
if($CheckMonth==$nowMonth){
	$Days=date("d")-1;
	}
else{
	$Days=date("t",strtotime($FristDay));
	}

$checkSql1 = mysql_query("SELECT Id FROM $DataIn.stafftempmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
if($checkRow1 = mysql_fetch_array($checkSql1)){
	$Days=date("t",strtotime($FristDay));
	}

//统计
$sumGTime=0;$sumWorkTime=0;$sumGJTime=0;$sumXJTime=0;$sumFJTime=0;$sumInLates=0;
$sumOutEarlys=0;$sumQjTime1=0;$sumQjTime2=0;$sumQjTime3=0;$sumQjTime4=0;$sumQjTime5=0;
$sumQjTime6=0;$sumQjTime7=0;$sumQjTime8=0;$sumQQTime=0;$sumKGTime=0;$sumYBs=0;$sumWXTime=0;
$T="<br><table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>
	<tr class=''>
		<td width='30' class='A1111' align='center' height='19'>日期</td>
		<td width='50' class='A1101' align='center'>星期</td>
		<td width='80' class='A1101' align='center'>日期类别</td>
		<td width='80' class='A1101' align='center'>签到记录</td>
		<td width='80' class='A1101' align='center'>签退记录</td>
		<td width='80' class='A1101' align='center'>1倍工时</td>
		<td width='80' class='A1101' align='center'>1.5倍工时</td>
		<td width='80' class='A1101' align='center'>2倍工时</td>
		<td width='80' class='A1101' align='center'>3倍工时</td>
		<td width='80' class='A1101' align='center'>夜班次数</td>
	</tr>";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
for($i=0;$i<$Days;$i++){//日循环
	$j=$i+1;
	$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
	$ToDay=$CheckDate;
	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	$weekDay=date("w",strtotime($CheckDate));	 
	$weekInfo="星期".$Darray[$weekDay];
	$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
	$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
	if($holidayRow = mysql_fetch_array($holidayResult)){
		$jbTimes=$holidayRow["jbTimes"];
		switch($holidayRow["Type"]){
		case 0:		$DateTypeTemp="W";		break;
		case 1:		$DateTypeTemp="Y";		break;
		case 2:		$DateTypeTemp="F";		break;
			}
		}
	////////////////////////////////////////
		$AIcolor="";	$AOcolor="";
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo="";
		$aiTime=0;		$aoTime=0;
		$ATime=0;		$BTime=0;		$CTime=0;		$DTime=0;		$YBs=0;
		$DateType=$DateTypeTemp;
		//对调情况落实到个人：因为有时是部分员工对调
		$rqddResult = mysql_query("SELECT Id FROM $DataIn.temp_kqrqdd WHERE Number='$Number' and (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){			
			$DateType=$DateType=="X"?"G":"X";
			$ddInfo="(调)";
			}
		$DateTypeColor=$DateType=="G"?"":"class='greenB'";
		//读取班次
		include "kqcodetemp/checkio_model_pb.php";
		//读取签卡记录:签卡记录必须是已经审核的
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkiotemp 
		WHERE 1 AND Number=$Number AND ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
		 order by CheckTime",$link_id);
		if($ioRow = mysql_fetch_array($ioResult)) {
			do{
				$CheckTime=$ioRow["CheckTime"];
				$CheckType=$ioRow["CheckType"];
				$KrSign=$ioRow["KrSign"];
				switch($CheckType){
					case "I":
						$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aiTime=date("H:i",strtotime("$CheckTime"));						
						break;
					case "O":
						$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));			
						$aoTime=date("H:i",strtotime("$CheckTime"));						
						break;
					}					
				}while ($ioRow = mysql_fetch_array($ioResult));
			//读取当天的班次时间
			}
		//当天的数据计算开始
		if($DateType=="G"){
			//工作日的数据计算
			//if($pbType==0){
				include "kqcodetemp/checkio_model_countG.php";//只有正常排班
				//}
			//else{
				///include "kqcodetemp/checkio_model_countGL.php";//临时班
				//}
			}
		else{
			//非工作日的数据计算			
			if($pbType==0){
				include "kqcodetemp/checkio_model_countX.php";}
			else{
				include "kqcodetemp/checkio_model_countXL.php";}
			}
	////////////////////////////////////////
	//离职或未入职处理
	$checkD=mysql_query("SELECT Id FROM $DataIn.stafftempmain WHERE Number='$Number' AND (ComeIn>'$CheckDate' OR (OutTime<='$CheckDate' AND OutTime!='0000-00-00'))",$link_id);
	if($checkDRow = mysql_fetch_array($checkD)){
		$ddInfo="(离)";
		$AIcolor="";
		$AOcolor="";
		$AI="";
		$AO="";
		$aiTime="";
		$aoTime="";
		$aiTime=0;
		$aoTime=0;
		$ATime=0;
		$BTime=0;
		$CTime=0;
		$DTime=0;
		$YBs=0;
		}
	//当天的数据计算结束
	
	$sumATime=$sumATime+$ATime;
	$sumBTime=$sumBTime+$BTime;
	$sumCTime=$sumCTime+$CTime;
	$sumDTime=$sumDTime+$DTime;
	$sumYBs=$sumYBs+$YBs;
	$aiTime=SpaceValue($aiTime);
	$aoTime=SpaceValue($aoTime);
	$ATime=zerotospace($ATime);
	$BTime=zerotospace($BTime);
	$CTime=zerotospace($CTime);
	$DTime=zerotospace($DTime);
	$YBs=zerotospace($YBs);
	//直落工时计算
	include "kqcodetemp/checkio_model_zl.php";
	$T.="<tr><td class='A0111' align='center' $rowBgcolor>$j</td>
		<td class='A0101' align='center'>$weekInfo</td>
		<td class='A0101' align='center'><div $DateTypeColor>$DateType$ddInfo</div></td>
		<td class='A0101' align='center'><span $AIcolor>$aiTime</span></td>
		<td class='A0101' align='center'><span $AOcolor>$aoTime</span></td>
		<td class='A0101' align='center'>$ATime</td>
		<td class='A0101' align='center'>$BTime</td>
		<td class='A0101' align='center'>$CTime</td>
		<td class='A0101' align='center'>$DTime</td>
		<td class='A0101' align='center'>$YBs</td>
		</tr>";
	}//end for



$T.="<tr>
	<td class='A0111' align='center' colspan='5' >合计</td>
	<td class='A0101' align='center'>".zerotospace($sumATime)."<input name='Ahours' type='hidden' id='SJhours' value='$sumATime'></td>
	<td class='A0101' align='center'>".zerotospace($sumBTime)."<input name='Bhours' type='hidden' id='BJhours' value='$sumBTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumCTime)."<input name='Chours' type='hidden' id='WXJhours' value='$sumCTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumDTime)."<input name='Dhours' type='hidden' id='YXJhours' value='$sumDTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumYBs)."<input name='YBs' type='hidden' id='YBs' value='$sumYBs'></td>
	</tr>
	<tr class=''>
		<td width='30' class='A0111' align='center' height='19'>日期</td>
		<td width='50' class='A0101' align='center'>星期</td>
		<td width='80' class='A0101' align='center'>日期类别</td>
		<td width='80' class='A0101' align='center'>签到记录</td>
		<td width='80' class='A0101' align='center'>签退记录</td>
		<td width='80' class='A0101' align='center'>1倍工时</td>
		<td width='80' class='A0101' align='center'>1.5倍工时</td>
		<td width='80' class='A0101' align='center'>2倍工时</td>
		<td width='80' class='A0101' align='center'>3倍工时</td>
		<td width='80' class='A0101' align='center'>夜班次数</td>
	</tr></table>";
?>