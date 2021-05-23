<?php
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;
$tableMenuS=400;
ChangeWtitle("$SubCompany 助学小孩信息列表");
$funFrom="childstudy";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|标识|50|部门|70|职位|70|员工姓名|100|小孩姓名|100|性别|40|开始申请助学年级|150|缴费金额|60|备注|200|状态|40|更新日期|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6";

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
$mySql="SELECT   A.Id,A.Number,A.ChildName,A.Sex,A.Amount,A.Remark,A.StartSchool,A.Date,A.Estate,A.Locks,A.Operator,M.Name,B.Name AS Branch,J.Name AS Job,M.Estate AS mEstate,M.cSign
FROM $DataPublic.childinfo A 
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows  ORDER BY M.cSign DESC,A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/childinfo/",$SinkOrder,$motherSTR);
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
        $mEstate=$myRow["mEstate"];
        $Name  =  $mEstate!=1 ?"<span class='redB'>$Name</span>":$Name;
		$ChildName=$myRow["ChildName"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Amount=$myRow["Amount"];
		$StartSchool=$myRow["StartSchool"];
		$Remark=$myRow["Remark"];
		$Attached=$myRow["Attached"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		$cSign=$myRow["cSign"];
		$cSignStr = $cSign == 7 ?"<span class='blueB'>研砼</span>":"<span class='greenB'>皮套</span>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];

/*		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached="C".$Id.".jpg";
			$Attached=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}*/

		$ValueArray=array(
			array(0=>$cSignStr,1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$JobName,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$ChildName,1=>"align='center'"),
			array(0=>$Sex,1=>"align='center'"),
			array(0=>$StartSchool,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
		//	array(0=>$Attached,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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