<?php 
/*$DataIn.电信---yang 20120801
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
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
$subTableWidth=1300;
/*
echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='10' height='20'></td>
		<td width='80' align='center'>订单PO</td>
		<td width='90' align='center'>内部单号</td>
		<td width='330' align='center'>产品名称</td>				
		<td width='55' align='center'>订单数</td>
		<td width='55' align='center'>本次完成</td>
		<td width='55' align='center'>总完成进度（%）</td>
		<td width='55' align='center'>组装总时间(分)</td>
		<td width='55' align='center'>人数</td>
		<td width='55' align='center'>人力(RMB)/单品</td>
		<td width='55' align='center'>人力总计(RMB)</td>	
		";
*/
//width='$subTableWidth'
//$Th_Col="选项|40|序号|40|采购|50|供应商|100|配件分类|100|配件编码|60|配件名称|300|申购备注|200|配件条码|100|货币|30|单价|70|本次申购|60|单位|40|金额|70|申购总数|60|在库|60|采购库存|60|最低库存|60|申购状态|50|申购时间|80|申购人|60";

echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC' align='center'>
		<td width='10' height='20'></td>
		<td width='80'>主分类</td>
		<td width='80'>配件分类</td>
		<td width='50'>配件ID</td>
		<td width='180'>配件名称</td>		
		<td width='150'>申购备注</td>
		<td width='30'>货币</td>
		<td width='70'>单价</td>		
		<td width='60'>本次申购</td>
		<td width='40'>单位</td>	
		<td width='70'>金额</td>		
		<td width='50'>申购总数</td>
		<td width='50'>在库</td>	
		<td width='50'>采购库存</td>		
		<td width='50'>最低库存</td>
		<td width='50'>申购状态</td>		
	    <td width='60'>申购时间</td>
	    <td width='50'>申购人</td>			
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
$sListResult = mysql_query("SELECT A.Id,A.GoodsId,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,A.Remark,A.ReturnReasons,A.Estate,A.Locks,A.Date,A.Operator,B.TypeName,C.Forshort,C.CompanyId,D.GoodsName,D.BarCode,D.Unit,D.Attached,E.wStockQty,E.oStockQty,E.mStockQty,F.Name AS StaffName,G.Symbol,H.Name AS mainType 
	FROM $DataIn.nonbom6_cgsheet A
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=D.TypeId
	LEFT JOIN $DataPublic.nonbom5_goodsstock E ON E.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.staffmain F ON F.Number=B.BuyerId
	LEFT JOIN $DataPublic.currencydata G ON G.Id=C.Currency
	LEFT JOIN $DataPublic.nonbom1_maintype H ON H.Id=B.mainType
	WHERE 1 AND A.MId='$cgMId' ORDER BY A.Date DESC,A.Id DESC",$link_id);
/*
echo "SELECT D.Mid,D.StuffId,D.ReQty,D.Remark,D.Date,D.Estate,D.Locks,D.Operator FROM $DataIn.ck10_tfsheet D
WHERE 1 And D.Mid=$Mid ORDER BY D.Date Desc ";
//echo "";
*/

$i=1;
if ($myRow = mysql_fetch_array($sListResult)) {
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$Id=$myRow["Id"];
		$mainType=$myRow["mainType"];
		$GoodsId=$myRow["GoodsId"];
		$StaffName=$myRow["StaffName"];
		$GoodsName=$myRow["GoodsName"];
		$BarCode=$myRow["BarCode"]==""?"&nbsp;":$myRow["BarCode"];;
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$TypeName=$myRow["TypeName"]==""?"&nbsp;":$myRow["TypeName"];
		switch($myRow["Estate"]){
			case 1:$Estate= "<div class='greenB'>已审核</div>";break;
			case 2:$Estate="<div class='redB'>需初审</div>";break;
			case 3:$Estate="<div class='redB'>需终审</div>";break;
			case 4:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    $Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
			break;
			}
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		$Unit=$myRow["Unit"];
		$Symbol=$myRow["Symbol"];
		$Price=sprintf("%.3f",$myRow["Price"]);
		$Qty=del0($myRow["Qty"]);
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$Forshort=$myRow["Forshort"];
		$wStockQty=del0($myRow["wStockQty"]);
		$oStockQty=del0($myRow["oStockQty"]);
		$mStockQty=del0($myRow["mStockQty"]);
		$CompanyId=$myRow["CompanyId"];
		$checkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE Mid=0 AND GoodsId='$GoodsId'",$link_id));
		$sgSUM=del0($checkQty["Qty"]);
		//加密
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//历史单价
		$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
		//配件分析
		$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
		$Locks=$myRow["Locks"];
		//申购总数计算

			
	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td >$mainType</td>";
		echo"<td  align='Left' >$TypeName</td>";
		echo"<td  align='center'>$GoodsId</td>";//
		echo"<td  align='Left'>$GoodsName</td>";//
		echo"<td  align='Left' >$Remark</td>";		
		echo"<td  align='Left'>$Symbol</td>";
		echo"<td  align='right'>$Price</td>";
		
		echo"<td  align='right' >$Qty</td>";	
		echo"<td  align='right'>$Unit</td>";//
		echo"<td  align='right' >$Amount</td>";		
		echo"<td  align='right'>$sgSUM</td>";
		echo"<td  align='right'>$wStockQty</td>";
		
		echo"<td  align='right' >$oStockQty</td>";	
		echo"<td  align='right'>$mStockQty</td>";//
		echo"<td  align='Left' >$Estate</td>";		
		echo"<td  align='Left'>$Date</td>";
		echo"<td  align='Left'>$Operator</td>";
		
		echo"</tr>";
		$i=$i+1;
		
		//echo "<td width='55' align='center'>$Date</td>";
	}while ($myRow = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>非BOM采购单.</td></tr>";
	}

echo"</table>"."";

?>