<?php   
/*
MC、DP共享代码电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 退回订单的配件数据分析");
$ColsNumber=4;
$tableWidth=750;
$i=1;
?>
<body>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="2">退回订单的配件数据分析</td>
    </tr>

	<tr>
		<td height="24">&nbsp;</td>
   	 <td height="24" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
   <td width="40" class="A1111" height="25" align="center">序 号</td>
    <td width="60" class="A1101" align="center">配件ID</td>
    <td width="300" class="A1101" align="center">配件名称</td>
    <td width="70" class="A1101" align="center">退回总数</td>
	 <td width="70" class="A1101" align="center">已使用数</td>
	 <td width="70" class="A1101" align="center">报废数量</td>
	 <td width="70" class="A1101" align="center">退回剩余</td>
	 <td width="70" class="A1101" align="center">可用库存</td>
  </tr>
	<?php   
	//读取未结付货款
	$ShipResult = mysql_query("
	SELECT SUM(G.FactualQty) AS Qty,G.StockId,D.StuffCname,D.StuffId,K.oStockQty
	FROM $DataIn.cg1_stocksheet G 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
	WHERE G.POrderId='' AND SUBSTRING(G.StockId,9,3)!='900' AND K.oStockQty>0 GROUP BY G.StuffId
	",$link_id);
	$Total=0;
	$SumAmount=0;
	if($ShipRow = mysql_fetch_array($ShipResult)){
		$i=1;
		do{
			$Qty=$ShipRow["Qty"];
			$StuffCname=$ShipRow["StuffCname"];
			$StuffId=$ShipRow["StuffId"];
			$oStockQty=$ShipRow["oStockQty"];//
			$StockId=$ShipRow["StockId"];//分析日期
			
			//此日期之后使用的库存数量
			$checkKQty= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(StockQty),0) AS StockQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' AND StockId>'$StockId'",$link_id));
			$StockQty=$checkKQty["StockQty"];
			//此日期之后的报废数量
			$checkbfQty= mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS bfQty FROM $DataIn.ck8_bfsheet WHERE StuffId='$StuffId' AND DATE_FORMAT(Date,'%Y%m%d')>LEFT('$StockId',8)",$link_id));
			$bfQty=$checkbfQty["bfQty"];
			$thsyQty=$Qty-($StockQty+$bfQty);
			if($thsyQty>0 && $oStockQty>$thsyQty){
				$oStockQty="<div class='redB'>".$oStockQty."</div>";
				}
			$thsyQty=$thsyQty<=0?"&nbsp;":"<div class='redB'>".$thsyQty."</div>";
			
			echo "<tr><td align='center' class='A0111'>$i</td>";
			echo "<td align='center' class='A0101'>$StuffId</td>";
			echo "<td class='A0101'>$StuffCname</td>";
			echo "<td align='right' class='A0101'>&nbsp;$Qty</td>";
			echo "<td align='right' class='A0101'>&nbsp;$StockQty</td>";
			echo "<td align='right' class='A0101'>&nbsp;$bfQty</td>";
			echo "<td align='right' class='A0101'>$thsyQty</td>";
			echo "<td class='A0101' align='right'>$oStockQty</td>";
			echo "</tr>";
			$i++;
			}while($ShipRow = mysql_fetch_array($ShipResult));
		}
	?>
</table>
</form>
</body>
</html>
