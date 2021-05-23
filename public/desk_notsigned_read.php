<?php 
//代码共享 -EWEN 2012-09-03
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;
$tableMenuS=700;
ChangeWtitle("$SubCompany 未签卡列表");
$funFrom="desk_notsigned";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|考勤日期|100|考勤情况|100|员工ID|80|姓名|60|部门|60|小组|60|职位|60|备注|300";
$ActioToS="70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$Keys=31;
//步骤3：
$CheckDate=$CheckDate==""?date("Y-m-d"):$CheckDate;
if($kqSignType==""){//
	if(date("H")<17){
		$kqSignType=1;
		}
	else{
		$kqSignType=0;
		}
	}
$TempkqSignType="kqSignType".strval($kqSignType); 
$$TempkqSignType="selected";	
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	echo"<input name='CheckDate' type='text' id='CheckDate' style='width:100px;' maxlength='10' value='$CheckDate' onchange='ResetPage(this.name)' >&nbsp;";
	echo"<select name='kqSignType' id='kqSignType' onchange='ResetPage(this.name)'><option value='1' $kqSignType1>签到</option><option value='0' $kqSignType0>签退</option>&nbsp;";
	$result = mysql_query("SELECT B.Id,B.Name FROM $DataPublic.staffmain A LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId WHERE B.Estate=1 GROUP BY A.BranchId ORDER BY B.Id",$link_id);
	echo"<select name='BranchId' id='BranchId' onchange='ResetPage(this.name)'><option value='' selected>--全部部门--</option>";
	if($myrow = mysql_fetch_array($result)){
		do{
			$theId=$myrow["Id"];
			$theName=$myrow["Name"];
			if ($theId==$BranchId){
				echo "<option value='$BranchId' selected>$theName</option>";
				$SearchRows=" AND A.BranchId='$theId' ";
				}
			else{
				echo "<option value='$theId'>$theName</option>";
				
				}
			}while ($myrow = mysql_fetch_array($result));
		}
	echo "</select>&nbsp;";
	//小组
	$resultGroup = mysql_query("SELECT B.GroupId,B.GroupName FROM $DataPublic.staffmain A LEFT JOIN $DataIn.staffgroup B ON B.GroupId=A.GroupId WHERE B.Estate=1 $SearchRows GROUP BY A.GroupId ORDER BY B.Id",$link_id);
	if($myrowGroup = mysql_fetch_array($resultGroup)){
	echo"<select name='GroupId' id='GroupId' onchange='ResetPage(this.name)'><option value='' selected>--全部小组--</option>";
		do{
			$theGroupId=$myrowGroup["GroupId"];
			$theGroupName=$myrowGroup["GroupName"];
			if ($theGroupId==$GroupId){
				echo "<option value='$theGroupId' selected>$theGroupName</option>";
				$SearchRows.=" AND A.GroupId='$theGroupId' ";
				}
			else{
				echo "<option value='$theGroupId'>$theGroupName</option>";
				
				}
			}while ($myrowGroup = mysql_fetch_array($resultGroup));
			echo "</select>&nbsp;";
	}
	}

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
//检查时间范围
//如果是同一天
$ShowList=1;
if($CheckDate>date("Y-m-d")){
	$ShowList=0;
	}
else{
	//$checkBCB=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.kqbcb WHERE Id='1'",$link_id));//班次1资料
	if($CheckDate==date("Y-m-d")){//同一天，要时间判断
		if($kqSignType==1 && date("H:i:s")<"08:00:00")
		$ShowList=0;
		if($kqSignType==0 && date("H:i:s")<"17:30:00")
		$ShowList=0;
		}
	}
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
if($ShowList==1){
	$mySql="
		SELECT 
		A.Id,A.Number,A.Name,A.KqSign,E.Name AS Branch,J.Name AS Job,D.GroupName,datediff('2012-09-03','$CheckDate') AS Days
		FROM $DataPublic.staffmain A
		LEFT JOIN (SELECT Number FROM $DataIn.checkinout WHERE kqDate='$CheckDate' AND kqSign='$kqSignType' GROUP BY Number) B ON B.Number=A.Number
		LEFT JOIN $DataPublic.staffsheet C ON C.Number=A.Number
		LEFT JOIN $DataIn.staffgroup D ON D.GroupId=A.GroupId
		LEFT JOIN $DataPublic.branchdata E ON E.Id=A.BranchId
		LEFT JOIN $DataPublic.jobdata J ON J.Id=A.JobId
		WHERE  B.Number IS NULL AND A.Estate=1 AND A.KqSign=1 AND A.cSign='$Login_cSign' $SearchRows ORDER BY A.BranchId,D.GroupName,A.JobId,A.ComeIn,A.Number";
		echo $mySql;
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
	if($myRow = mysql_fetch_array($myResult)){
		do{
			$m=1;
			$qjRemark="&nbsp;";
			$LockRemark="";
			$Id=$myRow["Id"];
			$Number=$myRow["Number"];
			$Name=$myRow["Name"];
			$Branch=$myRow["Branch"];
			$Job=$myRow["Job"];
			$KqSign=$myRow["KqSign"];
			
			$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"&nbsp;");
			$Name="<span class='greenB'>$Name</span>";
			//员工请假超过半个月的显示颜色
			$qjcolor="&nbsp;";
			$qjResult=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataIn.kqqjsheet WHERE Number='$Number' AND EndDate>='$today'",$link_id);
			if($qjRow=mysql_fetch_array($qjResult)){//请假
				$qjRemark="<span class='yellowB'>此员工请假:<br>从".$qjRow["StartDate"]."至".$qjRow["EndDate"]."</span>";
				$LockRemark="员工请假";
				}
			$GroupName=$myRow["GroupName"]==""?"<div class=\"redB\">未设置</div>":$myRow["GroupName"];
			$Locks=1;
			$Days=$myRow["Days"];
			$qjRemark=$Days>2?"<span class='redB'>超出处理时间范围</span>":$qjRemark;
			$LockRemark=$Days>2?"超出处理时间范围":$LockRemark;
				$ValueArray=array(
					array(0=>$CheckDate,1=>"align='center'"),
					array(0=>"上班未打卡",1=>"align='center'"),
					array(0=>$Number,1=>"align='center' $qjcolor"),
					array(0=>$Name,1=>"align='center'"),
					array(0=>$Branch,1=>"align='center'"),
					array(0=>$GroupName,1=>"align='center'"),
					array(0=>$Job,1=>"align='center'"),
					array(0=>$qjRemark)
					);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			}while ($myRow = mysql_fetch_array($myResult));
		}
	else{
		noRowInfo($tableWidth,"");
		}
	}
else{
	noRowInfo($tableWidth,"超出查询范围");
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>