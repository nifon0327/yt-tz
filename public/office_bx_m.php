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
$funFrom="office_bx";
$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|补休开始时间|125|补休结束时间|125|补休工时|60|补休原因|120|登记日期|70|操作人|80|审核|80|审核状态|80";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){$SearchRows="";}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select> $CencalSstr";
//步骤4：
$TitlePre="<br>&nbsp;&nbsp;退回原因&nbsp;<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
//取得当前用户的部门ID

if($Login_P_Number=='10082' || $Login_P_Number == "11008" )
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

$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Note,J.Reason,J.Date,J.Checker,J.Estate,J.type,J.Operator,M.Number,M.Name,M.KqSign,M.JobId,M.BranchId
 FROM $DataPublic.bxsheet J 
	LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	WHERE 1  $BranchIdSTR  AND   J.Estate=1 order by J.StartDate DESC";
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
		$calculateType = $myRow["type"];	
		$HourTotal=($calculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);

		$note = $myRow["Note"];
		$checker = $myRow["Checker"];
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
			
		$checker = $myRow["Checker"];
		if($checker != "")
		{
			$checkerResult = mysql_query("Select Name From $DataPublic.staffmain where Number = '$checker'");
			$checkerRow = mysql_fetch_assoc($checkerResult);
			$checker = $checkerRow["Name"];
		}
		
		$Operator=$myRow["Operator"];
		if($Operator != "")
		{
			$operatorResult = mysql_query("Select Name From $DataPublic.staffmain where Number = '$Operator'");
			$operatorRow = mysql_fetch_assoc($operatorResult);
			$Operator = $operatorRow["Name"];
		}

			
		$ValueArray=array(
			array(0=>$Branch,		1=>"align='center'"),
			array(0=>$Job, 			1=>"align='center'"),
			array(0=>$Number, 		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$StartDate, 	1=>"align='center'"),
			array(0=>$EndDate, 		1=>"align='center'"),
			array(0=>$HourTotal, 	1=>"align='center'"),
			array(0=>$note, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$checker,			1=>"align='center'"),
			array(0=>$Operator,			1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'")
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