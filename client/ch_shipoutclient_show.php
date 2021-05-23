<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
$ShipResult = mysql_query("SELECT M.DeliveryNumber,M.Remark,M.DeliveryDate ,M.Operator ,F.Forshort ,SUM(S.DeliveryQty) AS SumDeliveryQty
       FROM $DataIn.ch1_deliverysheet S
       LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
       LEFT JOIN $DataIn.ch1_deliverymain  M ON M.Id=S.Mid
	   LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=M.ForwaderId 
	   WHERE Y.ProductId='$ProductId'  AND NOT EXISTS( SELECT H.DeliveryNumber FROM $DataIn.ch1_deliveryhidden H WHERE  H.DeliveryNumber=M.DeliveryNumber)   GROUP BY M.Id",$link_id);
echo"<table width='640' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' align='center'>
<tr  bgcolor='#FFFFFF'><td height='50'>&nbsp;</td>
</tr>";

echo"<table width='640' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' align='center'>
<tr  bgcolor='#33CCCC'>
<td height='25' align='center' width='30' class='A1110'>NO.</td>
<td width='150' align='center'  class='A1110'>DeliveryNumber</td>
<td width='120' align='center' class='A1110'>DeliveryDate</td>
<td width='100' align='center' class='A1110'>DeliveryQty</td>
<td width='220' align='center' class='A1110'>Remark</td>
</tr>";

if($ShipRow=mysql_fetch_array($ShipResult)){
	$i=1;
    $SumQty=0;
    $d1=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);
	do{
		$DeliveryNumber=$ShipRow["DeliveryNumber"];
        $DeliveryDate=$ShipRow["DeliveryDate"];
		$Remark=$ShipRow["Remark"];
		$Forshort=$ShipRow["Forshort"];
		$SumDeliveryQty=$ShipRow["SumDeliveryQty"];
		$Operator=$ShipRow["Operator"];
		$filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
        if(file_exists($filename)){
		$f1=anmaIn($DeliveryNumber,$SinkOrder,$motherSTR);
		         $Bill="<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$DeliveryNumber</a>";
		}
      else     $Bill=$DeliveryNumber; 
       $SumQty+=$SumDeliveryQty;
      
      $checkHiddenNumber= mysql_query("SELECT H.DeliveryNumber FROM $DataIn.ch1_deliveryhidden H WHERE  H.DeliveryNumber='$DeliveryNumber'",$link_id);
     if (mysql_num_rows($checkHiddenNumber)<=0){
			echo"<tr bgcolor='#FFFFFF'>
			<td align='center' height='20' class='A0110'>$i</td>
			<td align='center' class='A0110'>$Bill</td>
			<td align='center' class='A0110'>$DeliveryDate</td>
			<td align='center' class='A0110'>$SumDeliveryQty</td>
			<td class='A0111'>$Remark</td>
			</tr>";
			$i++;
			}
		}while($ShipRow=mysql_fetch_array($ShipResult));
	}
			echo"<tr bgcolor='#FFFFFF'>
			<td align='center' class='A0110' colspan='3'>Total</td>
			<td align='center' class='A0110'>$SumQty</td>
			<td class='A0111'>&nbsp;</td>
			</tr>";
echo"</table>";
?>