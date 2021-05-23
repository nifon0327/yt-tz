<?php 
//电信-ZX  2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$OperationResult="N";
$StaffPhoto="&nbsp;";
$CheckTime=$InTime;

$CurDate=date("Y-m-d");  //当前日期

//$checkSql=mysql_query("SELECT Number,Name,KqSign,BranchId,JobId FROM $DataPublic.staffmain WHERE IdNum='$IdNum' AND IdNum!='' AND Estate='1' LIMIT 1",$link_id);
$checkSql=mysql_query("SELECT Number,Name,KqSign,BranchId,JobId FROM $DataPublic.staffmain WHERE IdNum='$IdNum' AND IdNum!='' AND ComeIn<='$CurDate' AND Estate='1' LIMIT 1",$link_id);

if($checkRow=mysql_fetch_array($checkSql)){
	$KqSign=$checkRow["KqSign"];//分三种情况：
	$Number=$checkRow["Number"];
	$Name=$checkRow["Name"];
	$BranchId=$checkRow["BranchId"];
	$JobId=$checkRow["JobId"];
	$PhotoFile="../download/staffPhoto/p".$Number.".jpg";
	//if(file_exists($PhotoFile)){//如果照片存在
		$StaffPhoto="<img src='$PhotoFile' width='164' height='211'>";
		//}
	//date_default_timezone_set('Asia/Shanghai');
	
	if($KqSign<3){//需考勤或考勤参考
		//签卡分析
		include "kq_sign_ajax_1.php";
		}
	else{
		$ReInfo="$Name<br>无需考勤";						//返回提示信息和员工姓名
		}
	}
else{//非正式员工
	//检查是否临时工
	$checkSqltemp=mysql_query("SELECT Number,Name,BranchId,JobId FROM $DataIn.stafftempmain WHERE IdNum='$IdNum' AND IdNum!='' AND Estate='1' LIMIT 1",$link_id);
	if($checkRowtemp=mysql_fetch_array($checkSqltemp)){
		$Number=$checkRowtemp["Number"];
		$Name=$checkRowtemp["Name"];
		$BranchId=$checkRowtemp["BranchId"];
		$JobId=$checkRowtemp["JobId"];
		include "kqtemp_sign_ajax_1.php";
		$PhotoFile="../download/stafftempPhoto/p".$Number.".jpg";
		//if(file_exists($PhotoFile)){//如果照片存在
			$StaffPhoto="<img src='$PhotoFile' width='164' height='211'>";
			//}
		}
	else{
		$ReInfo="无效卡";								//只返回提示信息
		}
	}
	
if($OperationResult=="N"){
	$ReBack="<table width=100% height=480><tr>
	<td width='25%' align='center' valign='middle' style='color: #FF0000;font-weight: bold;font-size:70'>$StaffPhoto</td>
	<td width='75%' align='center' valign='middle' style='color: #FF0000;font-weight: bold;font-size:70'>$ReInfo</td>
	</tr></table>";
	}
else{
	$ReBack="<table width=100% height=480><tr>
	<td width='25%' align='center' valign='middle' style='color: #FF0000;font-weight: bold;font-size:70'>$StaffPhoto</td>
	<td width='75%' align='center' valign='middle' style='color: #009900;font-weight: bold;font-size:70'>$ReInfo</td>
	</tr></table>";
	}
$ReBack=$Record==""?$ReBack:$ReBack."|".$Record;
echo $ReBack;
?>