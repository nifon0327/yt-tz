<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");



$ShipResult = mysql_query("SELECT M.DeliveryNumber,M.Remark,M.DeliveryDate ,M.Operator ,F.Forshort ,SUM(S.DeliveryQty) AS SumDeliveryQty
       FROM $DataIn.ch1_deliverysheet S
       LEFT JOIN $DataIn.ch1_deliverymain  M ON M.Id=S.Mid
	   LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=M.ForwaderId 
	   WHERE S.POrderId='$TempId' GROUP BY M.Id",$link_id);
echo"
<table width='80%' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' align='center'>
<tr align='center' bgcolor='#33CCCC'>
<td height='25' width='40'>序号</td>
<td width='80' >提货单号</td>
<td width='80' >提货日期</td>
<td width='100'>Forwarder公司</td>
<td width='80'>提货数量</td>
<td width='160'>备注</td>
<td width='60'>操作人</td>
</tr>";

if($ShipRow=mysql_fetch_array($ShipResult)){
	$i=1;
	do{
		$DeliveryNumber=$ShipRow["DeliveryNumber"];
        $DeliveryDate=$ShipRow["DeliveryDate"];
		$Remark=$ShipRow["Remark"];
		$Forshort=$ShipRow["Forshort"];
		$SumDeliveryQty=$ShipRow["SumDeliveryQty"];
		$Operator=$ShipRow["Operator"];
		include "../model/subprogram/staffname.php";
			echo"
			<tr bgcolor='#FFFFFF'>
			<td align='center' height='25'>$i</td>
			<td align='center'>$DeliveryNumber</td>
			<td align='center'>$DeliveryDate</td>
			<td>$Forshort</td>
			<td align='center'>$SumDeliveryQty</td>
			<td >$Remark</td>
			<td align='center'>$Operator</td>
			</tr>
			";
			$i++;
		}while($ShipRow=mysql_fetch_array($ShipResult));
	}
echo"</table>";
?>