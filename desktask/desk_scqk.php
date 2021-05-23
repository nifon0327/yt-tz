<?php   
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 未出订单生产情况统计");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1050;
$subTableWidth=1030;
$i=1;
?>
<body>
<form name="form1" method="post" action="">
<div id='Jp' style='position:absolute; left:341px; top:229px; width:300px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">未出订单生产情况统计</td>
    </tr>
	<tr>
		<td height="24" colspan="5">&nbsp;
	    </td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" height="25">&nbsp;&nbsp;&nbsp;&nbsp;情况分类</td>
  </tr>
</table>

<?php   
//分类订单数
$checkNumSql=mysql_query("SELECT count(*) AS Num,scFrom FROM $DataIn.yw1_ordersheet WHERE Estate>0 GROUP BY scFrom ORDER BY scFrom",$link_id);
if($checkNumRow=mysql_fetch_array($checkNumSql)){
	do{
		$Num=$checkNumRow["Num"];
		$scFrom=$checkNumRow["scFrom"];
		$TempNum="Nums".strval($scFrom); 
		$$TempNum=$Num;	
		}while($checkNumRow=mysql_fetch_array($checkNumSql));
	}
?>

<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable1" bgcolor="#FFFFFF" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
	<tr id="A">
		<td class="A0111" height="25"><div class="greenB">&nbsp;<img onClick="SandH('a1','1',this,'0','desk_scqk_a','<?php     echo get_currentDir(1); ?>');" id="ThisImg_a1" name="ThisImg_a1" src="../images/showtable.gif" alt="显示或隐藏下级资料." width="13" height="13" style="CURSOR: pointer">&nbsp;已生产的未出订单(<?php    echo $Nums0?>)<div></td>
	</tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="HideTable_a11" style="display:none">
	<tr bgcolor="#B7B7B7">
		<td class="A0111" height="30"><br><div id="HideDiv_a11" width="<?php    echo $subTableWidth?>" align="right">&nbsp;</div><br></td>
	</tr>
</table>

<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable2" bgcolor="#FFFFFF" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
	<tr id="A">
		<td class="A0111" height="25"><div class="yellowB">&nbsp;<img onClick="SandH('a2','2',this,'2','desk_scqk_a');" id="ThisImg_a2" name="ThisImg_a2" src="../images/showtable.gif" alt="显示或隐藏下级资料." width="13" height="13" style="CURSOR: pointer">&nbsp;生产中的未出订单(<?php    echo $Nums2?>)</div></td>
	</tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="HideTable_a22" style="display:none">
	<tr bgcolor="#B7B7B7">
		<td class="A0111" height="30"><br><div id="HideDiv_a22" width="<?php    echo $subTableWidth?>" align="right">&nbsp;</div><br></td>
	</tr>
</table>

<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable31" bgcolor="#FFFFFF" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
	<tr id="A">
		<td class="A0111" height="25"><div class="redB">&nbsp;<img onClick="SandH('a3','3',this,'1','desk_scqk_a');" id="ThisImg_a3" name="ThisImg_a3" src="../images/showtable.gif" alt="显示或隐藏下级资料." width="13" height="13" style="CURSOR: pointer">&nbsp;未生产的未出订单(<?php    echo $Nums1?>)</div></td>
	</tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="HideTable_a33" style="display:none">
	<tr bgcolor="#B7B7B7">
		<td class="A0111" height="30"><br><div id="HideDiv_a33" width="<?php    echo $subTableWidth?>" align="right">&nbsp;</div><br></td>
	</tr>
</table>

</form>
</body>
</html>