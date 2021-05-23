<?php
$mySql="SELECT 
M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort,S.InvoiceModel   
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id
WHERE 1 AND O.Id IS NOT NULL ORDER BY M.Date DESC";
//echo $mySql;
$unGettotalQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		//出货数量+提货数量
		 $ShipResult=mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id);
		 $ShipQty=mysql_result($ShipResult,0,"ShipQty");
		 $DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty FROM $DataIn.ch1_deliverysheet WHERE ShipId='$Id'",$link_id);
		 $DeliveryQty=mysql_result($DeliveryResult,0,"DeliveryQty");
		 $DeliveryQty=$DeliveryQty==""?0:$DeliveryQty;
		 $unDeQty=$ShipQty-$DeliveryQty;
         $unGettotalQty+=$unDeQty;
		}while ($myRow = mysql_fetch_array($myResult));   
}
$unGettotalQty=round($unGettotalQty/1000,0);
 $tmpTitle="<font color='red'>$unGettotalQty"."k</font>";
?>