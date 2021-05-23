<?php 
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

ChangeWtitle("$SubCompany 请假统计");
$Login_help="kq_qj_count";
//session_register("Login_help"); 
$_SESSION["Login_help"] = $Login_help;

$url="kq_qj_read";
$ALType="From=$From&Month=$Month";
if($YearTemp==""){
	$YearTemp=date("Y");
	}
$tableMenuS=600;
$tableWidth=800;
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php 
include "../model/characterset.php";
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
?>
<title></title>
</head>
<body>
<form name="form1" enctype="multipart/form-data" action="" method="post" >
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td class="timeTop" id="menuT1" width="<?php  echo $tableMenuS?>">
   <select name="YearTemp" id="YearTemp" onChange="document.form1.submit();">
   <?php 
   	$Syear=2007;
	$Eyear=date("Y");
	for($k=$Syear;$k<=$Eyear;$k++){
		if($YearTemp==$k){
     		echo"<option value='$k' selected>$k 年</option>";
			}
		else{
     		echo"<option value='$k'>$k 年</option>";
			}
		}
   ?>
   </select>
   <?php 
   //&nbsp;<input name="radiobutton" type="radio" value="radiobutton" checked>员工分列 <input type="radio" name="radiobutton" value="radiobutton">月份分列
   ?>
</td>
   <td width="150" id="menuT2" align="center" class="">
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<?php  echo"<a href='kqqj_print.php?Y=$YearTemp' target='_blank' $onClickCSS>列印</a>&nbsp;";?>
					<span onClick="javascript:ComeBack('<?php  echo $url?>','<?php  echo $ALType?>');" <?php  echo $onClickCSS?>>返回</span>
					</nobr>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
  </table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" ><tr><td class='A0011'>
     <table width="780" border="0" cellspacing="0" align="center">
       <tr bgcolor="#CCCCCC">
         <td width="50" class="A1111" height="25" align="center">序号</td>
		  <td width="65" class="A1101" align="center">姓名</td>
         <td width="65" class="A1101" align="center">ID</td>
         <td width="110" class="A1101" align="center">部门</td>
         <td width="110" class="A1101" align="center">职位</td>
         <td width="60" class="A1101" align="center">事假次数</td>
         <td width="60" class="A1101" align="center">事假工时</td>
         <td width="60" class="A1101" align="center">病假次数</td>
         <td width="60" class="A1101" align="center">病假工时</td>
         <td width="60" bgcolor="#CCCCCC" class="A1101" align="center">总次数</td>
         <td width="80" class="A1101" align="center">总工时</td>
         </tr>
       <?php 
//111开始某员工请假记录
$aResult = mysql_query("SELECT QJ.Number,M.Name,B.Name AS Branch,J.Name AS Job 
	FROM $DataPublic.kqqjsheet QJ
	LEFT JOIN $DataPublic.staffmain M ON QJ.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
	LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
	WHERE QJ.Type<3 and M.Estate=1 and left(QJ.StartDate,4)='$YearTemp'	group by QJ.Number order by M.BranchId,M.JobId,M.ComeIn",$link_id);
$i=1;
if($aRow = mysql_fetch_array($aResult)){
	$N1=1;
	do{
		$Number=$aRow["Number"];
		$Name=$aRow["Name"];
		$Branch=$aRow["Branch"];
		$Job=$aRow["Job"];
		//读取记录并计算
		$kq1_Result = mysql_query("SELECT * FROM $DataPublic.kqqjsheet where Number=$Number and Type<3 and left(kqqjsheet.StartDate,4)='$YearTemp'",$link_id);
		if($kq1_row = mysql_fetch_array($kq1_Result)){
			$mySJ_SUM=0;
			$myBJ_SUM=0;
			$myJ_SUM=0;
			$mySJhoursSUM=0;
			$myBJhoursSUM=0;
			do{
				$Type=$kq1_row["Type"];
				$StartDate=$kq1_row["StartDate"];
				$EndDate=$kq1_row["EndDate"];
				switch($Type){
					case 1:
						$mySJ_SUM=$mySJ_SUM+1;
						$SJ_SUM=$SJ_SUM+1;
						$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
						$Days=intval($HoursTemp/24);//取整求相隔天数
						//分析请假时间段包括几个休息日/法定假日/公司有薪假日
						//初始假日数
						$HolidayTemp=0;
						//分析是否有休息日
						$DateTemp=$StartDate;
						for($j=1;$j<=$Days;$j++){
							$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
							$weekDay=date("w",strtotime("$DateTemp"));	 
							if($weekDay==6 || $weekDay==0){
								$HolidayTemp=$HolidayTemp+1;
								}
							else{
								//读取假日设定表
								$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
								if($holiday_Row = mysql_fetch_array($holiday_Result)){
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
							$myHourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时		当次
							$mySJhoursSUM=$mySJhoursSUM+$myHourTotal;
							$SJhoursSUM=$SJhoursSUM+$myHourTotal;
					break;
					case 2:
						$myBJ_SUM=$myBJ_SUM+1;
						$BJ_SUM=$BJ_SUM+1;
						$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
						$Days=intval($HoursTemp/24);//取整求相隔天数
						//分析请假时间段包括几个休息日/法定假日/公司有薪假日
						//初始假日数
						$HolidayTemp=0;
						//分析是否有休息日
						$DateTemp=$StartDate;
						for($j=1;$j<=$Days;$j++){
							$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
							$weekDay=date("w",strtotime("$DateTemp"));	 
							if($weekDay==6 || $weekDay==0){
								$HolidayTemp=$HolidayTemp+1;
								}
							else{
								//读取假日设定表
								$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
								if($holiday_Row = mysql_fetch_array($holiday_Result)){
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
							$myHourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时		当次
							$myBJhoursSUM=$myBJhoursSUM+$myHourTotal;
							$BJhoursSUM=$BJhoursSUM+$myHourTotal;
					break;
					}
				$myJ_SUM=$mySJ_SUM+$myBJ_SUM;
				$myJhours_SUM=$mySJhoursSUM+$myBJhoursSUM;
				}while ($kq1_row = mysql_fetch_array($kq1_Result));
			}
			$mySJ_SUM=SpaceValue0($mySJ_SUM);
			$mySJhoursSUM=SpaceValue0($mySJhoursSUM);
			$myBJ_SUM=SpaceValue0($myBJ_SUM);
			$myBJhoursSUM=SpaceValue0($myBJhoursSUM);
			$myJ_SUM=SpaceValue0($myJ_SUM);
			$myJhours_SUM=SpaceValue0($myJhours_SUM);
			//取整
			$Reamrk1=intval($myJhours_SUM/8);
			//求余
			$Reamrk2=$myJhours_SUM%8;
			$Reamrk2=$Reamrk2==0?")":$Reamrk2."小时)";
			$myRemark=$myJhours_SUM."(".$Reamrk1."天".$Reamrk2;
			?>
       <tr id='A<?php  echo $i?>'>
         <td height="25" class="A0111">&nbsp;<img onClick='ShowOrHide(B<?php  echo $i?>,showtable<?php  echo $i?>,A<?php  echo $i?>);' name='showtable<?php  echo $i?>' src='../images/showtable.gif' alt='' width='11' height='11' style='CURSOR: pointer'>&nbsp;<?php  echo $N1?></td>
		 <td class="A0101"><?php  echo $Name?></td>
         <td class="A0101" align='center'><?php  echo $Number?></td>
         <td class="A0101">&nbsp;<?php  echo $Branch?></td>
         <td class="A0101">&nbsp;<?php  echo $Job?></td>
         <td class="A0101" align="center"><?php  echo $mySJ_SUM?></td>
         <td class="A0101" align="center"><?php  echo $mySJhoursSUM?></td>
         <td class="A0101" align="center"><?php  echo $myBJ_SUM?></td>
         <td class="A0101" align="center"><?php  echo $myBJhoursSUM?></td>
         <td class="A0101" align="center"><?php  echo $myJ_SUM?></td>
         <td class="A0101" align="center"><?php  echo $myJhours_SUM?></td>
         </tr>
       <tr align="right" id='B<?php  echo $i?>' style='display:none'>
         <td colspan="11" class="A0111" align="right">
		 
					<?php  
					$mResult = mysql_query("SELECT DATE_FORMAT(StartDate,'%Y-%m') AS Month FROM $DataPublic.kqqjsheet WHERE Number=$Number and left(kqqjsheet.StartDate,4)='$YearTemp' group by DATE_FORMAT(StartDate,'%Y-%m') order by StartDate DESC",$link_id);
					if($mRow = mysql_fetch_array($mResult)){
						do{
						$thisMonth=$mRow["Month"];
						 $i++;//222月份统计开始?>
						<TABLE width=750 border=0 cellSpacing=1 style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
							<TBODY>
								<TR bgcolor="#CCCCCC" id='A<?php  echo $i?>'><TD height="25">&nbsp;<img onClick='ShowOrHide(B<?php  echo $i?>,showtable<?php  echo $i?>,A<?php  echo $i?>);' name='showtable<?php  echo $i?>' src='../images/showtable.gif' alt='' width='11' height='11' style='CURSOR: pointer'>&nbsp;<?php  echo $thisMonth?>月份请假</TD><TD width="60">&nbsp;</TD><TD width="60">&nbsp;</TD><TD width="60">&nbsp;</TD><TD width="59">&nbsp;</TD><TD width="59">&nbsp;</TD><TD width="77">&nbsp;</TD></TR>
							   	<TR bgcolor="#CCCCCC" id='B<?php  echo $i?>' style="DISPLAY: none">
						   		  <TD colSpan=7>				 
									 <table width="730" border="0" align="right" cellspacing="1">
										 <tr bgcolor="#ceecd2">
											 <td height="22" align="center">序号</td>
											 <td align="center">请假起始时间</td>
											 <td align="center">请假结束时间</td>
											 <td width="58" align="center">事假</td>
											 <td width="58" align="center">事假工时</td>
											 <td width="57" align="center">病假</td>
											 <td width="58" align="center">病假工时</td>
											 <td width="57" align="center">次数</td>
											 <td width="73" align="center">总工时</td>
								   		</tr>
										<?php 	//具体月请假记录
										$nowResult = mysql_query("SELECT * FROM $DataPublic.kqqjsheet 
										WHERE Number=$Number and Type<3 and left(StartDate,7)='$thisMonth' order by StartDate DESC",$link_id);
										if($nowRow = mysql_fetch_array($nowResult)){
											$p=1;
											do{
												$nowStartDate=$nowRow["StartDate"];
												$nowEndDate=$nowRow["EndDate"];
												$Type=$nowRow["Type"];
												$sType="&nbsp;";
												$bType="&nbsp;";
												$sHours="&nbsp;";
												$bHours="&nbsp;";
												//开始计算工时
												$HoursTemp=abs(strtotime($nowStartDate)-strtotime($nowEndDate))/3600;//向上取整
												$Days=intval($HoursTemp/24);//取整求相隔天数
												//分析请假时间段包括几个休息日/法定假日/公司有薪假日
												//初始假日数
												$HolidayTemp=0;
												//分析是否有休息日
												$DateTemp=$StartDate;
												for($j=1;$j<=$Days;$j++){
													$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
													$weekDay=date("w",strtotime("$DateTemp"));	 
													if($weekDay==6 || $weekDay==0){
														$HolidayTemp=$HolidayTemp+1;
														}
													else{
														//读取假日设定表
														$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
														if($holiday_Row = mysql_fetch_array($holiday_Result)){
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
													$myHourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时		当次
												//结束计算工时
												switch($Type){
													case 1:
													$sType=1;
													$sHours=$myHourTotal;
													$allHours=$sHours;
													break;
													case 2:
													$bType=1;
													$bHours=$myHourTotal;
													$allHours=$bHours;
													break;
													}
												//计算 工时 次数 总工时												
												echo"<tr bgcolor='#ceecd2'>		
												<td height='22' align='center'>$p</td><td height='22' align='center'>$nowStartDate</td><td height='22' align='center'>$nowEndDate</td>
												<td height='22' align='center'>$sType</td>
												<td height='22' align='center'>$sHours</td>
												<td height='22' align='center'>$bType</td>
												<td height='22' align='center'>$bHours</td>
												<td height='22' align='center'>1</td>
												<td height='22' align='center'>$allHours</td>
												</tr>
												";
												$p++;
												}while ($nowRow = mysql_fetch_array($nowResult));
											}
										?>
									</table>									 
								  </TD>
							  </TR>
							</TBODY>
						</TABLE>
						
					  <?php  
					  		}while ($mRow = mysql_fetch_array($mResult));
						}
					  $i++;//222月份统计结束?>
			 </td>
		   </tr>
<?php 
//111结束某员工请假记录
		$N1++;
		}while ($aRow = mysql_fetch_array($aResult));
	}
?>
		<tr bgcolor="#CCCCCC"><td height="25" colspan="5" class="A0111">合 计: </td><td align="right" class="A0101">&nbsp;</td><td align="right" class="A0101">&nbsp;</td><td align="right" class="A0101">&nbsp;</td><td align="right" class="A0101">&nbsp;</td><td align="right" class="A0101">&nbsp;</td><td align="right" class="A0101">&nbsp;</td></tr>
	</table>
	
</td></tr></table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td class="timeBottom" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class="">
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>
					<?php  echo"<a href='kqqj_print.php?Year=$YearTemp' target='_blank' $onClickCSS>列印</a>&nbsp;";?>
					<span onClick="javascript:ComeBack('<?php  echo $url?>','<?php  echo $ALType?>');" <?php  echo $onClickCSS?>>返回</span>
					</nobr>
				</td>
			</tr>
	 </table>
   </td>
   </tr>
</table>
</form>
</body>
</html>