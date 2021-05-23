<?php 
include "../model/modelhead.php";
$sumCols="7";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 购房补助信息列表");
$funFrom="staffhouse";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|部门|70|职位|70|员工ID|60|员工姓名|100|补助金额|60|凭证|50|备注|250|状态|40|更新日期|70|操作人|60";
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
$mySql="SELECT  A.Id,A.Number,A.Amount,A.Remark,A.Attached,A.Date,A.Estate,A.Locks,A.Operator,
M.Name,B.Name AS Branch,J.Name AS Job,M.Estate AS mEstate
FROM $DataIn.staff_housesubsidy A 
LEFT JOIN $DataIn.staffmain M ON M.Number=A.Number
LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows  ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/staffhouseinfo/",$SinkOrder,$motherSTR);
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];		
        $mEstate=$myRow["mEstate"];	
        $Number=$myRow["Number"];	
        $Name  =  $mEstate!=1 ?"<span class='redB'>$Name</span>":$Name;
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];

		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);	
			$Attached="<a href=\"openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
			$Attached="-";
			}
			
		$ValueArray=array(
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$JobName,1=>"align='center'"),
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
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