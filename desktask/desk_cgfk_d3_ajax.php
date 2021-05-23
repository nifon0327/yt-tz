<?php   
/*电信-yang 20120801
已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$TypeId=$TempArray[0];
$CompanyId=$TempArray[1];
$predivNum=$TempArray[2];
$Month=$TempArray[3];

$tableWidth=990;
$TableId=$predivNum;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#99FF99'>
		<td width='30' align='center'>序号</td>
		<td width='70' align='center'>采购单号</td>
		<td width='90' align='center'>需求单号</td>
		<td width='50' align='center'>配件ID</td>
		<td width='463' align='center'>配件名称</td>				
		<td width='40' align='center'>图档</td>
		<td width='55' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='60' align='center'>采购数量</td>
		<td width='60' align='center'>未收数量</td>
		<td width='72' align='center'>采购金额</td>
		</tr>";
//订单列表
$sListResult = mysql_query("
	SELECT M.PurchaseID,S.StockId,S.StuffId,S.Price,U.Name AS UnitName,S.OrderQty,S.StockQty,(S.AddQty+S.FactualQty) AS Qty,D.StuffCname,D.Gfile,D.Gstate
	FROM $DataIn.cg1_stockmain M
	LEFT JOIN $DataIn.cg1_stocksheet S ON M.Id=S.Mid
	LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
	WHERE 1 AND M.CompanyId='$CompanyId' AND K.Estate=3 AND K.Month='$Month' ORDER BY M.Id
	",$link_id);
$i=1;
$sumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$PurchaseID=$StockRows["PurchaseID"];
		$rkDay=$StockRows["rkDay"];
		$StockId=$StockRows["StockId"];
		$StuffId=$StockRows["StuffId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
		$Qty=$StockRows["Qty"];
		$Amount=$Price*$Qty;
		$SumAmount+=$Amount;
		//总收货数量
		$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$wsQty=$Qty-$rkQty;
		$wsQty=$wsQty==0?"&nbsp;":"<div class='redB'>".$wsQty."</div>";
		$Gfile=$StockRows["Gfile"];
		$Gstate=$StockRows["Gstate"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		echo"<tr bgcolor=#D0FFD0>
		<td align='center'>$i</td>";
		echo"<td  align='center'>$PurchaseID</td>";	//采购单
		echo"<td  align='center'>$StockId</td>";	//需求单
		echo"<td  align='center'>$StuffId</td>";	//配件ID
		echo"<td>$StuffCname</td>";					//配件名称
		echo"<td  align='center'>$Gfile</td>";
		echo"<td  align='right'>$Price</td>";		//价格
		echo"<td align='center'>$UnitName</td>";
		echo"<td align='right'>$Qty</td>";		//采购数量
		echo"<td align='right'>$wsQty</td>";
		echo"<td align='right'>$Amount</td>";		//金额
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
	$SumAmount=sprintf("%.2f",$SumAmount);
	$SumAmount=number_format($SumAmount,2);
	echo"<tr  bgcolor=#99FF99><td colspan='10'>合计</td><td align='right'>$SumAmount</td></tr>";
	}
else{
	echo"<tr><td height='30' colspan='8'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr>";
	}
echo"</table>";
?>
