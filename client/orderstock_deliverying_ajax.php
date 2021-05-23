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
$subTableWidth=800;

echo"<table id='$TableId'  cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
	<td width='20' height='20'>&nbsp;</td>
	<td width='20'  align='center'>NO.</td>
	<td width='120' align='center'>DeliveryNO</td>
	<td width='100' align='center'>DeliveyDate</td>
	<td width='100' align='center'>DeliveryFile</td>
	<td width='100' align='center'>DeliveryMark</td>
	<td width='100' align='center'>DeliveryQty</td>
	<td width='100' align='center'>DeliveryAmount</td>
	</tr>";
$ordercolor=3;
$sListResult = mysql_query("SELECT M.Id, M.DeliveryNumber,M.Remark,M.DeliveryDate,M.Operator 
        FROM $DataIn.ch1_deliverymain M
		LEFT JOIN $DataIn.ch1_deliverysheet S ON S.Mid=M.Id
        WHERE S.ShipId='$ShipId' GROUP BY M.Id",$link_id);
	$SumQty=0;
	$SumAmount=0;	
	$i=1;
	$d1=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);
	if($myRow = mysql_fetch_array($sListResult)) {
		do{
		   $Id=$myRow["Id"];
           $DeliveryNumber=$myRow["DeliveryNumber"];
		   $Forshort=$myRow["Forshort"]; 
		   $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		   $DeliveryDate=$myRow["DeliveryDate"];
		   $Operator=$myRow["Operator"];
		   include "../model/subprogram/staffname.php";
           $Bill="&nbsp;"; 
		   $filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
           if(file_exists($filename)){
		   $f1=anmaIn($DeliveryNumber,$SinkOrder,$motherSTR);
		   $Bill="<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">view</a>";
		   }
		
		   $DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty,SUM(DeliveryQty*Price) AS DeliveryAmount  FROM ch1_deliverysheet WHERE Mid='$Id'",$link_id);
		   $DeliveryQty =mysql_result($DeliveryResult,0,"DeliveryQty");
		   $DeliveryAmount =mysql_result($DeliveryResult,0,"DeliveryAmount"); 
		   $DeliveryAmount =sprintf("%.2f",$DeliveryAmount);
		//检查是否有装箱
		   $Packing="<div class='redB'>&nbsp;</div>";
		   $checkPacking=mysql_query("SELECT Id FROM $DataIn.ch1_deliverypacklist WHERE Mid='$Id' LIMIT 1",$link_id);
		  if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$Packing="<a href='../admin/ch_shipoutlist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>view</a>";
			} 
			$SumQty+=$DeliveryQty;
			$SumAmount+=$DeliveryAmount;
			$OrderList="OrderList".$RowId.$i;
		    $shoucut="shoucut".$RowId.$i;
		    $HideDiv="HideDiv".$RowId.$i;
			$showPurchaseorder="<img name='$shoucut' id= '$shoucut' onClick='ShowOrderHidden($OrderList,$shoucut,$OrderList,$RowId,$i,\"$Id\",\"$ShipId\")' src='../images/showtable.gif' alt='显示订单' width='13' height='13' style='CURSOR: pointer'>";
	 
	       $HideTableHTML="<tr id='$OrderList' style='display:none' bgcolor='#EAEAEA'>
				     <td colspan='8' align='left'>
			          <table width='$subTableWidth' border='0' cellspacing='0'>
				       <tr bgcolor='#B7B7B7'>
					       <td class='A0000' height='20'>
						       <br>
							       <div id='$HideDiv' width='200'>&nbsp;</div>
						       <br>
					       </td>
				         </tr>
			           </table></td></tr>";
			echo"<tr bgcolor='$theDefaultColor'>";
			echo"<td  align='center' height='20'>$showPurchaseorder</td>";
			echo"<td  align='center'>$i</td>";
			echo"<td align='center'>$DeliveryNumber</td>";
			echo"<td align='center'>$DeliveryDate</td>";
			echo"<td align='center'>$Bill</td>";
            echo"<td align='center'>$Packing</td>";
			echo"<td align='right'>$DeliveryQty</td>";
			echo"<td align='right'>$DeliveryAmount</td>";
			echo"</tr>";
			$i++;
			echo $HideTableHTML;
			}while ($myRow = mysql_fetch_array($sListResult));	
			
			echo "<tr  bgcolor='$theDefaultColor'>
			<td colspan=6 align='right'>total:</td>
			<td align='right'>$SumQty</td>
			<td align='right'>$SumAmount</td>
			</tr>";
		}
?>