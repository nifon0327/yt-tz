<?php   
/*
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
$TempArray=explode("|",$TempId);
$predivNum=$TempArray[0];	//表格前置符
$BuyerId=$TempArray[1];		//采购
$CompanyId=$TempArray[2];		//供应商
$tableWidth=1010;
$TableId=$predivNum;
$SearchRows=" AND T.mainType<2";//需采购的配件需求单
echo"<table width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#99FF99'>
		<td width='40' align='center'>序号</td>
		<td width='90' align='center'>需求单号</td>
		<td width='40' align='center'>配件ID</td>
		<td width='410' align='center'>配件名称</td>
		<td width='40' align='center'>图档</td>
		<td width='48' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='40' align='center'>订单<br>数量</td>
		<td width='40' align='center'>使用<br>库存</td>
		<td width='40' align='center'>需购<br>数量</td>
		<td width='40' align='center'>增购<br>数量</td>
		<td width='40' align='center'>实购<br>数量</td>
		<td width='50' align='center'>金额</td>
		<td width='72' align='center'>备注</td>
		</tr>";
//订单列表
$mySql="SELECT S.StockId,S.StuffId,S.Price,U.Name AS UnitName,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.AddRemark,A.StuffCname,A.Gfile,A.Gstate
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId
		LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
		LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
		WHERE 1 AND S.Mid=0 AND S.Estate='0' AND E.Id IS NULL AND (S.FactualQty>0 OR S.AddQty>0) $SearchRows AND S.BuyerId='$BuyerId'
		AND S.CompanyId='$CompanyId' ORDER BY S.StockId DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$i=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$StockId=$myRow["StockId"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$cgQty=$AddQty+$FactualQty;
		$Amount=sprintf("%.2f",$cgQty*$Price);
		$AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
		$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];
		include "../model/subprogram/stuffimg_model.php";
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		////////////////////////////////////////////////////
		echo"<tr bgcolor='#BBFFBB'>";
		echo"<td align='center' height='20'>$i</td>";
		echo"<td align='center'>$StockId</td>";
		echo"<td align='center'>$StuffId</td>";
		echo"<td>$StuffCname</td>";
		echo"<td align='center'>$Gfile</td>";
		echo"<td align='right'>$Price</td>";
		echo"<td align='center'>$UnitName</td>";
		echo"<td align='right'>$OrderQty</td>";
		echo"<td align='right'>$StockQty</td>";
		echo"<td align='right'>$FactualQty</td>";
		echo"<td align='right'>$AddQty</td>";
		echo"<td align='right'>$cgQty</td>";
		echo"<td align='right'>$Amount</td>";
		echo"<td>$AddRemark</td>";
		echo"</tr>";
		$i++;
		}while($myRow = mysql_fetch_array($myResult));
	echo"</table>";
	}
else{
	echo"<tr><td height='30' colspan='7'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr></table>";
	}
?>
