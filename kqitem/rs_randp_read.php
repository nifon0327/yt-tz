<?php 
//电信-ZX  2012-08-01
/*
$DataIn.staffrandp
$DataPublic.staffmain
$DataPublic.branchdata
$DataPublic.jobdata
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 员工奖惩记录");
$funFrom="rs_randp";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|姓名|60|部门|60|职位|60|奖惩事项|400|奖惩金额|60|日期|70|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m') AS Month FROM $DataIn.staffrandp WHERE 1 group by DATE_FORMAT(Date,'%Y-%m') order by Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and DATE_FORMAT(R.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
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
$mySql="SELECT 
R.Id,R.Number,R.Type,R.Content,R.Amount,R.Date,R.Locks,R.Operator,M.Name,B.Name AS Branch,J.Name AS Job
FROM $DataIn.staffrandp R
LEFT JOIN $DataPublic.staffmain M ON R.Number=M.Number
LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
WHERE 1 $SearchRows ORDER BY R.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Date=$myRow["Date"];
		$MonthTemp=substr($Date,0,7);
		$Content=$myRow["Content"];
		$Type=$myRow["Type"];
		$Amount=$Type==1?"<div class='greenB'>$myRow[Amount]</div>":"<div class='redB'>($myRow[Amount])</div>";
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//是否强制锁
		$LockRemark="";
		$checkMonth=mysql_query("SELECT Id FROM $DataIn.cwxzsheet WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
		if($checkRow = mysql_fetch_array($checkMonth)){
			$LockRemark="该员工 $MonthTemp 月薪资已生成,禁止修改.";
			}
		$ValueArray=array(
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Content,	3=>"..."),
			array(0=>$Amount,	1=>"align='center'"),
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
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
