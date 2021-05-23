<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 未收货统计报表");
$NumberSTR="";	$CompanySTR="";	$NumberSTR1="全部";	$CompanySTR1="全部";
if($Number!=""){
	$NumberSTR=" and S.BuyerId='$Number'";
	$checkN= mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Number' LIMIT 1",$link_id));
	$NumberSTR1=$checkN["Name"];
	}
if($CompanyId!=""){
	$CompanySTR=" and S.CompanyId='$CompanyId'";
	$checkC= mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
	$CompanySTR1=$checkC["Forshort"];
	}

$Date=date("Y-m-d");
echo"<table width='900' border='0' cellspacing='0'";
echo"<tr><td align='center' height='30'>未收货统计报表(采购:$NumberSTR1 供应商:$CompanySTR1)</td></tr>";
echo"<tr><td align='right'>报表日期:$Date</td></tr>";
echo"</table>";
$Th_Col="序号|30|配件ID|45|配件名称|200|订单需求数|80|使用库存总数|80|需购总数|60|增购总数|60|特采总数|60|实购合计|60|未下单数|60|已下单数|60|已收总数|60|未收总数|60";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.StuffId,A.StuffCname FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
WHERE 1 and S.Mid>0 $NumberSTR $CompanySTR GROUP BY S.StuffId  ORDER BY S.StuffId DESC";
$myResult = mysql_query($mySql,$link_id);
$tempStuffId="";
echo"<table width='855' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["Picture"]==0?$myRow["StuffCname"]:"<span onClick='View(\"stufffile\",\"$myRow[Picture]\")' style='CURSOR: pointer;color:#FF6633'>$myRow[StuffCname]</span>";
				
		//已购总数
		$cgTemp=mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S WHERE 1 $SearchRows and S.Mid>0 and S.StuffId='$StuffId'",$link_id);
		$cgQty=mysql_result($cgTemp,0,"Qty");
		$cgQty=$cgQty==""?0:$cgQty;
		 
		//已收货总数
		$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM ck1_rksheet R 
		LEFT JOIN cg1_stocksheet S ON S.StockId=R.StockId
		WHERE R.StuffId='$StuffId' $SearchRows",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
			
		$noQty=$cgQty-$rkQty;
		if($noQty!=0){
		
			//订单总数\使用库存总数
			$Temp1=mysql_query("SELECT SUM(OrderQty) AS OrderQty,SUM(StockQty) AS StockQty,SUM(FactualQty) AS FactualQty,SUM(S.AddQty) AS AddQty FROM $DataIn.cg1_stocksheet S WHERE 1 $SearchRows and S.StuffId='$StuffId'",$link_id);
			$OrderQty=mysql_result($Temp1,0,"OrderQty");$OrderQty=$OrderQty==""?0:$OrderQty;
			$StockQty=mysql_result($Temp1,0,"StockQty");$StockQty=$StockQty==""?0:$StockQty;
			$FactualQty=mysql_result($Temp1,0,"FactualQty");$FactualQty=$FactualQty==""?0:$FactualQty;
			$AddQty=mysql_result($Temp1,0,"AddQty");$AddQty=$AddQty==""?0:$AddQty;		
			$Qty=$FactualQty+$AddQty;
			//特采数量
			$Temp1=mysql_query("SELECT SUM(FactualQty) AS tcQty FROM $DataIn.cg1_stocksheet S WHERE 1 $SearchRows and S.StuffId='$StuffId' and S.POrderId=''",$link_id);
			$tcQty=mysql_result($Temp1,0,"tcQty");$tcQty=$tcQty==""?0:$tcQty;		
			$FactualQty=$FactualQty-$tcQty;
			$nocgQty=$Qty-$cgQty;
			
			$OrderQty=zerotospace($OrderQty);
			$StockQty=zerotospace($StockQty);
			$FactualQty=zerotospace($FactualQty);
			$AddQty=zerotospace($AddQty);
			$tcQty=zerotospace($tcQty);
			$Qty=zerotospace($Qty);
			$nocgQty=zerotospace($nocgQty);
			$cgQty=zerotospace($cgQty);
			$rkQty=zerotospace($rkQty);
			$noQty=zerotospace($noQty);
			echo"<tr>
			<td class='A0111' align='center' width='30'>$i</td>
			<td class='A0101' align='center' width='45'>$StuffId</td>
			<td class='A0101' width='200'>$StuffCname</td>
			<td class='A0101' align='right' width='80'>$OrderQty</td>
			<td class='A0101' align='right' width='80'>$StockQty</td>
			<td class='A0101' align='right' width='60'>$FactualQty</td>
			<td class='A0101' align='right' width='60'>$AddQty</td>
			<td class='A0101' align='right' width='60'>$tcQty</td>
			<td class='A0101' align='right' width='60' bgcolor='#cccccc'>$Qty</td>
			<td class='A0101' align='right' width='60' bgcolor='#cccccc'>$nocgQty</td>
			<td class='A0101' align='right' width='60' bgcolor='#cccccc'>$cgQty</td>
			<td class='A0101' align='right' width='60'>$rkQty</td>
			<td class='A0101' align='right' width='60'>$noQty</td>
			</tr>";
			$i++;
			}
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
echo"</table>";
?>
