<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//需求单列表
$TempArray=explode("|",$TempId);
$StuffId=$TempArray[0];
$BuyerId=$TempArray[1];
$CompanyId=$TempArray[2];
$rkSign=$TempArray[3];
if($BuyerId!=""){
	$BuyerSTR="and S.BuyerId='$BuyerId'";
	}
if($CompanyId!=""){
	$CompanySTR=" and S.CompanyId='$CompanyId'";
	}
		
	echo"<table id='ListTable$StuffId' cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='timeTop'>
		<td align='center' class='A1111' width='38' height='20'>序号</td>
		<td align='center' class='A1101' width='120'>流水号</td>
		<td align='center' class='A1101' width='80'>交货日期</td>
		<td align='center' class='A1101' width='80'>原  交  期</td>
		<td align='center' class='A1101' width='80'>采购总数</td>
		<td align='center' class='A1101' width='80'>已收数量</td>
		<td align='center' class='A1101' width='80'>未收数量</td>
		</tr>";

		$mySql="SELECT M.Date,M.PurchaseID,S.Mid,S.StockId,S.POrderId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.DeliveryDate  
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M  ON M.Id=S.Mid
		WHERE 1 and S.StuffId='$StuffId' $BuyerSTR  $CompanySTR     GROUP BY S.Id ORDER BY S.Id";
		$myResult = mysql_query($mySql."",$link_id);
		$j=1;$oldStockId="";$sumOrderQty=0;$sumStockQty=0;$sumAddQty=0;$sumRkQty=0;$sumNoQty=0;
		if($myRow = mysql_fetch_array($myResult)){
			do{
				$Date=$myRow["Date"];
				$StockId=$myRow["StockId"];
				if($oldStockId!=$StockId)  //相同的不显示，会重得2011-11-05
				{
					$oldStockId=$oldStockId; 
					$POrderId=$myRow["POrderId"];
					$OrderQty=$myRow["OrderQty"];		$sumOrderQty+=$OrderQty;
					$StockQty=$myRow["StockQty"];		$sumStockQty+=$StockQty;
					$AddQty=$myRow["AddQty"];			
					$FactualQty=$myRow["FactualQty"];	
					$DeliveryDate=$myRow["DeliveryDate"];
					$PurchaseID=$myRow["PurchaseID"];
					$Mid=$myRow["Mid"];
					
					$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
				   $PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
					$Qty=$FactualQty+$AddQty;			
					//已收货总数
					$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R,$DataIn.cg1_stocksheet S WHERE 
					S.StockId=R.StockId 
					$BuyerSTR $CompanySTR 
					and R.StockId='$StockId'",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");
					$rkQty=$rkQty==""?0:$rkQty;
					$noQty=$Qty-$rkQty;
					if ($noQty<=0) continue;
					
					$sumQty+=$Qty;
					$sumRkQty+=$rkQty;
					$sumNoQty+=$noQty;
					
					$OnclickStr="";$ColbgColor="";
			     	if($DeliveryDate=="0000-00-00"){
					       $DeliveryDateShow="<span class='yellowN'>未设置</div>";
						   $OnclickStr="onclick='updateJq($StuffId,$j,$StockId)' style='CURSOR: pointer;'";
						   $ColbgColor=" bgcolor='#F8E700' ";
					    }
					else{
						$SetDate=CountDays($DeliveryDate,5);
						if($SetDate>-1){		//离交期不大于一天，为红色
							$DeliveryDateShow="<span class='redB'>".$DeliveryDate."</span>";
							}
						else{
							if($SetDate>-5){
								$DeliveryDateShow="<span class='yellowB'>".$DeliveryDate."</span>";
								}
							else{
								$DeliveryDateShow="<span class='greenB'>".$DeliveryDate."</span>";
								}
							}
				}
				
					//入库记录
					$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
					$rkList="<a href='../admin/ck_rk_list.php?Sid=$Sid' target='_black'>$StockId</a>";
					
					//原交货日期
					$CheckOldDate=mysql_query("SELECT DeliveryDate FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' AND DeliveryDate>'2010-01-01'  ORDER BY Id DESC LIMIT 1",$link_id);
					if($oldDateRow = mysql_fetch_array($CheckOldDate)){
					       $OldDeliveryDate=$myRow["DeliveryDate"]!=$oldDateRow["DeliveryDate"]?"<span class='redB'>".$oldDateRow["DeliveryDate"]."</span>":"&nbsp;";
					}
					else{
						   $OldDeliveryDate="&nbsp;";
					}
					
					echo"<tr>
							<td align='center' class='A0111' height='20'>$j</td>
							<td align='center' class='A0101'>$rkList</td>
							<td align='center' class='A0101' >$DeliveryDateShow</td>
							<td align='right' class='A0101'>$OldDeliveryDate</td>
							<td align='right' class='A0101'>$Qty</td>
							<td align='right' class='A0101'>$rkQty</td>
							<td align='right' class='A0101'><div class='redB'>$noQty</div></td></tr>";
							$j++;
							
				}
			}while ($myRow = mysql_fetch_array($myResult));
	}
	
	echo"<tr>
				<td align='center' class='A0111' height='20' colspan='4'>合计</td>
				<td align='right' class='A0101'>$sumQty</td>
				<td align='right' class='A0101'>$sumRkQty</td>
				<td align='right' class='A0101'><div class='redB'>$sumNoQty</div></td></tr>";
		echo"<table>";
?>