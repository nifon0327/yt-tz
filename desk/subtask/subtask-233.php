<?php   
//电信-zxq 2012-08-01
//客户退回 
$myResult=mysql_query("SELECT Sum(R.Qty) as ReturnedQty ,P.ProductId
FROM  $DataIn.product_returned  R 
LEFT JOIN $DataIn.productdata P ON P.ProductId=R.ProductId
WHERE 1  group by R.ProductId ",$link_id);
$sumPer5=0;
$sumPer10=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		
		$ProductId=$myRow["ProductId"];
		//$Qty=$myRow["Qty"];
		$ReturnedQty=$myRow["ReturnedQty"];
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=mysql_result($checkShipQty,0,"ShipQty");
	    if($ReturnedQty>0 && $ShipQtySum>0){
			//退货百分比
			$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$ShipQtySum)*1000));
			if($ReturnedPercent>=5){
				if($ReturnedPercent<10){
					$sumPer5=$sumPer5+1;
				}
				else
				{
					$sumPer10=$sumPer10+1;			
					
				}
			}
		 }
	
		}while ($myRow = mysql_fetch_array($myResult));
	}
//净利负值
//$contentSTR="、未出明细理论净利分类统计(<a href='../admin/yw_order_read1.php' target='_blank'><span class='purpleB'>负净利".$Type1Qty."单</span></a>&nbsp;/&nbsp;<a href='../admin/yw_order_read2.php' target='_blank'><span class='redN'>0-7%净利".$Type2Qty."单</span></a> / 订单总数".$OrderNum."单)<br>";

 $tmpTitle=" <a href='../admin/product_returned_ALL.php?ReQtyType=5' target='_blank'><span class='purpleB' title='5‰<=不良品<10‰'>$sumPer5</span></a>/<a href='../admin/product_returned_ALL.php?ReQtyType=10' target='_blank'><span class='redN' title='不良品>=10‰'>$sumPer10</span></a>";
?> 