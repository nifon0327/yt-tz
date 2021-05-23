<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.ck1_rksheet
二合一已更新
*/
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
$IsWeeks=$TempArray[3];
if($BuyerId!=""){
	$BuyerSTR="and S.BuyerId='$BuyerId'";
	}
if($CompanyId!=""){
	$CompanySTR=" and S.CompanyId='$CompanyId'";
	}
	
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];
                         	
	echo"<table id='ListTable$StuffId' cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='timeTop'>
		<td align='center' class='A1111' width='38' height='20'>序号</td>
		<td align='center' class='A1101' width='120'>采购日期</td>
		<td align='center' class='A1101' width='80'>采购单号</td>
		<td align='center' class='A1101' width='120'>流水号</td>
		<td align='center' class='A1101' width='80'>交货日期</td>
		<td align='center' class='A1101' width='80'>原  交  期</td>
		<td align='center' class='A1101' width='80'>采购总数</td>
		<td align='center' class='A1101' width='80'>已收数量</td>
		<td align='center' class='A1101' width='80'>未收数量</td>
		</tr>";

		$mySql="SELECT M.Date,M.PurchaseID,S.Mid,S.StockId,S.POrderId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.DeliveryDate,
		YEARWEEK(S.DeliveryDate,1) AS Weeks,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks  
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M  ON M.Id=S.Mid
		LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId 
		LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=S.POrderId AND W.ReduceWeeks=0 
		WHERE 1 and S.Mid>0 and S.StuffId='$StuffId' $BuyerSTR $CompanySTR AND YEARWEEK(S.DeliveryDate,1)='$IsWeeks' and S.rkSign>0 GROUP BY S.Id ORDER BY S.Id";
		$myResult = mysql_query($mySql."",$link_id);
		$j=1;$oldStockId="";$sumOrderQty=0;$sumStockQty=0;$sumAddQty=0;$sumRkQty=0;$sumNoQty=0;
		if($myRow = mysql_fetch_array($myResult)){
			do{
				$Date=$myRow["Date"];
				$StockId=$myRow["StockId"];
				//$oldStockId=$oldStockId==""?$StockId:$oldStockId;
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
				    //$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
					$PurchaseIDStr="<a href='../public/PurchaseToDownload.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
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
					$CheckOldDate=mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Weeks FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
					if($oldDateRow = mysql_fetch_array($CheckOldDate)){
					       $OldDeliveryDate=$myRow["Weeks"]!=$oldDateRow["Weeks"]?"<span class='redB'>Week " . substr($oldDateRow["Weeks"], 4, 2) ."</span>":"&nbsp;";
					       $trbgcolor=" bgcolor='#7AD8D4' ";
					}
					else{
						   $OldDeliveryDate="&nbsp;";
						   $trbgcolor="";
					}
					
				 $Weeks=$myRow["Weeks"];
				  if ($Weeks>0){
			         $WeekName="Week " . substr($Weeks, 4, 2);
			 	     if ($Weeks<$curWeeks)	 $WeekName="<span class='redB'>$WeekName</span>";
			 	  }
			 	  else{
				 	  $WeekName="<div class='blueB'>待 定</div>";
			 	  }
			 	  
			 	  $ReduceWeeks=$myRow["ReduceWeeks"];
	     	      $OrderSignColor=$ReduceWeeks==0?" bgcolor='#308CC0' ":"";
				
					echo"<tr>
							<td align='center' class='A0111' height='20' $OrderSignColor>$j</td>
							<td align='center' class='A0101'>$Date</td>
							<td align='center' class='A0101'>$PurchaseIDStr</td>
							<td align='center' class='A0101'>$rkList</td>
							<td align='center' class='A0101' >$WeekName</td>
							<td align='center' class='A0101'>$OldDeliveryDate</td>
							<td align='right' class='A0101'>$Qty</td>
							<td align='right' class='A0101'>$rkQty</td>
							<td align='right' class='A0101'><div class='redB'>$noQty</div></td></tr>";
							$j++;
							
				}
			}while ($myRow = mysql_fetch_array($myResult));
	}
	
	//未收货记录
		$mySql="SELECT M.Date,M.PurchaseID,S.Mid,S.StockId,S.POrderId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.DeliveryDate,YEARWEEK(S.DeliveryDate,1) AS Weeks,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks   
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M  ON M.Id=S.Mid
		LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId 
		LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=S.POrderId AND W.ReduceWeeks=0 
		WHERE 1 AND S.Mid>0 and S.StuffId='$StuffId' 
		$BuyerSTR $CompanySTR 
		and S.rkSign>0 AND YEARWEEK(S.DeliveryDate,1)!='$IsWeeks'   and R.StockId IS NULL 
		ORDER BY S.Id";
		$myResult = mysql_query($mySql."",$link_id);
		if($myRow = mysql_fetch_array($myResult)){
			//$j=1;
			do{
				$Date=$myRow["Date"];
				$StockId=$myRow["StockId"];
				$POrderId=$myRow["POrderId"];
				$OrderQty=$myRow["OrderQty"];			$sumOrderQty+=$OrderQty;
				$StockQty=$myRow["StockQty"];			$sumStockQty+=$StockQty;
				$AddQty=$myRow["AddQty"];				
				$FactualQty=$myRow["FactualQty"];	
				$DeliveryDate=$myRow["DeliveryDate"];	
				
				$PurchaseID=$myRow["PurchaseID"];
			    $Mid=$myRow["Mid"];
					
					$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		           //$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		           $PurchaseIDStr="<a href='../public/PurchaseToDownload.php?f=$MidSTR' target='_blank'>$PurchaseID</a>"; 
				$Qty=$FactualQty+$AddQty;				
				if($POrderId==""){
					$AddQty=$FactualQty;
					$FactualQty=0;
					}
				$sumAddQty+=$AddQty;
				$sumFactualQty+=$FactualQty;
				$sumQty+=$Qty;
				$sumNoQty+=$Qty;
				
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
                 
                 //原交货日期
					$CheckOldDate=mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Weeks FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
					if($oldDateRow = mysql_fetch_array($CheckOldDate)){
					       $OldDeliveryDate=$myRow["Weeks"]!=$oldDateRow["Weeks"]?"<span class='redB'>Week " . substr($oldDateRow["Weeks"], 4, 2) ."</span>":"&nbsp;";
					       $trbgcolor=" bgcolor='#7AD8D4' ";
					}
					else{
						   $OldDeliveryDate="&nbsp;";
						   $trbgcolor="";
					}
					
				$Weeks=$myRow["Weeks"];
			   if ($Weeks>0){
			         $WeekName="Week " . substr($Weeks, 4, 2);
			 	     if ($Weeks<$curWeeks)	 $WeekName="<span class='redB'>$WeekName</span>";
			 	  }
			 	  else{
				 	  $WeekName="<div class='blueB'>待 定</div>";
			 	  }
			 	  
			   $ReduceWeeks=$myRow["ReduceWeeks"];
	     	   $OrderSignColor=$ReduceWeeks==0?" bgcolor='#308CC0' ":"";
			 	  
				echo"<tr $trbgcolor>
				<td align='center' class='A0111' height='20' $OrderSignColor>$j</td>
				<td align='center' class='A0101'>$Date</td>
				<td align='center' class='A0101'>$PurchaseIDStr</td>
				<td align='center' class='A0101'>$StockId</td>
				<td align='center' class='A0101' >$WeekName</td>
				<td align='center' class='A0101'>$OldDeliveryDate</td>
				<td align='right' class='A0101'>$Qty</td>
				<td align='right' class='A0101'>&nbsp;</td>
				<td align='right' class='A0101'><div class='redB'>$Qty</div></td></tr>";//$OnclickStr
				$j++;
				}while ($myRow = mysql_fetch_array($myResult));
			}

   $sumRkQty=$sumRkQty==0?"&nbsp;":$sumRkQty;
	echo"<tr>
				<td align='center' class='A0111' height='20' colspan='6'>合计</td>
				<td align='right' class='A0101'>$sumQty</td>
				<td align='right' class='A0101'>$sumRkQty</td>
				<td align='right' class='A0101'><div class='redB'>$sumNoQty</div></td></tr>";
		echo"<table>";
?>