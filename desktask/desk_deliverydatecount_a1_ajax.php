<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.productdata
$DataIn.cg1_stocksheet
$DataIn.stuffdata
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TempArray=explode("|",$TempId);
$TypeId=$TempArray[0];
$predivNum=$TempArray[1];	//a

switch($Action){
   case 2:
      $OrderSort=" ORDER BY S.OrderPO";
      break;
   case 3:
      $OrderSort=" ORDER BY P.cName";
      break;
   case 4:
      $OrderSort=" ORDER BY PI.Leadtime DESC";
      break;
	case 1:
	default:
	   $OrderSort=" ORDER BY M.CompanyId,S.POrderId";
	  break;
}
$ShipResult = mysql_query("SELECT C.ForShort,S.OrderPO,M.OrderDate,S.POrderId,S.Qty,S.sgRemark,
S.DeliveryDate,S.ShipType,P.cName,P.eCode,P.TestStandard,P.pRemark,U.Name AS Unit,G.StockId, 
G.OrderQty,G.Price,PI.Leadtime
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE 1 AND S.Estate=1 AND T.TypeId='$TypeId' $OrderSort",$link_id);
echo"
<table width=\"98%\" cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr align=\"center\" bgcolor=\"#33CCCC\">
<td height=\"20\" width=\"40\">序号</td>
<td width=\"80\" onClick=\"OrderSort(1,$TypeId,$RowId)\">客户</td>
<td width=\"130\" onClick=\"OrderSort(2,$TypeId,$RowId)\">PO</td>
<td onClick=\"OrderSort(3,$TypeId,$RowId)\">产品名称</td>
<td width=\"150\">Product Code</td>
<td width=\"80\" onClick=\"OrderSort(4,$TypeId,$RowId)\">PI交期</td>
<td width=\"60\">订单数量</td>
<td width=\"70\">未生产数量</td>
<td width=\"60\">生产工价</td>
<td width=\"70\">未生产总值</td>
</tr>";
$Sum_Amount=0;
$Sum_Qty=0;
$Sum_OrderQty=0;
if($ShipRow=mysql_fetch_array($ShipResult)){
	$i=1;
	do{
		$ForShort=$ShipRow["ForShort"];
		$OrderPO=$ShipRow["OrderPO"];
		$OrderDate=$ShipRow["OrderDate"];
		$POrderId=$ShipRow["POrderId"];
		$Qty=$ShipRow["Qty"];
		$sgRemark=$ShipRow["sgRemark"];
		$Leadtime=$ShipRow["Leadtime"];
		
		$cName=$ShipRow["cName"];
		$eCode=$ShipRow["eCode"];
		$TestStandard=$ShipRow["TestStandard"];
		$pRemark=$ShipRow["pRemark"];
		$Unit=$ShipRow["Unit"];
		include "../admin/Productimage/getPOrderImage.php";
		//计算该订单未生产的数量
		$StockId=$ShipRow["StockId"];
		$OrderQty=$ShipRow["OrderQty"];
		$Price=sprintf("%.2f",$ShipRow["Price"]);
	
		//已生产的数量
		$checkOverSql=mysql_fetch_array(mysql_query("
		SELECT IFNULL(SUM(S.Qty),0) AS scQty
			FROM $DataIn.sc1_cjtj S
			WHERE 1 AND S.TypeId='$TypeId' AND S.POrderId='$POrderId'
		",$link_id));
		$scQty=sprintf("%.0f",$checkOverSql["scQty"]);
		$unScQty=$OrderQty-$scQty;
		$Amount=$Price*$unScQty;
		$OrderQty-=$scAmount;
		if($unScQty>0){
			$Sum_Amount+=$Amount;
			$Sum_OrderQty+=$OrderQty;
			$Sum_Qty+=$Qty;
			$DivNum=$predivNum."b".$i;
			$TempId=$POrderId;
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,\"$TempId\",\"desk_deliverydatecount_b1\",0);' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<tr id='HideTable_$DivNum$i' style='display:none' bgcolor='#B7B7B7'>
					<td  colspan=\"10\">
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>";
			echo"
			<tr bgcolor=\"#FFFFFF\">
			<td align=\"center\" height=\"20\">$showPurchaseorder $i</td>
			<td>$ForShort</td>
			<td>$OrderPO</td>
			<td>$TestStandard</td>
			<td>$eCode</td>
			<td align=\"center\">$Leadtime</td>
			<td align=\"right\">$Qty</td>
			<td align=\"right\">$unScQty</td>
			<td align=\"right\">$Price</td>
			<td align=\"right\">$Amount</td>
			</tr>
			";
			echo $HideTableHTML;//配件需求单明细
			$i++;
			}
		}while($ShipRow=mysql_fetch_array($ShipResult));
	echo "
	<tr bgcolor=\"#33CCCC\">
	<td align=\"center\" height=\"20\">合计</td>
	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align=\"right\">$Sum_Qty</td>
	<td align=\"right\">$Sum_OrderQty</td>
	<td>&nbsp;</td>
	<td align=\"right\">$Sum_Amount</td>
	</tr>
	";
	}
echo"</table>";
?>