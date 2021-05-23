<?php 
//$DataPublic.zw2_hzdoctype 二合一已更新
//电信-joseph
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 行政文件查询");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=745;
$subTableWidth=725;
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center"><td height="24" colspan="5">行政文件列表</td></tr>
	<tr><td height="24">&nbsp;</td><td height="24" colspan="4" align="right">&nbsp;</td></tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="650" class="A1111" height="25" align="center">行政文件项目</td>
	<td width="95" class="A1101" align="center">附件</td>
  </tr>
</table>
<?php 
//读取未结付货款
$ShipResult = mysql_query("SELECT Id,Name FROM $DataPublic.zw2_hzdoctype WHERE Estate!=0 ORDER BY Id",$link_id);
$Total=0;$Total_1=0;$Total_2=0;$Total_3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Id=$ShipRow["Id"];
		$Name=$ShipRow["Name"];
		//传递分类
		$DivNum="a";
		$TempId="$Id|$DivNum";			
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"zw_ad_data_a\",\"public\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
		$RowInfo=$i<10?"&nbsp;".$i:$i;
?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="650" height="25">&nbsp;<?php  echo $showPurchaseorder?>&nbsp;<?php  echo $RowInfo."-".$Name?></td>
			<td class="A0101" width="95" align="center">&nbsp;</td>
		</tr>
	</table>
<?php 
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>

</form>
</body>
</html>
