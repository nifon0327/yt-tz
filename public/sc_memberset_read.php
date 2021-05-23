<?php 
//电信---yang 20120801
include "../model/modelheadNew.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 车间小组每天成员列表");
$funFrom="sc_memberset";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|80|序号|80|生产日期|100|员工姓名|100|职位|100|考勤状态|100";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,7,8";//4,75,

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$Date=$Date==""?date("Y-m-d"):$Date;
	$SearchRows=" AND S.Date='$Date'";
	echo"<input name='Date' type='text' id='Date' size='10' maxlength='10' value='$Date' onchange='document.form1.submit()'  onFocus='WdatePicker()'/>&nbsp;";
	$result = mysql_query("SELECT G.GroupId,G.GroupName,M.Name 
	FROM $DataIn.staffgroup G 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
	WHERE B.TypeId=2 AND G.Estate=1 AND M.cSign=$Login_cSign  
	ORDER BY G.GroupId",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='GroupId' id='GroupId' onchange='ResetPage(this.name)'>";
		do{
			$theGroupId=$myrow["GroupId"];
			$Name=$myrow["Name"];
			$GroupName=$myrow["GroupName"];
			$GroupId=$GroupId==""?$theGroupId:$GroupId;
			if ($GroupId==$theGroupId){
				echo "<option value='$theGroupId' selected>$GroupName - $Name</option>";
				$SearchRows.=" AND S.GroupId='$theGroupId'";
				}
			else{
				echo "<option value='$theGroupId'>$GroupName - $Name</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}	//日期选择
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.KqSign,S.Date,S.Locks,M.Name,J.Name AS Job
FROM $DataIn.sc1_memberset S
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows AND M.cSign=$Login_cSign ORDER BY M.KqSign DESC,M.BranchId,M.JobId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Name=$myRow["Name"];
		$Job=$myRow["Job"];
		$KqSign=$myRow["KqSign"];
		$GroupName=$myRow["GroupName"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Operator= ($myRow["Leader"]=="")?"&nbsp;":$myRow["Leader"];
		
		include "../model/subprogram/staffname.php";
		switch($KqSign){
			case 1:$KqSign="<div class='greenB'>需要考勤</div>";break;
			case 2:$KqSign="考勤参考";break;
			case 3:$KqSign="不需考勤";break;
			}
		$ValueArray=array(
			array(0=>$Date,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$KqSign,		1=>"align='center'")
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