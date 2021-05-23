<?php   
/*电信---yang 20120801
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);
$BuyerId=$TempArray[0];	//采购
$predivNum=$TempArray[1];	//a

$DateTime=date("Y-m-d");   //从现在开始，到上一年的。采购的，其它就不显示了  add mby zx 2010-12-30
$StartDate=date("Y-m-d",strtotime("$DateTime-1 years"));
$SearchRows=" AND S.DeliveryDate>='$StartDate' ";
$SearchCk=" AND M.Date>='$StartDate' ";
$i=1;
$DivNum=$predivNum."b".$i;
$TempId="$BuyerId|$DivNum";
//3天以上未填交期的记录数
//$check
$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgwtjq1_b\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#CCCCCC'><td>&nbsp;$showPurchaseorder <span class='redB'>3天以上未填交期</div></td></tr></table>";
echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
		<tr bgcolor='#B7B7B7'>
			<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
			</td>
		</tr>
	</table>";
$i++;
$DivNum=$predivNum."b".$i;
$TempId="$BuyerId|$DivNum";
$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgwtjq1_b\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ' width='13' height='13' style='CURSOR: pointer'>";
?>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#CCCCCC" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr id='A'>
		<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;3天以内未填交期</td>
	</tr>
</table>
<?php   
echo"<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
		<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'>
				<br>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				<br>
			</td>
		</tr>
	</table>";
?>
