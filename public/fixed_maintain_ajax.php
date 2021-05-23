<?php 
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
//电信-joseph
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=600;

//width='$subTableWidth'
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='200' align='center'>维护项目名称</td>	
		<td width='30' align='center'>字母</td>
		<td width='50'  align='center'>类型</td>
		<td width='50'  align='center'>天数</td>
		<td width='70' align='center'>设置日期</td>
		<td width='80' align='center'>状态</td>
		<td width='80' align='center'>操作员</td>		
		";

echo "</tr>";

//$Th_Col="选项|45|序号|45|日期|70|订单PO|100|内部单号|80|产品名称|300|订单数|50|本次完成|50|总完成（%）|50|组装时间(分)|50|人数|50|人力(RMB)/单品|60|人力总计(RMB)|60|备注|50|登记|60";

/*
echo "SELECT M.OrderPO,S.Estate,S.ProductId,P.cName,D.Id,D.POrderId,S.Qty,D.FQty,D.AllMins,D.Workers,D.Remark,D.Date,D.Locks,D.Operator
FROM $DataIn.sc2_Pfinish D
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=D.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
WHERE 1 And D.Date=$Date ORDER BY D.ID Desc,M.OrderPO ";
*/
//echo "StuffId:$StuffId";

									
$sListResult = mysql_query("SELECT  O.Id,O.Name,O.Letter,O.Days,O.Date,O.Estate,O.Operator,O.Locks,M.CName FROM $DataPublic.oa3_maitaintype  O 
								    left join $DataPublic.oa3_maitaindays  M  ON M.ID=O.DaysID 
									where 1 AND O.TypeId='$TypeID' ORDER BY O.Estate DESC,O.Letter 
									",$link_id);



$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{

		$Id=$StockRows["Id"];
		$Name=$StockRows["Name"];
		$Letter=$StockRows["Letter"];
		
		$Date=$StockRows["Date"];
		$Estate=$StockRows["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$StockRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$StockRows["Locks"];
		
		$SubCName=$StockRows["CName"];	
		$SubDays=$StockRows["Days"];
		
		echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td  align='Left' >$Name</td>";
		echo"<td  align='center' >$Letter</td>";
		echo"<td  align='center' >$SubCName</td>";
		echo"<td  align='center' >$SubDays</td>";
		echo"<td  align='center'>$Date</td>";
		echo"<td  align='center'>$Estate</td>";
		echo"<td  align='Left'>$Operator</td>";
		echo"</tr>";
		$i=$i+1;
		
		//echo "<td width='55' align='center'>$Date</td>";
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的维护项目.</td></tr>";
	}

echo"</table>"."";

?>