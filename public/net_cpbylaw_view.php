<?php 
//电信-ZX  2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

$tableWidth=800;
$tableMenuS=600;
?>
<html>
<head>
<?php 
include "../model/characterset.php";
?>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php 
//CSS模板
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
?>
<script src="../model/pagefun.js" type=text/javascript></script>
<title>电脑管理规定</title>
</head>
<body >
<form name="form1" method="post" action="">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td <?php  echo $td_bgcolor?> class="A0100" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class="A1100" <?php  echo $Fun_bgcolor?>>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
				<nobr>电脑管理规定</nobr>
				</td>
			</tr>
		</table>
   </td>
  </tr>
  </table>
  
<table width="<?php  echo $tableWidth?>" border='0' cellspacing='0' bgcolor='#FFFFFF'>
<?php 
$Result = mysql_query("SELECT * FROM $DataPublic.net_cpbylaw order by Type,Id DESC",$link_id);
if ($myrow = mysql_fetch_array($Result)){
	$i=1;
	do {
		switch($myrow["Type"]){
			case"1":
				$TypeSTR="使用相关";
			break;
			case"2":$TypeSTR="下载相关";
			break;
			case"3":$TypeSTR="维护相关";
			break;
			case"4":$TypeSTR="保密相关";
			break;
			case"5":$TypeSTR="防毒相关";
			break;
			}		
		echo"<tr><td width='10' class='A0010'>&nbsp;</td><td><span class='redB'>规定$i:</span>&nbsp;&nbsp;$TypeSTR<br>&nbsp;&nbsp;$myrow[bylaw](加入日期:$myrow[Date])<p></td><td width='10' class='A0001'>&nbsp;</td></tr>";
		$i++;
		}while ($myrow = mysql_fetch_array($Result));
	}
?>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr><td height="5" colspan="6" class="A0011">&nbsp;</td></tr>
  <tr>
   <td <?php  echo $td_bgcolor?> class="A1000" id="menuB1" width="<?php  echo $tableMenuS?>">&nbsp;</td>
   <td width="150" id="menuT2" align="center" class="A1100" <?php  echo $Fun_bgcolor?>>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>电脑管理规定</nobr>				
				</td>
			</tr>
	 </table>
   </td>
   <td class="A0100">&nbsp;</td>
   </tr>
</table>
</form>
</body>
</html>
