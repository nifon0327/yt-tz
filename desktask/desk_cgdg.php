<?php   
/*电信-yang 20120801
已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 待购需求单分类统计");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1070;
$subTableWidth=1050;
$i=1;
$SearchRows=" AND T.mainType<2";//需采购的配件需求单
$checkRow=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums
				FROM $DataIn.cg1_stocksheet S
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
				WHERE 1 AND S.Mid=0 AND (S.FactualQty>0 OR S.AddQty>0) $SearchRows
				",$link_id));
			$NumsTemp =$checkRow["Nums"];
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">待购需求单分类统计</td>
    </tr>
	<tr>
	  <td height="24" colspan="5"><input type="radio" name="Action" value="0" id="Action0" checked><label for="Action0">以需求单状态为索引进行统计</label>
	  <?php   
	    //<input name="Action" type="radio" value="1" id="Action1" onClick="javascript:document.form1.action='desk_cgdg.php';document.form1.submit()"><label for="Action1">以采购为索引进行统计(财务使用)</label>
		?>
		</td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" width="828" height="25">&nbsp;&nbsp;&nbsp;&nbsp;需求单状态</td>
    <td class="A1101" width="242" align="center">待处理记录数(总数:<?php    echo $NumsTemp?>)</td>
  </tr>
</table>
<?php   

for($i=1;$i<4;$i++){
	$NumsTemp=0;
	switch($i){
		case 1:
			$TypeName="未确定的需求单";
			$NextPage="desk_cgdg_a1";
			//检查是否有内容
			$checkRow=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums
				FROM $DataIn.cg1_stocksheet S
				LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
				WHERE 1 AND S.Mid=0 AND E.Type='2' AND (S.FactualQty>0 OR S.AddQty>0) $SearchRows
				",$link_id));
			$NumsTemp =$checkRow["Nums"];
			break;
		case 2:
			$TypeName="审核中的需求单";
			$NextPage="desk_cgdg_a2";
			$checkRow=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums
				FROM $DataIn.cg1_stocksheet S
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
				WHERE 1 AND S.Mid=0 AND S.Estate='1' AND (S.FactualQty>0 OR S.AddQty>0) $SearchRows
				",$link_id));
			$NumsTemp =$checkRow["Nums"];
			break;
		case 3:
			$TypeName="可下单的需求单";
			$NextPage="desk_cgdg_a3";
			$checkRow=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums
				FROM $DataIn.cg1_stocksheet S
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
				LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
				WHERE 1 AND S.Mid=0 AND S.Estate='0' AND E.Id IS NULL AND (S.FactualQty>0 OR S.AddQty>0) $SearchRows
				",$link_id));
			$NumsTemp =$checkRow["Nums"];
			break;
		}
		$DivNum="a".$i;
		$TempId="$DivNum";
		if($NumsTemp>0){
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"$NextPage\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' border='0' cellspacing='0'  bgcolor='#FFFFFF' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;height:25'>
			<tr>
			<td class='A0111'>&nbsp;$showPurchaseorder $TypeName</td>
			<td width='85' align='right' class='A0100'>$NumsTemp</td>
			<td width='82' align='right' class='A0100'>&nbsp;</td>
			<td width='75' align='right' class='A0101'>&nbsp;</td></tr></table>";
			echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
					</td>
				</tr></table>";
		}
	}
?>
</form>
</body>
</html>