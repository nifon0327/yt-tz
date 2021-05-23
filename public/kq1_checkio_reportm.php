<?php 
/*
mc 验厂文件 ewen 2013-08-03 OK
验厂模式2013-05后的记录
工作日：加班时间截止20:00,超点与直落单独计算，不在此页面显示
周六：加班截止时间为20:00，超点与直落单独计算，不在此页面显示
周日：独立计算，不在此页面显示
要求：每周加班时间不超过60小时，超出的另计

原模式：
全部显示，直浇工时累加至加班工时中

修改：
#1、签退分上述两类处理
#2、验厂模式过滤周日记录
#3、直落记录只在原模式进行处理
*/
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
$KqSignStr="";	
if ($KqSign!="") {
	$KqSignStr=" AND M.KqSign='$KqSign'";
	}
if($needSign == 'no'){
        $CheckStaffSql = "SELECT M.Number,M.Name,J.Name AS JobName
                          FROM $DataPublic.staffmain M 
                          INNER JOIN $DataPublic.jobdata J ON J.Id=M.JobId
                          WHERE M.Number = '$Number'";
    }else{
        $CheckStaffSql = "SELECT A.Number,A.Name,A.JobName FROM (
							SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId
							FROM $DataPublic.staffmain M
							LEFT JOIN $DataIn.checkinout C ON  M.Number=C.Number
							LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE C.CheckTime LIKE '$CheckMonth%' AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' $KqSignStr
							GROUP BY C.Number 
							UNION ALL 
							SELECT M.Number,M.Name,J.Name AS JobName,M.BranchId,M.JobId  
							FROM $DataPublic.staffmain M
							LEFT JOIN $DataPublic.kqqjsheet Q ON  M.Number=Q.Number
							LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
							LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
							WHERE (Q.StartDate  LIKE '$CheckMonth%'  OR Q.EndDate  LIKE '$CheckMonth%'  OR  (Q.StartDate<'$CheckMonth-01'  AND Q.EndDate>'$CheckMonth-01'))  AND M.GroupId!='517' AND G.Estate=1 AND M.cSign='$Login_cSign' AND M.KqSign=1 $KqSignStr
							GROUP BY Q.Number 
							) A GROUP BY A.Number 
							ORDER BY A.BranchId,A.JobId,A.Number";
    }

$CheckStaff= mysql_query($CheckStaffSql ,$link_id);
if($StaffRow = mysql_fetch_array($CheckStaff)){
	$StaffList="<select name='Number' id='Number' onchange='document.form1.submit()'>";
	$n=1;
	do{
		$NumberT=$StaffRow["Number"];
		$Number=$Number==""?$NumberT:$Number;
		$NameT=$StaffRow["Name"];
		$JobName=$StaffRow["JobName"];
		if($Number==$NumberT){
			$StaffList.="<option value='$NumberT' selected>$n $JobName $NameT $NumberT</option>";
			}
		else{
			$StaffList.="<option value='$NumberT' >$n $JobName $NameT $NumberT</option>";
			}
		$n++;
		}while ($StaffRow = mysql_fetch_array($CheckStaff));
	$StaffList.="</select>&nbsp;";
	}
$SelectCode=$StaffList."<input name='CheckMonth' type='text' id='CheckMonth' size='10' maxlength='7' value='$CheckMonth' onchange='javascript:document.form1.submit();'>&nbsp;<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select> &nbsp; ";
$selStr="selFlag" . $KqSign;
$$selStr="selected";
$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''><option  value=''   $selFlag>--全部--</option><option  value='1'  $selFlag1>考勤有效</option><option value='2' $selFlag2>考勤参考</option></select>";
//如果是之前月份检查统计是否存在:如果是当月，则只有离职员工可以保存
$checkSql = mysql_query("SELECT Id FROM $DataIn.kqdata WHERE 1 and Number=$Number and Month='$CheckMonth' ORDER BY Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkSql)){
	$SaveSTR="NO";
	}
$checkSql1 = mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE 1 and Number=$Number and Estate=0 ORDER BY Id LIMIT 1",$link_id);
if($checkRow1 = mysql_fetch_array($checkSql1)){
	$Days=date("t",strtotime($FristDay));
	}
include "../model/subprogram/add_model_t.php";
//统计
$sumGTime=0;$sumWorkTime=0;$sumGJTime=0;$sumXJTime=0;$sumFJTime=0;$sumInLates=0;
$sumOutEarlys=0;$sumQjTime1=0;$sumQjTime2=0;$sumQjTime3=0;$sumQjTime4=0;$sumQjTime5=0;
$sumQjTime6=0;$sumQjTime7=0;$sumQjTime8=0;$sumQQTime=0;$sumKGTime=0;$sumYBs=0;$sumWXTime=0;$sumdkHour=0;
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
echo"<tr class='' align='center'>
		<td width='30' rowspan='2' class='A1111'>日期</td>
		<td width='50' rowspan='2' class='A1101'>星期</td>
		<td width='60' rowspan='2' class='A1101'>日期类别</td>
		<td height='19' width='80' colspan='2' class='A1101'>签卡记录</td>
		<td width='30' rowspan='2' class='A1101'>应到<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>实到<br>工时</td>
		<td width='120' colspan='3' class='A1101'>加点加班工时(+直落)</td>
		<td width='30' rowspan='2' class='A1101'>迟到</td>
		<td width='30' rowspan='2' class='A1101'>早退</td>
		<td width='160' colspan='9' class='A1101'>请、休假工时</td>
		<td width='30' rowspan='2' class='A1101'>缺勤<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>旷工<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>无效<br>工时</td>
		<td width='30' rowspan='2' class='A1101'>有薪<br>工时</td>
 	</tr>
  	<tr class=''  align='center'>
		<td width='40' heigh38t='20' class='A0101'>签到</td>
		<td width='40' class='A0101'>签退</td>
		<td width='38' class='A0101'>1.5倍</td>
		<td width='38' class='A0101'>2倍</td>
		<td width='38' class='A0101'>3倍</td>
		<td width='20' class='A0101'>事</td>
		<td width='20' class='A0101'>病</td>		
		<td width='20' class='A0101'>无</td>
		<td width='20' class='A0101'>年</td>
		<td width='20' class='A0101'>补</td>
		<td width='20' class='A0101'>婚</td>
		<td width='20' class='A0101'>丧</td>
		<td width='20' class='A0101'>产</td>
		<td width='20' class='A0101'>工</td>
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
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo=$ddSign="";
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$XJTime=0;		$FJTime=0;		$InLates=0;		$OutEarlys=0;		
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;$QjTime9=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;			$WXTime=0;   
		$DateType=$DateTypeTemp;
		//对调情况落实到个人：因为有时是部分员工对调
		$rqddResult = mysql_query("SELECT Id,XDate FROM $DataIn.kqrqdd WHERE Number='$Number' AND (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){
			$ddSign=1;
			$weekDayTempX=date("w",strtotime($rqddRow["XDate"]));		//调动的休息日
			$weekDayTempG=date("w",strtotime($rqddRow["GDate"]));	//调动的工作日
			if($DateType=="G"){
				$ddInfo=$weekDayTempX==0?"(调日)":"(调六)";
				}
			else{
				$ddInfo="(调".$Darray[$weekDayTempG].")";
				}
			$DateType=$DateType=="X"?"G":"X";
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
					case "O"://#1 有区别
						$CheckYM=date("Y-m",strtotime($CheckTime));//默认日期
						//echo $CheckYM;
						$Yischeck=0;
						if($Number!=10572 && $Number!=11710 && $Number!=11359 && $Number!=10551 && $Number!=10812 && $Number!=10470 && $Number!=11271 
							   && $Number!=11742 && $Number!=11661 && $Number!=11624 && $Number!=10641 && $Number!=10518 && $Number!=10397) {
							$Yischeck=1;
							
						}else {
							if ($CheckYM>'2013-07') {
								//echo "Herere----$CheckYM";
								$Yischeck=1;	
							}
						}	
						//echo "$Yischeck -- $CheckTime --- $Number";
						if($CheckMonth>"2013-04"){//按新的计算方式显示
							
							if($pbType==1){//临时班
								$EndTimeGap=strtotime($CheckTime)-strtotime($AI);//时间差秒数
								//$EndTimeTemp1=intval($EndTimeGap/3600/0.5)-20;//30分钟的次数
								$EndTimeTemp1=intval($EndTimeGap/3600/0.25)-40;//30分钟的次数
								if($EndTimeTemp1>0){//超出时间:
									//$EndMinute=$EndTimeTemp1*30;//超出部分的总分钟数
									$EndMinute=$EndTimeTemp1*15;//超出部分的总分钟数
									$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
									$ioTime=date("H:i",strtotime($CheckTime));
									}
								//*************
								}
							else{//正班：工作日、周六计算，周日不计算
								$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 20:00:00");
								//$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
								$EndTimeTemp1=intval($EndTimeGap/3600/0.25);//30分钟的次数
								if($EndTimeTemp1>0){//超出时间:
									//$EndMinute=$EndTimeTemp1*30;//超出部分的总分钟数
									$EndMinute=$EndTimeTemp1*15;//超出部分的总分钟数
									$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
									$ioTime=date("H:i",strtotime($CheckTime));
									}
								else{
									//##################################17:30-18:30签退的情况：需将时间前移 ewen 2013-08-27
									//if($Number!=11710 && $Number!=11359 && $Number!=10572 && $Number!=10812 && $Number!=10551 && $Number!=11271 && $Number!=11742){
									if($Yischeck==1) {
										
										if($CheckTime>=($CheckDate." 17:00:00")){
											$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 17:00:00");
										}else{
											if($CheckTime>=($CheckDate." 12:00:00")){
												$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 12:00:00");
											}
										}	
										//$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
										$EndTimeTemp1=intval($EndTimeGap/3600/0.25);//30分钟的次数
										if($EndTimeTemp1>0 && $EndTimeTemp1<6){//17:30-18:30签退，则时间前移处理
											//$EndMinute=$EndTimeTemp1*30;//超出17:00的总分钟数
											$EndMinute=$EndTimeTemp1*15;//超出17:00的总分钟数
											$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
											$ioTime=date("H:i",strtotime($CheckTime));
											}
										}
									//##################################17:30-18:30签退的情况：需将时间前移
									}
								}
							}
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
		else{//非工作日的数据计算	
			if($pbType==0){//休息日正班
				include "kqcode/checkio_model_countX.php";
				}
			else{//休息日临时班
				include "kqcode/checkio_model_countXL.php";
				}
			}
	////////////////////////////////////////
	
	
	//离职或未入职处理
	$checkD=mysql_query("SELECT A.Id,A.ComeIn,B.outDate FROM $DataPublic.staffmain A LEFT JOIN $DataPublic.dimissiondata B On B.Number = A.Number WHERE A.Number='$Number' and (A.ComeIn>'$CheckDate' OR A.Number IN(SELECT B.Number FROM $DataPublic.dimissiondata WHERE B.Number='$Number' and B.OutDate<'$CheckDate'))",$link_id);
	if($checkDRow = mysql_fetch_array($checkD)){
		$outDate = $checkDRow["outDate"];
		$comeIn = $checkDRow["ComeIn"];
		{
			$ddInfo="(离)";
			$AIcolor="";$AOcolor="";$jbTime=0;$AI="";$AO="";$aiTime="";$aoTime="";	$aiTime=0;$aoTime=0;$WorkTime=0;$GJTime=0;$XJTime=0;$FJTime=0;$InLates=0;$OutEarlys=0;
			$QjTime1=0;$QjTime2=0;$QjTime3=0;$QjTime4=0;$QjTime5=0;$QjTime6=0;$QjTime7=0;$QjTime8=0;$QjTime9=0;$QQTime=0;$KGTime=0;$YBs=0;
			$WXTime=$DateType=="X"?0:8;
			$GTime=$DateType=="X"?0:8;
			}
		}
	if($j==4 && substr($CheckDate,0,7)=='2012-10'){			
		$WXTime=0;
		$GTime=0;
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
//#2 过滤周日记录
	if($CheckMonth>"2013-04"){//按新的计算方式显示:如果是周日，不计算；验厂模式，周日记录
		
		/*
		if($ddInfo=="(调日)" || $weekDay==0){
			$aiTime=$aoTime="&nbsp;";
			$XJTime=0;
			}
			*/
			if(($ddInfo=="(调日)"  && $ddSign==1) || ($ddSign=="" && $weekDay==0)){//如果是调动且调为周日，以及没有调动且为周日的
				$aiTime=$aoTime="&nbsp;";
				$XJTime=0;
				}
		
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
	
	//#3 直落工时计算
	if($CheckMonth<"2013-05"){//2013-05之前的直落工时，与加班工时累加在一起处理；之后的为验厂模式不做处理
		include "kqcode/checkio1_model_zl.php";
		}
	if($j==4 && substr($CheckDate,0,7)=='2012-10'  && $ZL_Hours!=0){
    	$XJTime=$XJTime+$ZL_Hours;
        $GJTime=0;
		$GJTime=zerotospace($GJTime);
        $sumXJTime=$sumXJTime+$ZL_Hours;
        }
         
	echo"<tr align='center'><td class='A0111' $rowBgcolor>$j</td>";
	echo"<td class='A0101'>$weekInfo</td>";
	echo"<td class='A0101'><div $DateTypeColor>$DateType$ddInfo</div></td>";
	echo"<td class='A0101'><span $AIcolor>$aiTime</span></td>";
	echo"<td class='A0101'><span $AOcolor>$aoTime</span></td>";
	echo"<td class='A0101'>$GTime</td>";
	echo"<td class='A0101'>$WorkTime</td>";
	echo"<td class='A0101'>$GJTime</td>";
	echo"<td class='A0101'>$XJTime</td>";
	echo"<td class='A0101'>$FJTime</td>";
	echo"<td class='A0101'>$InLates</td>";
	echo"<td class='A0101'>$OutEarlys</td>";
	echo"<td class='A0101'>$QjTime1</td>";
	echo"<td class='A0101'>$QjTime2</td>";
	echo"<td class='A0101'>$QjTime3</td>";
	echo"<td class='A0101'>$QjTime4</td>";
	echo"<td class='A0101'>$QjTime5</td>";
	echo"<td class='A0101'>$QjTime6</td>";//  $test
	echo"<td class='A0101'>$QjTime7</td>";
	echo"<td class='A0101'>$QjTime8</td>";// $qjTest
	echo"<td class='A0101'>$QjTime9</td>";
	echo"<td class='A0101'>$QQTime</td>";
		
	if($TodaydkHour>0){
		$KGTime=$KGTime-$TodaydkHour;
		$sumKGTime=$sumKGTime-$TodaydkHour;
		$KGTime=zerotospace($KGTime);
		echo "<td class='A0101'>$KGTime</td>";	
		}
	else {
		echo"<td class='A0101'>$KGTime</td>";
		}
	$TodaydkHour=zerotospace($TodaydkHour);
	echo"<td class='A0101'>$WXTime $TT</td>";
	echo"<td class='A0101'>$TodaydkHour</td>";
	echo"</tr>";	
	}//end for
$sumYXJall=$sumQjTime4+$sumQjTime5+$sumQjTime6+$sumQjTime7+$sumQjTime8+$sumQjTime9;
echo"<tr align='center'>
	<td class='A0111' colspan='5' >合计</td>
	<td class='A0101'>".zerotospace($sumGTime)."<input name='Dhours' type='hidden' id='Dhours' value='$sumGTime'></td>
	<td class='A0101'>".zerotospace($sumWorkTime)."<input name='Whours' type='hidden' id='Whours' value='$sumWorkTime'></td>
	<td class='A0101'>".zerotospace($sumGJTime)."<input name='Ghours' type='hidden' id='Ghours' value='$sumGJTime'></td>
	<td class='A0101'>".zerotospace($sumXJTime)."<input name='Xhours' type='hidden' id='Xhours' value='$sumXJTime'></td>
	<td class='A0101'>".zerotospace($sumFJTime)."<input name='Fhours' type='hidden' id='Fhours' value='$sumFJTime'></td>
	<td class='A0101'>".zerotospace($sumInLates)."<input name='InLates' type='hidden' id='InLates' value='$sumInLates'></td>
	<td class='A0101'>".zerotospace($sumOutEarlys)."<input name='OutEarlys' type='hidden' id='OutEarlys' value='$sumOutEarlys'></td>
	<td class='A0101'>".zerotospace($sumQjTime1)."<input name='SJhours' type='hidden' id='SJhours' value='$sumQjTime1'></td>
	<td class='A0101'>".zerotospace($sumQjTime2)."<input name='BJhours' type='hidden' id='BJhours' value='$sumQjTime2'></td>
	<td class='A0101'>".zerotospace($sumQjTime3)."<input name='WXJhours' type='hidden' id='WXJhours' value='$sumQjTime3'></td>
	<td class='A0101'>".zerotospace($sumQjTime4)."<input name='YXJhours' type='hidden' id='YXJhours' value='$sumYXJall'></td>
	<td class='A0101'>".zerotospace($sumQjTime5)."</td>
	<td class='A0101'>".zerotospace($sumQjTime6)."</td>
	<td class='A0101'>".zerotospace($sumQjTime7)."</td>		
	<td class='A0101'>".zerotospace($sumQjTime8)."</td>
	<td class='A0101'>".zerotospace($sumQjTime9)."</td>
	<td class='A0101'>".zerotospace($sumQQTime)."<input name='QQhours' type='hidden' id='QQhours' value='$sumQQTime'></td>
	<td class='A0101'>".zerotospace($sumKGTime)."<input name='KGhours' type='hidden' id='KGhours' value='$sumKGTime'></td>
	<td class='A0101'>".zerotospace($sumWXTime)."<input name='WXhours' type='hidden' id='WXhours' value='$sumWXTime'></td>
	<td class='A0101'>".zerotospace($sumdkHour)."<input name='dkhours' type='hidden' id='dkhours' value='$sumdkHour'></td>
	</tr>";
echo"<tr class='' align='center'>
	<td rowspan='2' class='A0111'>日期</td>
	<td rowspan='2' class='A0101'>星期</td>
	<td rowspan='2' class='A0101'>日期类别</td>
	<td height='19' colspan='2' class='A0101'>签卡记录</td>
	<td rowspan='2' class='A0101'>应到<br>工时</td>
	<td rowspan='2' class='A0101'>实到<br>工时</td>
	<td colspan='3' class='A0101'>加点加班工时(+直落)</td>
	<td rowspan='2' class='A0101'>迟到</td>
	<td rowspan='2' class='A0101'>早退</td>
	<td colspan='9' class='A0101'>请、休假工时</td>
	<td rowspan='2' class='A0101'>缺勤<br>工时</td>
	<td rowspan='2' class='A0101'>旷工<br>工时</td>
	<td rowspan='2' class='A0101'>无效<br>工时</td>
	<td rowspan='2' class='A1101'>有薪<br>工时</td>
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
include "../model/subprogram/add_model_b.php";
/*
//读取加班时薪资料，计算验厂加班费
$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE ValueCode>'101' AND ValueCode<'105' AND Estate=1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	do{
		$ValueCode=$checkRow["ValueCode"];
		$TempEstateSTR="HourlyWage".strval($ValueCode); 
		$$TempEstateSTR=$checkRow["Value"];
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
echo "1.5倍时薪：".$HourlyWage102."<br>";
echo "&nbsp;&nbsp;&nbsp;2倍时薪：".$HourlyWage103."<br>";
echo "&nbsp;&nbsp;&nbsp;3倍时薪：".$HourlyWage104."<br>";
if($CheckMonth<'2013-05'){
	echo "<div class='redB'>工作日加班费:".floor($sumGJTime*$HourlyWage102);
	echo "<br />节假日加班费:".floor($sumXJTime*$HourlyWage103+$sumFJTime*$HourlyWage104);
	}
else{
	echo "<div class='redB'>加班费合计：".floor($sumGJTime*$HourlyWage102+$sumXJTime*$HourlyWage103);
	}
echo"</div>";*/
?>