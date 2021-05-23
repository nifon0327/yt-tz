<?php 
//电信-EWEN
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
$nowWebPage ="kq_stat_reportm";
$fromWebPage="kq_stat_reportm";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage";
//步骤3：
$tableMenuS=600;$tableWidth=855;
ChangeWtitle("$SubCompany 月考勤统计");//需处理
$toWebPage="checkinout_save";
//第一步：计算要查询的月份及当月考勤天数
$nowMonth=date("Y-m");			//现在的月份，默认
if($CheckMonth==""){			//如果没有填月份，则为默认的现在月份
	$CheckMonth=substr($CheckDate,0,7);
	}
$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
if($CheckMonth==$nowMonth){
	$Days=date("d")-1;
	}
else{
	$Days=date("t",strtotime($FristDay));
	}
//如果是之前月份检查统计是否存在:如果是当月，则只有离职员工可以保存
$checkSql = mysql_query("SELECT Id FROM $DataIn.kqdata WHERE 1 and Number=$Number and Month='$CheckMonth' ORDER BY Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkSql)){
	$SaveSTR="NO";
	}
$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
if($checkRow1 = mysql_fetch_array($checkSql1)){
	$Days=date("t",strtotime($FristDay));
	}
//统计
$sumGTime=0;$sumWorkTime=0;$sumGJTime=0;$sumXJTime=0;$sumFJTime=0;$sumInLates=0;
$sumOutEarlys=0;$sumQjTime1=0;$sumQjTime2=0;$sumQjTime3=0;$sumQjTime4=0;$sumQjTime5=0;
$sumQjTime6=0;$sumQjTime7=0;$sumQjTime8=0;$sumQjTime9=0;$sumQQTime=0;$sumKGTime=0;$sumYBs=0;$sumWXTime=0;$sumdkHour=0;
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>日期</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>星期</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
		<td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
		<td width='120' colspan='3' class='A1101'><div align='center'>加点加班工时(+直落)</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
		<td width='160' colspan='9' class='A1101'><div align='center'>请、休假工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>夜班</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>无效<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>有薪<br>工时</div></td>
 	</tr>
  	<tr class=''>
		<td width='40' heigh38t='20'  align='center' class='A0101'>签到</td>
		<td width='40' class='A0101' align='center'>签退</td>
		<td width='38' class='A0101' align='center'>1.5倍</td>
		<td width='38' class='A0101' align='center'>2倍</td>
		<td width='38' class='A0101' align='center'>3倍</td>
		<td width='20' class='A0101' align='center'>事</td>
		<td width='20' class='A0101' align='center'>病</td>		
		<td width='20' class='A0101' align='center'>无</td>
		<td width='20' class='A0101' align='center'>年</td>
		<td width='20' class='A0101' align='center'>补</td>
		<td width='20' class='A0101' align='center'>婚</td>
		<td width='20' class='A0101' align='center'>丧</td>
		<td width='20' class='A0101' align='center'>产</td>
		<td width='20' class='A0101' align='center'>工</td>
	</tr>";
for($i=0;$i<$Days;$i++){//日循环
	$j=$i+1;
	$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
	//步骤4：星期处理
	$ToDay=$CheckDate;
	//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
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
		$test="";		$AIcolor="";	$AOcolor="";	$jbTime=0;
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo="";
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$XJTime=0;		$FJTime=0;		$InLates=0;		$OutEarlys=0;		
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;$QjTime9=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;			$WXTime=0;
		$DateType=$DateTypeTemp;
		//对调情况落实到个人：因为有时是部分员工对调
		$rqddResult = mysql_query("SELECT Id FROM $DataIn.kqrqdd WHERE Number='$Number' and (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){			
			$DateType=$DateType=="X"?"G":"X";
			$ddInfo="(调)";
			}
		$DateTypeColor=$DateType=="G"?"":"class='greenB'";
		//读取班次
		include "kqcode/checkio_model_pb.php";
		//读取签卡记录:签卡记录必须是已经审核的
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkinout 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
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
		$GTime=$DateType=="G"?8:0;
		if($DateType=="G"){
			//工作日的数据计算
			if($pbType==0){
				include "kqcode/checkio_model_countG.php";}
			else{
				include "kqcode/checkio_model_countGL.php";}
			}
		else{
			//非工作日的数据计算			
			if($pbType==0){
				include "kqcode/checkio_model_countX.php";}
			else{
				include "kqcode/checkio_model_countXL.php";}
			}
	////////////////////////////////////////
	//离职或未入职处理
	$checkD=mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE Number='$Number' and (ComeIn>'$CheckDate' OR Number IN(SELECT Number FROM $DataPublic.dimissiondata WHERE Number='$Number' and OutDate<'$CheckDate'))",$link_id);
	if($checkDRow = mysql_fetch_array($checkD)){
		$ddInfo="(离)";
		$AIcolor="";$AOcolor="";$jbTime=0;$AI="";$AO="";$aiTime="";$aoTime="";$aiTime=0;$aoTime=0;$WorkTime=0;$GJTime=0;$XJTime=0;$FJTime=0;$InLates=0;$OutEarlys=0;
		$QjTime1=0;$QjTime2=0;$QjTime3=0;$QjTime4=0;$QjTime5=0;$QjTime6=0;$QjTime7=0;$QjTime8=0;$QjTime9=0;$QQTime=0;$KGTime=0;$YBs=0;
		$WXTime=$DateType=="X"?0:8;
		$GTime=$DateType=="X"?0:8;
		}

	  $TodaydkHour=0;  //是否当天有抵扣，不能算旷工
	  $rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='$Number' AND dkDate='$CheckDate'  LIMIT 1",$link_id);
	  if($rqddRow = mysql_fetch_array($rqddResult)){
		    $TodaydkHour=$rqddRow["dkHour"];
			$sumdkHour=$sumdkHour+$TodaydkHour;
			$DateType="D";
			$ddInfo="($TodaydkHour)";
			$DateTypeColor="class='greenB'";
		}

	//当天的数据计算结束
	$sumGTime=$sumGTime+$GTime;
	$sumWorkTime=$sumWorkTime+$WorkTime;
	$sumGJTime=$sumGJTime+$GJTime;
	$sumXJTime=$sumXJTime+$XJTime;
	$sumFJTime=$sumFJTime+$FJTime;
	$sumInLates=$sumInLates+$InLates;
	$sumOutEarlys=$sumOutEarlys+$OutEarlys;
	$sumQjTime1=$sumQjTime1+$QjTime1;
	$sumQjTime2=$sumQjTime2+$QjTime2;
	$sumQjTime3=$sumQjTime3+$QjTime3;
	$sumQjTime4=$sumQjTime4+$QjTime4;
	$sumQjTime5=$sumQjTime5+$QjTime5;
	$sumQjTime6=$sumQjTime6+$QjTime6;
	$sumQjTime7=$sumQjTime7+$QjTime7;
	$sumQjTime8=$sumQjTime8+$QjTime8;
	$sumQjTime9=$sumQjTime9+$QjTime9;
	$sumQQTime=$sumQQTime+$QQTime;
	$sumKGTime=$sumKGTime+$KGTime;
	$sumYBs=$sumYBs+$YBs;
	$sumWXTime=$sumWXTime+$WXTime;
		$aiTime=SpaceValue($aiTime);
		$aoTime=SpaceValue($aoTime);
		$GTime=zerotospace($GTime);
		$WorkTime=zerotospace($WorkTime);
		$GJTime=zerotospace($GJTime);
		$XJTime=zerotospace($XJTime);
		$FJTime=zerotospace($FJTime);
		$InLates=zerotospace($InLates);
		$OutEarlys=zerotospace($OutEarlys);
		$QjTime1=zerotospace($QjTime1);
		$QjTime2=zerotospace($QjTime2);
		$QjTime3=zerotospace($QjTime3);
		$QjTime4=zerotospace($QjTime4);
		$QjTime5=zerotospace($QjTime5);
		$QjTime6=zerotospace($QjTime6);
		$QjTime7=zerotospace($QjTime7);
		$QjTime8=zerotospace($QjTime8);
		$QjTime9=zerotospace($QjTime9);
		$QQTime=zerotospace($QQTime);		
		$KGTime=zerotospace($KGTime);
		$BKTime=zerotospace($BKTime);
		$YBs=zerotospace($YBs);
		$WXTime=zerotospace($WXTime);		
		//直落工时计算
		include "kqcode/checkio_model_zl.php";
		echo"<tr><td class='A0111' align='center' $rowBgcolor>$j</td>";
		echo"<td class='A0101' align='center'>$weekInfo</td>";
		echo"<td class='A0101' align='center'><div $DateTypeColor>$DateType$ddInfo</div></td>";
		echo"<td class='A0101' align='center'><span $AIcolor>$aiTime</span></td>";
		echo"<td class='A0101' align='center'><span $AOcolor>$aoTime</span></td>";
		echo"<td class='A0101' align='center'>$GTime</td>";
		echo"<td class='A0101' align='center'>$WorkTime</td>";
		echo"<td class='A0101' align='center'>$GJTime</td>";
		echo"<td class='A0101' align='center'>$XJTime</td>";
		echo"<td class='A0101' align='center'>$FJTime</td>";
		echo"<td class='A0101' align='center'>$InLates</td>";
		echo"<td class='A0101' align='center'>$OutEarlys</td>";
		echo"<td class='A0101' align='center'>$QjTime1</td>";
		echo"<td class='A0101' align='center'>$QjTime2</td>";
		echo"<td class='A0101' align='center'>$QjTime3</td>";
		echo"<td class='A0101' align='center'>$QjTime4</td>";
		echo"<td class='A0101' align='center'>$QjTime5</td>";
		echo"<td class='A0101' align='center'>$QjTime6</td>";//  $test
		echo"<td class='A0101' align='center'>$QjTime7</td>";
		echo"<td class='A0101' align='center'>$QjTime8</td>";// $qjTest
		echo"<td class='A0101' align='center'>$QjTime9</td>";// $qjTest
		echo"<td class='A0101' align='center'>$QQTime</td>";
		//echo"<td class='A0101' align='center'>$KGTime</td>";
		if($TodaydkHour>0){
			$KGTime=$KGTime-$TodaydkHour;
			$sumKGTime=$sumKGTime-$TodaydkHour;
			$KGTime=zerotospace($KGTime);
			echo "<td class='A0101' align='center'>$KGTime</td>";	
		}
		else {
			echo"<td class='A0101' align='center'>$KGTime</td>";
			}	
		$TodaydkHour=zerotospace($TodaydkHour);			
		echo"<td class='A0101' align='center'>$YBs</td>";
		echo"<td class='A0101' align='center'>$WXTime</td>";
		echo"<td class='A0101' align='center'>$TodaydkHour</td>";
		echo"</tr>";
		
	}//end for
$sumYXJall=$sumQjTime4+$sumQjTime5+$sumQjTime6+$sumQjTime7+$sumQjTime8+$sumQjTime9;
echo"<tr>
	<td class='A0111' align='center' colspan='5' >合计</td>
	<td class='A0101' align='center'>".zerotospace($sumGTime)."<input name='Dhours' type='hidden' id='Dhours' value='$sumGTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumWorkTime)."<input name='Whours' type='hidden' id='Whours' value='$sumWorkTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumGJTime)."<input name='Ghours' type='hidden' id='Ghours' value='$sumGJTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumXJTime)."<input name='Xhours' type='hidden' id='Xhours' value='$sumXJTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumFJTime)."<input name='Fhours' type='hidden' id='Fhours' value='$sumFJTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumInLates)."<input name='InLates' type='hidden' id='InLates' value='$sumInLates'></td>
	<td class='A0101' align='center'>".zerotospace($sumOutEarlys)."<input name='OutEarlys' type='hidden' id='OutEarlys' value='$sumOutEarlys'></td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime1)."<input name='SJhours' type='hidden' id='SJhours' value='$sumQjTime1'></td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime2)."<input name='BJhours' type='hidden' id='BJhours' value='$sumQjTime2'></td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime3)."<input name='WXJhours' type='hidden' id='WXJhours' value='$sumQjTime3'></td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime4)."<input name='YXJhours' type='hidden' id='YXJhours' value='$sumYXJall'></td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime5)."</td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime6)."</td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime7)."</td>		
	<td class='A0101' align='center'>".zerotospace($sumQjTime8)."</td>
	<td class='A0101' align='center'>".zerotospace($sumQjTime9)."</td>
	<td class='A0101' align='center'>".zerotospace($sumQQTime)."<input name='QQhours' type='hidden' id='QQhours' value='$sumQQTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumKGTime)."<input name='KGhours' type='hidden' id='KGhours' value='$sumKGTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumYBs)."<input name='YBs' type='hidden' id='YBs' value='$sumYBs'></td>
	<td class='A0101' align='center'>".zerotospace($sumWXTime)."<input name='WXhours' type='hidden' id='WXhours' value='$sumWXTime'></td>
	<td class='A0101' align='center'>".zerotospace($sumdkHour)."<input name='dkhours' type='hidden' id='dkhours' value='$sumdkHour'></td>
	</tr>";
echo"<tr class=''>
	<td rowspan='2' class='A0111'><div align='center'>日期</div></td>
	<td rowspan='2' class='A0101'><div align='center'>星期</div></td>
	<td rowspan='2' class='A0101'><div align='center'>日期<br>类别</div></td>
	<td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
	<td rowspan='2' class='A0101'><div align='center'>应到<br>工时</div></td>
	<td rowspan='2' class='A0101'><div align='center'>实到<br>工时</div></td>
	<td colspan='3' class='A0101'><div align='center'>加点加班工时(+直落)</div></td>
	<td rowspan='2' class='A0101'><div align='center'>迟到</div></td>
	<td rowspan='2' class='A0101'><div align='center'>早退</div></td>
	<td colspan='9' class='A0101'><div align='center'>请、休假工时</div></td>
	<td rowspan='2' class='A0101'><div align='center'>缺勤<br>工时</div></td>
	<td rowspan='2' class='A0101'><div align='center'>旷工<br>工时</div></td>
	<td rowspan='2' class='A0101'><div align='center'>夜班</div></td>
	<td rowspan='2' class='A0101'><div align='center'>无效<br>工时</div></td>
	<td rowspan='2' class='A1101'><div align='center'>有薪<br>工时</div></td>
	</tr>
	<tr class=''>
	<td heigh38t='20'  align='center' class='A0101'>签到</td>
	<td class='A0101' align='center'>签退</td>
	<td class='A0101' align='center'>1.5倍</td>
	<td class='A0101' align='center'>2倍</td>
	<td class='A0101' align='center'>3倍</td>
	<td class='A0101' align='center'>事</td>
	<td class='A0101' align='center'>病</td>		
	<td class='A0101' align='center'>无</td>
	<td class='A0101' align='center'>年</td>
	<td class='A0101' align='center'>补</td>
	<td class='A0101' align='center'>婚</td>
	<td class='A0101' align='center'>丧</td>
	<td class='A0101' align='center'>产</td>
	<td class='A0101' align='center'>工</td>
	</tr></table>";
?>