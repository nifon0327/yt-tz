<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 配件出入库金额统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=1000;
$subTableWidth=1000;
$ColsNumber =4;
?>
<body>
<form name="form1" method="post" action="">
<input name="MergeRows" type="hidden" id="MergeRows">
<input name="sumCols" type="hidden" id="sumCols">
<table width="1015" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" >配件出入库金额统计</td>
    </tr>
	<tr>
   	 <td height="24"  align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="50" class="A1111" height="25" align="center">序 号</td>
    <td width="500" class="A1101" align="center">项目分类</td>
    <td width="150" class="A1101" align="center">含税金额</td>
    <td width="150" class="A1101" align="center">成本金额</td>
	<td width="150" class="A1101" align="center">差额</td>
  </tr>
</table>
<?php   
//读取未结付货款
$i = 1;
$numArray = array(1,2,3,4,5,6);
$titleArray = array("采购单入库","备品转入入库","补仓入库","工单领料出库","报废领料出库","退货领料出库");
for($k=0;$k<count($numArray);$k++){
		$TypeId=$numArray[$k];
		$titleName=$titleArray[$k];
		$Amount1 = $Amount2 = $Amount3 =0;
		switch($TypeId){
			
			case 1://采购单入库
			     $CheckRow = mysql_fetch_array(mysql_query("SELECT SUM(G.Price*C.Rate*K.Qty) AS Amount1,
			     SUM(G.Price/(1+T.Value)*C.Rate*K.Qty) AS Amount2
                 FROM $DataIn.ck1_rksheet K 
			     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = K.StockId 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE K.Type =1 AND G.Price>0 ",$link_id));
			     $Amount1 = sprintf("%.3f", $CheckRow["Amount1"]);
			     $Amount2 = sprintf("%.3f", $CheckRow["Amount2"]);
			     $Amount3 = $Amount1 - $Amount2;
			
			break;
			case 2: //备品转入
			     $CheckRow = mysql_fetch_array(mysql_query("SELECT SUM(B.Price*C.Rate*B.Qty) AS Amount1,
			     SUM(B.Price/(1+T.Value)*C.Rate*B.Qty) AS Amount2
                 FROM $DataIn.ck7_bprk B  
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = B.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE B.Estate =0 AND B.CompanyId>0 AND B.Price>0",$link_id));
			     $Amount1 = sprintf("%.3f", $CheckRow["Amount1"]);
			     $Amount2 = sprintf("%.3f", $CheckRow["Amount2"]);
			     $Amount3 = $Amount1 - $Amount2;
			
			break;
			case 3://补仓入库
			     $CheckRow = mysql_fetch_array(mysql_query("SELECT SUM(K.Price*C.Rate*K.Qty) AS Amount1,
			     SUM(K.Price/(1+T.Value)*C.Rate*K.Qty) AS Amount2
                 FROM $DataIn.ck1_rksheet K 
                 LEFT JOIN $DataIn.ck3_bcsheet B ON B.RkId = K.Id 
                 LEFT JOIN $DataIn.ck3_bcmain BM ON BM.Id = B.Mid 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = BM.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE K.Type =3 AND K.Price>0",$link_id));
			     $Amount1 = sprintf("%.3f", $CheckRow["Amount1"]);
			     $Amount2 = sprintf("%.3f", $CheckRow["Amount2"]);
			     $Amount3 = $Amount1 - $Amount2;
			break;
			case 4://工单领料出库
			     $CheckRow = mysql_fetch_array(mysql_query("SELECT SUM(G.Price*C.Rate*L.Qty) AS Amount1,
			     SUM(G.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2
                 FROM $DataIn.ck5_llsheet L  
			     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = L.StockId 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Type =1 AND G.Price>0",$link_id));
			     $Amount1 = sprintf("%.3f", $CheckRow["Amount1"]);
			     $Amount2 = sprintf("%.3f", $CheckRow["Amount2"]);
			     $Amount3 = $Amount1 - $Amount2;
			break;
			case 5:
			     $CheckRow = mysql_fetch_array(mysql_query("SELECT SUM(D.Price*C.Rate*L.Qty) AS Amount1,
			     SUM(D.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2
                 FROM $DataIn.ck8_bfsheet L   
                 LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = L.StuffId
                 LEFT JOIN $DataIn.bps  B ON B.StuffId = D.StuffId
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = B.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Estate =0 AND D.Price>0",$link_id));
			     $Amount1 = sprintf("%.3f", $CheckRow["Amount1"]);
			     $Amount2 = sprintf("%.3f", $CheckRow["Amount2"]);
			     $Amount3 = $Amount1 - $Amount2;
			break;
			case 6:
                 $CheckRow = mysql_fetch_array(mysql_query("SELECT SUM(L.Price*C.Rate*L.Qty) AS Amount1,
			     SUM(L.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2
                 FROM $DataIn.ck5_llsheet L 
                 LEFT JOIN $DataIn.ck2_thsheet B ON B.Id = L.FromId 
                 LEFT JOIN $DataIn.ck2_thmain BM ON BM.Id = B.Mid 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = BM.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Type =3 AND L.Price>0 ",$link_id));
			     $Amount1 = sprintf("%.3f", $CheckRow["Amount1"]);
			     $Amount2 = sprintf("%.3f", $CheckRow["Amount2"]);
			     $Amount3 = $Amount1 - $Amount2;
			break;	
		}
		
		
		$DivNum="a";
		$TempId="$TypeId";	
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"stuff_inout_Amount_a\",\"desktask\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$HideTableHTML="
			<table width='1016' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<?php   
	echo"<tr id='A' bgcolor='$theDefaultColor'
	onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";?>
			<td width="50" class="A0111" height="25" align="center"><?php    echo $i?></td>
			<td class="A0101" width="500">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;<?php    echo $titleName?></td>
			<td class="A0101" width="150" align="right"><?php echo number_format($Amount1)?>&nbsp;</td>
            <td class="A0101" width="150" align="right"><?php echo number_format($Amount2)?>&nbsp;</td>
			<td class="A0101" width="150" align="right"><?php echo number_format($Amount3)?>&nbsp;</td>
		</tr>
	</table>
<?php   
		echo $HideTableHTML;
		$i++;
	}
?>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr class=''>
    <td class="A0111" height="25" width="553">合 计: </td>
    <td class="A0101" width="150" align="right"><?php    echo number_format($Amount)?>&nbsp;</td>
    <td class="A0101" width="150" align="right"><?php    echo number_format($Amount)?>&nbsp;</td>
	<td class="A0101" width="150" align="right">&nbsp;</td>
  </tr>
</table>
<?php   
 	include "../model/subprogram/read_model_menu.php";	
?>
</form>
</body>
</html>
