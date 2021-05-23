<?php

	$ListTR="";
	if ($eCode!="") {
	    $checkSql=mysql_fetch_array(mysql_query("SELECT P.cName,P.eCode,C.Forshort FROM $DataIn.productdata P LEFT JOIN $DataIn.trade_object C ON P.CompanyId=C.CompanyId WHERE P.eCode='$eCode' ORDER BY P.eCode LIMIT 1",$link_id));	
	}
	else{
		$checkSql=mysql_fetch_array(mysql_query("SELECT P.cName,P.eCode,C.Forshort FROM $DataIn.productdata P LEFT JOIN $DataIn.trade_object C ON P.CompanyId=C.CompanyId WHERE P.ProductId='$Pid' ORDER BY P.Id DESC LIMIT 1",$link_id));
	}
		$cName=$checkSql["cName"];
		$eCode=$checkSql["eCode"];
		$Forshort=$checkSql["Forshort"];

	$checkReturnedSQL= mysql_query("SELECT R.ReturnMonth,R.Qty,R.Price,(R.Qty*R.Price) AS Amount FROM $DataIn.product_returned R WHERE R.eCode='$eCode' ORDER BY R.ReturnMonth DESC,R.Id",$link_id);
	if($checkReturnedRow=mysql_fetch_array($checkReturnedSQL)){
		$i=1;
		$AmountSum=0;
		$QtySum=0;
		do{
			$ReturnMonth=$checkReturnedRow["ReturnMonth"];
			$Qty=$checkReturnedRow["Qty"];
			$Price=$checkReturnedRow["Price"];
			$Amount=sprintf("%.2f",$checkReturnedRow["Amount"]);
			$AmountSum+=$Amount;
			$QtySum+=$Qty;
			$ListTR.="<tr>
			<td class=\"A0111\" align=\"center\" height=\"20\">$i</td>
			<td class=\"A0101\" align=\"center\">$ReturnMonth</td>
			<td class=\"A0101\" align=\"center\">$Qty</td>
			<td class=\"A0101\" align=\"center\">$Price</td>
			<td class=\"A0101\" align=\"center\">$ $Amount</td>
			</tr>";
			$i++;
			}while($checkReturnedRow=mysql_fetch_array($checkReturnedSQL));
			$AmountSum=sprintf("%.2f",$AmountSum);
		$ListTR.="<tr bgcolor=\"#CCCCCC\" height=\"20\">
			<td class=\"A0111\" align=\"center\">合计</td>
			<td class=\"A0101\" align=\"center\">&nbsp;</td>
			<td class=\"A0101\" align=\"center\">$QtySum</td>
			<td class=\"A0101\" align=\"center\">&nbsp;</td>
			<td class=\"A0101\" align=\"center\">$ $AmountSum</td>
			</tr>";
		}
	//出货总数
	//echo "SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$Pid' <br>";
	$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$Pid'",$link_id);
	$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
	$ReturnedPercent=$ShipQtySum==0?0:sprintf("%.1f",(($QtySum/$ShipQtySum)*1000));
	if($ReturnedPercent>=5){
				$ReturnedPercent="<span class=\"redB\">".$ReturnedPercent."‰</span>";
				}
			else{
					if($ReturnedPercent>=2){
						$ReturnedPercent="<span class=\"yellowB\">".$ReturnedPercent."‰</span>";
						}
					else{
						$ReturnedPercent="<span class=\"greenB\">".$ReturnedPercent."‰</span>";
						}
					}
	//退货记录
	echo"<tr  bgcolor='#B7B7B7'><td colspan='10'><table width=\"460\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"TABLE-LAYOUT: fixed; WORD-WRAP: break-word\" align='center'>
	<tr>
    <td colspan=\"2\" height=\"20\">$Forshort</td>
    <td width=\"100\" align=\"center\">$ReturnedPercent</td>
	<td colspan=\"2\"  align=\"right\">$eCode</td>
  	</tr>
  	<tr align=\"center\" bgcolor=\"#CCCCCC\">
    <td class=\"A1111\" height=\"20\">序号</td>
  	<td class=\"A1101\">退货月份</td>
    <td class=\"A1101\">退货数量</td>
    <td class=\"A1101\">单价</td>
	<td class=\"A1101\">退货金额</td>
  	</tr> $ListTR";
echo"</table></td></tr>";
?>