<?php 
//电信-ZX  2012-08-01
include "../model/kq_YearHolday.php";
//正式工年假查询
$nowYear=date("Y");//默认年
$T="<br><table border='0' cellspacing='0' bgcolor='#CCCCCC' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	<tr>
		<td width='60' height='25' class='A1111' align='center'>序号</td>
		<td width='120' class='A1101' align='center'>入职日期</td>
		<td width='120' class='A1101' align='center'>年假年份</td>
		<td width='110' class='A1101' align='center'>年总天数</td>
		<td width='110' class='A1101' align='center'>在职天数</td>
		<td width='110' class='A1101' align='center'>有效年假</td>
		<td width='110' class='A1101' align='center'>已用年假</td>
		<td width='145' class='A1101' align='center'>可休年假</td>
 	</tr>
	<tr>
	<td colspan='8' height='425' class='A0111' valign='top'><div style='width:880;height:425;overflow-x:hidden;overflow-y:scroll'><table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$checkLJ="SELECT M.ComeIn,M.KqSign FROM $DataPublic.staffmain M WHERE M.Number='$Number' LIMIT 1";
$LJResult = mysql_query($checkLJ." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($LJResult)){
	
		$ComeIn=$myRow["ComeIn"];
		$KqSign=$myRow["KqSign"];
		$ComeInY=substr($ComeIn,0,4);
		$InYears=$nowYear-$ComeInY;
		for($YearTemp=1;$YearTemp<=$InYears;$YearTemp++){
			$CheckYear=$ComeInY+$YearTemp;
			$ValueY=$CheckYear-$ComeInY;
			$DefaultLastM=$CheckYear."-12-01";
			$ThisEndDay=$CheckYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
			$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
			//计算休假工时
			/*
			if($KqSign>1){//固定薪
				if($ValueY>1){	//年份间隔在2以上的
					$inDays=$CountDays;
					$AnnualLeave=7*8;
					if($ValueY>4){
						$AnnualLeave=12*8;
						}
					}
				else{
					if($ValueY==1){
						$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
						$AnnualLeave=intval((7*8*$inDays)/$CountDays);
						}
					else{
						$AnnualLeave=0;
						$inDays=0;
						}
					}					
				}
			else{			//非固定薪
				if($ValueY>1){//1年以上
					$inDays=$CountDays;
					$AnnualLeave=5*8;
					}
				else{//不满1年,计算天数:
					if($ValueY==1){
						$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
						$AnnualLeave=intval((5*8*$inDays)/$CountDays);
						}
					else{
						$AnnualLeave=0;
						$inDays=0;
						}
					}
				}
			*/
			if($ValueY>1){	//年份间隔在2以上的
				$inDays=$CountDays;
				$AnnualLeave=5*8;
				if($ValueY>=10){
					$AnnualLeave=10*8;
					}
				if($ValueY>=20){
					$AnnualLeave=15*8;
					}					
				}
			else{
				if($ValueY==1){
					$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
					$AnnualLeave=intval((5*8*$inDays)/$CountDays);
					}
				else{
					$AnnualLeave=0;
					$inDays=0;
					}
				}								
			//检查请年假的记录
                        //echo $YearTemp;
                        $HourTotal=HaveYearHolDayDays($Number,$CheckYear."-01-01 00:00:00",$CheckYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id);
                        
			/*$HourTotal=0;  //modify by zx 20101214  否则把以往的一起累加
			$CheckQLJ=mysql_query("SELECT J.StartDate,J.EndDate,J.bcType,J.Type FROM $DataPublic.kqqjsheet J WHERE 1 AND J.Number='$Number' AND J.Type=4 AND J.StartDate LIKE '$CheckYear%' ORDER BY J.StartDate",$link_id);
			//echo "SELECT J.StartDate,J.EndDate,J.bcType,J.Type FROM $DataPublic.kqqjsheet J WHERE 1 AND J.Number='$Number' AND J.Type=4 AND J.StartDate LIKE '$CheckYear%' ORDER BY J.StartDate <br>";
			if($myRowQLJ = mysql_fetch_array($CheckQLJ)){
				do{
					$StartDate=$myRowQLJ["StartDate"];
					$EndDate=$myRowQLJ["EndDate"];
					$bcType=$myRowQLJ["bcType"];
					$Type=$myRowQLJ["Type"];
                                	///////////////////////////////////////
					$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
					$Days=intval($HoursTemp/24);//取整求相隔天数
					$HolidayTemp=0;
					$DateTemp=$StartDate;
					for($n=1;$n<=$Days;$n++){
						$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
						$weekDay=date("w",strtotime("$DateTemp"));	 
						if($weekDay==6 || $weekDay==0){
							$HolidayTemp=$HolidayTemp+1;
                                                        $isHolday=1;
							}
						else{
							$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date='$DateTemp'",$link_id);
							if($holiday_Row = mysql_fetch_array($holiday_Result)){
								$HolidayTemp=$HolidayTemp+1;
                                                                $isHolday=1;
								}
							}
                                             //分析是否有工作日对调
				              if($isHolday==1){  //节假日上班，所以其休息时间要减
					           $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					            //echo "SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'";
					            if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							  $HolidayTemp=$HolidayTemp-1;
                                                    }				
				                }			
				
				             else{  //非节假日调班，则其休息时间要加,
					          $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
					           //echo "SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'";
					           if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					     }
			   }	
					 }
					//计算请假工时
					$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
					//如果是临时班，则按实际计算
					if($bcType==0){
						$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
						}
					$HourTotal+=$Days*8-$HolidayTemp*8+$Hours;//总工时
					///////////////////////////////////////
					}while ($myRowQLJ = mysql_fetch_array($CheckQLJ));
				}
                         * 
                         */
			
		$inDays=$inDays==0?"&nbsp;":$inDays;
		
		$QjHourTotal=$HourTotal/8;
		$AnnualLeave1=intval($AnnualLeave/8);
		$UseDay=$AnnualLeave1-$QjHourTotal;
		$AnnualLeave1=$AnnualLeave1==0?"&nbsp;":$AnnualLeave1."天";
		$QjHourTotal=$QjHourTotal==0?"&nbsp;":$QjHourTotal."天";
		$UseDay=$UseDay==0?"&nbsp;":$UseDay."天";
		$T.="<tr>
			<td class='A0101' align='center' width='58'  height='20'>$YearTemp</td>
			<td class='A0101' align='center' width='120'>$ComeIn</td>
			<td class='A0101' align='center' width='120'>$CheckYear 年</td>
			<td class='A0101' align='center' width='110'>$CountDays</td>
			<td class='A0101' align='center' width='110'>$inDays</td>
			<td class='A0101' align='center' width='110'>$AnnualLeave1</td>
			<td class='A0101' align='center' width='110'>$QjHourTotal</td>
			<td class='A0101' align='center' width='145'>$UseDay</td>
			</tr>";
			}//end for
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$T.="</table></div></td></tr></table>";
?>