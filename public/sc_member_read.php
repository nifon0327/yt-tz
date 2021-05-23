<?php 
//电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 车间小组默认成员");
$funFrom="sc_member";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|60|隶属小组|100|小组成员|100|考勤状态|100";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT G.GroupId,G.GroupName,M.Name 
	FROM $DataIn.staffgroup G 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
	WHERE B.TypeId=2  AND M.cSign=$Login_cSign 
	ORDER BY G.GroupId ",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='GroupId' id='GroupId' onchange='ResetPage(this.name)'>";
		do{
			$theGroupId=$myrow["GroupId"];
			$Name=$myrow["Name"];
			$GroupName=$myrow["GroupName"];
			$GroupId=$GroupId==""?$theGroupId:$GroupId;
			if ($GroupId==$theGroupId){
				echo "<option value='$theGroupId' selected>$GroupName - $Name</option>";
				$SearchRows=" AND M.GroupId='$theGroupId'";
				}
			else{
				echo "<option value='$theGroupId'>$GroupName - $Name</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT G.GroupName,M.Id,M.Locks,M.Name,M.KqSign
FROM $DataPublic.staffmain M
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
WHERE 1 $SearchRows AND M.Estate=1 AND M.cSign=$Login_cSign  ORDER BY M.KqSign DESC,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$GroupName=$myRow["GroupName"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"需考勤":"<div class='redN'>不需考勤</div>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$GroupName, 	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$KqSign, 	1=>"align='center'")
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