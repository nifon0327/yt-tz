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
	<tr><td class="A1111" height="25">&nbsp;&nbsp;1.Slim case(<a href="Charttopng_1.php" target="_blank">View</a>)</td></tr>
	<tr><td class="A0111" height="25">&nbsp;&nbsp;2.COVFACO case(<a href="charttopng_2.php" target="_blank">View</a>)</td></tr>
	<tr><td class="A0111" height="25">&nbsp;&nbsp;3.Crystal casev(<a href="Charttopng_3.php" target="_blank">View</a>)</td></tr>
	<tr><td class="A0111" height="25">&nbsp;&nbsp;4.Screenprotector(<a href="Charttopng_4.php" target="_blank">View</a>)</td></tr>
	<tr><td class="A0111" height="25">&nbsp;&nbsp;5.Minigel case/Mirback case(<a href="Charttopng_5.php" target="_blank">View</a>)</td></tr>
</table>
