<?php   
/*
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=1000;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='80' align='center'>PO</td>
		<td width='300' align='center'>中文名</td>
		<td width='240' align='center'>Product Code</td>
		<td width='60' align='center'>售价</td>
		<td width='60' align='center'>数量</td>
		<td width='60' align='center'>金额</td>
		<td width='100' align='center'>流水号</td>
		<td width='70' align='center'>交期</td>
	</tr>";

//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$Month=$TempArray[1];//a
$predivNum=$TempArray[2];
$mySql="SELECT S.OrderPO,S.POrderId,S.Price,S.Qty,S.Price*S.Qty AS Amount,P.cName,P.eCode,P.TestStandard,P.ProductId ,PI.Leadtime
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
WHERE S.Estate>0 AND M.CompanyId='$CompanyId' AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$Month' ORDER BY M.OrderDate";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
$AmountSum=0;
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	$FK_1=0;$FK_2=0;$FK_3=0;
	do{
		$OrderPO=$myRow["OrderPO"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$QtySum=$QtySum+$Qty;
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$AmountSum=sprintf("%.2f",$AmountSum+$Amount);
		$POrderId=$myRow["POrderId"];
		$OrderDate=$myRow["OrderDate"];
		$Leadtime=$myRow["Leadtime"]==""?"&nbsp;":$myRow["Leadtime"];
		//Invoice查看
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$d1=anmaIn("download/invoice",$SinkOrder,$motherSTR);		
		$InvoiceNO=$InvoiceFile==0?$InvoiceNO:"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNO</span>";
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		echo"
			<tr bgcolor='#FFFFFF'>
				<td height='20' align='center'>$i</td>
				<td>$OrderPO</td>
				<td>$TestStandard</td>
				<td>$eCode</td>
				<td align='right'>$Price</td>
				<td align='right'>$Qty</td>
				<td align='right'>$Amount</td>
				<td align='center'>$POrderId</td>
				<td align='center'>$Leadtime</td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
echo"
	<tr bgcolor='#99FF99'>
		<td height='20' align='center' colspan='5'>合 计</td>
		<td align='right'>$QtySum</td>
		<td align='right'>$AmountSum</td>
		<td align='center'>&nbsp;</td>
		<td align='center'>&nbsp;</td>
	</tr>
</table>";
?>