<?php 
/*
$DataIn.zw1_assetuse
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//$TableId="ListTB".$RowId;
echo"<table width='500' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='40' align='center'>序号</td>
		<td width='80' align='center'>领用日期</td>
		<td width='60' align='center'>领用人</td>				
		<td width='350' align='center'>领用说明</td>
		<td width='60' align='center'>原领用人</td>
		</tr>";
//需求单列表
$sListResult = mysql_query("SELECT U.Remark,U.Date,P.Name AS User,U.Estate,U.Operator 
	FROM $DataIn.zw1_assetuse U LEFT JOIN $DataPublic.staffmain P ON P.Number=U.User 
	WHERE U.AssetId='$Id' ORDER BY U.Date DESC,U.Id DESC",$link_id);
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
		//颜色	0绿色	1白色	2黄色	3绿色
		$Remark=$StockRows["Remark"];
		$Date=$StockRows["Date"];
		$User=$StockRows["User"];
		$Estate=$StockRows["Estate"];
		$Operator=$StockRows["Operator"];		
		if($Estate==0){
			$Operator="初始记录";
			}
		else{
			include "../model/subprogram/staffname.php";
			}
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";//配件状态 
		echo"<td  align='center'>$Date</td>";
		echo"<td  align='center'>$User</td>";//配件采购流水号
		echo"<td> $Remark </td>";//配件名称
		echo"<td align='center'>$Operator</td>";//配件价格
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));	
	}
else{
	echo"<tr><td height='30' clos='5'>没有领用记录.</td></tr>";
	}
echo"</table>";
?>