<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=15;				
$tableMenuS=600;
$sumCols="11";		//求和列
$funFrom="kq_jrjb";
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 假日加班费审核");
$Th_Col="选项|40|序号|40|员工ID|50|员工姓名|60|部门|60|职位|60|月份|60|2倍时薪|60|2倍工时|60|3倍时薪|60|3倍工时|60|加班费|60|状态|40|更新日期|80|操作|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过

//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT Month FROM $DataIn.hdjbsheet WHERE 1 AND Estate=2 GROUP BY Month order by Id DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>";
      }
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Number,S.Month,S.xHours,S.xWage,S.fHours,S.fWage,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,M.Name,J.Name AS Job,B.Name AS Branch
FROM $DataIn.hdjbsheet S
LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
WHERE 1  $SearchRows AND S.Estate=2 ORDER BY M.BranchId,M.JobId,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Month=$myRow["Month"];
		$MonthSTR="<a href='kq_checkio_count.php?CountType=1&Number=$Number&CheckMonth=$Month' target='_blank'>$Month</a>";		
		$xHours=$myRow["xHours"];
		$fHours=$myRow["fHours"];	
		$xWage=$myRow["xWage"];
		$fWage=$myRow["fWage"];		
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$Locks=1;
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='出错'>出错</div>";
				$Locks=0;
				break;
			}
		$ValueArray=array(
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$Job, 		1=>"align='center'"),
			array(0=>$MonthSTR,	1=>"align='center'"),
			array(0=>$xWage, 	1=>"align='center'"),
			array(0=>$xHours,	1=>"align='center'"),
			array(0=>$fWage,	1=>"align='center'"),
			array(0=>$fHours,	1=>"align='center'"),
			array(0=>$Amount, 	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
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