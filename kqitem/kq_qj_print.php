<?php 
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

if($Y==""){
	$Y=date("Y");
	}
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
<table width="746" border="0" cellspacing="0">
       <tr bgcolor="#CCCCCC">
         <td width="35" class="A1111" height="25"><div align="center">序号</div></td>
		  <td width="44" class="A1101"><div align="center">部门</div></td>
         <td width="50" class="A1101"><div align="center">职位</div></td>
         <td width="59" class="A1101"><div align="center">姓名</div></td>
         <td width="50" class="A1101" align="center">ID</td>
         <td width="61" class="A1101" align="center">事假次数</td>
         <td width="58" class="A1101" align="center">事假工时</td>
         <td width="56" class="A1101" align="center">病假次数</td>
         <td width="57" class="A1101" align="center">病假工时</td>
         <td width="55" class="A1101" align="center">总次数</td>
         <td width="57" class="A1101" align="center">总工时</td>
		 <td width="100" class="A1101" align="center">备注</td>
         </tr>
       <?php 
//月结和现金供应商的订金计算
$aResult = mysql_query("SELECT QJ.Number,M.Name,B.Name AS Branch,J.Name AS Job 
	FROM $DataPublic.kqqjsheet QJ
	LEFT JOIN $DataPublic.staffmain M ON QJ.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
	LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
	WHERE QJ.Type<3 and M.Estate=1 and left(QJ.StartDate,4)='$Y' group by QJ.Number order by M.BranchId,M.JobId,M.ComeIn",$link_id);
$i=1;
if($aRow = mysql_fetch_array($aResult)){
	$N1=1;
	do{
		$Number=$aRow["Number"];
		$Name=$aRow["Name"];
		$Branch=$aRow["Branch"];
		$Job=$aRow["Job"];
		//读取记录并计算
		$kq1_Result = mysql_query("SELECT * FROM $DataPublic.kqqjsheet where Number=$Number and Type<3 and left(kqqjsheet.StartDate,4)='$Y'",$link_id);
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
			$Reamrk1=$Reamrk1>0?"<span class='redB'>".$Reamrk1."天</span>":"";
			//求余
			$Reamrk2=$myJhours_SUM%8;
			$Reamrk2=$Reamrk2==0?"":$Reamrk2."小时";
			$myRemark=$Reamrk1.$Reamrk2;
			?>
       <tr id='A<?php  echo $i?>'>
         <td height="25" class="A0111" align="center"><?php  echo $N1?></td>
		 <td class="A0101" align='center'><?php  echo $Branch?></td>
         <td class="A0101" align='center'><?php  echo $Job?>           </td>
         <td class="A0101" align='center'><?php  echo $Name?></td>
         <td class="A0101" align='center'><?php  echo $Number?></td>
         <td class="A0101" align="center"><?php  echo $mySJ_SUM?></td>
         <td class="A0101" align="center"><?php  echo $mySJhoursSUM?></td>
         <td class="A0101" align="center"><?php  echo $myBJ_SUM?></td>
         <td class="A0101" align="center"><?php  echo $myBJhoursSUM?></td>
         <td class="A0101" align="center"><?php  echo $myJ_SUM?></td>
         <td class="A0101" align="center"><?php  echo $myJhours_SUM?></td>
		 <td class="A0101"><?php  echo $myRemark?></td>
         </tr>
       <?php 
//*************结束员工
		$N1++;
		}while ($aRow = mysql_fetch_array($aResult));
	}
	
	$Total_Number=$SJ_SUM+$BJ_SUM;
	$Total_Gwork=$SJhoursSUM+$BJhoursSUM;
	//取整
	$Total_Remark1=intval($Total_Gwork/8);
	$Total_Remark1=$Total_Remark1>0?"<span class='redB'>".$Total_Remark1."天</span>":"";
	//求余
	$Total_Remark2=$Total_Gwork%8;
	$Total_Remark2=$Total_Remark2==0?"":$Total_Remark2."小时";
	$Total_Remark=$Total_Remark1.$Total_Remark2;
?>
       <tr bgcolor="#CCCCCC">
         <td height="25" colspan="5" class="A0111">合 计: </td>
         <td align="center" class="A0101"><?php  echo $SJ_SUM?></td>
		 <td align="center" class="A0101"><?php  echo $SJhoursSUM?></td>
		 <td align="center" class="A0101"><?php  echo $BJ_SUM?></td>
		 <td align="center" class="A0101"><?php  echo $BJhoursSUM?></td>
		 <td align="center" class="A0101"><?php  echo $Total_Number?></td>
         <td align="center" class="A0101"><?php  echo $Total_Gwork?></td>
		 <td class="A0101"><?php  echo $Total_Remark?></td>
         </tr>
     </table>
</body>
</html>