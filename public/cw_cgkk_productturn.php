<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$tableWidth=1120;
echo"<table id='$TableId'  cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
	<td width='20' >序号</td>
	<td width='60' align='center'>退货日期</td>
	<td width='50' align='center'>产品ID</td>
	<td width='230' align='center'>产品名称</td>
	<td width='130' align='center'>Product Code</td>
	<td width='80' align='center'>已出数量<br>(下单次数)</td>
	<td width='60' align='center'>退货数量</td>
	<td width='50' align='center'>单价</td>
	<td width='65' align='center'>退货金额</td>
	<td width='60' align='center'>操作</td>
	</tr>";
$i=1;
$mySql="SELECT R.Id,R.eCode,R.ReturnMonth,R.Qty,R.Price,(R.Qty*R.Price) AS Amount,R.Locks,P.cName,P.ProductId,R.Operator
FROM $DataIn.product_returned R 
LEFT JOIN $DataIn.productdata P ON P.ProductId=R.ProductId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.ProductId=P.ProductId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=Y.POrderId
WHERE 1  AND G.StuffId='$StuffId' group by R.ProductId  ORDER BY R.ReturnMonth DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		set_time_limit(0);
		$Id=$myRow["Id"];
		$ReturnMonth=$myRow["ReturnMonth"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$Qty=$myRow["Qty"];
		$tmpQty=$Qty;
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$myRow["Amount"]);
		$tmpAmount=$Amount;
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";	

		$checkAllQty= mysql_query("
								  SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode='$eCode' GROUP BY OrderPO
									)A
								  ",$link_id);	
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		$Orders=mysql_result($checkAllQty,0,"Orders");
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));

		$checkLastdate= mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m-%d') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId            
                FROM $DataIn.ch1_shipmain M 
	            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
                WHERE  S.ProductId='$ProductId'  " ,$link_id);		
		$LastMonth=mysql_result($checkLastdate,0,"LastMonth");
		$Months=mysql_result($checkLastdate,0,"Months");
		if($Months!=NULL){
			if($Months<6){//6个月内绿色
				$LastShipMonth="<div class='greenB'>".$LastMonth."</div>";
				}
			else{
				if($Months<12){//6－12个月：橙色
					$LastShipMonth="<div class='yellowB'>".$LastMonth."</div>";
					}
				else{//红色
					$LastShipMonth="<div class='redB'>".$LastMonth."</div>";
					}
				}
			
			}
		else{//没有出过货
			$LastShipMonth="&nbsp;";
			}

		$checkLastdate= mysql_query("SELECT DATE_FORMAT(MAX(M.OrderDate),'%Y-%m-%d') AS LastOrderDate,TIMESTAMPDIFF(MONTH,MAX(M.OrderDate),now()) AS OrderMonths,S.ProductId            
				FROM $DataIn.yw1_ordermain M 
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
				WHERE   S.ProductId='$ProductId'  " ,$link_id);
		

		$LastOrderDate=mysql_result($checkLastdate,0,"LastOrderDate");
		$OrderMonths=mysql_result($checkLastdate,0,"OrderMonths");	
		if($OrderMonths!=NULL){
			if($OrderMonths>=18){//6个月内绿色
				$LastOrderDate="<div class='redB'>".$LastOrderDate."</div>";
				}
		}
		else{//最后下单
			$LastOrderDate="&nbsp;";
			}		
		
		//百分比
		$TempInfo="style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
		$TempPC=$AllQtySum==0?0:($ShipQtySum/$AllQtySum)*100;
		$TempPC=$TempPC>=1?(round($TempPC)."%"):(sprintf("%.2f",$TempPC)."%");
		if($AllQtySum>0){
			$TempInfo.="title='订单总数:$AllQtySum,已出数量占:$TempPC'";
			}
         //退货数量
		$checkReturnedQty= mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE ProductId='$ProductId'",$link_id);
		$ReturnedQty=toSpace(mysql_result($checkReturnedQty,0,"ReturnedQty"));
		
		
		if($ReturnedQty>0 && $ShipQtySum>0){
			//退货百分比
			$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$ShipQtySum)*1000));
			if($ReturnedPercent>=5){
				$Qty="<span class=\"redB\">".$Qty."</span>";
				}
			else{
					if($ReturnedPercent>=2){
						$Qty="<span class=\"yellowB\">".$Qty."</span>";
						}
					else{
						$Qty="<span class=\"greenB\">".$Qty."</span>";
						}
					}
			$ReturnedP=
			$TempInfo2="style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\" 总退货量：$ReturnedQty  退货率：$ReturnedPercent ‰\"";
			}
		else{
			$TempInfo2="";
			}
	
		$ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
		$GfileStr=$GfileStr==""?"&nbsp;":$GfileStr;
		$TableId="ListTable$i";
		
		//出货数量和下单次数
		if($Orders>0){
			if($Orders<2){
				$ShipQtySum=$ShipQtySum."<span class=\"redB\">($Orders)</span>";
				}
			else{
				if($Orders>4){
					$ShipQtySum=$ShipQtySum."<span class=\"greenB\">($Orders)</span>";
					}
				else{
					$ShipQtySum=$ShipQtySum."<span class=\"yellowB\">($Orders)</span>";	
					}
				}
			}
	    $StuffList="StuffList".$RowId.$i;
	    $showtable="showtable".$RowId.$i;
		$HideDiv="showStuffTB".$RowId.$i;
        $FromDir='public';
		$URL="productdata_chart.php";
        $theParam="Pid=$ProductId&Type=2";
		//$showPurchaseorder="<img onClick='P_ShowOrHide($StuffList,$showtable,$StuffList,\"$URL\",\"$theParam\",$RowId$i,\"\",\"$FromDir\");' name='$showtable' src='../images/showtable.gif' alt='显示或隐藏子分类情况.' width='13' height='13' style='CURSOR: pointer'>";
		$HideTableHTML="<tr id='$StuffList' style='display:none' bgcolor='$theDefaultColor'>
				     <td colspan='10' align='left'>
			            <table width='$tableWidth' border='0' cellspacing='0'>
				          <tr bgcolor='#B7B7B7'>
					          <td class='A0111' height='80'>
						       <br><div id='$HideDiv' width='200'>&nbsp;   </div><br>
					         </td>
				           </tr>
			           </table></td></tr>";

		echo "<tr bgcolor='#FFFFFF'>
		      <td align='center'>$i</td>
			  <td align='center'>$ReturnMonth</td>
			  <td align='center'>$ProductId</td>
			  <td >$cName</td>
			  <td>$eCode</td>
			  <td align='center' $TempInfo>$ShipQtySum</td>
			  <td align='center' $TempInfo2>$Qty</td>
			  <td align='center'>$Price</td>
			  <td align='center'>$Amount</td>
			  <td align='center'>$Operator</td>
		      </tr>";
		      $i++;
		//		echo $HideTableHTML;	
         include "cw_cgkk_returnRate.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
