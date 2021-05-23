<?php 
/*
mc 验厂文件 ewen 2013-08-03 OK
验厂模式2013-05后的记录
工作日：加班时间截止20:00,超点与直落单独计算，不在此页面显示
周六：加班截止时间为20:00，超点与直落单独计算，不在此页面显示
周日：独立计算，不在此页面显示
临时班：工作时间超过10小时，按10小时计，超点不在此页面显示
要求：每周加班时间不超过60小时，超出的另计

原模式：
全部显示，直浇工时累加至加班工时中

修改：
#1、签退分上述两类处理
#2、验厂模式过滤周日记录
#3、直落记录只在原模式进行处理
*/
$ActioToS="";
if($CheckDate==""){
	$CheckDate=date("Y-m-d");
	}
$CheckMonth=substr($CheckDate,0,7);
$SelectCode="<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='javascript:document.form1.submit();'>&nbsp;
<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select>";
$selStr="selFlag" . $KqSign;
$$selStr="selected";
$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value=''   $selFlag>--全部--</option> 
      <option  value='1'  $selFlag1>考勤有效</option>
	   <option value='2' $selFlag2>考勤参考</option>
	  </select>";
$KqSignStr="";	
if ($KqSign!="") {
	$KqSignStr=" AND M.KqSign='$KqSign'";
	}
$SaveFun="&nbsp;";
$CustomFun="<a href='kq_checkio_print.php?CheckDate=$CheckDate' target='_blank' $onClickCSS>列印</a>&nbsp;&nbsp;";
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
//步骤4：星期处理
$ToDay=$CheckDate;//计算当天
$NowToDay=date("Y-m-d");//
//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekDay=date("w",strtotime($CheckDate));	 
$weekInfo="星期".$Darray[$weekDay];
$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
$jbTimes=0;

$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
if($holidayRow = mysql_fetch_array($holidayResult)){
	$jbTimes=$holidayRow["jbTimes"];
	switch($holidayRow["Type"]){
	case 0:		$DateTypeTemp="W";		break;
	case 1:		$DateTypeTemp="Y";		break;
	case 2:		$DateTypeTemp="F";		break;
		}
	}
echo"<input name='kqList' type='hidden' id='kqList'>";
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF' id='kqTable'>
<tr><td colspan='27' class='A0011'>当天是：$weekInfo</td></tr>";
echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>编号</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
		<td width='70' rowspan='2' class='A1101'><div align='center'>小组</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>现职</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
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
//3		读取需统计的有效的员工资料
/*有效员工的条件：
	需要考勤且没有调动记录的（即入职起就需要考勤）；
	或有调动记录,则取考勤月份比调动生效月份大的最小那个月份的调入状态；
	同时员工的入职月份要少于或等于考勤那个月份
	且员工不在离职日期少于考勤月份的员工

*/
$MySql="SELECT M.Number,M.Name,J.Name AS Job,G.GroupName FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE M.cSign='$Login_cSign' $KqSignStr AND
((M.Number NOT IN (SELECT K.Number FROM $DataPublic.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign<3)
OR(
	M.Number IN(
		SELECT K.Number FROM $DataPublic.redeployk K 
			INNER JOIN(
				SELECT Number,max(Month) as Month FROM $DataPublic.redeployk group by Number) k2 ON K.Number=k2.Number and K.Month=k2.Month 	
			WHERE K.ActionIn<3 and K.Month<='$CheckMonth'))
OR(
	M.Number IN(
		SELECT Ka.Number FROM $DataPublic.redeployk Ka 
			INNER JOIN(
				SELECT Number,min(Month) as Month FROM $DataPublic.redeployk WHERE Month>'$CheckMonth' group by Number) k2a ON Ka.Number=k2a.Number and Ka.Month=k2a.Month 
			WHERE Ka.ActionOut<3)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataPublic.dimissiondata D WHERE D.Number=M.Number and  D.outDate<'$CheckDate')
ORDER BY M.BranchId,M.GroupId,M.Number";

//echo "$MySql";    /////////////////////////////////////////////////////////////
$i=1;
$Qty_SUM=0;
$Amount_SUM=0;
$Result = mysql_query($MySql,$link_id);
if($myrow = mysql_fetch_array($Result)) {
	do{
		$test="";		$AIcolor="";	$AOcolor="";	$jbTime=0;
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo="";
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$XJTime=0;		$FJTime=0;		$InLates=0;		$OutEarlys=0;		
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;$QjTime9=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;
		$DateType=$DateTypeTemp;$GJTimeAll=0;$XJTimeAll=0;$FJTimeAll=0;
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		
		include_once("../model/subprogram/factoryCheckDate.php");
		if(skipStaff($Number))
		{
			continue;
		}
		
		
		$GroupName=$myrow["GroupName"];
		$Job=$myrow["Job"];
		//对调情况落实到个人：因为有时是部分员工对调
		$rqddResult = mysql_query("SELECT Id,XDate FROM $DataIn.kqrqdd WHERE Number='$Number' AND (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){
			$weekDayTempX=date("w",strtotime($rqddRow["XDate"]));		//调动的休息日
			$weekDayTempG=date("w",strtotime($rqddRow["GDate"]));	//调动的工作日
			if($DateType=="G"){
				$ddInfo=$weekDayTempX==0?"(周日)":"(周六)";
				}
			else{
				$ddInfo="(周".$Darray[$weekDayTempG].")";
				}
			$DateType=$DateType=="X"?"G":"X";
			}
		$DateTypeColor=$DateType=="G"?"":"class='greenB'";

		//**********************************************
		//读取班次
		//include "kqcode/checkio_model_pb.php";
		
		$pbType=0;
		$dDateTimeIn=$ToDay." 08:00:00";
		$dTimeIn=date("H:i",strtotime($dDateTimeIn));
		$dDateTimeOut=$ToDay." 17:00:00";
		$dTimeOut=date("H:i",strtotime($dDateTimeOut));
		$dRestTime1=$ToDay." 12:00:00";
		$dRestTime2=$ToDay." 13:00:00";
		$dRestTime3=$ToDay." 18:00:00";
		$dRestTime=60;
		$dInLate=0;
		$dOutEarly=0;
		$dKrSign=0;
		
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
				
				if($Number == "10744" && $CheckDate > "2014-03")
				{
					$mintArray = array("2", "2", "4", "8", "4", "7", "3", "8", "5", "6","2", "3", "4", "8", "6", "6", "3", "8", "5", "6","2", "2", "4", "8", "4", "2", "3", "8", "2", "6","4");
					$currentTimeMin = substr($CheckDate, 9, 1);
					$CheckTime = ($CheckType == "I")?$CheckDate." 07:5".$mintArray[$i]:$CheckDate." 17:0".$mintArray[count($mintArray)-1-$i];
				}
				
				//若有跨日记
				
				$KrSign=$ioRow["KrSign"];
				if($KrSign == "1" && $CheckDate > "2014-03")
				{
					$CheckTime = ($CheckType == "I")?$CheckDate." 07:56":$CheckDate." 17:02";
				}

				
				
				$overTimehourSql = mysql_query("Select * From $DataIn.kqovertime Where otDate = '$CheckDate'");
				$overTimehourResult = mysql_fetch_assoc($overTimehourSql);
				$overTimeHours = 0;
				switch($DateType)
				{
					case "G":
					{
						$overTimeHours = $overTimehourResult["workday"];
					}
					break;
					case "X":
					{
						$overTimeHours = $overTimehourResult["weekday"];
					}
					case "F":
					{
						$overTimeHours = $overTimehourResult["weekday"];
					}
					break;
				}
			
				if($overTimeHours == 0 && ($DateType == "X" || $DateType == "F") && $CheckDate > "2014-03")
				{
					break;
				}
				
						
				switch($CheckType){
					case "I":
						$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aiTime=date("H:i",strtotime("$CheckTime"));						
						break;
					case "O":
						//验厂模式*****************************************************************************
						$CheckYM=date("Y-m",strtotime($CheckTime));//默认日期
						//echo $CheckYM;
						$Yischeck=0;
						if($Number!=10572 && $Number!=11710 && $Number!=11359 && $Number!=10551 && $Number!=10812 && $Number!=10470 && $Number!=11271 
							   && $Number!=11742 && $Number!=11661 && $Number!=11624 && $Number!=10641 && $Number!=10518 && $Number!=10397) {
							$Yischeck=1;
							
						}else {
							if ($CheckYM>'2013-07') {
								$Yischeck=1;	
							}
						}
						
						
						if($CheckDate > "2014-03")
						{
							if(strtotime($CheckTime) > strtotime($dDateTimeOut))
							{
								//查询是否有直落
								$zlTimeSql = mysql_query("Select Hours From $DataPublic.kqzltime Where Number = '$Number' and Date = '$CheckDate'");
								$zlHoursResult = mysql_fetch_assoc($zlTimeSql);
								$zlHours = ($zlHoursResult["Hours"] == "")?"0":$zlHoursResult["Hours"];
			
								$realTime = date("H:i", strtotime($CheckTime));
								$minCount = substr($realTime, strlen($realTime)-1, 1);
								//更新时间
								if($DateType == "G")
								{
									$addHours = ($overTimeHours - $zlHours < 0)?0:$overTimeHours - $zlHours;
									$otMins = $addHours * 60 + $minCount;
									$checkHolder = $CheckDate." ".$checkOutTime;
									$CheckTime = ($overTimeHours == 0)?date("Y-m-d H:i:s",strtotime("$dDateTimeOut+$otMins minute")):date("Y-m-d H:i:s",strtotime("$dRestTime3+$otMins minute"));
									$ioTime=date("H:i",strtotime($CheckTime));
								}
								else
								{
									$addHours = $overTimeHours;
									$otMins = $addHours * 60 + $dRestTime + $minCount;
									$CheckTime = date("Y-m-d H:i:s",strtotime("$dDateTimeIn+$otMins minute"));
									$ioTime=date("H:i",strtotime($CheckTime));
								}
								
							}

						}			
						else if($CheckDate>="2013-05-01" ){
							//**************检查 $CheckTime 是否超过20:00
							if($pbType==1){//临时班：看加班是否超过10小时，是则重置签退时间
								$EndTimeGap=strtotime($CheckTime)-strtotime($AI);//时间差秒数
								//$EndTimeTemp1=intval($EndTimeGap/3600/0.5)-20;//30分钟的次数
								$EndTimeTemp1=intval($EndTimeGap/3600/0.25)-40;//15分钟的次数
								
								if($EndTimeTemp1>0){//超出时间:
									$EndMinute=$EndTimeTemp1*15;//超出部分的总分钟数
									$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
									$ioTime=date("H:i",strtotime($CheckTime));
									}
								}
							else{								
								$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 20:00:00");
								//$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
								$EndTimeTemp1=intval($EndTimeGap/3600/0.25);//30分钟的次数
								if($EndTimeTemp1>0){//超出时间:
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
										
										//$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 17:00:00");
										//$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
										$EndTimeTemp1=intval($EndTimeGap/3600/0.25);//30分钟的次数
										if($EndTimeTemp1>0 && $EndTimeTemp1<6){//17:30-18:30签退，则时间前移处理
											//$EndMinute=$EndTimeTemp1*30;//超出17:00的总分钟数
											$EndMinute=$EndTimeTemp1*15;//超出17:00的总分钟数
											$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
											$ioTime=date("H:i",strtotime($CheckTime));
											}
										}//特定员工除外
									//###################################
									}
								}
							}
						//验厂模式*****************************************************************************
						//echo $CheckTime;
						$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));			
						$aoTime=date("H:i",strtotime("$CheckTime"));		
						
						break;
					}					
				}while ($ioRow = mysql_fetch_array($ioResult));
			}
		//过滤2013-05-01之后周日或调为周日的记录//
		if($CheckDate>="2013-05-01" && $DateType=="X"){
			if(($Info=="(周日)"  && $ddSign==1) || ($ddSign=="" && $weekDay==0)){//如果是调动且调为周日，以及没有调动且为周日的
				$aiTime=$aoTime=$AI=$AO="";
				}	
			}
			
			
		//当天的数据计算开始
		$GTime=$DateType=="G"?8:0;
		if($DateType=="G"){//工作日的数据计算
			if($pbType==0){
				include "kqcode/checkio_model_countG.php";
				}
			else{
				include "kqcode/checkio_model_countGL.php";
				}
			}
		else{//非工作日的数据计算	：如果是周日或调为周日
			if($pbType==0){
				include "kqcode/checkio_model_countX.php";
				}
			else{
				include "kqcode/checkio_model_countXL.php";
				}
			}
		//当天的数据计算结束
		$YXJhours=$QjTime4+$QjTime5+$QjTime6+$QjTime7+$QjTime8;//有薪假总工时
		//直落工时计算
		//include "kqcode/checkio_model_zl.php";
		//将数据存入日统计表
		if(($aiTime!="" || $aoTime!="") || $GTime>0) {
			$GJTimeAll=$GJTimeAll==""?$GJTime:$GJTimeAll;
			$XJTimeAll=$GXTimeAll==""?$XJTime:$XJTimeAll;
			$FJTimeAll=$FJTimeAll==""?$FJTime:$FJTimeAll;
			$inRecode1="INSERT INTO $DataIn.kqrtj 
					SELECT NULL,Number,'$GTime','$WorkTime','$GJTimeAll','$XJTimeAll','$FJTimeAll','$InLates','$OutEarlys','$QjTime1','$QjTime2','$YXJhours','$QjTime3','$QQTime','$YBs','$KGTime','$CheckDate','0', 1,0,'$Operator',NOW(),'$Operator',NOW(),null
					FROM $DataPublic.staffmain WHERE Number='$Number' AND '$CheckDate'<'$NowToDay' AND '$CheckDate'>'2010-10-01' AND Number NOT IN (SELECT Number FROM $DataIn.kqrtj WHERE Date='$CheckDate' AND Number='$Number')";
			//echo $inRecode1;
			$inAction1=@mysql_query($inRecode1);
			}

		$aiTime=SpaceValue($aiTime);
		$aoTime=SpaceValue($aoTime);
		$GTime=zerotospace($GTime);			//应到时间
		$WorkTime=zerotospace($WorkTime);	//实到时间
		$GJTime=$gzlSign==1?$GJTime:zerotospace($GJTime);		//工作日加班工时+直落
		$XJTime=$xzlSign==1?$XJTime:zerotospace($XJTime);		//休息日加班工时+直落
        if($CheckDate=="2012-10-04" &&  $ZL_Hours!=0)//2012-10-04这天单独处理。
           {
                 $XJTime=$XJTime+$ZL_Hours;
                  $GJTime=0;
		          $GJTime=zerotospace($GJTime);
              }//特殊情况
        
        $grenderSql = mysql_query("Select A.Sex,B.kqSign From $DataPublic.staffsheet A 
							   Left Join $DataPublic.staffmain B On B.Number = A.Number
							   Where A.Number = '$Number'");
	    $grenderResult = mysql_fetch_assoc($grenderSql);
		$grender = $grenderResult["Sex"];
		$KqSign = $grenderResult["kqSign"];
	
		if($CheckDate == "2014-03-08" && $grender == "0" && $KqSign < 3)
		{
	    	 if($XJTime >= 4)
			 {
			 	$FJTime = $XJTime - 4;
			 	$XJTime = 4;
			 }	        
		}
              
		$FJTime=$fzlSign==1?$FJTime:zerotospace($FJTime);		//法定假日加班工时+直落
		$InLates=zerotospace($InLates);		//迟到次数
		$OutEarlys=zerotospace($OutEarlys); //早退次数
		$QjTime1=zerotospace($QjTime1);		//事假
		$QjTime2=zerotospace($QjTime2);		//病假
		$QjTime3=zerotospace($QjTime3);		//无薪假
		$QjTime4=zerotospace($QjTime4);		//年休假
		$QjTime5=zerotospace($QjTime5);		//补休
		$QjTime6=zerotospace($QjTime6);		//婚假
		$QjTime7=zerotospace($QjTime7);		//丧假
		$QjTime8=zerotospace($QjTime8);		//产假
		$QjTime9=zerotospace($QjTime9);		//工伤
		$QQTime=zerotospace($QQTime);		//缺勤工时
		$WXTime=zerotospace($WXTime);		//无薪工时：当月未到职或已离职
		$KGTime=zerotospace($KGTime);		//旷工工时
		$BKTime=zerotospace($BKTime);		//应扣工时，已取消
		$YBs =  "0";
		$YBs=zerotospace($YBs);				//夜班次数
		
		
		$CheckTable="kqdaytj";
		include "kq_checkio_check.php";//检查是否有记录没有保存，是否有记录已作修改
		
	  $TodaydkHour=0;  //是否当天有抵扣，不能算旷工
	  $rqddResult = mysql_query("SELECT Id,dkHour FROM $DataPublic.staff_dkdate WHERE Number='$Number' AND dkDate='$CheckDate'  LIMIT 1",$link_id);
	  if($rqddRow = mysql_fetch_array($rqddResult)){
		    $TodaydkHour=$rqddRow["dkHour"];
			$DateType="D";
			$ddInfo="($TodaydkHour)";
			$DateTypeColor="class='greenB'";
		}	  
		
		echo"<tr align='center'><td class='A0111'>$i</td>";
		echo"<td class='A0101'>$Number</td>";
		echo"<td class='A0101' align='left'>$Name</td>";
		echo"<td class='A0101' align='left'>$GroupName</td>";
		echo"<td class='A0101' align='left'>$Job</td>";
		echo"<td class='A0101' align='left'><div $DateTypeColor>$DateType$ddInfo</div></td>";
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
		echo"<td class='A0101' align='center'>$QjTime6</td>";// $test
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
			
		echo"<td class='A0101' align='center'>$YBs</td>";
		echo"</tr>";
		$i++;
		}while ($myrow = mysql_fetch_array($Result));
	    
	}
else{
	echo"<tr bgcolor='#FFFFFF'><td colspan='25' scope='col' height='60' class='A0111' align='center'><p>暂时还没有资料。</td></tr>";
	}  
	  
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
		echo"
			<tr class=''>
				<td rowspan='2' class='A0111'><div align='center'>序号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>编号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>姓名</div></td>
				<td rowspan='2' class='A0101'><div align='center'>小组</div></td>
				<td rowspan='2' class='A0101'><div align='center'>现职</div></td>
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
//步骤5：
include "../model/subprogram/add_model_b.php";
echo"<br>";
//include "../model/subprogram/read_model_menu.php";
?>
<input name="CheckNote" type="hidden" id="CheckNote" value="<?php  echo $CheckNote?>"/>
<script language="javascript" type="text/javascript">
function SaveDaytj(){
        var message=confirm("保存之前请确认相关数据是否正确，点取消可以返回修改。");
		if (message){
		   var kqList="";
		   for(var m=3; m<kqTable.rows.length-2; m++){
		         for(var n=9;n<13;n++){
				 var s=kqTable.rows[m].cells[n].innerText;
				      s=s.replace(/(^\s*)/g,"");
				   if(s.length>0){
		               kqList=kqList+","+
				      kqTable.rows[m].cells[1].innerHTML+"^^"+
					  kqTable.rows[m].cells[9].innerText+"^^"+
					  kqTable.rows[m].cells[10].innerText+"^^"+
					  kqTable.rows[m].cells[11].innerText+"^^"+
					  kqTable.rows[m].cells[12].innerText;
					  break;
					  }
				    }
                }	
		    //alert(kqList);
			document.getElementById("kqList").value=kqList;
			document.form1.action="kq_checkio_updated.php?ActionId=kq";
			document.form1.submit();
			}
		else{
			return false;
			}

    
}
</script>
