<?php
//2	客户退款支出				OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.djAmount,A.Payee,A.Receipt,A.Checksheet,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw1_tkoutmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE  A.PayDate='$PayDate' AND A.Remark LIKE '$Id_Remark%'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>已付订金</td>
<td width='60' class='A1101'>已抵扣款</td>
<td width='60' class='A1101'>实付金额</td>
<td width='80' class='A1101'>结付银行</td>
<td width='30' class='A1101'>序号</td>
<td width='100' class='A1101'>采购流水号</td>
<td class='A1101'>配件名称</td>
<td width='55' class='A1101'>订单数量</td>
<td width='55' class='A1101'>采购数量</td>
<td width='45' class='A1101'>单价</td>
<td width='55' class='A1101'>金额</td>
<td width='50' class='A1101'>未收数</td>
<td width='50' class='A1101'>未补数</td>
<td width='70' class='A1101'>出货日期</td>
<td width='50' class='A1101'>请款月份</td>
</tr>";
$i=1;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		//**********
		//结付主表数据
		$Mid=$checkRow["Id"];
		$PayDate=$checkRow["PayDate"];
		$djAmount=$checkRow["djAmount"];
		$PayAmount=$checkRow["PayAmount"];
		$BankName=$checkRow["Title"];
		$ImgDir="download/cwfk/";
		$Payee=$checkRow["Payee"];
		include "../model/subprogram/cw0_imgview.php";		
		$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
			
		$checkSheetSql=mysql_query("SELECT S.Id,S.StockId,S.POrderId,S.StuffId,S.Qty,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.Amount,S.Month,D.StuffCname,U.Name AS UnitName,H.Date as OutDate
			FROM $DataIn.cw1_tkoutsheet S
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
			LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
			LEFT JOIN $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
			LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=C.Mid	
			WHERE  S.Mid='$Mid'
			",$link_id);
		if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
			//计算子记录数量
			$Rowspan=mysql_num_rows($checkSheetSql);
			//输出首行前段
			echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
			echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
			echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
			echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$djAmount</td>";							//订金总额
			echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$KKAmount</td>";						//供应商扣款
			echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
			echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
			$j=1;
			do{
				//结付明细数据
				$Id=$checkSheetRow["Id"];
				$StockId=$checkSheetRow["StockId"];
				$POrderId=$checkSheetRow["POrderId"];
				$StuffCname=$checkSheetRow["StuffCname"];
				$Qty=$checkSheetRow["Qty"];
				$Price=$checkSheetRow["Price"];
				$UnitName=$checkSheetRow["UnitName"]==""?"&nbsp;":$checkSheetRow["UnitName"];
				$OrderQty=$checkSheetRow["OrderQty"];
				$StockQty=$checkSheetRow["StockQty"];
				$AddQty=$checkSheetRow["AddQty"];
				$FactualQty=$checkSheetRow["FactualQty"];
				$CompanyId=$checkSheetRow["CompanyId"];
				$BuyerId=$checkSheetRow["BuyerId"];
				$Amount=$checkSheetRow["Amount"];
				$Month=$checkSheetRow["Month"];
				//收货情况				
				$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");
				$rkQty=$rkQty==""?0:$rkQty;
				$Mantissa=$Qty-$rkQty;
				if($Mantissa<$Qty){//如果尾数《采购数：黄色
					$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
					$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
					if($Mantissa==0){//如果尾数=0：绿色
						$Mantissa="&nbsp;";
						}
					else{
						$Mantissa="<div class='yellowB'>$Mantissa</div>";
						}
					}
				else{
					$Mantissa="<div class='redB'>$Mantissa</div>";
					}
				//未补数量计算
				$StuffId=$checkSheetRow["StuffId"];//配件ID
				$sSearch1=" AND S.StuffId='$StuffId'";
				$checkWbSql=mysql_query("
				SELECT (B.thQty-A.bcQty) AS wbQty
					FROM (
						SELECT IFNULL(SUM(S.Qty),0) AS thQty,'$StuffId' AS StuffId FROM $DataIn.ck2_thsheet S 
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
						WHERE 1 $sSearch1
						)B
					LEFT JOIN (
						SELECT IFNULL(SUM(Qty),0) AS bcQty,'$StuffId' AS StuffId FROM $DataIn.ck3_bcsheet  S
						LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
						WHERE 1 $sSearch1
						) A ON A.StuffId=B.StuffId",$link_id);
				$wbQty=mysql_result($checkWbSql,0,"wbQty");
				if($wbQty!=0){
					$wbQty="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>$wbQty</a>";
					}
				else{
					$wbQty="&nbsp;";
					}
				$OutDate=$checkSheetRow["OutDate"]==""?"&nbsp":$checkSheetRow["OutDate"];    
				if($j>1) echo "<tr>";
				echo"<td class='A0101' align='center' height='20'>$j</td>";			//序号				
				echo"<td class='A0101' align='center'>$StockId</td>";					//流水号
				echo"<td class='A0101' >$StuffCname</td>";								//配件名称
				echo"<td class='A0101' align='right'>$OrderQty</td>";				//订单数量
				echo"<td class='A0101' align='right'>$Qty</td>";							//采购数量
				echo"<td  class='A0101' align='right'>$Price</td>";						//单价			
				echo"<td  class='A0101' align='right'>$Amount</td>";					//金额
				echo"<td  class='A0101' align='center'>$Mantissa</td>";				//未收数量
				echo"<td  class='A0101' align='center'>$wbQty</td>";					//未补数量
				echo"<td  class='A0101' align='center'>$OutDate</td>";				//出货日期			
				echo"<td  class='A0101' align='center'>$Month</td>";					//请款日期
				echo"</tr>";
				$j++;
				}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
			}
		$i++;echo $i;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
?>