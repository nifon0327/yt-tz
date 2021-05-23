<?php   
/*
MC、DP共享代码电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 取消订单的配件数据分析");
$ColsNumber=4;
$tableWidth=820;
$i=1;
?>
<body>
<form id="form1" name="form1" method='post' action="desk_delorder_pjfx.php">
<table width="940" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" >
        <p style="font-size:20px;font-weight:bold;color:#39C;">取消订单的配件数据分析</p>
        </td>
    </tr>

	<tr>
   	 <td height="24" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
   <td width="40" class="A1111" height="25" align="center">序 号</td>
    <td width="60" class="A1101" align="center">配件ID</td>
    <td width="300" class="A1101" align="center">配件名称</td>
    <td width="70" class="A1101" align="center">采购总数</td>
	 <td width="70" class="A1101" align="center">已使用数</td>
	 <td width="70" class="A1101" align="center">报废数量</td>
	 <td width="70" class="A1101" align="center">采购剩余</td>
	 <td width="70" class="A1101" align="center">可用库存</td>
     <td width="70" class="A1101" align="center">在库</td>
  </tr>
	<?php   
	//读取取消订单转为特采单数据
	$ShipResult = mysql_query("
	SELECT SUM(G.FactualQty) AS Qty,G.StockId,D.StuffCname,D.StuffId,K.oStockQty,K.tStockQty,T.delType,D.Gfile,D.Gstate
	FROM $DataIn.cg1_stocksheet G 
	LEFT JOIN $DataIn.yw1_orderdeleted T ON T.POrderId=SUBSTRING(G.StockId,1,12) 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
	WHERE 1 AND  G.POrderId='' AND SUBSTRING(G.StockId,9,3)!='900' AND G.FactualQty>0 AND  K.oStockQty>0 AND G.Estate=0 GROUP BY G.StuffId
	",$link_id);
	$Total=0;
	$SumAmount=0;
	if($ShipRow = mysql_fetch_array($ShipResult)){
		$i=1;
		do{
			$Qty=$ShipRow["Qty"];
			$StuffCname=$ShipRow["StuffCname"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		    include "../model/subprogram/stuffimg_model.php";
		    $Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
		    $Gfile=$myRow["Gfile"];
		    $Gstate=$myRow["Gstate"];
		    include "../model/subprogram/stuffimg_Gfile.php";
			$StuffId=$ShipRow["StuffId"];
			$StockId=$ShipRow["StockId"];//分析日期
			$oStockQty=$ShipRow["oStockQty"];
			$tStockQty=$ShipRow["tStockQty"];
			$delType=$ShipRow["delType"];
		if ($delType!=3){
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
			echo "<td class='A0101' align='right'>$tStockQty</td>";
			echo "</tr>";
			$i++;
		 }
	  }while($ShipRow = mysql_fetch_array($ShipResult));
		if ($i==1){
			echo "<tr><td align='center' colspan='9' height='80' class='A0111'>暂未有符合条件记录！</td></tr>";
		}
	  }
	  else{
			echo "<tr><td align='center' colspan='9' height='80' class='A0111'>暂未有符合条件记录！</td></tr>";
		}
	?>
    <tr class=''>
   <td  class="A0111" height="25" align="center">序 号</td>
    <td  class="A0101" align="center">配件ID</td>
    <td  class="A0101" align="center">配件名称</td>
    <td  class="A0101" align="center">采购总数</td>
	 <td  class="A0101" align="center">已使用数</td>
	 <td class="A0101" align="center">报废数量</td>
	 <td  class="A0101" align="center">采购剩余</td>
	 <td  class="A0101" align="center">可用库存</td>
     <td  class="A0101" align="center">在库</td>
  </tr>
</table>
</form>
<br />
<span><b>说明：</b>2011年4月29日前为所有取消订单统计数据;29日后为客户撤单及内部取消尾数单的统计数据;</span>
</body>
</html>
