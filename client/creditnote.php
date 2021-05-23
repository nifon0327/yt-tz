<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch1_shipmain 
$DataIn.ch1_shipmain 
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.ch2_packinglist
$DataIn.ch1_shipsheet
$DataIn.ch4_freight
$DataIn.cw6_orderinsheet
$DataIn.cw6_orderinmain
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=550;
ChangeWtitle("$SubCompany Credit Note");
$funFrom="shipment";
$nowWebPage=$funFrom."_read";
$Th_Col="&nbsp;|50|NO.|40|DeliveryDate|140|InvoiceNO|200|Shipping Mark|100|Amount|100|T/T|100|Cartons|100|WG|100";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
$sumCols=$sumCol==""?"5":("".$sumCol);			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份数组
	$SearchRows=" and M.Estate='0' AND M.Sign='-1' AND M.CompanyId=$myCompanyId";	
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{			
			$dateValue=date("M-y",strtotime($dateRow["Date"]));
			$StartDate=date("Y-m-01",strtotime($dateRow["Date"]));
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
echo $CencalSstr;
//步骤5：
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,C.Forshort,D.Rate 
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
WHERE 1 $SearchRows ORDER BY M.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
//合计初始化
$AmountSUM=0;
$rmbAmountSUM=0;
$BoxQtySUM=0;
$mcWGSUM=0;
$depotChargeSUM=0;
$FreightSUM=0;
$cwAmountSUM=0;
	do{
		$OrderSignColor="";
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Rate=$myRow["Rate"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='../admin/ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>View</a>";
			}
		//Invoice查看
		//加密参数
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
		$InvoiceFile=$InvoiceFile==0?$InvoiceNO:"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNO</span>";
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		//$Days=CountDays($Date,0)==0?"Today":CountDays($Date,0);
		$Date=date("d-M-Y",strtotime($Date));
		$Locks=$myRow["Locks"];		//出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"]);
		$AmountSUM=sprintf("%.2f",$AmountSUM+$Amount);
		$rmbAmount=sprintf("%.2f",$Rate*$Amount);
		$rmbAmountSUM=sprintf("%.2f",$rmbAmountSUM+$rmbAmount);
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//中港运费处理
		$BoxQty="&nbsp;";$mcWG="&nbsp;";$depotCharge="&nbsp;";$Freight="&nbsp;";
		$checkFreight=mysql_query("SELECT BoxQty,mcWG,depotCharge,mcWG*Price AS Amount FROM $DataIn.ch4_freight WHERE chId='$Id' LIMIT 1",$link_id);
		if($FreightRow=mysql_fetch_array($checkFreight)){
			$BoxQty=$FreightRow["BoxQty"];
			$mcWG=$FreightRow["mcWG"];
			$depotCharge=$FreightRow["depotCharge"];
			$Freight=sprintf("%.2f",$FreightRow["Amount"]);
			$BoxQtySUM+=$BoxQty;
			$mcWGSUM+=$mcWG;
			$depotChargeSUM+=$depotCharge;
			$FreightSUM+=$Freight;
			}
		//已收款
		$chId=$mainRows["chId"];
		$checkShipAmount=mysql_query("SELECT SUM(S.Amount) AS ShipAmount,concat(M.Remark) AS Remark 
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$Id' GROUP BY S.chId",$link_id);
		$Remark=mysql_result($checkShipAmount,0,"Remark");
		$Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' alt='$Remark' width='18' height='18'>";
		$ShipAmount=mysql_result($checkShipAmount,0,"ShipAmount");
		$ShipAmount=$ShipAmount==""?"&nbsp;":sprintf("%.2f",$ShipAmount);
		if($ShipAmount!="&nbsp;"){
			$cwAmountSUM=sprintf("%.2f",$cwAmountSUM+$ShipAmount);
			if($Amount==$ShipAmount){
				$ShipAmount="<span class='greenB'>$ShipAmount</span>";
				$OrderSignColor="bgColor='#339900'";
				}
			else{
				$ShipAmount="<span class='yellowB'>$ShipAmount</span>";
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
			
		$ValueArray=array(
			array(0=>$Date, 			1=>"align='center'"),
			array(0=>$InvoiceFile),
			array(0=>$BoxLable,		1=>"align='center'"),
			array(0=>$Amount, 		1=>"align='right'"),
			array(0=>$Remark, 		1=>"align='center'"),			
			array(0=>$BoxQty,		1=>"align='center'"),
			array(0=>$mcWG,			1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	$mcWGSUM=sprintf("%.2f",$mcWGSUM);
	echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr>
		<td height='25' class='A0111' align='center'>TOTAL</td>
		<td class='A0101' align='right' width='100'>$AmountSUM</td>
		<td class='A0101' align='right' width='100'>&nbsp;</td>
		<td class='A0101' align='center' width='100'>$BoxQtySUM</td>
		<td class='A0101' align='right' width='100'>$mcWGSUM</td>
		</tr></table>";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
echo"<input name='sumAmount' type='hidden' id='sumAmount'>";
?>