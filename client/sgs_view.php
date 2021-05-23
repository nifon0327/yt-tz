<?php   
//电信-zxq 2012-08-01
/*
$DataIn.sgsdata
$DataIn.sgsfile
二合一已更新
*/
include "../model/modelhead.php";
//SGS资料
$Sgs_Result = mysql_query("SELECT * FROM $DataIn.sgsdata WHERE SgsId=$SgsId",$link_id);
if ($Sgs_Row = mysql_fetch_array($Sgs_Result)) {
	$SgsNo=$Sgs_Row["SgsNo"]."/".$Sgs_Row["Type"];
	$ItemE=$Sgs_Row["ItemE"];
	}
//SGS文档
$SgsFile_Result = mysql_query("SELECT * FROM $DataIn.sgsfile WHERE SgsId=$SgsId",$link_id);
?>
<style type="text/css">
<!--
body {background-color: #E3E3E3;}
-->
</style></head>
<body>
<form name="form1" method="post" action="">

<table width="772" height="1389" border="1" align="center" bgcolor="#FFFFFF">
  <tr>
    <td width="132" height="20">Test Report NO: </td>
    <td width="714"><?php    echo $SgsNo?></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
    <td><?php    echo $ItemE?></td>
  </tr>
  <tr>
    <td height="20" colspan="2">Page:
	<?php   
	$i=1;
	if ($SgsFile_Row = mysql_fetch_array($SgsFile_Result)) {
		do{
			//$SgsFile_Row
			echo "<a href='sgs_view1.php?Filename=$SgsFile_Row[FileName]' target='sgsphoto'>[".$i."]</a>&nbsp;&nbsp;";
			if($i==1){
				$Firstname=$SgsFile_Row["FileName"];
				}
			$i++;
			}while ($SgsFile_Row = mysql_fetch_array($SgsFile_Result));		
		}
	?>
	</td>
  </tr>
  <tr valign="top">
    <td height="1317" colspan="2">
	<iframe name="sgsphoto" frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='850' height='1200'  src='sgs_view1.php?Filename=<?php    echo $Firstname?>'></iframe>
  </tr>
</table>
</form>
</body>
</html>