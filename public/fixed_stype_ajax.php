<?php 
//电信-joseph
//代码、数据共享-EWEN 2012-08-15
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=600;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='40' align='center'>分类ID</td>
		<td width='200' align='center'>分类名称</td>	
		<td width='80' align='center'>分类字母</td>
		<td width='150' align='center'>设置日期</td>
		<td width='80' align='center'>状态</td>
		<td width='80' align='center'>操作员</td>";
echo "</tr>";
$sListResult = mysql_query("SELECT * FROM $DataPublic.oa2_fixedsubtype
where 1 AND MainTypeId='$MainTypeID' ORDER BY Estate DESC,Letter ",$link_id);
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
		echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";
		echo"<td  align='center'>$Id</td>";//
		echo"<td  align='Left' >$Name</td>";
		echo"<td  align='center' >$Letter</td>";
		echo"<td  align='center'>$Date</td>";
		echo"<td  align='center'>$Estate</td>";
		echo"<td  align='Left'>$Operator</td>";
		echo"</tr>";
		$i=$i+1;
	}while ($StockRows = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关的子分类.</td></tr>";
	}

echo"</table>"."";

?>