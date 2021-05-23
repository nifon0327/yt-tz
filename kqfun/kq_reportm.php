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

$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
if($checkRow1 = mysql_fetch_array($checkSql1)){
	$Days=date("t",strtotime($FristDay));
	}

//统计
$sumGTime=0;$sumWorkTime=0;$sumGJTime=0;$sumXJTime=0;$sumFJTime=0;$sumInLates=0;
$sumOutEarlys=0;$sumQjTime1=0;$sumQjTime2=0;$sumQjTime3=0;$sumQjTime4=0;$sumQjTime5=0;
$sumQjTime6=0;$sumQjTime7=0;$sumQjTime8=0;$sumQQTime=0;$sumKGTime=0;$sumYBs=0;$sumWXTime=0;
$T="<table width='$tableWidth' width='100%' border='0' cellspacing='0' bgcolor='#CCCCCC' style='font-size:9px;'>
	<tr>
		<td width='30' height='15' rowspan='2' class='A1111'><div align='center'>日期</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>星期</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
		<td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
		<td width='105' colspan='3' class='A1101'><div align='center'>加班工时(+直落)</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
		<td width='250' colspan='8' class='A1101'><div align='center'>请、休假工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>夜班</div></td>
		<td width='30' rowspan='2' class='A1100'><div align='center'>无效<br>工时</div></td>
		<td width='15' rowspan='2' class='A1101'>&nbsp;</td>
 	</tr>
  	<tr>
		<td width='40' height='15' align='center' class='A0101'>签到</td>
		<td width='40' class='A0101' align='center'>签退</td>
		<td width='35' class='A0101' align='center'>1.5倍</td>
		<td width='35' class='A0101' align='center'>2倍</td>
		<td width='35' class='A0101' align='center'>3倍</td>
		<td width='30' class='A0101' align='center'>事假</td>
		<td width='30' class='A0101' align='center'>病假</td>		
		<td width='40' class='A0101' align='center'>无薪假</td>
		<td width='30' class='A0101' align='center'>年假</td>
		<td width='30' class='A0101' align='center'>补休</td>
		<td width='30' class='A0101' align='center'>婚假</td>
		<td width='30' class='A0101' align='center'>丧假</td>
		<td width='30' class='A0101' align='center'>产假</td>
	</tr>
	<tr>
	<td colspan='25' height='425' class='A0011' valign='top'><div style='width:881;height:425;overflow-x:hidden;overflow-y:scroll'><table width='867' border='0' cellspacing='0'>";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;
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
				include "kqcode/checkio_model_countG.php";
				}
			else{
				include "kqcode/checkio_model_countGL.php";
				}
			}
		else{
			//非工作日的数据计算			
			if($pbType==0){
				include "kqcode/checkio_model_countX.php";
				}
			else{
				include "kqcode/checkio_model_countXL.php";
				}
			}
	////////////////////////////////////////
	//离职或未入职处理
	$checkD=mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE Number='$Number' and (ComeIn>'$CheckDate' OR Number IN(SELECT Number FROM $DataPublic.dimissiondata WHERE Number='$Number' and OutDate<'$CheckDate'))",$link_id);
	if($checkDRow = mysql_fetch_array($checkD)){
		$ddInfo="(离)";
		$AIcolor="";$AOcolor="";$jbTime=0;$AI="";$AO="";$aiTime="";$aoTime="";$aiTime=0;$aoTime=0;$WorkTime=0;$GJTime=0;$XJTime=0;$FJTime=0;$InLates=0;$OutEarlys=0;
		$QjTime1=0;$QjTime2=0;$QjTime3=0;$QjTime4=0;$QjTime5=0;$QjTime6=0;$QjTime7=0;$QjTime8=0;$QQTime=0;$KGTime=0;$YBs=0;
		$WXTime=$DateType=="X"?0:8;
		$GTime=$DateType=="X"?0:8;
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
		$QQTime=zerotospace($QQTime);		
		$KGTime=zerotospace($KGTime);
		$BKTime=zerotospace($BKTime);
		$YBs=zerotospace($YBs);
		$WXTime=zerotospace($WXTime);		
		//直落工时计算
		include "kqcode/checkio_model_zl.php";
		$T.="<tr><td class='A0101' align='center' $rowBgcolor width='30'>$j</td>
		<td class='A0101' align='center' width='50'>$weekInfo</td>
		<td class='A0101' align='center' width='40'><div $DateTypeColor>$DateType$ddInfo</div></td>
		<td class='A0101' align='center' width='40'><span $AIcolor>$aiTime</span></td>
		<td class='A0101' align='center' width='40'><span $AOcolor>$aoTime</span></td>
		<td class='A0101' align='center' width='30'>$GTime</td>
		<td class='A0101' align='center' width='30'>$WorkTime</td>
		<td class='A0101' align='center' width='35'>$GJTime</td>
		<td class='A0101' align='center' width='35'>$XJTime</td>
		<td class='A0101' align='center' width='35'>$FJTime</td>
		<td class='A0101' align='center' width='30'>$InLates</td>
		<td class='A0101' align='center' width='30'>$OutEarlys</td>
		<td class='A0101' align='center' width='30'>$QjTime1</td>
		<td class='A0101' align='center' width='30'>$QjTime2</td>
		<td class='A0101' align='center' width='40'>$QjTime3</td>
		<td class='A0101' align='center' width='30'>$QjTime4</td>
		<td class='A0101' align='center' width='30'>$QjTime5</td>
		<td class='A0101' align='center' width='30'>$QjTime6</td>
		<td class='A0101' align='center' width='30'>$QjTime7</td>
		<td class='A0101' align='center' width='30'>$QjTime8</td>
		<td class='A0101' align='center' width='30'>$QQTime</td>
		<td class='A0101' align='center' width='30'>$KGTime</td>
		<td class='A0101' align='center' width='30'>$YBs</td>
		<td class='A0100' align='center' width='32'>$WXTime</td>
		</tr>";
		
	}//end for
$sumYXJall=$sumQjTime4+$sumQjTime5+$sumQjTime6+$sumQjTime7+$sumQjTime8;
$sumGTime=zerotospace($sumGTime);
$sumWorkTime=zerotospace($sumWorkTime);
$sumGJTime=zerotospace($sumGJTime);
$sumXJTime=zerotospace($sumXJTime);
$sumFJTime=zerotospace($sumFJTime);
$sumInLates=zerotospace($sumInLates);
$sumOutEarlys=zerotospace($sumOutEarlys);
$sumQjTime1=zerotospace($sumQjTime1);
$sumQjTime2=zerotospace($sumQjTime2);
$sumQjTime3=zerotospace($sumQjTime3);
$sumQjTime4=zerotospace($sumQjTime4);
$sumQjTime5=zerotospace($sumQjTime5);
$sumQjTime6=zerotospace($sumQjTime6);
$sumQjTime7=zerotospace($sumQjTime7);
$sumQjTime8=zerotospace($sumQjTime8);
$sumQQTime=zerotospace($sumQQTime);
$sumKGTime=zerotospace($sumKGTime);
$sumYBs=zerotospace($sumYBs);
$sumWXTime=zerotospace($sumWXTime);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$T.="</table></div></td>
	</tr>
	<td class='A1111' align='center' colspan='5'>合计</td>
	<td class='A1101' align='center'>$sumGTime<input name='Dhours' type='hidden' id='Dhours' value='$sumGTime'></td>
	<td class='A1101' align='center'>$sumWorkTime<input name='Whours' type='hidden' id='Whours' value='$sumWorkTime'></td>
	<td class='A1101' align='center'>$sumGJTime<input name='Ghours' type='hidden' id='Ghours' value='$sumGJTime'></td>
	<td class='A1101' align='center'>$sumXJTime<input name='Xhours' type='hidden' id='Xhours' value='$sumXJTime'></td>
	<td class='A1101' align='center'>$sumFJTime<input name='Fhours' type='hidden' id='Fhours' value='$sumFJTime'></td>
	<td class='A1101' align='center'>$sumInLates<input name='InLates' type='hidden' id='InLates' value='$sumInLates'></td>
	<td class='A1101' align='center'>$sumOutEarlys<input name='OutEarlys' type='hidden' id='OutEarlys' value='$sumOutEarlys'></td>
	<td class='A1101' align='center'>$sumQjTime1<input name='SJhours' type='hidden' id='SJhours' value='$sumQjTime1'></td>
	<td class='A1101' align='center'>$sumQjTime2<input name='BJhours' type='hidden' id='BJhours' value='$sumQjTime2'></td>
	<td class='A1101' align='center'>$sumQjTime3<input name='WXJhours' type='hidden' id='WXJhours' value='$sumQjTime3'></td>
	<td class='A1101' align='center'>$sumQjTime4<input name='YXJhours' type='hidden' id='YXJhours' value='$sumYXJall'></td>
	<td class='A1101' align='center'>$sumQjTime5</td>
	<td class='A1101' align='center'>$sumQjTime6</td>
	<td class='A1101' align='center'>$sumQjTime7</td>		
	<td class='A1101' align='center'>$sumQjTime8</td>
	<td class='A1101' align='center'>$sumQQTime<input name='QQhours' type='hidden' id='QQhours' value='$sumQQTime'></td>
	<td class='A1101' align='center'>$sumKGTime<input name='KGhours' type='hidden' id='KGhours' value='$sumKGTime'></td>
	<td class='A1101' align='center'>$sumYBs<input name='YBs' type='hidden' id='YBs' value='$sumYBs'></td>
	<td class='A1100' align='center'>$sumWXTime<input name='WXhours' type='hidden' id='WXhours' value='$sumWXTime'></td>
	<td class='A1101' align='center'>&nbsp;</td>
	</tr>
	</table>";
?>