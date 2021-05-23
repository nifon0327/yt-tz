<?php 
/*
$DataPublic.adminitype
$DataPublic.currencydata
二合一已更新
电信-joseph
*/
//步骤1
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=600;
$sumCols="4";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 部门员工待审核列表");
$funFrom="office_qj";
$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|请假开始时间|125|请假结束时间|125|班次|35|请假<br>工时|40|请假<br>类别|60|病历<br>证明|35|请假<br>原因|35|审核状态|60|登记日期|70|申请|50|审核|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,17,162";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){$SearchRows="";}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select> $CencalSstr";
//步骤4：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
//取得当前用户的部门ID

if($Login_P_Number=='10082' || $Login_P_Number=='11008' )
{
	$BranchIdSTR="";
}
else 
{
	$bResult = mysql_query("SELECT BranchId FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' ",$link_id);
	while($bRow = mysql_fetch_array($bResult)){
		$BranchIdSTR=$BranchIdSTR==""?$bRow["BranchId"]:"," .$bRow["BranchId"];
	}
	$BranchIdSTR="AND M.BranchId IN ($BranchIdSTR)"; 
}

$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,J.Estate,J.Locks,J.Operator,M.Number,M.Name,M.KqSign,M.JobId,M.BranchId,T.Name AS Type,J.Checker
 FROM $DataPublic.kqqjsheet J 
	LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
	LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	WHERE 1  $BranchIdSTR  AND   J.Estate=1 order by J.StartDate DESC";
//echo $mySql;
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
		$KqSign=$myRow["KqSign"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Date=substr($myRow["Date"],0,10);		
		$Proof=$myRow["Proof"];
		$Checker = $myRow["Checker"];
				
		if($Proof==1){
			$d=anmaIn("download/bjproof/",$SinkOrder,$motherSTR);
			$Proof="proof".$Id.".jpg";
			$f=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		}else{
			$Proof="&nbsp;";
		}


		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		if($Checker==0 || $Checker==""){
			$Checker="<span class=\"redB\">未审核</span>";
		}else{	
				$checkerNameSql = "SELECT Name FROM $DataPublic.staffmain WHERE Number = $Checker";
				$checkerNameResult = mysql_query($checkerNameSql);
				$checkerNameRow = mysql_fetch_assoc($checkerNameResult);
				$Checker = $checkerNameRow['Name'];
		}

		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

		$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
		//$Days=intval($HoursTemp/24);//取整求相隔天数
		$Days=intval($HoursTemp/24);//取整求相隔天数
			//分析请假时间段包括几个休息日/法定假日/公司有薪假日
			//初始假日数
			$HolidayTemp=0;
			//分析是否有休息日
			$isHolday=0;  //0 表示工作日
			$DateTemp=$StartDate;
			//echo "开始日期:$StartDate <br>";
			$DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			for($n=0;$n<=$Days;$n++){
				$isHolday=0;  //0 表示工作日
				//$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				//echo "DateTemp:$DateTemp <br>";
				$weekDay=date("w",strtotime("$DateTemp"));	 
				if($weekDay==6 || $weekDay==0){
					$HolidayTemp=$HolidayTemp+1;
					//echo "$n:周六或日<br>";
					$isHolday=1;
					}
				else{
					//读取假日设定表
					$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					if($holiday_Row = mysql_fetch_array($holiday_Result)){
						$HolidayTemp=$HolidayTemp+1;
						//echo "$n:节假日<br>";
						$isHolday=1;
						}
					}
                
				//分析是否有工作日对调
				if($isHolday==1){  //节假日上班，所以其休息时间要减
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					//echo "SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'";
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
						    //echo "$n: 节假日调班 <br>";
							$HolidayTemp=$HolidayTemp-1;
					}				
				}			
				
				else{  //非节假日调班，则其休息时间要加,
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
					//echo "SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'";
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
						    //echo "$n: 非节假日调班 <br>";
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
			//echo $HourTotal;
			$HourTotal=$HourTotal<0?0:$HourTotal;   //有时假，只请半天，但调假一天，所以要去掉
			
			$OrderSignColor="";
			if ($Type=="带薪年假"){
				  $YearHolDay=GetYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)*8;
				 // echo "$HourTotal>$YearHolDay";
				 $AllYearHolDays=HaveYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id) ;
				 
				 if ($AllYearHolDays>$YearHolDay)  
				 {
				      $HourDays=$HourTotal/8;
				      $YearHolDay=$YearHolDay/8;
				      $AllYearHolDays=($AllYearHolDays-$HourTotal)/8;
				      $OrderSignColor="bgcolor='#f00' title='请假：$HourDays 天+已请:$AllYearHolDays 天 > 应休年假：$YearHolDay 天'";
				      $LockRemark="请假：$HourDays 天+已请:$AllYearHolDays 天 > 应休年假：$YearHolDay 天";
				 }
			}

			$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:$Estate="<div class='greenB'>通过</div>";
			break;
			case 1:$Estate="<div class='yellowB'>申请中</div>";
			break;
			default:
			$Estate="<div class='redB'>未通过</div>";
			break;
			}
			$bcType=$bcType==0?"标准":"<div class=yellowB>临时</div>";
			$LockRemark="";
			$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
			if($checkRow = mysql_fetch_array($checkMonth)){
				$LockRemark="该月考勤统计已生成,禁止修改.";
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
			array(0=>$Operator, 	1=>"align='center'"),
			array(0=>$Checker, 	1=>"align='center'")
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