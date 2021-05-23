<?php 
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="proxyTB".$RowId;
echo"<table id='$TableId' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>客户</td>
		<td width='100' align='center'>产品ID</td>				
		<td width='260' align='center'>中文名称</td>
		<td width='200' align='center'>Product Code</td>
		</tr>";
$proxyResult = mysql_query("
SELECT A.ProductId,P.cName,P.eCode,C.Forshort FROM $DataIn.yw7_clientproduct A
LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE A.cId='$proxyId'",$link_id);
$i=1;
if ($proxyRows = mysql_fetch_array($proxyResult)) {
	do{
		$Forshort=$proxyRows["Forshort"];
		$cName=$proxyRows["cName"];
		$eCode=$proxyRows["eCode"];
		$ProductId=$proxyRows["ProductId"];
		                
   		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  align='center'>$Forshort</td>";				//客户				
		echo"<td  align='center'>$ProductId</td>";				//产品Id
		echo"<td>$cName</td>";					//产品名称
		echo"<td>$eCode</td>";					//Code
		echo"</tr>";
		$i++;
 		}while ($proxyRows = mysql_fetch_array($proxyResult));
	}
else{
	echo"<tr><td height='30'>Nothing</td></tr>";
	}
echo"</table>";
?>