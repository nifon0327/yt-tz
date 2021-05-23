
<?php 
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$tableWidth=780;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'  align='center'>
       <tr bgcolor='#99FF99' >
		<td width='30' align='center'>序号</td>
		<td width='310' align='center'>配件名称</td>
		<td width='80' align='center'>类型</td>
		<td width='80' align='center'>单价</td>
		<td width='80' align='center'>对应关系</td>
		<td width='80' align='center'>刀模</td>
		<td width='60' align='center'>切割关系</td>
		<td width='100' align='center'>供应商</td>
		</tr>";
//订单列表
$sList_Sql="SELECT D.Id,D.ItemId,D.StuffCname,D.TypeId,D.Price,D.CompanyId,P.Forshort,S.TypeName,D.Relation,D.Diecut,D.Cutrelation
            FROM $DataIn.developsheet D
            LEFT JOIN $DataIn.stufftype S ON S.TypeId=D.TypeId
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=D.CompanyId
            WHERE ItemId IN (SELECT ItemId FROM $DataIn.development WHERE Id='$Id')";
//echo $sList_Sql;
$sListResult = mysql_query($sList_Sql,$link_id);
$i=1;
$Amount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {	
	do{ 
	    $sId=$StockRows["Id"];
		$ItemId=$StockRows["ItemId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$TypeId=$StockRows["TypeId"];
		$TypeName=$StockRows["TypeName"];
		$Relation=$StockRows["Relation"];
		$Diecut=$StockRows["Diecut"];
		$Cutrelation=$StockRows["Cutrelation"]==0?"&nbsp;":$StockRows["Cutrelation"];
		$CompanyId=$StockRows["CompanyId"];
		$Forshort=$StockRows["Forshort"];
		$Amount=$Amount+$Price;
		$OnclickRelation="onclick='updateRelation(\"$TableId\",$i,$ItemId,$sId)' style='CURSOR: pointer;'";
		//$OnclickDiecut="onclick='updateDiecut(\"$TableId\",$i,$ItemId)' style='CURSOR: pointer;'";
        //$OnclickCutrelation="onclick='updateCutrelation(\"$TableId\",$i,$ItemId)' style='CURSOR: pointer;'";
		echo"<tr bgcolor=#D0FFD0><td align='center'>$i</td>";
		echo"<td  align='center'>$StuffCname</td>";
		echo"<td  align='center'>$TypeName</td>";
		echo"<td  align='center'>$Price</td>";
		echo"<td  align='center' $OnclickRelation>$Relation</td>";
		echo"<td  align='center' >$Diecut</td>";
		echo"<td  align='center'>$Cutrelation</td>";
		echo"<td  align='center'>$Forshort</td>";
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
		echo"<tr bgcolor=#D0FFD0><td align='center' colspan='3'>总成本:</td>";
		echo"<td  align='center'>$Amount</td>";
		echo"<td  align='center' colspan='4'>&nbsp;</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td colspan='7' height='30'>没有资料,请检查.</td></tr>";
	}
echo"</table>";
?>