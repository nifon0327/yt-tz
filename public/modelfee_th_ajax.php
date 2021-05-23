<?php 
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="QCTB".$RowId;
echo"<table id='$TableId' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='280' align='center'>配件名称</td>
		<td width='80' align='center'>类型</td>				
		<td width='80' align='center'>单价</td>
		<td width='100' align='center'>供应商</td>
		</tr>";
//订单列表//LEFT JOIN yw1_ordermain M ON M.OrderNumber=O.OrderNumber 
$stuffResult = mysql_query("SELECT D.StuffCname,D.TypeId,D.Price,P.CompanyId,P.Forshort,S.TypeName
            FROM $DataIn.modelfeestuff M
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=M.StuffId
            LEFT JOIN $DataIn.stufftype S ON S.TypeId=D.TypeId
            LEFT JOIN $DataIn.bps  B  ON B.StuffId =D.StuffId
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
            WHERE M.mId='$Id'",$link_id);
$i=1;
if ($stuffRows = mysql_fetch_array($stuffResult)) {
	do{
		$Forshort=$stuffRows ["Forshort"];
		$StuffCname=$stuffRows ["StuffCname"];
		$TypeName=$stuffRows ["TypeName"];
		$Price=$stuffRows ["Price"];
		                
   		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td  >$StuffCname</td>";				//客户				
		echo"<td  align='center'>$TypeName</td>";				//产品Id
		echo"<td align='right'>$Price</td>";					//产品名称
		echo"<td align='center'>$Forshort</td>";					//Code
		echo"</tr>";
		$i++;
 		}while ($stuffRows= mysql_fetch_array($stuffResult));
	}
else{
	echo"<tr><td height='30'>Nothing</td></tr>";
	}
echo"</table>";
?>