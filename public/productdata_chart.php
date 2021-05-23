<?php 
//2012-01-06 ewen更新电信---yang 20120801
include "../model/modelhead.php";
if($Type==1){
?>
<img src="productdata_chart_img.php?Pid=<?php  echo $Pid?>">
<table width="897" border="0" cellpadding="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr align="center" bgcolor="#CCCCCC">
    <td width="100" class="A1111" height="20">序号</td>
  	<td width="197" class="A1101">月份</td>
    <td width="200" class="A1101">接单数量</td>
    <td width="200" class="A1101">出货数量</td>
	<td width="200" class="A1101">目前未出总数</td>
  </tr>
  <tr>
    <td height="160" colspan="6" class="A0010" valign="top">
	 <div style="width:897;height:100%;overflow-x:hidden;overflow-y:scroll">
	<table width="896" border="0" cellpadding="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	  <?php 
		$checkSql=mysql_query("
				SELECT Month,SUM(orderQty) AS orderQty,SUM(shipQty) AS shipQty FROM (
				SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,'0' AS orderQty,SUM(S.Qty) AS shipQty FROM $DataIn.ch1_shipsheet S 
					LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
					WHERE S.ProductId='$Pid' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
				UNION ALL 
				SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,SUM(S.Qty) AS orderQty,'0' AS shipQty 
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
					WHERE S.ProductId='$Pid' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
				)A GROUP BY Month ORDER BY Month DESC",$link_id);
		$i=1;
		$orderSum=0;
		$shipSum=0;
		  if($checkRow=mysql_fetch_array($checkSql)){
			do{
				$orderQty=$checkRow["orderQty"];
				$shipQty=$checkRow["shipQty"];
				$orderSum+=$orderQty;
				$shipSum+=$shipQty;
				$MonthStr=$checkRow["Month"];
				$orderQty=$orderQty==0?"&nbsp;":"<span class=\"blueB\">".$orderQty."</span>";
				$shipQty=$shipQty==0?"&nbsp;":"<span class=\"greenB\">".$shipQty."</span>";
				echo"<tr align=\"center\"><td class=\"A0101\" height=\"20\" width=\"99\">$i</td>
				<td class=\"A0101\" width\"197\">$MonthStr</td>
				<td class=\"A0101\" width=\"200\">$orderQty</td>
				<td class=\"A0101\" width=\"200\">$shipQty</td>
				<td class=\"A0101\" width=\"200\">&nbsp;</td>
				";
				$i++;
				}while ($checkRow=mysql_fetch_array($checkSql));
			for($j=$i;$j<9;$j++){
				echo"<tr align=\"center\"><td class=\"A0101\" height=\"20\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">&nbsp;</td>";
				}
			}
		else{
			echo"<tr><td height=\"20\" align=\"center\" width=\"750\">无记录</td></tr>";
			}
		?>
	</table>
	</div>
	</td>
  </tr>
	<?php 
	if($i>1){
		$unQty=$orderSum-$shipSum;
		$unQty=$unQty==0?"无":"<span class=\"redB\">".$unQty."</span>";
		$shipSum=$shipSum==0?"无":"<span class=\"greenB\">".$shipSum."</span>";
		$orderSum=$orderSum==0?"无":"<span class=\"blueB\">".$orderSum."</span>";
		echo"<tr align=\"center\" bgcolor=\"#CCCCCC\"><td class=\"A0111\" height=\"20\">合计</td><td class=\"A0101\">&nbsp;</td><td class=\"A0101\">$orderSum</td><td class=\"A0101\">$shipSum</td><td class=\"A0101\">$unQty</td><td  class=\"A1101\">&nbsp;</td>";
		}
	?>
</table>
<?php 
	}
else{
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
	echo"<table width=\"460\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"TABLE-LAYOUT: fixed; WORD-WRAP: break-word\">
	<tr align=\"center\">
    <td colspan=\"5\"  height=\"40\">$cName</td>
	</tr>
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
	}

echo"</table>";
?>
