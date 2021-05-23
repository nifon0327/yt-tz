<?php 
//电信-EWEN
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 年假统计");
$funFrom="kq_lj";
$nowWebPage=$funFrom."_read";
$chooseYear=$chooseYear==""?date("Y"):$chooseYear;
$NextYear=$chooseYear+1;
$LastYear=$chooseYear-1;
$Th_Col="序号|40|薪酬类型|80|部门|60|小组|80|职位|60|员工姓名|80|入职日期|100|年休有效日期|100|总天数/年|80|有效天数|100|&nbsp;$chooseYear 年假|80|已休天数|60|&nbsp;$NextYear 年假|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,11";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项:2008-现在
//$SearchRows ="";
   echo"<select name='chooseYear' id='chooseYear' onchange='ResetPage(this.name)'>";
   for($i=2011;$i<=date("Y");$i++){
      //$chooseYear=$chooseYear==""?$i:$chooseYear;
	  if($chooseYear==$i){
		echo"<option value='$i' selected>$i 年</option>";
		}
	  else{
		echo"<option value='$i'>$i 年</option>";
		}
	 }
    echo"</select>&nbsp;";

   $KqSign=$KqSign==""?0:$KqSign;
   $StrSel="KqSign".$KqSign;
   $$StrSel="Selected";
   echo"<select name='KqSign' id='KqSign' onchange='ResetPage(this.name)'>";
   echo "<option value='0' $KqSign0>全部</option>
         <option value='1' $KqSign1>非固定薪</option>
	     <option value='3' $KqSign3>固定薪</option>";
   echo"</select>&nbsp;";
    if($KqSign>0)$KqSignSTR=" AND M.KqSign='$KqSign'";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName 
	FROM $DataPublic.staffmain M
        LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
	LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
	LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
	WHERE M.cSign='$Login_cSign' AND M.Estate=1 AND offStaffSign=0 $KqSignSTR $SearchRows   ORDER BY M.BranchId,M.GroupId,M.JobId,M.Number";
	
$SearchRows="";
$myResult = mysql_query($mySql."",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$KqSign=$myRow["KqSign"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		
		$ComeIn=$myRow["ComeIn"];
		$GroupName=$myRow["GroupName"];
		//入职当年
		$ComeInY=substr($ComeIn,0,4);
		//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
		$ValueY=$chooseYear-$ComeInY;
		if (substr($ComeIn,5,5)=="01-01") $ValueY+=1;	
				
		$DefaultLastM=$chooseYear."-12-01";
		$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$CountDays=date("z",strtotime($ThisEndDay))+1;	//年假当年总天数
				
		//计算休假工时  1~9年:5天,10~19:10天,20年以上的 15天
		//计算本年请假的时间(除年休)，超过15天以上的要扣除
		$sumQjTime=0;
		$qjTimeSql=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,2) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
	
        //echo "SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,3) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')";
		if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年处理
		    do{
		       $bcType=$qjTimeRow["bcType"];
			   $StartDate=$qjTimeRow["StartDate"];
		       $EndDate=$qjTimeRow["EndDate"];
			   $frist_Year=substr($StartDate,0,4);
			   $end_Year=substr($EndDate,0,4);
			   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
			   //if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";
			   if($end_Year>$LastYear)$EndDate=$LastYear."-12-31 17:00:00";
			   
			   $HourTotal=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);  //本次请假换算小时数
			   /*
			   	$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
				//echo "$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整";
		        $Days=intval($HoursTemp/24);//取整求相隔天数
			    //分析请假时间段包括几个休息日/法定假日/公司有薪假日
			    $HolidayTemp=0;     //初始假日数
			    //分析是否有休息日
			     $isHolday=0;  //0 表示工作日
			     $DateTemp=$StartDate;
			     $DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			      for($n=0;$n<=$Days;$n++){
				         $isHolday=0;  //0 表示工作日
				         $DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				         $weekDay=date("w",strtotime("$DateTemp"));	
						 
					   //读取假日设定表
						  $holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
						  if($holiday_Row = mysql_fetch_array($holiday_Result)){
							$HolidayTemp=$HolidayTemp+1;
							$isHolday=1;
						}
						else {
				         if($weekDay==6 || $weekDay==0){
					             $HolidayTemp=$HolidayTemp+1;
					             $isHolday=1;
					             }
					     }            
				         //分析是否有工作日对调
				         if($isHolday==1){  //节假日上班，所以其休息时间要减
					        $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'
														  UNION 
												          SELECT XDate FROM $DataOut.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'
														 ",$link_id);
					         if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							  $HolidayTemp=$HolidayTemp-1;
					          }				
			           	}			
				else{  //非节假日调班，则其休息时间要加,
					     $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'
													   UNION 
												       SELECT XDate FROM $DataOut.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'
													  ",$link_id);
					      if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					     }
			        }              
		      	}
			     //计算请假工时
			     $Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			     $HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时
			     $HourTotal=$HourTotal<0?0:$HourTotal;   //有时假，只请半天，但调假一天，所以要去掉
				 */
				 $HourTotal=$HourTotal<0?0:$HourTotal;
				 
			     $sumQjTime+=$HourTotal/8;
		       }while($qjTimeRow=mysql_fetch_array($qjTimeSql));
		    }
			if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
			//echo $sumQjTime;
			if($ValueY>1){	//年份间隔在2以上的
				$inDays=$CountDays-$sumQjTime;
				$AnnualLeave=intval((5*8*$inDays)/$CountDays);
				if($ValueY>10){
					$AnnualLeave=intval((10*8*$inDays)/$CountDays);
					}
				if($ValueY>20){
					$AnnualLeave=intval((15*8*$inDays)/$CountDays);
					}			
				}
			else{
				if($ValueY==1){
					$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays-$sumQjTime;
					$AnnualLeave=intval((5*8*$inDays)/$CountDays);
					}
				else{
					$AnnualLeave=0;
					$inDays=0;
					}
				}		
		$hasAnnual = getLastYearLeave($Number, $DataIn,$link_id);
		if($hasAnnual == 0)	{
			$AnnualLeave = 0;
		}
		$qjAllDays=HaveYearHolDayDays($Number,$chooseYear."-01-01 00:00:00",$chooseYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id)/8 ;
		//明年休假计算
		$ValueY=$NextYear-$ComeInY;
		if (substr($ComeIn,5,5)=="01-01") $ValueY+=1;	
		$DefaultLastM=$NextYear."-12-01";
		$NextEndDay=$NextYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$NextCountDays=date("z",strtotime($NextEndDay))+1;	//年假当年总天数
		    
			if($ValueY>1){	//年份间隔在2以上的
				$NextAnnualLeave=5*8;
				if($ValueY>10){
					$NextAnnualLeave=10*8;
					}
				if($ValueY>20){
					$NextAnnualLeave=15*8;
					}					
				}
			else{
				if($ValueY==1){
					$NextinDays=abs(strtotime($NextEndDay)-strtotime($ComeIn))/3600/24-$NextCountDays;
					$NextAnnualLeave=intval((5*8*$NextinDays)/$NextCountDays);
					}
				else{
					$NextAnnualLeave=0;
					}
				}					
				
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		$KqSign=$KqSign==1?"非固定薪":"固定薪";
		$AnnualLeave1=intval($AnnualLeave/8);
		//$AnnualLeave1=$AnnualLeave/8;
		$colColor=$qjAllDays>$AnnualLeave1?"bgcolor='#f00'":"";
		
		$AnnualLeave1=$AnnualLeave1==0?"&nbsp;":$AnnualLeave1."天";
		$NextAnnualLeave=intval($NextAnnualLeave/8);
		$NextAnnualLeave=$NextAnnualLeave==0?"&nbsp;":$NextAnnualLeave."天";
		$inDays=$inDays==0?"&nbsp;":ceil($inDays);
		$startYear=date("Y-m-d",strtotime("1 year",strtotime($ComeIn)));
		$NowToday=date("Y-m-d");
                $Title="";
			if($startYear>$NowToday){
			    $startYear="<div class='redB'>$startYear</div>";
				$AnnualLeave1="<div class='redB'>$AnnualLeave1</div>";
				$Title=" title='还未到一年,不能请年休假!'";
			}
		echo"<td class='A0111' width=$Field[1] align='center' $colColor>$i</td>";
		echo"<td height='20' width=$Field[3] class='A0101' align='center'>$KqSign</td>";
		echo"<td class='A0101' width=$Field[5] align='center'>$Branch</td>";
        echo"<td class='A0101' width=$Field[7] align='center'>$GroupName</td>";
		echo"<td class='A0101' width=$Field[9] align='center'>$Job</td>";
		echo"<td class='A0101' width=$Field[11] align='center'>$Name</td>";
		echo"<td class='A0101' width=$Field[13] align='center'>$ComeIn</td>";//入职日期
		echo"<td class='A0101' width=$Field[15] align='center'>$startYear</td>";//年休有效日期
		echo"<td class='A0101' width=$Field[17] align='center'>$CountDays</td>";//总天数/年
		echo"<td class='A0101' width=$Field[19] align='center'>$inDays</td>";//有效天数
		echo"<td class='A0101' width=$Field[21] align='center' $Title>$AnnualLeave1</td>";// 当年年假
		echo"<td class='A0101' width=$Field[23] align='center'>$qjAllDays</td>";//已休天数
		echo"<td class='A0101' width='' align='center'>$NextAnnualLeave</td>";// 下一年年假
		echo"</tr></table>";
		$i++;		
		}while ($myRow = mysql_fetch_array($myResult));
	}
echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany 年假列表");
include "../model/subprogram/read_model_menu.php";
?>