<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployk
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
$DataPublic.kqtype
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 考勤调动记录");
$funFrom="rs_tdk";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|部门|70|职位|70|员工ID|70|员工姓名|70|原考勤设定|90|现考勤设定|90|调动原因|60|起效月份|80|更新日期|100|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT K.Id,K.Number,K.ActionIn,K.ActionOut,K.Month,K.Remark,K.Date,K.Locks,K.Operator,M.Name,M.BranchId,M.JobId
	FROM $DataPublic.redeployk K 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
	WHERE 1 $SearchRows ORDER BY K.Id DESC,M.Estate DESC,K.Month DESC,K.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$BranchId=$myRow["BranchId"];	
		//
		$B_Query = mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1");		
		if($B_Query)	
		{
			$B_Result = mysql_fetch_array($B_Query);
			$Branch=$B_Result["Name"];	
		}			
		$JobId=$myRow["JobId"];
		$J_Query = mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id);
		if($J_Query)
		{
			$J_Result = mysql_fetch_array($J_Query);
			$Job=$J_Result["Name"];
		}
		//
		$ActionIn=$myRow["ActionIn"];
		$inResult = mysql_query("SELECT Name FROM $DataPublic.kqtype WHERE Id=$ActionIn Limit 1",$link_id);
		if($inRow = mysql_fetch_array($inResult)){
			$inType=$inRow["Name"];
			}
		else{
			$inType="&nbsp;";
			}
		$ActionOut=$myRow["ActionOut"];
		$outResult = mysql_query("SELECT Name FROM $DataPublic.kqtype WHERE Id=$ActionOut Limit 1",$link_id);
		if($outRow = mysql_fetch_array($outResult)){
			$outType=$outRow["Name"];
			}
		else{
			$outType="&nbsp;";
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Month=$myRow["Month"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Branch,		1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$outType,		1=>"align='center'"),
			array(0=>$inType, 		1=>"align='center'"),
			array(0=>$Remark,		1=>"align='center'"),
			array(0=>$Month,		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
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