<?
//非BOM货款 ewen 2013-09-03 加入凭证连接
$MonthSTR=$Month==""?"":" AND A.Month='$Month'";
$PayMonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
$QkMonthSTR=$Month==""?"":" AND DATE_FORMAT(A.qkDate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录";	$EstateSTR=" AND A.Estate='0'"; break;
	case "W":$DataTSTR="未结付记录";$EstateSTR=" AND A.Estate='3'"; break;
	case "A":$DataTSTR="全部记录";$EstateSTR=" AND (A.Estate='0' OR A.Estate='3')"; break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND E.mainType IN($Parameters)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>";
	echo"
	<tr><td height='450px'>";
		//****************************************
		echo"<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td width='50' height='20' class='A1011'>序号</td>
		<td width='60' class='A1001'>请款日期</td>
		<td width='60' class='A1001'>采购单号</td>
		<td width='80' class='A1001'>供应商</td>
		<td width='100' class='A1001'>费用名称</td>
		<td width='200' class='A1001'>费用说明</td>
		<td width='60' class='A1001'>采购数量</td>
		<td width='60' class='A1001'>单价</td>
		<td width='50' class='A1001'>状态</td>
		<td width='50' class='A1001'>货币</td>
		<td width='80' class='A1001'>金额</td>
		<td width='80' class='A1001'>转RMB金额</td>
		</tr>
		<tr>
		<td colspan='11' height='450px'>
		<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
		<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:450px' align='center'>
		";
		
		//读取记录
		/*
		$checkSql=mysql_query("
			SELECT  A.Date,DATE_FORMAT(A.Date,'%Y-%m') as Month,A.Id,'1' AS TypeName,A.Content  AS Remark,'' as Qty,'' as Price,A.Amount,(A.Amount*C.Rate) AS AmountRMB,A.Estate,'' AS Forshort,C.Symbol,A.Bill
										  FROM $DataIn.hzqksheet A
										  LEFT JOIN $DataPublic.adminitype B ON B.TypeId=A.TypeId
										  LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
										  WHERE  A.TypeId='681' AND A.Date>='2008-07-01'   $PayMonthSTR  $EstateSTR
		UNION ALL							  
			SELECT A.Date,DATE_FORMAT(A.Date,'%Y-%m') as Month,A.Id,'2' AS TypeName,CONCAT(E.Forshort,'-',D.ItemName,':<br>',A.Description) AS Remark,'' as Qty,'' as Price,A.Amount,IFNULL(A.Amount*C.Rate,0) AS AmountRMB,A.Estate,A.Provider AS Forshort,C.Symbol,A.Bill 
			FROM $DataIn.cwdyfsheet  A
			LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
			LEFT JOIN $DataIn.producttest D ON D.ItemId=A.ItemId
			LEFT JOIN $DataIn.trade_object E ON E.CompanyId=D.CompanyId
			WHERE 1 $PayMonthSTR $EstateSTR 
		UNION ALL
			  SELECT A.qkDate as Date,DATE_FORMAT(A.qkDate,'%Y-%m') as Month,A.Id,B.TypeName,A.Remark,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,(A.Qty*A.Price) AS AmountRMB,A.Estate,C.cName AS Forshort,'RMB' as Symbol,A.Bill 
			FROM $DataIn.zw3_purchases  A
			LEFT JOIN $DataIn.zw3_purchaset B ON B.Id=A.TypeId 
			LEFT JOIN $DataIn.retailerdata C ON C.Id=A.Cid 
			WHERE 1 $QkMonthSTR $EstateSTR
			ORDER BY Date DESC 
			",$link_id);	*/
		$checkSql=mysql_query("
			SELECT  A.Date,DATE_FORMAT(A.Date,'%Y-%m') as Month,A.Id,'1' AS TypeName,A.Content  AS Remark,'' as Qty,'' as Price,A.Amount,(A.Amount*C.Rate) AS AmountRMB,A.Estate,'' AS Forshort,C.Symbol,A.Bill
										  FROM $DataIn.hzqksheet A
										  LEFT JOIN $DataPublic.adminitype B ON B.TypeId=A.TypeId
										  LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
										  WHERE  A.TypeId='681' AND A.Date>='2008-07-01'   $PayMonthSTR  $EstateSTR
		UNION ALL							  
			SELECT A.Date,DATE_FORMAT(A.Date,'%Y-%m') as Month,A.Id,'2' AS TypeName,CONCAT(E.Forshort,'-',A.ItemName,':<br>',A.Description) AS Remark,'' as Qty,'' as Price,A.Amount,IFNULL(A.Amount*C.Rate,0) AS AmountRMB,A.Estate,A.Provider AS Forshort,C.Symbol,A.Bill 
			FROM $DataIn.cwdyfsheet  A
			LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
			LEFT JOIN $DataIn.trade_object E ON E.CompanyId=A.CompanyId
			WHERE 1 $PayMonthSTR $EstateSTR 
		UNION ALL
			  SELECT A.qkDate as Date,DATE_FORMAT(A.qkDate,'%Y-%m') as Month,A.Id,B.TypeName,A.Remark,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,(A.Qty*A.Price) AS AmountRMB,A.Estate,C.cName AS Forshort,'RMB' as Symbol,A.Bill 
			FROM $DataIn.zw3_purchases  A
			LEFT JOIN $DataIn.zw3_purchaset B ON B.Id=A.TypeId 
			LEFT JOIN $DataIn.retailerdata C ON C.Id=A.Cid 
			WHERE 1 $QkMonthSTR $EstateSTR
			ORDER BY Date DESC 
			",$link_id);		
			$i=1;
		$SumAmount=0;
		if($checkRow=mysql_fetch_array($checkSql)){
			$Dir2=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
			$Dir1=anmaIn("../download/cwadminicost/",$SinkOrder,$motherSTR);
			$Dir3=anmaIn("download/zwbuy/",$SinkOrder,$motherSTR);
			do{
				$Date=$checkRow["Date"];
				$Id=$checkRow["Id"];
				$Forshort=$checkRow["Forshort"];
				$Remark=$checkRow["Remark"];
				$TypeName=$checkRow["TypeName"];
				$Bill=$checkRow["Bill"];//凭证
				$Amount=sprintf("%.2f",$checkRow["Amount"]);
				$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
				$Qty=$checkRow["Qty"]==""?1:$checkRow["Qty"];
				$Price=$checkRow["Price"]==""?$Amount:sprintf("%.2f",$checkRow["Price"]);
				switch($TypeName){
					case 1:
						$TypeName="行政费用-刀模";
						 if($Bill==1){
							$Bill="H".$Id.".jpg";
							$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
							$Bill="<span onClick='OpenOrLoad(\"$Dir1\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$Remark</span>";
							}
						 else{
							 $Bill="&nbsp;";
						 	}
						break;
					case 2:
						$TypeName="开发费用";
						if($Bill==1){		
							$Bill="DYF".$Id.".jpg";
							$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
							$Remark="<span onClick='OpenOrLoad(\"$Dir2\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$Remark</span>";
							}
						else{
							$Bill="&nbsp;";
							}
						break;
					default:				
						if($Bill==1){
							$Bill="Z".$Id.".jpg";
							$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
							$Bill="<span onClick='OpenOrLoad(\"$Dir3\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$Remark</span>";
							}
						else{
							$Bill="&nbsp;";
							}
						break;
					}
				
				$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
				$Symbol=$checkRow["Symbol"];
				
				$SumAmount+=$AmountRMB;
				$AmountRMB=number_format($AmountRMB);
				echo"
					<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
					<td width='50' height='20' class='A0111' align='center'>$i</td>
					<td width='60' class='A0101'>$Date</td>
					<td width='60' class='A0101'>$Id</td>
					<td width='80' class='A0101'>$Forshort</td>
					<td width='100' class='A0101'>$TypeName</td>
					<td width='200' class='A0101'>$Remark</td>
					<td width='60' class='A0101' align='right'>$Qty</td>
					<td width='60' class='A0101' align='right'>$Price</td>
					<td width='50' class='A0101' align='center'>$Estate</td>
					<td width='50' class='A0101' align='center'>$Symbol</td>		
					<td width='80' class='A0101' align='right'>$Amount</td>
					<td width='80' class='A0101' align='right'>$AmountRMB</td>
					</tr>";
				$i++;
				}while($checkRow=mysql_fetch_array($checkSql));
			}
		for($j=$i;$j<27;$j++){//补空行
			echo"
			<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#FFD1DD'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111'>$j</td>
			<td width='60'  class='A0101'>&nbsp;</td>
			<td width='60'  class='A0101'>&nbsp;</td>
			<td width='80'  class='A0101'>&nbsp;</td>
			<td width='100' class='A0101'>&nbsp;</td>
			<td width='200' class='A0101'>&nbsp;</td>
			<td width='60' class='A0101'>&nbsp;</td>
			<td width='60' class='A0101'>&nbsp;</td>
			<td width='50' class='A0101'>&nbsp;</td>
			<td width='50' class='A0101'>&nbsp;</td>
			<td width='80' class='A0101'>&nbsp;</td>
			<td width='80' class='A0100'>&nbsp;</td>
			</tr>";
			}
		$SumAmount=number_format(sprintf("%.0f",$SumAmount));
		echo"</table>
		</div>
		</td>
		</tr>
		<tr bgcolor='#36C' align='center' style='Color:#FFF'>
		<td height='20' class='A0111' colspan='2'>合计</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0100'>&nbsp;</td>
		<td  class='A0101' align='right'>¥$SumAmount</td>
		</tr>
		</table>";
		//****************************************
	echo"</td></tr>";
?>