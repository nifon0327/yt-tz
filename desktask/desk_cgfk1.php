<?php   
/*电信-yang 20120801
已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 采购货款统计明细");//需处理
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$tableWidth=1070;
$subTableWidth=1050;
$i=1;
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="5">采购货款统计明细</td>
    </tr>
	<tr>
	  <td height="24" colspan="5"><input type="radio" name="Action" value="0" id="Action0" onClick="javascript:document.form1.action='desk_cgfk.php';document.form1.submit()"><label for="Action0">以供应商为索引进行统计(采购使用)</label>
	    <input name="Action" type="radio" value="1" id="Action1" checked><label for="Action1">以货款状态为索引进行统计(财务使用)</label>
		 </td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td class="A1111" width="728" height="25">&nbsp;&nbsp;&nbsp;&nbsp;货款状态</td>
    <td class="A1101" width="342" align="center">金额</td>
  </tr>
</table>
<?php   

for($i=1;$i<5;$i++){
	$AmountTemp=0;
	switch($i){
		case 1:
			$TypeName="未请款货款 (以送货日期为索引)";
			$NextPage="desk_cgfk1_a1";
			//检查是否有内容
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*G.Price*C.Rate) AS Amount
				FROM $DataIn.ck1_rksheet S
				LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
				WHERE 1 AND K.StockId IS NULL
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);
			break;
		case 2:
			$TypeName="请款中货款 (以请款月份为索引)";
			$NextPage="desk_cgfk1_a2";
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty*K.Price*C.Rate) AS Amount
				FROM $DataIn.cw1_fkoutsheet K
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=K.CompanyId
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
				WHERE 1 AND K.Estate=2
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);			
			break;
		case 3:
			$TypeName="待结付货款 (以请款月份为索引)";
			$NextPage="desk_cgfk1_a3";
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty*K.Price*C.Rate) AS Amount
				FROM $DataIn.cw1_fkoutsheet K
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=K.CompanyId
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
				WHERE 1 AND K.Estate=3
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);			
			break;
		case 4:
			$TypeName="已结付货款 (以结付月份为索引)";
			$NextPage="desk_cgfk1_a4";
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty*K.Price*C.Rate) AS Amount
				FROM $DataIn.cw1_fkoutsheet K
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=K.CompanyId
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
				WHERE 1  AND K.Estate=0
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);			
			break;
		}
		$DivNum="a".$i;
		$TempId="$i|$DivNum";
		if($AmountTemp>0){
			$AmountTemp=number_format($AmountTemp,2);
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"$NextPage\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' border='0' cellspacing='0'  bgcolor='#FFFFFF' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;height:25'><tr><td class='A0111'>&nbsp;$showPurchaseorder $TypeName</td><td width='100' align='right' class='A0100'>￥$AmountTemp</td><td width='85' align='right' class='A0100'>&nbsp;</td><td width='82' align='right' class='A0100'>&nbsp;</td><td width='75' align='right' class='A0101'>&nbsp;</td></tr></table>";
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