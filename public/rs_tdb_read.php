<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 员工调动部门记录");
$funFrom="rs_tdb";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|员工ID|60|员工姓名|80|调出部门|100|调入部门|100|调动原因|80|起效月份|80|更新日期|100|操作员|80";
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
$mySql="SELECT B.Id,B.Number,B.ActionIn,B.ActionOut,B.Month,B.Remark,
	B.Date,B.Locks,B.Operator,M.Name
	FROM $DataPublic.redeployb B 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Number 
	WHERE 1 $SearchRows order by B.Id DESC,M.Estate DESC,B.Month DESC,B.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$ActionIn=$myRow["ActionIn"];
		$inResult = mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE Id=$ActionIn Limit 1",$link_id);
		if($inRow = mysql_fetch_array($inResult)){
			$inBranch=$inRow["Name"];
			}
		else{
			$inBranch="&nbsp;";
			}
		$ActionOut=$myRow["ActionOut"];
		$outResult = mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE Id=$ActionOut Limit 1",$link_id);
		if($outRow = mysql_fetch_array($outResult)){
			$outBranch=$outRow["Name"];
			}
		else{
			$outBranch="&nbsp;";
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Month=$myRow["Month"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		$Branch=$myRow["Branch"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$outBranch,	1=>"align='center'"),
			array(0=>$inBranch, 	1=>"align='center'"),
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