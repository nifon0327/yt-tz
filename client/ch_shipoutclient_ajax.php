<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1050;

echo"<table id='$TableId'  cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
	<td width='30' >NO</td>
	<td width='80' align='center'>Number</td>
	<td width='150' align='center'>Invoice</td>
	<td width='80' align='center'>InvoiceFile</td>
	<td width='80' align='center'>Qty</td>
	<td width='100' align='center'>ShipDate</td>
	</tr>";
$i=1;
$sListResult = mysql_query("SELECT M.Id,M.Number,M.InvoiceNO,M.InvoiceFile,M.Date,M.Remark,M.ShipType,M.Ship,M.Operator,SUM(Qty) AS ShipQty
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet H ON H.Mid=M.Id 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=H.ProductId  
WHERE  1 AND O.Id IS NOT NULL AND  P.ProductId =$ProductId GROUP BY M.Id ",$link_id);
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	if($myRow = mysql_fetch_array($sListResult)) {
		do{
		   $Id=$myRow["Id"];
           $Number=$myRow["Number"];
		   $InvoiceNO=$myRow["InvoiceNO"]; 
		   $Date=$myRow["Date"];
           $Remark=$myRow["Remark"];
           $InvoiceFile=$myRow["InvoiceFile"];
		   $Operator=$myRow["Operator"];
            $ShipQty=$myRow["ShipQty"];
		   include "../model/subprogram/staffname.php";
		   $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		   $InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">View</a>";
			echo"<tr bgcolor='$theDefaultColor'>";
			//echo"<td  align='center' height='20'>$showPurchaseorder</td>";
			echo"<td  align='center'>$i</td>";
			echo"<td align='center'>$Number</td>";
			echo"<td align='center'>$InvoiceNO</td>";
			echo"<td align='center'>$InvoiceFile</td>";
			echo"<td align='right'>$ShipQty</td>";
            echo"<td align='center'>$Date</td>";
			echo"</tr>";
			$i++;
			}while ($myRow = mysql_fetch_array($sListResult));			
		}
?>