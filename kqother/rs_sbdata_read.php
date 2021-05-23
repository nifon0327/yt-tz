<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.sbdata
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 社保资料");
$funFrom="rs_sbdata";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|员工姓名|60|社保类型|100|部门|70|职位|70|起始月份|70|结束月份|70|缴费记录|60|备注|200|状态|40|设置日期|70|操作员|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";	
	$type_Result = mysql_query("SELECT Id,Name FROM $DataPublic.rs_sbtype WHERE Id<4 ORDER BY Id",$link_id);
	if ($typeRow = mysql_fetch_array($type_Result)){
		echo"<select name='chooseType' id='chooseType' onchange='ResetPage(this.name)'>
		<option value='' selected>全部</option>";
		 		do{
			       $tId=$typeRow["Id"];
					$tName=$typeRow["Name"];
					if($tId==$chooseType){
						echo "<option value='$tId' selected>$tName</option>";
						$SearchRows=" AND  S.Type='$tId' ";
						}
					else{
						echo "<option value='$tId'>$tName</option>";
						}
			}while($typeRow = mysql_fetch_array($type_Result));
		echo"</select>&nbsp;";
		}
	}

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Number,S.sMonth,S.eMonth,S.Note,S.Date,S.Estate,S.Locks,S.Operator,
	M.Name,M.BranchId,M.JobId,M.Estate AS mEsate,T.Name AS Type
	FROM $DataPublic.sbdata S 
    LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
    LEFT JOIN $DataPublic.rs_sbtype T  ON S.Type=T.Id
	WHERE 1  $SearchRows AND M.cSign='$Login_cSign'  ORDER BY  M.Estate DESC,S.Estate DESC,M.BranchId,M.JobId,M.Number,S.Id DESC";	
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Type=$myRow["Type"];
		$sMonth =$myRow["sMonth"];
		$mEsate =$myRow["mEsate"];
		$Name=$mEsate==1?$myRow["Name"]:"<span class='redB'>$myRow[Name]</span>";
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$sMonth =$myRow["sMonth"];
		$eMonth =$myRow["eMonth"]==0?"&nbsp;":$myRow["eMonth"];
		$Note =$myRow["Note"]==""?"&nbsp;":$myRow["Note"];
		
		$Estate =$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
		$ValueArray=array(
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$Type, 	1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$sMonth,	1=>"align='center'"),
			array(0=>$eMonth,	1=>"align='center'"),			
			array(0=>"<a href='../public/staff_sbview.php?f=$ViewNumber' target='_blank'>查看</a>",1=>"align='center'"),
			array(0=>$Note,	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
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
if($myResult)
{
	$RecordToTal= mysql_num_rows($myResult);
}
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>