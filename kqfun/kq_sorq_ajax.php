<?php 
//电信-ZX  2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "kqcode/kq_function.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$OperationResult="N";
$StaffPhoto="&nbsp;";
$StaffType=1;
/*
$checkSql=mysql_query("SELECT Number,Name,KqSign FROM $DataPublic.staffmain WHERE IdNum='$IdNum' AND IdNum!='' AND cSign=7 AND Estate='1' LIMIT 1",$link_id);
*/
$checkSql=mysql_query("SELECT Number,Name,KqSign FROM $DataPublic.staffmain WHERE IdNum='$IdNum' AND IdNum!='' AND cSign=$Login_cSign AND Estate='1' LIMIT 1",$link_id);

if($checkRow=mysql_fetch_array($checkSql)){
	$KqSign=$checkRow["KqSign"];//分三种情况：
	$Number=$checkRow["Number"];
	$Name=$checkRow["Name"];
	$PhotoFile="../download/staffPhoto/p".$Number.".jpg";
	$StaffPhoto="<img src='$PhotoFile' width='164' height='211'>";
	$OperationResult="Y";
	}
else{//非正式员工
	//检查是否临时工
	$StaffType=0;
	$checkSqltemp=mysql_query("SELECT Number,Name FROM $DataIn.stafftempmain WHERE IdNum='$IdNum' AND IdNum!='' AND Estate='1' LIMIT 1",$link_id);
	if($checkRowtemp=mysql_fetch_array($checkSqltemp)){
		$Number=$checkRowtemp["Number"];
		$Name=$checkRowtemp["Name"];
		$PhotoFile="../download/stafftempPhoto/p".$Number.".jpg";
		//if(file_exists($PhotoFile)){//如果照片存在
			$StaffPhoto="<img src='$PhotoFile' width='164' height='211'>";
			//}
		$OperationResult="Y";
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
	switch($CheckType){
	case "S"://查询
		//默认为考勤统计
		if($StaffType==0){		//临时工查询
			$SELECTSTR="
				<select name='sType' id='sType' onChange='javascript:ToSearch(3)'>
				<option value='1' selected>考勤资料</option>
				<option value='2'>薪资资料</option>
				</select>";
			//年份
			$CheckYearSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%Y') AS Year FROM $DataIn.checkiotemp WHERE 1 AND Number=$Number GROUP BY DATE_FORMAT(CheckTime,'%Y') ORDER BY CheckTime DESC",$link_id);
			if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
				$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
				$sYear="";
				do{
					$YearTemp=$CheckYearRow["Year"];
					if($sYear=="")$sYear=$YearTemp;
					$Y.="<option value='$YearTemp'>$YearTemp</option>";
					}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
				$Y.="</select> 年";
				}
			//月份
			$CheckMonthSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%m') AS Month FROM $DataIn.checkiotemp WHERE 1 AND Number=$Number AND DATE_FORMAT(CheckTime,'%Y')='$sYear' GROUP BY DATE_FORMAT(CheckTime,'%Y-%m') ORDER BY CheckTime DESC",$link_id);
			if($CheckMonthRow=mysql_fetch_array($CheckMonthSql)){
				$M="<select name='sMonth' id='sMonth' style='width:85px' onChange='javascript:ToSearch(2)'>";
				$sMonth="";
				do{
					$MonthTemp=$CheckMonthRow["Month"];
					if($sMonth=="")$sMonth=$MonthTemp;
					$M.="<option value='$MonthTemp'>$MonthTemp</option>";
					}while ($CheckMonthRow=mysql_fetch_array($CheckMonthSql));
				$M.="</select> 月";
				}
			if($sYear!="" && $sMonth!=""){
					include "report_kq_lsg.php";
				}
			}
		else{					//正式工查询
			
			$SELECTSTR="
				<select name='sType' id='sType' onChange='javascript:ToSearch(3)'>
				<option value='1' selected>考勤资料</option>
				<option value='2'>薪资资料</option>
				<option value='3'>请假资料</option>
				<option value='4'>年假资料</option>
				</select>";
			//年份
			$CheckYearSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%Y') AS Year FROM $DataIn.checkinout WHERE 1 AND Number=$Number GROUP BY DATE_FORMAT(CheckTime,'%Y') ORDER BY CheckTime DESC",$link_id);
			if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
				$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
				$sYear="";
				do{
					$YearTemp=$CheckYearRow["Year"];
					if($sYear=="")$sYear=$YearTemp;
					$Y.="<option value='$YearTemp'>$YearTemp</option>";
					}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
				$Y.="</select> 年";
				}
			//月份
			$CheckMonthSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%m') AS Month FROM $DataIn.checkinout WHERE 1 AND Number=$Number AND DATE_FORMAT(CheckTime,'%Y')='$sYear' GROUP BY DATE_FORMAT(CheckTime,'%Y-%m') ORDER BY CheckTime DESC",$link_id);
			if($CheckMonthRow=mysql_fetch_array($CheckMonthSql)){
				$M="<select name='sMonth' id='sMonth' style='width:85px' onChange='javascript:ToSearch(2)'>";
				$sMonth="";
				do{
					$MonthTemp=$CheckMonthRow["Month"];
					if($sMonth=="")$sMonth=$MonthTemp;
					$M.="<option value='$MonthTemp'>$MonthTemp</option>";
					}while ($CheckMonthRow=mysql_fetch_array($CheckMonthSql));
				$M.="</select> 月";
				}
			if($sYear!="" && $sMonth!=""){
				include "report_kq_zsg.php";
				}
			}
		//$T 查询结果 动态改变 通过Ajax提取
		//$SELECTSTR 可选功能项目，触发Ajax
		$ReBack="
		<input name='Number' type='hidden' id='Number' value='$Number'>
		<table width='100%' height='488' bgcolor='#CCCCCC'>
		<tr>
			<td height='50' align='center' style='font-weight: bold;font-size:30'>查询</td>
			<td width='80%' rowspan='6' align='center' valign='top'><div id='ResultsTable'>$T</div></td>
		</tr>
		<tr>
		  <td  height='50' align='center' style='font-weight: bold;font-size:30'><div id='ResultsYear'>$Y</div></td>
  		</tr>
		<tr>
		  <td  height='50' align='center' style='font-weight: bold;font-size:30'><div id='ResultsMonth'>$M</div></td>
  		</tr>
		<tr>
		  <td  height='50' align='center' style='font-weight: bold;font-size:20'>$SELECTSTR</td>
  		</tr>
		<tr><td height='230' align='center' valign='bottom' style='color: #009933;font-size:30'>$StaffPhoto</td></tr>
		<tr><td height='32' align='center' valign='middle' style='color: #009933;font-size:30'>$Name</td></tr>
		</table>
		";
		break;
	case "Q"://请假
		//请假或审核状态,检查该卡号是否管理员，如果是管理员，读取其级别
		$CheckPowerSql=mysql_query("SELECT Powers FROM $DataIn.kqqjpower WHERE Number=$Number LIMIT 1",$link_id);
		if($CheckPowerRow=mysql_fetch_array($CheckPowerSql)){
			$PowerS=$CheckPowerRow["Powers"];
			$ReBack="请假审核";
			include "qjsh_read.php";
			$ReBack="
			<input name='Number' type='hidden' id='Number' value='$Number'>
			<table width='100%' height='480' bgcolor='#CCCCCC'><tr><td align='center'>待审核请假记录$T</td></tr></table>";
			}
		else{//请假
			$qjtypeSql =  mysql_query("SELECT Id,Name FROM $DataPublic.qjtype WHERE Estate=1 AND ( Id in (1,4) ) ORDER BY Id",$link_id);
			if($qjtypeRow = mysql_fetch_array($qjtypeSql)){
				$qjStr="";
				do{
					$Id=$qjtypeRow["Id"];
					$qjName=$qjtypeRow["Name"];
					$qjStr.="<option value='$Id' selected>$Id - $qjName</option>";
					}while($qjtypeRow = mysql_fetch_array($qjtypeSql));
				}
			$StimeStr="
			<option value='08:00:00'>08:00:00</option>
			<option value='09:00:00'>09:00:00</option>
			<option value='10:00:00'>10:00:00</option>
			<option value='11:00:00'>11:00:00</option>
			<option value='12:00:00'>12:00:00</option>
			<option value='13:00:00'>13:00:00</option>
			<option value='14:00:00'>14:00:00</option>
			<option value='15:00:00'>15:00:00</option>
			<option value='16:00:00'>16:00:00</option>
			<option value='17:00:00'>17:00:00</option>
			";//<form name='qjForm' method='post' action=''><option value='' selected>请选择</option>$qjStr</select>
			$ReBack="
			<input name='Number' type='hidden' id='Number' value='$Number'>
			<table width=80% height=480>
			<tr><td  colspan='3' height='80' align='center' valign='middle' style='font-weight: bold;font-size:50'>员工请假申请</td></tr>
			<tr>
				<td width='25%' rowspan='5' align='right' valign='top'>$StaffPhoto</td>
				<td width='20%' align='right' valign='middle' style='font-size:30'>员工姓名</td>
				<td width='55%' valign='middle' style='color: #009933;font-size:30'>$Name</td>
			</tr>
			<tr>
				<td align='right' valign='middle' style='font-size:30'>请假原因</td>
				<td valign='middle'>
					<select name='Type' id='Type' style='width: 345px;'>$qjStr</select>
				</td>
			</tr>
			<tr>
				<td align='right' valign='middle' style='font-size:30'>起始时间</td>
				<td valign='middle'>
					<input type='text' name='Sdate' id='Sdate' size='10' maxlength='10' onfocus='new WdatePicker(this,null,false,\"whyGreen\")' readonly>
					<select name='Stime' id='Stime' style='width: 170px;'>
					<option value='08:00' selected>请选择</option>$StimeStr</select>
				</td>
			</tr>
			<tr>
				<td align='right' valign='middle' style='font-size:30'>结束时间</td>
				<td valign='middle'>
					<input type='text' name='Edate' id='Edate' size='10' maxlength='10' onfocus='new WdatePicker(this,null,false,\"whyGreen\")' readonly>
					<select name='Etime' id='Etime' style='width: 170px;'>
					<option value='17:00' selected>请选择</option>$StimeStr</select>
				</td>
			</tr>
			<tr><td>&nbsp;</td><td align='right'><input type='button' name='Submit' value='确定' onclick='qjAction()'></td></tr>
			</table>";
			}
		break;
		}
	}
$ReBack=$Record==""?$ReBack:$ReBack."|".$Record;
echo $ReBack;
?>