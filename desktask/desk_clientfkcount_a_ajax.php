<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=980;
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$MonthTemp=$TempArray[1];
$predivNum=$TempArray[2];
$TableId="ListTB".$predivNum.$RowId;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>出货流水号</td>
		<td width='100' align='center'>Invoice名称</td>
		<td width='70' align='center'>出货日期</td>
		<td width='100' align='center'>货运信息</td>
		<td width='60' align='center'>报关方式</td>
		<td width='60' align='center'>How to Ship</td>
		<td width='60' align='center'>出货分类</td>
		<td width='210' align='center'>备注</td>
		<td width='60' align='center'>操作员</td>
		<td width='70' align='center'>未收金额</td>
	</tr></table>";
$mySql="SELECT SUM(S.Price*S.Qty*M.Sign) AS Amount,M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,
M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort,C.PayType,SM.InvoiceModel  
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel SM ON SM.Id=M.ModelId 
WHERE M.Estate =0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp' GROUP BY M.Id ORDER BY M.Date";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$subTableWidth=910;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$incomeType=$myRow["incomeType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		//Invoice查看
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
		$InvoiceFile=$InvoiceFile==0?$InvoiceNO:"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",7)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNO</span>";
		$ShipType=$myRow["ShipType"];
		switch ($ShipType){
			case 'replen':
				$ShipType="补货"; 
				$shipColor=" bgcolor='#FEA085' ";
				break;			
			case 'credit': 
				$ShipType="扣款"; 
				$shipColor=" bgcolor='#FFFF93' ";
				break;
			case 'debit':
				$ShipType="其它收款"; 
				$shipColor=" bgcolor='#6ACFFF' ";
				break;
		    default:
			    $ShipType="出货"; 
				$shipColor="";
		}
		$Ship=$myRow["Ship"];
		switch ($Ship){
			case '-1':$Ship=""; break;			
			case '0': $Ship="air"; break;
			case '1':$Ship="sea";break;
			case '7':$Ship="陆运";break;
			case '8':$Ship="库存";break;
			case '9':$Ship="UPS";break;
			case '10':$Ship="DHL";break;
			case '11':$Ship="SF";break;
			case '12':$Ship="Fedx";break;
		}
		$Ship=$Ship=""?"&nbsp;":$Ship;
		$Amount=$myRow["Amount"];
		$SignColor="";
		if($Amount<0){
			$SignColor="class='redB'";
			}
		//检查已收部分
		$CheckPart=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		FROM $DataIn.cw6_orderinsheet P
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		WHERE M.cwSign='2' AND M.Id='$Id'",$link_id));
		$PayedAmount=$CheckPart["GatheringSUM"];
		$Amount=$Amount+$PayedAmount;

		$DivNum=$predivNum."d";
		$TempId="$CompanyId|$Id|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_clientfkcount_b\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$showPurchaseorder</td>
				<td width='70' align='center'>$Number</td>
				<td width='100' align='center'>$InvoiceFile</td>
				<td width='70' align='center'>$Date</td>
				<td width='100' align='center'>$Wise</td>
				<td width='60' align='center'>$incomeType</td>
                <td width='60' align='center'> $Ship</td>
				<td width='60' align='center'>$ShipType</td>
				<td width='210' align='center'>$Remark</td>
				<td width='60' align='center' >$Operator</td>
				<td width='70' align='right'>$Amount</td>
			</tr></table>";
		echo"<table width='$tableWidth' cellspacing='1' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#FFFFFF'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>