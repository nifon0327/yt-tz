<?php 
//电信-ZX  2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

//收货
/*
$IndepotResult = mysql_query("SELECT InDepotMain.Date,InDepotMain.Operator,InDepotMain.BillNumber,
	InDepotSheet.StockId,InDepotSheet.Qty,InDepotSheet.StoreQty,InDepotSheet.Remark 
	FROM InDepotMain,InDepotSheet 
	where InDepotSheet.StockId=$Sid and InDepotSheet.Mid=InDepotMain.Id order by InDepotMain.Date desc",$link_id);
*/
//收货
$IndepotResult = mysql_query("SELECT M.Date,M.Operator,M.BillNumber,
	S.StockId,S.Qty,S.StoreQty,S.Remark 
	FROM InDepotMain M ,InDepotSheet S 
	where S.StockId=$Sid and S.Mid=M.Id order by M.Date desc",$link_id);

?>
<html>
<head>
<?php 
include "../model/characterset.php";
?>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php 
//CSS模板
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
?>
<script src="../model/pagefun.js" type=text/javascript></script>
<title></title>
</head>
<body>
<table width="861" height="64" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td height="38" colspan="6"><div align="center">收货记录</div></td>
  </tr>
  <tr>
    <td height="30" colspan="6">采购流水号：<?php  echo $Sid?></td>
  </tr>
  <tr <?php  echo $Fun_bgcolor?>> 
  	<td width="63" height="25" class="A1111"><div align="center">序号</div></td>
    <td width="90" class="A1101"><div align="center">收货日期</div></td>
    <td width="108" class="A1101"><div align="center">收货单号</div></td>
    <td width="76" class="A1101"><div align="center">收货数量</div></td>
   
    <td width="77" class="A1101"><div align="center">签收人</div></td>
    <td width="419" class="A1101"><div align="center">备 注</div></td>
  </tr>
  <?php 
	$IndepotSUM=0;
  	if ($IndepotRow = mysql_fetch_array($IndepotResult)) {
		$i=1;
		do{
			$Id=$IndepotRow["Id"];
			$Date=substr($IndepotRow["Date"],0,10);//
			$BillNumber=$IndepotRow["BillNumber"]==""?"&nbsp":"<span onClick='ViewDoc(\"deliverybill\",\"$IndepotRow[BillNumber].pdf\")' style='CURSOR: pointer;color:#FF6633'>$IndepotRow[BillNumber]</span>";//送货单号
			$DeliveryBill=$IndepotRow["DeliveryBill"];			  
			$Provider=$IndepotRow["Forshort"];
			$Buyer=$IndepotRow["Buyer"];
			$StockId=$IndepotRow["StockId"];
			$DeliveryBill=$IndepotRow["DeliveryBill"];
			$StuffCname=$IndepotRow["StuffCname"];
			$Qty=$IndepotRow["Qty"];//收货数量
			$StoreQty=$IndepotRow["StoreQty"];			  
			$Remark=$IndepotRow["Remark"]==""?"<div align='center'>-</div>":$IndepotRow["Remark"];	  
			$Operator=$IndepotRow["Operator"];
			$IndepotSUM=$IndepotSUM+$Qty;
			echo"<tr>
			<td class='A0111' align='center' height='25'>$i</td>
			<td class='A0101' align='center'>$Date</td>
			<td class='A0101' align='center'>$BillNumber</td>
			<td class='A0101' align='center'>$Qty</td>
			<td class='A0101' align='center'>$Operator</td>
			<td class='A0101'>$Remark</td>
			</tr>";
			$i++;
  			}while ($IndepotRow = mysql_fetch_array($IndepotResult));
 		}
  ?>
  <tr> 
  	<td height="25" colspan="3" class="A0111" align="center" <?php  echo $Fun_bgcolor?>>合计</td>
  	<td width="76" class="A0101" align="center"><?php  echo $IndepotSUM?></td>   
    <td width="77" class="A0101">&nbsp;</td>
    <td width="419" class="A0101">&nbsp;</td>
  </tr>
</table>
</body>
</html>
