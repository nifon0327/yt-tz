<?php   
////$DataIn.电信---yang 20120801
echo "<table width='$tableWidth' border='0' id='StuffList$i' style='display:none'>
	<tr bgcolor='#CCCCCC'>
	<td width='5'></td>
	<td width='80' align='center'>采购日期</td>
	<td width='80' align='center'>待购流水号</td>
	<td align='center'>配件名称</td>
	<td width='60' align='center'>配件价格</td>
	<td width='80' align='center'>订单需求数量</td>
	<td width='80' align='center'>使用库存数</td>
	<td width='60' align='center'>采购数量</td>
	<td width='60' align='center'>增购数量</td>
	<td width='60' align='center'>采购员</td>
	<td width='100' align='center'>供应商</td>
	<td width='60' align='center'>收货数量</td>
	<td width='60' align='center'>欠数</td>
	<td width='60' align='center'>领料数量</td>
	<td width='80' align='center'>供应商交货期</td>
	</tr>";
//需求单列表
$StockResult = mysql_query("SELECT 
	A.Id,A.StockId,A.StuffId,A.StuffPrice,A.OrderQty,A.StockQty,A.AddQty,A.FactualQty,A.DeliveryDate,A.StockRemark,
	B.StuffCname,B.Picture,C.Forshort,D.Name,E.Date
	FROM $DataIn.stockdatasheet A
	LEFT JOIN $DataIn.stuffdata B ON A.StuffId=B.StuffId 
	LEFT JOIN $DataIn.trade_object C ON A.CompanyId=C.CompanyId 
	LEFT JOIN $DataPublic.staffmain D ON D.Number=A.BuyerId 
	LEFT JOIN $DataIn.stockdatamain E ON E.Id=A.Mid 
	WHERE A.POrderId=$POrderId Order by A.Id",$link_id);
	
if($StockRows = mysql_fetch_array($StockResult)){
	do{
		$thisId=$StockRows["Id"];
		$StockId=$StockRows["StockId"];	
		$StuffId=$StockRows["StuffId"];	
		$StuffPrice=$StockRows["StuffPrice"];
		$OrderQty=$StockRows["OrderQty"];
		$StockQty=$StockRows["StockQty"];
		$AddQty=$StockRows["AddQty"];
		$FactualQty=$StockRows["FactualQty"];
		$Buyer=$StockRows["Name"];		
		$Provider=$StockRows["Forshort"];
		$DeliveryDate=$StockRows["DeliveryDate"];
		$StockRemark=$StockRows["StockRemark"];
		$StuffCname=$StockRows["StuffCname"];
		$Picture=$StockRows["Picture"];
		$Date=$StockRows["Date"];
		$QtyCount=$FactualQty+$AddQty;	
		$StuffCname=$Picture==""?"$StuffCname":"<span onClick='View(\"stufffile\",\"$Picture\")' style='CURSOR: pointer;color:#FF6633'>$StuffCname</span>";
		//供应商货币
		$gRateSheet = mysql_query("SELECT Rate FROM $DataIn.trade_object 
		LEFT JOIN $DataPublic.currencydata ON $DataPublic.currencydata.Symbol=$DataIn.trade_object.Currency WHERE $DataIn.trade_object.CompanyId=$CompanyId",$link_id);
		//供应商货币
		if($gRateRow=mysql_fetch_array($gRateSheet)){
			$gRate=$gRateRow["Rate"];//汇率
			}
		$thisRMB=sprintf("%.4f",$gRate*$StuffPrice*$OrderQty/$Qty);
		$buyRMB=$buyRMB+$thisRMB;
						///////////////////////
		//需求单情况 如果有足够库存
		switch($QtyCount){
			case "0"://使用库存
				$TempColor=2;
				$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
				$Date="使用库存";
				$FactualQty="-";$AddQty="-";$Name="-";$Company="-";$IndepotQty="-";$UntreadQty="-";$ExchangeoutQty="-";$ExchangeinQty="-";$Mantissa="-";$DeliveryDate="-";
				break;
			default:
				if($Date==""){//未下采购单
					$Date="未下采购单";
					$TempColor=0;
					$ordercolor=0;
					$outdepotsheet_Qty="-";$IndepotQty="-";$UntreadQty="-";$ExchangeoutQty="-";$ExchangeinQty="-";$Mantissa="-";$DeliveryDate="-";
					}
				else{//已下采购单
					//最后收货日期.需采购更新
					$TempColor=2;
					$ReceiveDate=$StockRows["ReceiveDate"];
					//收货情况				
					$Receive_Temp=mysql_query("SELECT SUM(Qty) AS abc FROM $DataIn.indepotsheet where Sid=$Sid order by Sid",$link_id);; 
					$IndepotQty=mysql_result($Receive_Temp,0,"abc");
					$IndepotQty=$IndepotQty==""?0:$IndepotQty;
					//尾数
					$Mantissa=$QtyCount-$IndepotQty;
					//如果收货数量大于或等于采购总数,或使用库存数与订单需求数一致,则行的着色为绿色$bgcolor="#339900";
					}
				if($ReceiveDate=="0000-00-00"){	$ReceiveDate="未确定";}
					break;
					}
				//领料总数
				$outdepotsheet_Temp=mysql_query("SELECT SUM(Qty) AS l1 FROM $DataIn.outdepotsheet where StockId='$StockId' order by StockId",$link_id); 
				$outdepotsheet_Qty=mysql_result($outdepotsheet_Temp,0,"l1");
				$outdepotsheet_Qty=$outdepotsheet_Qty==""?0:$outdepotsheet_Qty;
		
				//已退料总数
				$returnedQty_Temp=mysql_query("SELECT SUM(Qty) AS a2 FROM $DataIn.outreturnsheet WHERE StockId='$StockId' order by StockId",$link_id); 
				$returnedCircs=mysql_result($returnedQty_Temp,0,"a2");
				$returnedCircs=$returnedCircs==""?0:$returnedCircs;				
		
				//实际领料数量
				$outdepotsheet_Qty=$outdepotsheet_Qty-$returnedCircs;
				if($TempColor>0){
					if($outdepotsheet_Qty==$OrderQty){//订单需求
						$TempColor=2;}
					else{
						$TempColor=1;}
					}
						if($DeliveryDate!="-"){
							$DeliveryDate="<input type='text' size='10' name='gysDeliveryDate$j' value='$DeliveryDate'  class='textINPUT' onMouseOver='this.style.border=\"1px solid #7F9DB9\"' onMouseOut='this.style.border=\"none\"' onChange='ChangeThis($StockId,$j,\"gysDeliveryDate\")'>";
							$j++;
							}
													
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
						//////////////////////						
		//采单颜色标记
		echo"<tr bgcolor='#CCCCCC'><td bgcolor='$Sbgcolor'>&nbsp;</td>";//配件状态 
		echo"<td>$Date</td>";
		echo"<td>$StockId</td>";//配件采购流水号
		echo"<td>$StuffCname</td>";//配件名称
		echo"<td><div align='right'>$StuffPrice</div></td>";//配件价格
		echo"<td><div align='right'>$OrderQty<div></td>";//订单需求数量
		echo"<td><div align='right'>$StockQty<div></td>";//使用库存数
		echo"<td><div align='right'>$FactualQty</div.</td>";//采购数量
		echo"<td><div align='right'>$AddQty</div></td>";//增购数量
		echo"<td>$Name</td>";//采购员
		echo"<td>$Forshort</td>";//供应商
		echo"<td>$IndepotQty</td>";//收货进度
		echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//欠数
		echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$outdepotsheet_Qty</div></td>";//领料数量
		echo"<td>$DeliveryDate</td>";//供应商交货期
		echo"</tr>";
		$ExchangeinQty="";$UntreadQty="";$Mantissa="";$ExchangeoutQty="";$outdepotsheet_Qty="";
 		}while ($StockRows = mysql_fetch_array($StockResult));
	echo"</table>";
	//订单状态颜色重置
	$k=$i;
	switch($ordercolor){
		case "0"://白色
			echo"<script>ListTable$i.rows[0].cells[0].bgColor='#FFFFFF'</script>";
			break;
		case "1"://黄色
			echo"<script>ListTable$i.rows[0].cells[0].bgColor='#FFCC00'</script>";
			break;
		case "2";//绿色
			echo"<script>ListTable$i.rows[0].cells[0].bgColor='#339900'</script>";
			break;
		}
 	}
echo"</table>";
?>