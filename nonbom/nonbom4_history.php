<?php 
//ewen 2013-03-12 OK
include "../model/modelhead.php";
//首次下单
$checkFirst=mysql_fetch_array(mysql_query("
SELECT DATE_FORMAT(B.Date,'%Y-%m-%d') AS Date FROM $DataIn.nonbom6_cgsheet A LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid WHERE A.GoodsId='$GoodsId' GROUP BY DATE_FORMAT(B.Date,'%Y-%m-%d') LIMIT 1
",$link_id));
$FirstDate=$checkFirst["Date"];

$Qty1=$Qty2=$Qty3=$Qty4=$Qty5=$Qty6=$Qty7=0;
//申购中总数
$CheckRow1=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet A WHERE A.GoodsId='$GoodsId' AND A.Mid='0'",$link_id));
$Qty1=$CheckRow1["Qty"];
//采购总数
$CheckRow2=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet A WHERE A.GoodsId='$GoodsId' AND A.Mid!='0'",$link_id));
$Qty2=$CheckRow2["Qty"];
//入库总数
$CheckRow3=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty),0) AS Qty FROM $DataIn.nonbom7_insheet A WHERE A.GoodsId='$GoodsId'",$link_id));
$Qty3=$CheckRow3["Qty"];
//领料总数
$CheckRow4=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty),0) AS Qty FROM $DataIn.nonbom8_outsheet A WHERE A.GoodsId='$GoodsId'",$link_id));
$Qty4=$CheckRow4["Qty"];
//转入总数
$CheckRow5=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty),0) AS Qty FROM $DataIn.nonbom9_insheet A WHERE A.GoodsId='$GoodsId'",$link_id));
$Qty5=$CheckRow5["Qty"];
//报废总数
$CheckRow6=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(A.Qty),0) AS Qty FROM $DataIn.nonbom10_outsheet A WHERE A.GoodsId='$GoodsId'",$link_id));
$Qty6=$CheckRow6["Qty"];

//目前可用库存
$CheckRow=mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.nonbom5_goodsstock A WHERE A.GoodsId='$GoodsId'",$link_id));
$wStockQty=$CheckRow["wStockQty"];
$oStockQty=$CheckRow["oStockQty"];
$mStockQty=$CheckRow["mStockQty"];
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2"><?php  echo"<img src='nonbom4_toimg.php?GoodsId=$GoodsId'><br>";?></td>
  	</tr>
	<tr>
    	<td width="45">&nbsp;</td>
  		<td>
			<table border="0" cellpadding="0" cellspacing="0">
            	<tr>
          			<td height="25" colspan="10" ><a href='nonbom4_report.php?GoodsId=<?php  echo $GoodsId?>' target='_blank'>分析报告</a></td>
        		</tr>
   			  <tr align="center">
				<td width="70" class="A1111" height="25">首单日期</td>
				<td width="70" bgcolor="#FFCCFF" class="A1101">申购中数量</td>
				<td width="70" bgcolor="#ffcfcf" class="A1101">采购数量</td>
				<td width="70" bgcolor="#FFCCFF" class="A1101">入库数量</td>
				<td width='70' bgcolor='#FFCCFF' class='A1101'>领料数量</td>
				<td width='70' bgcolor='#FFCCFF' class='A1101'>转入数量</td>
				<td width="70" bgcolor="#AAFFAA" class="A1101">报废数量</td>
				<td width="70" bgcolor="#AAFFAA" class="A1101">在库</td>
				<td width="70" bgcolor="#ebd6d6" class="A1101">采购库存</td>
				<td width="70" bgcolor="#ffebd6" class="A1101">最低库存</td>
   			  </tr>
    			<tr align="center">
      				<td class="A0111" height="25"><?php  echo $FirstDate?>&nbsp;</td>
      				<td class="A0101"><?php  echo $Qty1?>&nbsp;</td>
      				<td class="A0101"><?php  echo $Qty2?>&nbsp;</td>
					<td class="A0101"><?php  echo $Qty3?>&nbsp;</td>
					<td class="A0101"><?php  echo $Qty4?>&nbsp;</td>
					<td class="A0101"><?php  echo $Qty5?>&nbsp;</td>
					<td class="A0101"><?php  echo $Qty6?>&nbsp;</td>
					<td class="A0101"><?php  echo $wStockQty?>&nbsp;</td>
					<td class="A0101"><?php  echo $oStockQty?>&nbsp;</td>
                    <td class="A0101"><?php  echo $mStockQty?>&nbsp;</td>
    			</tr>
  			</table>
		</td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>
	  <br>
	  <table width="710" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
        <tr>
          <td height="25" colspan="5"  class='A0100'><div class="rmbB">历史单价</div></td>
        </tr>
		<tr class="">
          <td class='A0111' align="center" width="40" height="25">序号</td>
          <td class='A0101' align="center" width="100">购买日期</td>
          <td class='A0101' align="center"  width="100">供应商</td>
          <td class='A0101' align="center" width="100">结付货币</td>
          <td class='A0101' align="center" width="100">购买单价</td>
        </tr>
        <?php 
	 $PriceResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y-%m-%d') AS Date,B.Price,C.Forshort,D.Symbol
	 FROM $DataIn.nonbom6_cgmain A
	 LEFT JOIN $DataIn.nonbom6_cgsheet B ON B.Mid=A.Id
	 LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
	 LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	 WHERE B.GoodsId='$GoodsId' AND B.Mid!=0 GROUP BY B.Price ORDER BY DATE_FORMAT(A.Date,'%Y-%m-%d')",$link_id);
	 if($PriceRows = mysql_fetch_array($PriceResult)){
		$i=1;
		$hPrice=0;
		$lPrice=0;
		do{
			$Date=$PriceRows["Date"];
			$Price=$PriceRows["Price"];
			$Forshort=$PriceRows["Forshort"];
			$Symbol=$PriceRows["Symbol"];
			if($i==1){
				$hPrice=$Price;
				$lPrice=$Price;
				}
			else{
				$hPrice=$Price>$hPrice?$Price:$hPrice;
				$lPrice=$Price<$lPrice?$Price:$lPrice;
				}
			echo"<tr>
					<td class='A0111' align='center' height='25'>$i</td>
                    <td class='A0101' align='center'>$Date</td>
					<td class='A0101' align='center'>$Forshort</td>
					<td class='A0101' align='center'>$Symbol</td>
					<td class='A0101' align='center'>$Price</td>
				</tr>";
			$i++;
			}while($PriceRows = mysql_fetch_array($PriceResult));
			echo"<tr>
				<td class='A0111' align='right' colspan='5' height='25'><span class='redB'>最高历史价格：$hPrice </span> &nbsp;&nbsp;<span class='greenB'>最低历史价格：$lPrice</span>&nbsp;&nbsp;</td>
				</tr>";
		}
	else{
		echo"<tr><td class='A0111' align='center' colspan='5' height='25'>无历史价格记录</td></tr>";
		}
?>
      </table></td>
  </tr>
</table>
</body>
</html>