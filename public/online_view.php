<?php 
//电信-joseph
//代码共享-EWEN 2012-08-20
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<script src='../model/pagefun.js' type=text/javascript></script></head>";
$tableWidth=706;
ChangeWtitle("$SubCompany 在线用户列表");
?>
1、内部员工在线记录
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr align="center" bgcolor="#CCCCCC">
    <td width="60" height="25" class="A1111">序号</td>
    <td width="60" class="A1101">姓名</td>
	<td width="100" class="A1101">部门</td>
    <td width="120" class="A1101">登录时间</td>  
	 <td width="160" class="A1101">登录IP</td>
     <td width="200" class="A1101">登录位置</td>
  </tr>
<?php 
$mySql=mysql_query("SELECT O.uFrom,O.IP,O.LastTime,U.uName,U.Number,U.lDate,S.Name AS Name,B.Name AS BName
FROM $DataIn.online O 
LEFT JOIN $DataIn.UserTable U ON U.Id=O.uId
LEFT JOIN $DataPublic.staffmain S ON S.Number=U.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
WHERE 1 AND U.uType='1' AND S.Estate=1 GROUP BY S.Number order by S.BranchId,S.JobId,S.Number,U.lDate",$link_id);
if($myRow = mysql_fetch_array($mySql)){
	$i=1;
	do{
		$LastTime=$myRow["LastTime"];
		$uName=$myRow["uName"];
		$Number=$myRow["Number"];
		$lDate=$myRow["lDate"];
		$Name=$myRow["Name"];
		$Branch=$myRow["BName"];
		$uFrom=$myRow["uFrom"];
		$Add="内部登录";
		switch($uFrom){
			case 1:$IP="内网:".$myRow["IP"];break;
			case 2:$IP="<div class='yellowB'>内网域名:".$myRow["IP"]."</div>";break;
			case 3:$IP="<div class='redB'>外网:".$myRow["IP"]."</div>";
			$Add="<div class='redB'>".convertip($myRow["IP"])."</div>";
			break;
			}
		
		echo"<tr align='center'><td align='center' class='A0111' height='25'>$i</td>
		<td align='center' class='A0101'>$Name</td><td align='center' class='A0101'>$Branch</td><td align='center' class='A0101'>$lDate</td>
		<td align='center' class='A0101'>$IP</td>
		<td align='center' class='A0101'>$Add</td>
		</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($mySql));
	}
else{
	noRowInfo($tableWidth);
  	}
?></table>
</p>
2、外部人员在线记录
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr align="center" bgcolor="#CCCCCC">
    <td width="60" height="25" class="A1111">序号</td>
    <td width="60" class="A1101">姓名</td>
	<td width="100" class="A1101">部门</td>
    <td width="120" class="A1101">登录时间</td>
	 <td width="160" class="A1101">登录IP</td>
     <td width="200" class="A1101">登录位置</td>
  </tr>
<?php 
$mySql=mysql_query("SELECT O.uFrom,O.IP,O.LastTime,U.uName,U.Number,U.lDate,S.Name AS Name
FROM $DataIn.online O 
LEFT JOIN $DataIn.UserTable U ON U.Id=O.uId
LEFT JOIN $DataIn.ot_staff S ON S.Number=U.Number
WHERE 1 AND U.uType='4' AND S.Estate=1 GROUP BY S.Number order by S.Number,U.lDate",$link_id);
if($myRow = mysql_fetch_array($mySql)){
	$i=1;
	do{
		$LastTime=$myRow["LastTime"];
		$uName=$myRow["uName"];
		$Number=$myRow["Number"];
		$lDate=$myRow["lDate"];
		$Name=$myRow["Name"];
		$Branch="&nbsp;";
		$uFrom=$myRow["uFrom"];
		$Add="内部登录";
		switch($uFrom){
			case 1:$IP="内网:".$myRow["IP"];break;
			case 2:$IP="<div class='yellowB'>内网域名:".$myRow["IP"]."</div>";break;
			case 3:$IP="<div class='redB'>外网:".$myRow["IP"]."</div>";
			$Add="<div class='redB'>".convertip($myRow["IP"])."</div>";
			break;
			}
		
		echo"<tr align='center'><td align='center' class='A0111' height='25'>$i</td>
		<td align='center' class='A0101'>$Name</td><td align='center' class='A0101'>$Branch</td><td align='center' class='A0101'>$lDate</td><td align='center' class='A0101'>$IP</td><td align='center' class='A0101'>$Add</td></tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($mySql));
	}
else{
	noRowInfo($tableWidth,"");
  	}
?></table>
<p>
3、供应商在线记录
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr align="center" bgcolor="#CCCCCC">
    <td width="60" height="25" class="A1111">序号</td>
    <td width="60" class="A1101">姓名</td>
	<td width="100" class="A1101">供应商</td>
    <td width="120" class="A1101">登录时间</td>
	 <td width="160" class="A1101">登录IP</td>
     <td width="200" class="A1101">登录位置</td>
  </tr>
<?php 
$mySql=mysql_query("SELECT O.uFrom,O.IP,O.LastTime,U.uName,U.Number,U.lDate,P.Forshort AS Forshort,S.Name
FROM $DataIn.online O 
LEFT JOIN $DataIn.UserTable U ON U.Id=O.uId
LEFT JOIN $DataIn.linkmandata S ON S.Id=U.Number
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
WHERE 1 AND U.uType='3' AND S.Estate=1 GROUP BY S.Id ORDER BY S.Id,U.lDate",$link_id);
if($myRow = mysql_fetch_array($mySql)){
	$i=1;
	do{
		$LastTime=$myRow["LastTime"];
		$uName=$myRow["uName"];
		$Number=$myRow["Number"];
		$lDate=$myRow["lDate"];
		$Name=$myRow["Name"];
		$Forshort=$myRow["Forshort"];
		$uFrom=$myRow["uFrom"];
		$Add="内部登录";
		switch($uFrom){
			case 1:$IP="内网:".$myRow["IP"];break;
			case 2:$IP="<div class='yellowB'>内网域名:".$myRow["IP"]."</div>";break;
			case 3:$IP="<div class='redB'>外网:".$myRow["IP"]."</div>";
			$Add="<div class='redB'>".convertip($myRow["IP"])."</div>";
			break;
			}
		
		echo"<tr align='center'><td align='center' class='A0111' height='25'>$i</td>
		<td align='center' class='A0101'>$Name</td><td align='center' class='A0101'>$Forshort</td><td align='center' class='A0101'>$lDate</td><td align='center' class='A0101'>$IP</td><td align='center' class='A0101'>$Add</td></tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($mySql));
	}
else{
	noRowInfo($tableWidth);
  	}
?></table>
</p>
<p>
4、客户在线记录
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
  <tr align="center" bgcolor="#CCCCCC">
    <td width="60" height="25" class="A1111">序号</td>
    <td width="60" class="A1101">姓名</td>
	<td width="100" class="A1101">客户</td>
    <td width="120" class="A1101">登录时间</td>
	 <td width="160" class="A1101">登录IP</td>
     <td width="200" class="A1101">登录位置</td>
  </tr>
<?php 
$mySql=mysql_query("SELECT O.uFrom,O.IP,O.LastTime,U.uName,U.Number,U.lDate,P.Forshort AS Forshort,S.Name
FROM $DataIn.online O 
LEFT JOIN $DataIn.UserTable U ON U.Id=O.uId
LEFT JOIN $DataIn.linkmandata S ON S.Id=U.Number
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
WHERE 1 AND U.uType='2' AND S.Estate=1 GROUP BY S.Id ORDER BY S.Id,U.lDate",$link_id);
if($myRow = mysql_fetch_array($mySql)){
	$i=1;
	do{
		$LastTime=$myRow["LastTime"];
		$uName=$myRow["uName"];
		$Number=$myRow["Number"];
		$lDate=$myRow["lDate"];
		$Name=$myRow["Name"];
		$Forshort=$myRow["Forshort"];
		$uFrom=$myRow["uFrom"];
		$Add="内部登录";
		switch($uFrom){
			case 1:$IP="内网:".$myRow["IP"];break;
			case 2:$IP="<div class='yellowB'>内网域名:".$myRow["IP"]."</div>";break;
			case 3:$IP="<div class='redB'>外网:".$myRow["IP"]."</div>";
			$Add="<div class='redB'>".convertip($myRow["IP"])."</div>";
			break;
			}
		
		echo"<tr align='center'><td align='center' class='A0111' height='25'>$i</td>
		<td align='center' class='A0101'>$Name</td><td align='center' class='A0101'>$Forshort</td><td align='center' class='A0101'>$lDate</td><td align='center' class='A0101'>$IP</td><td align='center' class='A0101'>$Add</td></tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($mySql));
	}
else{
	noRowInfo($tableWidth);
  	}
?></table>
</p>