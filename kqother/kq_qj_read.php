<?php
//电信-EWEN
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;
$tableMenuS=450;
ChangeWtitle("$SubCompany 员工请假记录");
$funFrom="kq_qj";
$nowWebPage=$funFrom."_read";
$sumCols="11";		//求和列
$Th_Col="选项|40|序号|35|公司|30|工作</br>地点|40|部门|50|职位|50|员工Id|45|员工姓名|60|请假开始时间|125|请假结束时间|125|班次|35|请假<br>工时|40|请假<br>类别|60|病历<br>证明|35|请假<br>原因|35|审核状态|60|登记日期|70|操作员|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8,111";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	echo"<select name='chooseMonth' id='chooseMonth' onchange='RefreshPage(\"$nowWebPage\")'>";
	$date_Result = mysql_query("SELECT DATE_FORMAT(StartDate,'%Y-%m') AS Month FROM $DataPublic.kqqjsheet group by DATE_FORMAT(StartDate,'%Y-%m') order by StartDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.="and (DATE_FORMAT(J.StartDate,'%Y-%m')='$dateValue' or (DATE_FORMAT(J.StartDate,'%Y-%m')<'$dateValue'  AND DATE_FORMAT(J.EndDate,'%Y-%m')>='$dateValue' )  )";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		}
	 	echo"</select>&nbsp;";

	 $SelectTB="M";$SelectFrom=1;
	 //选择地点
    include "../model/subselect/WorkAdd.php";
     //选择部门
	  include "../model/subselect/BranchId.php";
	     //选择公司
	   include "../model/subselect/cSign.php";
	}
			//薪资分类
	echo"<select name='KqSign' id='KqSign' onchange='document.form1.submit()'>";
		$KqSignFlag="SelFlag" . $KqSign;
		$$KqSignFlag="selected";
		 echo "<option value='' $SelFlag>全部</option>";
		 echo "<option value='0' $SelFlag0>固定薪资</option>";
		 echo "<option value='1' $SelFlag1>考勤薪资</option>";
	echo"</select>&nbsp;";
	     if ($KqSign=="1") $SearchRows.=" AND M.KqSign=1";
		 if ($KqSign=="0") $SearchRows.=" AND M.KqSign!=1";
	    $cSignTB="M";$SelectFrom=5;
echo " &nbsp; &nbsp; <input name='sokeyword' type='text' id='sokeyword' value='请假月份:' style='width:65px;border:0px;background:Transparent' readonly='readonly' />
  <input name='qjMonth' type='text' id='qjMonth'  autocomplete='off' size='12' value='$qjMonth'/> 
  <input type='button' name='Submit' value='快速查询' onClick='toSearchMonth()'>";
if($qjMonth!=""){
         $SearchRows.=" AND  '$qjMonth'>=DATE_FORMAT(StartDate,'%Y-%m')  AND '$qjMonth'<=DATE_FORMAT(EndDate,'%Y-%m')";
         $Orderby=" ORDER BY M.cSign DESC,M.BranchId,CONVERT(M.Name USING gbk)";
   }
else{
         $Orderby=" order by M.Number,J.StartDate DESC";
}
/*echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";*/
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,J.Estate,J.Locks,J.Operator,M.cSign,M.Number,
M.WorkAdd,M.Name,M.KqSign,M.JobId,M.BranchId,T.Name AS Type
FROM $DataPublic.kqqjsheet J 
LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
WHERE 1 $SearchRows $Orderby";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
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
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		//$cSign=$myRow["cSign"]==3?"鼠宝":"<div style='color:#F00;'>研砼</div>";
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

		if($Proof==1){
			$d=anmaIn("download/bjproof/",$SinkOrder,$motherSTR);
			$Proof="proof".$Id.".jpg";
			$f=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="&nbsp;";
			}

        $WorkAddFrom=$myRow["WorkAdd"];
		include "../model/subselect/WorkAdd.php";

		$Locks=$myRow["Locks"];
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
		   array(0=>$cSign,		1=>"align='center'"),
		   array(0=>$WorkAdd,	1=>"align='center'"),
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
if ($myResult ) $RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script >
function toSearchMonth(){
     var qjMonth=document.getElementById("qjMonth").value;
             if(qjMonth==""){alert("请输入月份!");return false;}
             document.getElementById("From").value="slist";
             document.form1.action="kq_qj_read.php?qjMonth="+qjMonth;
             document.form1.submit();
}
</script>