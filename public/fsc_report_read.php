<?php 
//电信-joseph
include "../model/modelhead.php";
//检查时间
$checkDate="";
if($sDate!="" || $eDate!=""){
	if($sDate!=""  && $eDate!=""){
		$checkDate=" AND M.OrderDate BETWEEN '$sDate!' AND '$eDate!'";
		$checkDate1=" AND M.Date BETWEEN '$sDate!' AND '$eDate!'";
		}
	else{
		if($sDate!="" ){//初始日期不为空
			$checkDate=" AND M.OrderDate='$sDate'";
			$checkDate1=" AND M.Date='$sDate'";
			//$checkDate=" AND M.OrderDate>='$sDate'";
			}
		else{//最后日期不为空
			$checkDate=" AND M.OrderDate='$eDate'";
			$checkDate1=" AND M.Date='$sDate'";
			//$checkDate=" AND M.OrderDate<='$eDate'";
			}
		}
	}
?>
<script LANGUAGE="JavaScript">
function window.onload() {
	factory.printing.header ="";
  	factory.printing.footer ="";
  	factory.printing.portrait = false ;//纵向,false横向
	factory.printing.leftMargin =5;
  	factory.printing.topMargin = 1.5;
  	factory.printing.rightMargin =5;
  	factory.printing.bottomMargin = 0.5;
	}
//  End -->
</script>
<body lang=ZH-CN>
<object id="factory" viewastext  style="display:none"
  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  codebase="http://www.middlecloud.com/basic/smsx.cab#Version=6,2,433,70">
</object>
<form name="form1" method="post" action="?">

<table cellpadding="2"  cellspacing="0">
  <tr valign="top">
   	<td colspan="14" scope="col" align="center">FSC汇总表</td>
  </tr>
  <tr align="right">
   	<td colspan="5" align="left" class="A0100">日期：<INPUT name=sDate class=textfield id="sDate" style="width:120px;" value="<?php echo $sDate;?>" onFocus="WdatePicker()" readonly> 至 <INPUT name=eDate class=textfield id="eDate" style="width:120px;" value="<?php echo $eDate;?>" onFocus="WdatePicker()" readonly>
    <input type="submit" name="submit" id="submit" value="查询" onClick=""  style='font-size: 12px;background-color: #48bbcb;color: #ffffff;border: 0;line-height: 20px;height: 20px;width: 50px'></td>
   	<td colspan="9" align="right" class="A0100">PAGE-1</td>
  </tr>
  <tr align="center">
   	<td width="80" bgcolor="#CCCCCC" class="A0111">下单日期</td>    
    	<td width="100" bgcolor="#CCCCCC" class="A0101">订单PO</td>
    	<td width="380" bgcolor="#CCCCCC"  class="A0101">产品名称</td>
		<td width="65" bgcolor="#CCCCCC"  class="A0101">订单数量</td>
		<td width="260" class="A0101">彩盒编号</td>
    	<td width="65" class="A0101">使用库存</td>
		<td width="65" class="A0101">购买数量</td>
    	<td width="65" class="A0101">入库数量</td>
	 	<td width="65" class="A0101">领料数量</td>
     	<td width="100" class="A0101">Invoice</td>
     	<td width="120" class="A0101">供应商发票号码</td>
     	<td width="65" class="A0101">成品库存</td>
     	<td width="65" class="A0101">销售数量</td>
  </tr><!--<td width="65" class="A0101">库存数量</td>-->
	<?php 
	$mySql="SELECT A.* FROM (
	SELECT M.OrderDate,S.OrderPO,S.POrderId,S.ProductId,S.Qty,P.cName,D.StuffCname,G.OrderQty,G.StockQty,(G.AddQty+G.FactualQty) AS CgQty,G.StuffId,G.Id,G.StockId 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber  
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata	D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
	WHERE 1 $checkDate AND P.cName LIKE '%Orange%' AND D.StuffCname LIKE '%FSC%' 
UNION ALL
	SELECT M.Date AS OrderDate,'特采单' AS OrderPO,'-' AS POrderId,'-' AS ProductId,'0' AS Qty, '' AS cName,D.StuffCname,G.OrderQty,G.StockQty,(G.AddQty+G.FactualQty) AS CgQty,G.StuffId,G.Id,G.StockId  
	FROM $DataIn.cg1_stocksheet G 
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
	LEFT JOIN $DataIn.stuffdata	D ON D.StuffId=G.StuffId
	WHERE 1 $checkDate1 AND G.Mid>0 AND D.StuffCname LIKE '%FSC%' ) A GROUP BY A.Id ORDER BY A.OrderDate desc,A.OrderPO,A.POrderId ";
	//echo $mySql . "<br>";
	$checkSql= mysql_query($mySql,$link_id);

	$i=1;
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$OrderDate=$checkRow["OrderDate"];
			$OrderPO=$checkRow["OrderPO"];

			$ProductId=$checkRow["ProductId"];
			$Qty=$checkRow["Qty"];	$SUM_Qty+=$Qty;
			$cName=$checkRow["cName"];
			$StuffCname=$checkRow["StuffCname"];
			$StuffId=$checkRow["StuffId"];
			$OrderQty=$checkRow["OrderQty"];$SUM_OrderQty+=$OrderQty;
			$StockQty=$checkRow["StockQty"];$SUM_StockQty+=$StockQty;
			$CgQty=$checkRow["CgQty"];
			$SUM_CgQty+=$CgQty;
			$POrderId=$checkRow["POrderId"];
			$StockId=$checkRow["StockId"];
			//以下需要通过PO和产品ID来计算
			//入库数量计算
			$checkRkSql= mysql_query("
			SELECT IFNULL(SUM(R.Qty),0) AS Qty
			FROM $DataIn.cg1_stocksheet G  
			LEFT JOIN $DataIn.ck1_rksheet R  ON G.StockId=R.StockId
			WHERE 1 AND G.StockId='$StockId'",$link_id);
			$RkQty=mysql_result($checkRkSql,0,"Qty");
			$SUM_RkQty+=$RkQty;
			//领料数量计算出货数量计算
			if ($POrderId!="-"){
				$checkChSql= mysql_query("
				SELECT IFNULL(SUM(S.Qty),0) AS Qty,CM.InvoiceNO
				FROM $DataIn.yw1_ordersheet S
				LEFT JOIN $DataIn.ch1_shipsheet CS ON CS.POrderId=S.POrderId
				LEFT JOIN $DataIn.ch1_shipmain CM ON CM.Id=CS.Mid
				WHERE 1 AND S.ProductId='$ProductId' AND S.POrderId='$POrderId' AND S.Estate=0",$link_id);
				if($checkChRow=mysql_fetch_array($checkChSql)){
					$ChQty=$checkChRow["Qty"];
					$SUM_ChQty+=$ChQty;
					$InvoiceNO=$checkChRow["InvoiceNO"];
					}
			}else{
				$ChQty=0;
				$InvoiceNO="";
				
				$checkChSql= mysql_query("
				SELECT IFNULL(SUM(S.Qty),0) AS Qty,CM.InvoiceNO,P.cName,S.OrderPO,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
				FROM $DataIn.yw1_ordersheet S
				LEFT JOIN $DataIn.ch1_shipsheet CS ON CS.POrderId=S.POrderId
				LEFT JOIN $DataIn.ch1_shipmain CM ON CM.Id=CS.Mid
				LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
				LEFT JOIN $DataIn.cg1_stocksheet  G ON G.POrderId=S.POrderId AND G.StuffId='$StuffId' 
				WHERE 1 AND G.StuffId='$StuffId'  AND S.Estate=0",$link_id);
				if($checkChRow=mysql_fetch_array($checkChSql)){
					$ChQty=$checkChRow["Qty"];
					$Qty=$checkChRow["OrderQty"];
					$SUM_ChQty+=$ChQty;
					$InvoiceNO=$checkChRow["InvoiceNO"];
					$OrderPO=$checkChRow["OrderPO"];
					$cName=$checkChRow["cName"];
					}
			}
			
			//库存数量计算
			//$KcQty=$RkQty-$ChQty;
			$KcQty=$RkQty-$ChQty+$StockQty;
			$SUM_KcQty+=$KcQty;
			//$RkQty=zerotospace($RkQty);
			//$ChQty=zerotospace($ChQty);
			//$KcQty=zerotospace($KcQty);
			//$RkQty=zerotospace($RkQty);

			if($OrderPO == '31553'){
				continue;
			}

			echo"<tr>";
			echo"<td class=\"A0111\" align=\"center\">$OrderDate</td>";		//订单日期
			echo"<td class=\"A0101\">$OrderPO</td>";						//订单PO
			echo"<td class=\"A0101\">$cName&nbsp;</td>";							//产品名称
			echo"<td class=\"A0101\" align=\"right\">$Qty&nbsp;</td>";		//订单数量
			echo"<td class=\"A0101\">$StuffCname</td>";							//配件名称
			echo"<td class=\"A0101\" align=\"right\">$StockQty&nbsp;</td>";		//使用库存
			echo"<td class=\"A0101\" align=\"right\">$CgQty&nbsp;</td>";		//采购数量
			echo"<td class=\"A0101\" align=\"right\">$RkQty&nbsp;</td>";		//入库数量
			echo"<td class=\"A0101\" align=\"right\">$ChQty&nbsp;</td>";		//领实数量
			//echo"<td class=\"A0101\" align=\"right\">$KcQty&nbsp;</td>";		//可用库存
			echo"<td class=\"A0101\" align=\"center\">$InvoiceNO&nbsp;</td>";	//出货文件
			echo"<td class=\"A0101\">&nbsp;</td>";								//发票号码
			echo"<td class=\"A0101\" align=\"right\">0&nbsp;</td>";				//成品库存
			echo"<td class=\"A0101\" align=\"right\">$ChQty&nbsp;</td>";		//出货数量
			echo"</tr>";
			
			$i++;
			}while($checkRow=mysql_fetch_array($checkSql));
		$SUM_CgQty+=484;		$SUM_RkQty+=484;		
		echo"<tr bgcolor='#CCCCCC'>";
			echo"<td class=\"A0111\" align=\"center\" colspan=\"3\">合 计</td>";		//订单日期
			echo"<td class=\"A0101\" align=\"right\">$SUM_Qty&nbsp;</td>";		//订单数量
			echo"<td class=\"A0101\">&nbsp;</td>";											//配件名称
			echo"<td class=\"A0101\" align=\"right\">$StockQty&nbsp;</td>";		//使用库存//$SUM_StockQty
			echo"<td class=\"A0101\" align=\"right\">$SUM_CgQty&nbsp;</td>";		//采购数量
			echo"<td class=\"A0101\" align=\"right\">$SUM_RkQty&nbsp;</td>";		//入库数量
			echo"<td class=\"A0101\" align=\"right\">$SUM_ChQty&nbsp;</td>";		//领实数量
		//	echo"<td class=\"A0101\" align=\"right\">$KcQty&nbsp;</td>";		//可用库存//$SUM_KcQty
			echo"<td class=\"A0101\" align=\"center\">&nbsp;</td>";	//出货文件
			echo"<td class=\"A0101\">&nbsp;</td>";								//发票号码
			echo"<td class=\"A0101\" align=\"right\">0&nbsp;</td>";				//成品库存
			echo"<td class=\"A0101\" align=\"right\">$SUM_ChQty&nbsp;</td>";		//出货数量
			echo"</tr>";
		}
	else{
		echo"<tr>";
			echo"<td class=\"A0111\" align=\"center\" colspan=\"14\">没有找到符合条件的记录</td>";
		}
	?>
</table>
</form>
</body>
</html>