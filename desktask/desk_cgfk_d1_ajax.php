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
		<td width='40' align='center'>入库<br>日期</td>
		<td width='60' align='center'>送货单</td>
		<td width='70' align='center'>采购单号</td>
		<td width='90' align='center'>需求单号</td>
		<td width='50' align='center'>配件ID</td>
		<td width='325' align='center'>配件名称</td>
		<td width='40' align='center'>图档</td>
		<td width='55' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='45' align='center'>采购<br>数量</td>
		<td width='45' align='center'>本次<br>收货</td>
		<td width='45' align='center'>未收<br>数量</td>
		<td width='72' align='center'>采购金额</td>
		</tr>";
//订单列表
$sListResult = mysql_query("
	SELECT M.BillNumber,DATE_FORMAT(M.Date,'%d') AS rkDay,MG.PurchaseID,G.StockId,G.StuffId,D.Gfile,D.Gstate,G.Price,U.Name AS UnitName,(G.AddQty+G.FactualQty) AS cgQty,D.StuffCname,S.Qty
	FROM $DataIn.ck1_rksheet S
	LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
	LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
	LEFT JOIN $DataIn.cg1_stockmain MG ON MG.Id=G.Mid
	WHERE 1 AND M.CompanyId='$CompanyId' AND K.StockId IS NULL AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' ORDER BY M.Date,G.Mid,G.Id
	",$link_id);
$i=1;
$SumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$BillNumber=$StockRows["BillNumber"];
		$FilePath1="../download/deliverybill/$BillNumber.jpg";
		if(file_exists($FilePath1)){
			$BillNumber="<a href='$FilePath1' target='_blank'>$BillNumber</a>";
			}
		$PurchaseID=$StockRows["PurchaseID"];
		$rkDay=$StockRows["rkDay"];
		$StockId=$StockRows["StockId"];
		$StuffId=$StockRows["StuffId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
		$cgQty=$StockRows["cgQty"];
		$Qty=$StockRows["Qty"];
		$Amount=sprintf("%.2f",$Price*$Qty);
		$SumAmount+=$Amount;
		//总收货数量
		$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$wsQty=$cgQty-$rkQty;
		$wsQty=$wsQty==0?"&nbsp;":"<div class='redB'>".$wsQty."</div>";
		$Gfile=$StockRows["Gfile"];
		$Gstate=$StockRows["Gstate"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		echo"<tr bgcolor=#D0FFD0>
		<td align='center'>$i</td>";
		echo"<td  align='center'>$rkDay</td>";
		echo"<td  align='center'>$BillNumber</td>";	//送货单
		echo"<td  align='center'>$PurchaseID</td>";	//采购单
		echo"<td  align='center'>$StockId</td>";	//需求单
		echo"<td  align='center'>$StuffId</td>";	//配件ID
		echo"<td>$StuffCname</td>";					//配件名称
		echo"<td  align='center'>$Gfile</td>";
		echo"<td  align='right'>$Price</td>";		//价格
		echo"<td align='center'>$UnitName</td>";
		echo"<td align='right'>$cgQty</td>";		//采购数量
		echo"<td align='right'>$Qty</td>";			//
		echo"<td align='right'>$wsQty</td>";
		echo"<td align='right'>$Amount</td>";		//金额
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	$SumAmount=sprintf("%.2f",$SumAmount);
	$SumAmount=number_format($SumAmount,2);
	echo"<tr  bgcolor=#D0FFD0><td colspan='13'>合计</td><td align='right'>$SumAmount</td></tr>";
	}
else{
	echo"<tr><td height='30' colspan='6'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr>";
	}
echo"</table>";
?>
