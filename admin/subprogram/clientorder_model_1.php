<?php   
//需求单列表$DataIn.电信---yang 20120801
$StockResult = mysql_query("SELECT 
	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
	M.Date,A.StuffCname,A.Picture,B.Name,C.Forshort
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
	LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
	WHERE S.POrderId='$POrderId' ORDER BY S.StockId",$link_id);
if ($StockRows = mysql_fetch_array($StockResult)) {
	do{
		//初始化
		$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;		
		$thisId=$StockRows["Id"];
		$StockId=$StockRows["StockId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$Forshort=$StockRows["Forshort"];
		$Buyer=$StockRows["Name"];
		$OrderQty=$StockRows["OrderQty"];
		$StockQty=$StockRows["StockQty"];
		$FactualQty=$StockRows["FactualQty"];
		$AddQty=$StockRows["AddQty"];
		$Date=$StockRows["Date"];
		$DeliveryDate=$StockRows["DeliveryDate"];
		$StuffCname=$StockRows["Picture"]==""?"$StuffCname":"<span onClick='View(\"stufffile\",\"$StockRows[Picture]\")' style='CURSOR: pointer;color:#FF6633'>$StuffCname</span>";
		//需求单情况 如果有足够库存
		if($FactualQty==0 && $AddQty==0){
			$TempColor=1;
			$Date="使用库存";
			$FactualQty="-";$AddQty="-";$Buyer="-";$Forshort="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
			}
		else{
			if($Date==""){//未下采购单
				$Date="未下采购单";
				$TempColor=0;
				$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
				}
			else{//已下采购单
				//最后收货日期.需采购更新
				$TempColor=1;
				$ReceiveDate=$StockRows["ReceiveDate"];
				//收货情况				
				$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);; 
				$rkQty=mysql_result($rkTemp,0,"Qty");
				$rkQty=$rkQty==""?0:$rkQty;
				$Mantissa=$FactualQty+$AddQty-$rkQty;
				//如果收货数量大于或等于采购总数,或使用库存数与订单需求数一致,则行的着色为绿色$bgcolor="#339900";
				}
			if($DeliveryDate=="0000-00-00"){$DeliveryDate="未确定";}
			}
		//领料总数
		$llTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet where StockId='$StockId' order by StockId",$link_id); 
		$llQty=mysql_result($llTemp,0,"Qty");
		$llQty=$llQty==""?0:$llQty;
		if($TempColor>0){
			if($llQty==$OrderQty){//订单需求
				$TempColor=2;}
			else{
				$TempColor=1;}
			}
		//采单颜色标记
		switch($TempColor){
			case "0"://白色
				$Sbgcolor="#FFFFFF";
				$ordercolor=0;
				break;
			case "1"://黄色
				$Sbgcolor="#FFCC00";
				$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
				break;
			case "2";//绿色
				$Sbgcolor="#339900";
				$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
				break;
				}

		echo"<tr bgcolor=#EAEAEA>
		<td bgcolor='$Sbgcolor'>&nbsp;</td>";//配件状态 
		echo"<td  align='center'>$Date</td>";
		echo"<td  align='center'>$StockId</td>";//配件采购流水号
		echo"<td>$StuffCname</td>";//配件名称
		echo"<td align='right'>$Price</td>";//配件价格
		echo"<td align='right'>$OrderQty</td>";//订单需求数量
		echo"<td align='right'>$StockQty</td>";//使用库存数
		echo"<td align='right'>$FactualQty</td>";//采购数量
		echo"<td align='right'>$AddQty</td>";//增购数量
		echo"<td  align='center'>$Buyer</td>";//采购员
		echo"<td >$Forshort</td>";//供应商
		echo"<td >$rkQty</td>";//收货进度
		echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//欠数
		echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$llQty</div></td>";//领料数量
		echo"<td align='center'>$DeliveryDate</td>";//供应商交货期
		echo"</tr>";		
 		}while ($StockRows = mysql_fetch_array($StockResult));	
	}echo"</table>";
$orderRow=$i-1;
switch($ordercolor){
	case "0"://白色
		echo"<script>ListTable$orderRow.rows[0].cells[1].bgColor='#FFFFFF'</script>";
		break;
	case "1"://黄色
		echo"<script>ListTable$orderRow.rows[0].cells[1].bgColor='#FFCC00'</script>";
		break;
	case "2";//绿色
		echo"<script>ListTable$orderRow.rows[0].cells[1].bgColor='#339900'</script>";
		break;
	}
?>