<?php 
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
//============================行政费用
echo"<table id='$TableId' cellspacing='1' border='1' align='center'>
       <tr bgcolor='#CCCCCC' height='30'>
	    <td colspan='7' align='center' style='font-size:14px'>行政费用列表</td>
	    </tr>
        <tr bgcolor='#EAEAEA'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>请款日期</td>
		<td width='80' align='center'>请款人</td>
		<td width='300' align='center'>说明</td>
		<td width='120' align='center'>类型</td>
		<td width='50' align='center'>货币</td>
		<td width='80' align='center'>金额</td>
		</tr>";
 
$sListResult = mysql_query("SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.Date,S.Estate,S.Locks,S.Operator,T.Name AS Type,C.Symbol AS Currency
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype    T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata  C ON C.Id=S.Currency
	LEFT JOIN $DataIn.cw14_mdtaxfee F ON F.otherfeeNumber =S.Id
	LEFT JOIN $DataIn.cw14_mdtaxmain M ON M.TaxNo=F.TaxNo
	WHERE 1 AND M.Id=$ShipId",$link_id);
$i=1;
$sumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
	    $Content=$StockRows["Content"];
		$Amount=$StockRows["Amount"];
		$Type=$StockRows["Type"];
		$Currency=$StockRows["Currency"];
		$Date=$StockRows["Date"];
	    $Operator=$StockRows["Operator"];
		include "../model/subprogram/staffname.php";
		$sumAmount=$sumAmount+$Amount;
		echo"<tr bgcolor=#EAEAEA>";	
		echo"<td align='center'>$i</td>";//序号
		echo"<td  align='center'>$Date</td>";				//请款日期
		echo"<td  align='center'>$Operator</td>";			//请款人					
		echo"<td><DIV STYLE='width:300 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$Content</NOBR></DIV></td>";//说明
		echo"<td  align='center'>$Type</td>";				//类型
		echo"<td  align='center'>$Currency</td>";            //货币
		echo"<td  align='right'>$Amount</td>";              //金额
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='6'>合 计</td>";
		echo"<td align='right'>$sumAmount</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td height='30' colspan='7'>行政费用资料,请检查.</td></tr>";
	}
echo"</table>";
echo"<tr height='10'><td  colspan='7' align='center'>&nbsp;</td> ";


//============================供应商税款
echo"<table id='$TableId' cellspacing='1' border='1' align='center'>
       <tr bgcolor='#CCCCCC' height='30'>
	    <td colspan='7' align='center'  style='font-size:14px'>供应商税款列表</td>
	    </tr>
        <tr bgcolor='#EAEAEA'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>请款日期</td>
		<td width='80' align='center'>供应商</td>				
		<td width='300' align='center'>说明</td>
		<td width='120' align='center'>发票号</td>
		<td width='50' align='center'>货币</td>
		<td width='80' align='center'>税款</td>
		</tr>";
 
$gysResult = mysql_query("select G.Id,G.Forshort,G.Amount,G.Getdate,G.InvoiceNUM ,G.Date,G.Remark ,C.Symbol AS Currency,DATE_FORMAT(M.Taxdate,'%Y-%m') AS TaxtDate   
     From $DataIn.cw2_gyssksheet G
	 LEFT JOIN $DataPublic.currencydata  C ON C.Id=G.Currency
	 LEFT JOIN $DataIn.cw14_mdtaxmain M ON DATE_FORMAT(M.Taxdate,'%Y-%m')=DATE_FORMAT(G.Getdate,'%Y-%m')
	 WHERE 1 AND G.InvoiceNUM is Not NUll AND M.Id=$ShipId",$link_id);  // modify by zx  2013-12-25 如果无发票号的，不要放上去! AND G.InvoiceNUM is Not NUll
$j=1;
$sumgysfee=0;
if ($gysRows = mysql_fetch_array($gysResult)) {
	do{
        $Forshort=$gysRows["Forshort"];
		$Date=$gysRows["Date"];
		$Currency =$gysRows["Currency"];
		$InvoiceNUM=$gysRows["InvoiceNUM"];
		$Remark=$gysRows["Remark"];
		$Amount=$gysRows["Amount"];
		$TaxtDate=$gysRows["TaxtDate"];
		$sumgysfee=$sumgysfee+$Amount;

		echo"<tr bgcolor=#EAEAEA>";	
		echo"<td align='center'>$j</td>";//序号
		echo"<td  align='center'>$Date</td>";    //日期
		echo"<td  align='center'>$Forshort</td>";			//供应商				
		echo"<td><DIV STYLE='width:300 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$Remark</NOBR></DIV></td>";//说明
		echo"<td  align='center'>$InvoiceNUM</td>";				//发票号
		echo"<td  align='center'>$Currency</td>";            //货币
		echo"<td  align='right'>$Amount</td>";              //金额
		echo"</tr>";
		$j++;
 		}while ($gysRows = mysql_fetch_array($gysResult));
 
	 if ($TaxtDate=="2013-04"){
		 $gysResult = mysql_query("select G.Id,G.Forshort,G.Amount,G.Getdate,G.InvoiceNUM ,G.Date,G.Remark ,C.Symbol AS Currency  
     From $DataIn.cw2_gyssksheet G
	 LEFT JOIN $DataPublic.currencydata  C ON C.Id=G.Currency 
	 WHERE 1 AND DATE_FORMAT(G.Getdate,'%Y-%m')='2013-03'",$link_id);
	 if ($gysRows = mysql_fetch_array($gysResult)) {
			do{
		        $Forshort=$gysRows["Forshort"];
				$Date=$gysRows["Date"];
				$Currency =$gysRows["Currency"];
				$InvoiceNUM=$gysRows["InvoiceNUM"];
				$Remark=$gysRows["Remark"];
				$Amount=$gysRows["Amount"];
				$sumgysfee=$sumgysfee+$Amount;
				
				echo"<tr bgcolor=#EAEAEA>";	
				echo"<td align='center'>$j</td>";//序号
				echo"<td  align='center'>$Date</td>";    //日期
				echo"<td  align='center'>$Forshort</td>";			//供应商				
				echo"<td><DIV STYLE='width:300 px;overflow: hidden; text-overflow:ellipsis' title='$cName'><NOBR>$Remark</NOBR></DIV></td>";//说明
				echo"<td  align='center'>$InvoiceNUM</td>";				//发票号
				echo"<td  align='center'>$Currency</td>";            //货币
				echo"<td  align='right'>$Amount</td>";              //金额
				echo"</tr>";
				$j++;
		 		}while ($gysRows = mysql_fetch_array($gysResult));
		 	}
	 }
	 	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='6'>合 计</td>";
		echo"<td align='right'>$sumgysfee</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td height='30' colspan='7'>供应商税款资料,请检查.</td></tr>";
	}
echo"</table>";
echo"<tr height='10'><td  colspan='7' align='center'>&nbsp;</td> ";
//========================================报关费用
echo"<table id='$TableId' cellspacing='1' border='1' align='center'>
       <tr bgcolor='#CCCCCC' height='30'>
	    <td colspan='7' align='center' style='font-size:14px'>报关费用列表</td>
	    </tr>
        <tr bgcolor='#EAEAEA'>
		<td width='30' align='center'>序号</td>
		<td width='110' align='center'>出货日期</td>
		<td width='120' align='center'>Invoice</td>
		<td width='120' align='center'>报关公司</td>
		<td width='120' align='center'>报关单号</td>
		<td width='120' align='center'>操作人</td>
		<td width='120' align='center'>报关费用</td>
		</tr>";

$BaoResult = mysql_query("SELECT M.Date AS ShipDate,M.InvoiceNO,M.InvoiceFile,F.Id,F.Termini,F.ExpressNO,F.declarationCharge,F.Date AS fDate,F.Operator,D.Forshort
    FROM $DataIn.ch4_freight_declaration F
    LEFT JOIN $DataIn.ch1_shipmain M  ON F.chId=M.Id
	LEFT JOIN $DataIn.cw14_mdtaxsheet S ON S.InvoiceNumber=M.InvoiceNO
	LEFT JOIN $DataIn.cw14_mdtaxmain A ON A.TaxNo=S.TaxNo
    LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	WHERE 1 AND A.Id='$ShipId'",$link_id);
$i=1;
$sumBao=0;
if ($BaoRows = mysql_fetch_array($BaoResult)) {
	do{
	    $ShipDate=$BaoRows["ShipDate"];
		$InvoiceNO=$BaoRows["InvoiceNO"];
		$ExpressNO=$BaoRows["ExpressNO"];
		$declarationCharge=$BaoRows["declarationCharge"]==""?"0.00":$BaoRows["declarationCharge"];
		$Operator=$BaoRows["Operator"];
		$Forshort=$BaoRows["Forshort"];
		include "../model/subprogram/staffname.php";
		$sumBao=$sumBao+$declarationCharge;
		
		echo"<tr bgcolor=#EAEAEA>";	
		echo"<td align='center'>$i</td>";//序号
		echo"<td  align='center'>$ShipDate</td>";				//请款日期
		echo"<td  align='center'>$InvoiceNO</td>";			//请款人					
		echo"<td  align='center'>$Forshort</td>";//说明
		echo"<td  align='center'>$ExpressNO</td>";				//类型
		echo"<td  align='center'>$Operator</td>";            //货币
		echo"<td  align='right'>$declarationCharge</td>";              //金额
		echo"</tr>";
		$i++;
 		}while($BaoRows = mysql_fetch_array($BaoResult));
	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='6'>合 计</td>";
		echo"<td align='right'>$sumBao</td>";
		echo"</tr>";
	}
else{
	echo"<tr><td height='30' colspan='7'>报关费用资料,请检查.</td></tr>";
	}
echo"</table>";

?>

