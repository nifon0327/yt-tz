<?php
//电信-EWEN
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=19;
$tableMenuS=450;
ChangeWtitle("$SubCompany 员工请假记录");
$funFrom="kq_qj";
$nowWebPage=$funFrom."_read";
$sumCols="11";		//求和列
$Th_Col="选项|40|序号|35|公司|30|工作</br>地点|40|部门|50|职位|50|员工Id|45|小组|60|员工姓名|60|请假开始时间|125|请假结束时间|125|班次|35|请假<br>工时|40|请假<br>类别|60|病历<br>证明|35|请假<br>原因|35|审核状态|60|退回原因|100|登记日期|70|登记人员|50|审核|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8,111";
//步骤3：
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	echo"<select name='chooseMonth' id='chooseMonth' onchange='RefreshPage(\"$nowWebPage\")'>";
	$date_Result = mysql_query("SELECT DATE_FORMAT(StartDate,'%Y-%m') AS Month FROM $DataIn.kqqjsheet group by DATE_FORMAT(StartDate,'%Y-%m') order by StartDate DESC",$link_id);
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

	 echo"<select name='qjType' id='qjType' onchange='RefreshPage(\"$nowWebPage\")'>";
	 echo"<option value=''>全部</option>";
	$date_Result = mysql_query("SELECT T.Id,T.Name FROM $DataIn.kqqjsheet J 
	               LEFT JOIN  $DataIn.qjtype T ON J.Type=T.Id 
	               WHERE T.Estate=1  $SearchRows GROUP BY T.Id",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		do{
			$J_Id=$dateRow["Id"];
			$J_Name=$dateRow["Name"];
			if($qjType==$J_Id){
				echo"<option value='$J_Id' selected>$J_Name</option>";
				$SearchRows.=" AND J.Type=$J_Id ";
				}
			else{
				echo"<option value='$J_Id'>$J_Name</option>";
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
M.WorkAdd,M.Name,M.KqSign,M.JobId,M.BranchId,T.Name AS Type,J.Checker,IF(M.cSign!=7,OG.GroupName,G.GroupName) AS GroupName
FROM $DataIn.kqqjsheet J 
LEFT JOIN $DataIn.staffmain M ON J.Number=M.Number 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
LEFT JOIN $DataIn.staffgroup OG ON OG.GroupId=M.GroupId
LEFT JOIN $DataIn.qjtype T ON J.Type=T.Id
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
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataIn.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataIn.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Date=substr($myRow["Date"],0,10);
		$Proof=$myRow["Proof"];

		if($Proof==1){
			$d=anmaIn("download/bjproof/",$SinkOrder,$motherSTR);
			$Proof="proof".$Id.".jpg";
			$f=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$d\",\"$f\",\"\",\"bjproof\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="&nbsp;";
			}

        $WorkAddFrom=$myRow["WorkAdd"];
		include "../model/subselect/WorkAdd.php";

		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

		$Checker = $myRow['Checker'];
		if($Checker == ''){
			$Checker = '未提供';
		}else{
			$checkerNameSql = "SELECT Name FROM $DataIn.staffmain WHERE Number = $Checker";
			//echo $checkerNameSql;
			$CheckerResult = mysql_query($checkerNameSql);
			$CheckerRow = mysql_fetch_assoc($CheckerResult);
			$Checker = $CheckerRow['Name'];
		}

			//$HourTotal=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataIn,$link_id);  //本次请假换算小时数
			$HourTotal=calculateDateToDateHours($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataIn,$link_id);  //本次请假换算小时数
			//echo $HourTotal."<br>";
			$HourTotal2=$HourTotal;

			$OrderSignColor="";
			if ($Type=="带薪年假"){
				  $YearHolDay=GetYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataIn,$link_id)*8;
				 // echo "$HourTotal>$YearHolDay";
				 $AllYearHolDays=HaveYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataIn,$link_id) ;

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
			case 2:
			{
				$Estate="<div class='blueB'>退回</div>";
			}
			break;
			default:
			$Estate="<div class='redB'>未通过</div>";
			break;
			}
			//退回原因
			$returnReasonSql = mysql_query("Select * From $DataPublic.returnreason Where tableId = '$Id' and targetTable = '$DataPublic.kqqjsheet' order by DateTime Desc Limit 1");
			$returnReasonRow = mysql_fetch_assoc($returnReasonSql);
			$returnReason = ($returnReasonRow["Reason"]=="")?"&nbsp;":$returnReasonRow["Reason"];


			$bcType=$bcType==0?"标准":"<div class=yellowB>临时</div>";
			$LockRemark="";
			$GroupName=$myRow["GroupName"]==""?"<div class=\"redB\">未设置</div>":$myRow["GroupName"];//小组

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
			array(0=>$GroupName,1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$StartDate, 	1=>"align='center'"),
			array(0=>$EndDate, 		1=>"align='center'"),
			array(0=>$bcType, 		1=>"align='center'"),
			array(0=>$HourTotal2, 	1=>"align='center'"),
			array(0=>$Type,			1=>"align='center'"),
			array(0=>$Proof,		1=>"align='center'"),
			array(0=>$Reason, 		1=>"align='center'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$returnReason, 		1=>"align='center'"),
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