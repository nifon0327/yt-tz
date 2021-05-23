<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
if($KeyWord==""){
	echo"&nbsp;";
	}
else{
	$mySql="SELECT D.ProductId,D.eCode,D.Description FROM $DataIn.productdata D WHERE 1 AND D.CompanyId='$myCompanyId' AND (D.eCode LIKE '%$KeyWord%' OR D.Description LIKE '%$KeyWord%' ) ORDER BY D.ProductId";
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
	if($myRow = mysql_fetch_array($myResult)){
		//搜索有记录
		echo"<table id='$TableId' cellspacing='0' border='0'>
		<tr class=''>
			<td class='A1111' width='50' align='center'>&nbsp;</td>	
			<td class='A1101' width='40' height='25' align='center'>Item</td>
			<td class='A1101' width='60' align='center'>产品Id</td>
			<td class='A1101' width='150' align='center'>Product code</td>				
			<td class='A1101' width='300' align='center'>Description</td>
			<td class='A1101' width='60' align='center'>Shipping Qty</td>
			<td class='A1101' width='60' align='center'>Backlog Qty</td>
		</tr>";
		$i=1;
		do{
			$ProductId=$myRow["ProductId"];
			$eCode=$myRow["eCode"];
			$Description=$myRow["Description"];
			$Description=$Description==""?"&nbsp;":$Description;
			//检查已出货数量
			$checkShipSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id));
			$ShipQty=$checkShipSql["Qty"];
			
			//检查未出货数量
			$checkunShipSql=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.yw1_ordersheet WHERE ProductId='$ProductId' AND Estate>'0'",$link_id));
			$unShipQty=$checkunShipSql["Qty"];
			
			$DivNum="a";
			$TempId="$ProductId|$DivNum";
			$IMG="&nbsp;";
			if($ShipQty>0 || $unShipQty>0){
				$IMG="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"OrderInquiry_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='Open OR Close Info.' width='13' height='13' style='CURSOR: pointer'>";
				}
			$ShipQty=$ShipQty==""?"&nbsp;":$ShipQty;
			$unShipQty=$unShipQty==""?"&nbsp;":$unShipQty;
			echo"<tr>
			<td class='A0111' align='center'>$IMG</td>
			<td class='A0101' align='center' height='20'>$i</td>
			<td class='A0101' align='center'>$ProductId</td>
			<td class='A0101'>$eCode</td>
			<td class='A0101'>$Description</td>
			<td class='A0101' align='right'>$ShipQty</td>
			<td class='A0101' align='right'>$unShipQty</td>
			</tr>";
			echo"<tr id='HideTable_$DivNum$i' style='display:none' bgcolor='#B7B7B7'><td colspan='7'>
				<br>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				<br>
			</td></tr>";
			$i++;
			}while($myRow = mysql_fetch_array($myResult));
		echo"</table>";
		}
	else{
		echo"No Recode";
		}
	}
?>
