<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployg
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 等级调动记录");
$funFrom="rs_tdg";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|部门|70|职位|70|员工ID|60|员工姓名|80|原等级|60|新等级|60|调动原因|80|起效月份|80|更新日期|100|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
if($Number!=""){ $SearchRows.=" AND M.Number=$Number";}//来自人事资料等级链接
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT G.Id,G.Number,G.ActionIn,G.ActionOut,G.Month,G.Remark,
	G.Date,G.Locks,G.Operator,M.Name,M.BranchId,M.JobId
	FROM $DataPublic.redeployg G 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=G.Number 
	WHERE 1 $SearchRows order by G.Id DESC,M.Estate DESC,G.Month DESC,G.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$ActionIn=$myRow["ActionIn"];
		$ActionOut=$myRow["ActionOut"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where  Id='$BranchId' LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id='$JobId' LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Month=$myRow["Month"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Branch,		1=>"align='center'"),
			array(0=>$Job,			1=>"align='center'"),
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name, 		1=>"align='center'"),
			array(0=>$ActionOut,	1=>"align='center'"),
			array(0=>$ActionIn,		1=>"align='center'"),
			array(0=>$Remark,		1=>"align='center'"),
			array(0=>$Month, 		1=>"align='center'"),
			array(0=>$Date,			1=>"align='center'"),
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