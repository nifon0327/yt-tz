<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=15;				
$tableMenuS=450;
ChangeWtitle("$SubCompany 员工请假待审记录");
$funFrom="kq_qjsh";
$nowWebPage=$funFrom."_read";
$sumCols="9";		//求和列
$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|请假开始时间|125|请假结束时间|125|班次|35|请假<br>工时|40|请假<br>类别|60|病历<br>证明|35|请假<br>原因|35|审核<br>状态|50|登记日期|70|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$ActioToS="17,15";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	
	$date_Result = mysql_query("SELECT DATE_FORMAT(StartDate,'%Y-%m') AS Month FROM $DataPublic.kqqjsheet WHERE Estate=1 GROUP BY DATE_FORMAT(StartDate,'%Y-%m') order by StartDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and DATE_FORMAT(J.StartDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
echo $CencalSstr;
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,J.Estate,J.Locks,J.Operator,M.Number,M.Name,M.JobId,M.BranchId,T.Name AS Type
 FROM $DataPublic.kqqjsheet J 
	LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
	LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
	WHERE 1 $SearchRows AND M.cSign=$Login_cSign order by J.StartDate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$MonthTemp=substr($EndDate,0,7);
		$Reason=$myRow["Reason"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Reason]' width='18' height='18'>";
		$Type=$myRow["Type"];
		$bcType=$myRow["bcType"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Date=substr($myRow["Date"],0,10);		
		$Proof=$myRow["Proof"];
				
		if($Proof==1){
			$d=anmaIn("download/bjproof/",$SinkOrder,$motherSTR);
			$Proof="proof".$Id.".jpg";
			$f=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="&nbsp;";
			}


		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
		$Days=intval($HoursTemp/24);//取整求相隔天数
			//分析请假时间段包括几个休息日/法定假日/公司有薪假日
			//初始假日数
			$HolidayTemp=0;
			//分析是否有休息日
			$DateTemp=$StartDate;
			for($n=1;$n<=$Days;$n++){
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
			$HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时
			
			$bcType=$bcType==0?"标准":"<div class=yellowB>临时</div>";
			$LockRemark="";
			$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
			if($checkRow = mysql_fetch_array($checkMonth)){
				$LockRemark="该月考勤统计已生成,禁止修改.";
				}
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:$Estate="<div class='greenB'>通过</div>";
			$LockRemark=$LockRemark==""?"审核已通过":$LockRemark;
			break;
			case 1:$Estate="<div class='yellowB'>申请中</div>";
			break;
			default:
			$Estate="<div class='redB'>未通过</div>";
			break;
			}
		$ValueArray=array(
			array(0=>$Branch,		1=>"align='center'"),
			array(0=>$Job, 			1=>"align='center'"),
			array(0=>$Number, 		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$StartDate, 	1=>"align='center'"),
			array(0=>$EndDate, 		1=>"align='center'"),
			array(0=>$bcType, 		1=>"align='center'"),
			array(0=>$HourTotal, 	1=>"align='center'"),
			array(0=>$Type,			1=>"align='center'"),
			array(0=>$Proof,		1=>"align='center'"),
			array(0=>$Reason, 		1=>"align='center'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$Operator, 	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>