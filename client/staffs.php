<?php   
//电信-zxq 2012-08-01
/*
$DataIn.sys_clientstaffs
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//需处理参数
$tableMenuS=500;
$tableWidth=800;
$Parameter="myCompanyId,$myCompanyId";
include "../admin/subprogram/mycompany_info.php";
ChangeWtitle("$SubCompany Staffs of $E_Forshort");//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="center">
	<tr><td class="A1111">
<?php   
$CheckStaffSql=mysql_query("SELECT S.Remark,M.Nickname,M.Mail FROM $DataIn.sys_clientstaffs S ,$DataPublic.staffmain M WHERE S.Number=M.Number AND M.Estate=1 AND S.Estate=1 AND S.CompanyId='$myCompanyId'",$link_id);
if($CheckStaffRow=mysql_fetch_array($CheckStaffSql)){
	echo"<table width='750' height='110' border='0' align='center'>";
	$i=1;
	do{
    	$Remark=$CheckStaffRow["Remark"];
		$Nickname=$CheckStaffRow["Nickname"];
		$Mail=$CheckStaffRow["Mail"];
		echo"<tr><td width='40' height='33' align='center'>$i)</td>";
    	echo"<td width='130'><a href='mailto:$Mail'>$Nickname</a></td>";
    	echo"<td width='600'>$Remark</td></tr>";
		$i++;
		}while($CheckStaffRow=mysql_fetch_array($CheckStaffSql));
	echo"</table>";
  }
 else{
 	echo "&nbsp;";
	}
?>
</table></td></tr></table>
