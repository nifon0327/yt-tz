<?php 
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=18;				
$tableMenuS=600;
$sumCols="14,15,16";		//求和列
ChangeWtitle("$SubCompany 假日加班统计");
$funFrom="kq_jrjb";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属公司|60|员工ID|50|员工姓名|60|部门|60|职位|60|月份|60|1.5倍时薪|60|1.5倍工时|60|2倍时薪|60|2倍工时|60|3倍时薪|60|3倍工时|60|加班费|60|个税扣款|60|实付|60|状态|40|更新日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="1,14,2,26,4,7,8,11,19";

$ActioToS="1,11,19";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	
	 //选择公司
	  $SelectTB="M";$SelectFrom=1; 
	  //选择地点
	 include "../model/subselect/WorkAdd.php"; 
	 	
	
	$date_Result = mysql_query("SELECT Month FROM $DataIn.hdjbsheet WHERE 1 GROUP BY Month order by Id DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.="and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
	//$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata WHERE 1 ORDER BY Id",$link_id);
	$B_Result=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
						    WHERE 1 AND (cSign=$Login_cSign OR cSign=0 )  ORDER BY Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
		echo "<option value='' selected>全部部门</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND S.BranchId='$BranchId'";
				}
			else{
				echo "<option value='$B_Id'>$B_Name</option>";
				}
			}while ($B_Row = mysql_fetch_array($B_Result));
		}
	echo"</select>&nbsp;";
	     $FormalSign=$FormalSign==""?0:$FormalSign;
		$selStr="selFlag" . $FormalSign;
		$$selStr="selected";
		echo"<select name='FormalSign' id='FormalSign' onchange='RefreshPage(\"$nowWebPage\")'>
		     <option value='0' $selFlag0>全部</option>
			 <option value='1' $selFlag1>正式工</option>
			 <option value='2' $selFlag2>试用期</option>";
		echo "</select>&nbsp;";
		if($FormalSign>0)$SearchRows.=" AND M.FormalSign='$FormalSign'";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
//有效的员工
$mySql="SELECT S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.oHours,S.oWage,S.xHours,S.xWage,S.fHours,S.fWage,S.oRandP,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,M.Name,S.cSign
FROM $DataIn.hdjbsheet S
LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
WHERE 1 $SearchRows ORDER BY S.BranchId,S.JobId,S.Number";

//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
			$BranchId=$myRow["BranchId"];				
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
		$Month=$myRow["Month"];
		$MonthSTR="<a href='kq_checkio_count.php?CountType=1&Number=$Number&CheckMonth=$Month' target='_blank'>$Month</a>";	
		$oHours=$myRow["oHours"];
		$oWage=$myRow["oWage"];	
		$xHours=$myRow["xHours"];
		$xWage=$myRow["xWage"];		
		$fHours=$myRow["fHours"];
		$fWage=$myRow["fWage"];
		
		$oRandP=$myRow["oRandP"];
		$Amount=$myRow["Amount"];
		$TotalAmount=$Amount+$oRandP;
		
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
				break;
			}
		//读取加班时薪资料
$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE ValueCode='103' OR ValueCode='104' and Estate=1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	do{
		$ValueCode=$checkRow["ValueCode"];
		switch($ValueCode){
			case "103"://2倍时薪
				$HourlyWage2=$checkRow["Value"];
				break;
			case "104"://3倍时薪
				$HourlyWage3=$checkRow["Value"];
				break;
			}
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
$HourlyWage2=$HourlyWage2==""?0:$HourlyWage2;
$HourlyWage3=$HourlyWage3==""?0:$HourlyWage3;
$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";

		$ValueArray=array(
		    array(0=>$cSign,	1=>"align='center'"),
			array(0=>$Number,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$MonthSTR,	1=>"align='center'"),
			array(0=>$oWage,	1=>"align='center'"),
			array(0=>$oHours,	1=>"align='center'"),
			array(0=>$xWage,	1=>"align='center'"),
			array(0=>$xHours,	1=>"align='center'"),
			array(0=>$fWage,	1=>"align='center'"),
			array(0=>$fHours, 	1=>"align='center'"),
			array(0=>$TotalAmount,	1=>"align='center'"),
			array(0=>$oRandP,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
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
