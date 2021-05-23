<?php   
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
ChangeWtitle("Balance Of Order");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<center>";

echo "<table width='750'  cellpadding='0' cellspacing='0'>";
echo "<tr><td colspan=6 height='50'  align='center' style='font-size:20px;'><b>Balance Of Order</b></td></tr>";
echo "<tr> <td colspan=6 height='20' valign='top'><b>InvoiceNO:</b>$InvoiceNO</td></tr>";
echo "<tr><td  width='40'  align='center' class='A1111' height='30' ><b>NO</b></td>
          <td  width='100' align='center' class='A1101'><b>PO</b></td>
		  <td  width='250' align='center' class='A1101'><b>Product Code</b></td>
          <td  width='120' align='center' class='A1101'><b>Qty</b></td>
          <td  width='120' align='center' class='A1101'><b>DeliveryQty</b></td>
		  <td  width='120' align='center' class='A1101'><b>BalanceQty</b></td>
		  </tr>"; 

$dataResult=mysql_query("SELECT S.Qty,Y.OrderPO,P.eCode,Y.POrderId  
            FROM $DataIn.ch1_shipsheet S
			LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
			WHERE S.Mid='$ShipId'",$link_id);
$i=1;
$sumQty=0;
$sumDeliveryQty=0;
$sumBalanceQty=0;
if ($data_Row = mysql_fetch_array($dataResult)){
   do{
	  $Qty=$data_Row["Qty"];
	  $OrderPO=$data_Row["OrderPO"];
	  $eCode=$data_Row["eCode"];
	  $POrderId=$data_Row["POrderId"];
      $DeliveryReuslt=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty FROM $DataIn.ch1_deliverysheet WHERE POrderId='$POrderId'",$link_id);
      $DeliveryQty=mysql_result($DeliveryReuslt,0,"DeliveryQty");
	  $DeliveryQty=$DeliveryQty==""?0:$DeliveryQty;
	  $BalanceQty=$Qty-$DeliveryQty;
	  //传递
		$DivNum="a".$i;
		$TempId=$POrderId;
			$HideTableHTML="
			<tr id='HideTable_$DivNum$i' style='display:none' bgcolor='#B7B7B7'>
					<td class='A0111'  colspan=\"6\">
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>";
	  
      echo  "<tr>
                 <td align='center' height='30' class='A0111' $tdbgcolor>$i</td>
                 <td class='A0101' align='center'>$OrderPO</td>
				 <td class='A0101' align='left'>$eCode</td>
                 <td class='A0101' align='center'>$Qty</td>
                 <td class='A0101' align='center' style='color:#339933;font:bold;' onClick='SandH(\"$DivNum\",$i,\"$TempId\",\"orderstock_unqty\",0);' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' alt='显示或隐藏下级资料. ' style='CURSOR: pointer;'>$DeliveryQty</td>
				 <td class='A0101' align='center' style='color:#FF0000;font:bold;'>$BalanceQty</td>
            </tr>";
	 echo $HideTableHTML;
	 $sumQty+=$Qty;
	 $sumDeliveryQty+=$DeliveryQty;
	 $sumBalanceQty+=$BalanceQty;
	 $i++;
     }while($data_Row = mysql_fetch_array($dataResult));  
     echo  "<tr>
                 <td colspan=3 height='30' align='center' class='A0111'><b>Total(Qty)</b></td>
                 <td class='A0101' align='center'>$sumQty</td> 
	             <td class='A0101' align='center' style='color:#339933;font:bold;'>$sumDeliveryQty</td>
                 <td class='A0101' align='center' style='color:#FF0000;font:bold;'>$sumBalanceQty</td>
         </tr></table>";
   }
?>
<script>
function SandH(divNum,RowId,TempId,ToPage,Action){
	var e=eval("HideTable_"+divNum+RowId);
	if(Action==0)e.style.display=(e.style.display=="none")?"":"none";
	else e.style.display=="";
	//var yy=f.src;
	if (e.style.display==""){
		if(TempId!=""){			
			var url="../client/"+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId+"&Action="+Action;
		　	var show=eval("HideDiv_"+divNum+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
			
		}
	}
</script>
