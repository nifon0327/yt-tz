<?php   
//代码 branchdata by zx 2012-08-13
/*
MC、DP共享代码电信---yang 20120801
*/

include "../model/modelhead.php";
ChangeWtitle("$SubCompany 上班人数统计");//需处理
$tableWidth=650;
$i=1;
$Today=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
?>
<body>
<table width="<?php    echo $tableWidth?>" border="0" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24"><?php    echo $Today?>&nbsp;人数统计</td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="30" width="80" align="center">序号</td>
	<td class="A1101" width="100" align="center">部门</td>
    <td class="A1101" width="180" align="center">部门员工数</td>
    <td class="A1101" width="120" align="center">上班人数</td>
    <td class="A1101" width="120" align="center">缺勤人数</td>
  </tr>
<?php   
$i=1;
$BranchTotal=0;//各部门数
$KqTotal=0;//考勤人数
$OfficeTotal=0;
$QjTotal=0;//缺勤人数
$WorkTotal=0;
//$ShipResult = mysql_query("SELECT Id, Name FROM $DataPublic.branchdata  ",$link_id);
$ShipResult = mysql_query("SELECT Id, Name FROM $DataPublic.branchdata  
						   WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id
						  ",$link_id);

if ($ShipRow = mysql_fetch_array($ShipResult)) {
	do{
	    $BranchId=$ShipRow["Id"];
		$BranchName=$ShipRow["Name"];
		$StaffSql=mysql_query("SELECT COUNT(*) AS  BranchNumber FROM $DataPublic.staffmain M 
		           WHERE M.Estate=1 AND M.BranchId=$BranchId AND M.cSign=$Login_cSign",$link_id);
		//echo $StaffSql;
		$BranchNumber=mysql_result($StaffSql,0,"BranchNumber");
		$BranchTotal+=$BranchNumber;
		$Kq_Sql=mysql_query("SELECT COUNT(*) AS KqNumber FROM $DataPublic.staffmain M 
		           WHERE M.Estate=1 AND M.KqSign=1 AND M.BranchId=$BranchId AND M.cSign=$Login_cSign",$link_id);
		$KqNumber=mysql_result($Kq_Sql,0,"KqNumber");
		$KqTotal+=$KqNumber;
		$OfficeNumber=$BranchNumber-$KqNumber;
		$OfficeTotal+=$OfficeNumber;
		//上班人数
		$Today=date("Y-m-d");
		$WorkNumber=0;
		$WorkSql=mysql_query("SELECT COUNT(*) AS WorkNumber
				FROM $DataPublic.staffmain M 
				WHERE 1 AND M.Estate=1 AND M.KqSign=3 AND M.BranchId=$BranchId  AND M.cSign=$Login_cSign AND M.Number NOT IN 
				(SELECT Number FROM $DataPublic.kqqjsheet K WHERE K.EndDate>='$DateTime' 
				AND  K.StartDate<='$DateTime' AND K.Estate=0)
				UNION ALL
				SELECT COUNT(*) AS WorkNumber
				FROM(SELECT C.Number FROM $DataIn.checkinout C
				LEFT JOIN  $DataPublic.staffmain M ON M.Number=C.Number
                WHERE 1 AND M.Estate=1 AND DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$Today' 
				AND M.BranchId=$BranchId GROUP BY C.Number
				) B",$link_id);
		if($WorkRow=mysql_fetch_array($WorkSql)){
		   do{
			  $WorkNumber1=$WorkRow["WorkNumber"];
			  $WorkNumber+=$WorkNumber1;
			 }while($WorkRow=mysql_fetch_array($WorkSql));
		  }
		$QjNumber=$BranchNumber-$WorkNumber;
		$QjTotal+=$QjNumber;
		$WorkTotal+=$WorkNumber;
		$Branch="<a href=\"desk_worknumber_ajax.php?BranchId=$BranchId&Type=1\" target=\"_blank\">$BranchNumber</a>";
		$Work="<a href=\"desk_worknumber_ajax.php?BranchId=$BranchId&Type=2\" target=\"_blank\">$WorkNumber</a>";
		$Qj="<a href=\"desk_worknumber_ajax.php?BranchId=$BranchId&Type=3\" target=\"_blank\">$QjNumber</a>";
		echo"<tr><td class='A0111' align='center' height='25'>$i</td>";
		echo"<td class='A0101' align='center'>$BranchName</td>";
		echo"<td class='A0101' align='right'><span style='color:#cccccc'>(固定薪$OfficeNumber,非固定薪$KqNumber)</span>$Branch&nbsp;</td>";//员工数
		echo"<td class='A0101' align='right'>$Work&nbsp;</td>";
		echo"<td class='A0101' align='right'>$Qj&nbsp;</td>";
		echo"<tr>";
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
//===========试用期
/*$TempSql=mysql_query("SELECT COUNT(*) AS TempTotal FROM $DataIn.stafftempmain WHERE Estate=1",$link_id);
$TempTotal=mysql_result($TempSql,0,"TempTotal");

$TempWorkSql=mysql_query("SELECT COUNT(*) TempWork FROM (
		              SELECT Number FROM $DataIn.checkiotemp 
                      WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')='$Today' GROUP BY Number
					  )A",$link_id);
$TempWork=mysql_result($TempWorkSql,0,"TempWork");
$TempQj=$TempTotal-$TempWork;

$BranchTotal+=$TempTotal;
$WorkTotal+=$TempWork;
$QjTotal+=$TempQj;

$TempTotal="<a href=\"desk_worknumber_ajax.php?TempType=1\" target=\"_blank\">$TempTotal</a>";
$TempWork="<a href=\"desk_worknumber_ajax.php?TempType=2\" target=\"_blank\">$TempWork</a>";
$TempQj="<a href=\"desk_worknumber_ajax.php?TempType=3\" target=\"_blank\">$TempQj</a>";

echo"<tr><td class='A0111' align='center' height='25'>$i</td>";
		echo"<td class='A0101' align='center'>试用期</td>";
		echo"<td class='A0101' align='right'>$TempTotal&nbsp;</td>";//员工数
		echo"<td class='A0101' align='right'>$TempWork&nbsp;</td>";
		echo"<td class='A0101' align='right'>$TempQj&nbsp;</td>";
		echo"<tr>";
*/
//============总计
$BranchTotal="<a href=\"desk_worknumber_ajax.php?BranchId=0&TotalType=1\" target=\"_blank\">$BranchTotal</a>";
$WorkTotal="<a href=\"desk_worknumber_ajax.php?BranchId=0&TotalType=2\" target=\"_blank\">$WorkTotal</a>";
$QjTotal="<a href=\"desk_worknumber_ajax.php?BranchId=0&TotalType=3\" target=\"_blank\">$QjTotal</a>";

echo"<tr class=''><td class='A0110' align='center' height='25'>&nbsp;</td>";
echo"<td class='A0101'>总计</td>";
echo"<td class='A0101' align='right'><span style='color:#ffffff'>(固定薪$OfficeTotal,非固定薪$KqTotal)</span>$BranchTotal&nbsp;</td>";
echo"<td class='A0101' align='right'>$WorkTotal&nbsp;</td>";
echo"<td class='A0101' align='right'>$QjTotal&nbsp;</td>";
echo"<tr>";
?>

</table>
</html>