<?php 
$productId = $info[0];
$dataArray= array();

if ($productId > 0) {
	
	$connectStuffSql = mysql_query("select d.oStockQty,d.tStockQty,r.StuffId ,s.StuffCname 
	from $DataIn.pands r
	left join $DataIn.ck9_stocksheet d on d.StuffId=r.StuffId
	left join $DataIn.stuffdata s on s.StuffId=r.StuffId
	where ProductId=$productId and s.Estate>0");
	
	while ($connectStuffRow = mysql_fetch_assoc($connectStuffSql)) {
		$stuffId = $connectStuffRow["StuffId"];
		$check = mysql_query("select  1 as Test from $DataIn.pands where 1 and StuffId='$stuffId' and ProductId!=$productId limit 1");
		$onlyOne = 1;
		if ($checkRow = mysql_fetch_array($check)) {
			$onlyOne = 0;
			continue;
		}
		$oStockQty = $connectStuffRow["oStockQty"];
		$tStockQty = $connectStuffRow["tStockQty"];
		$StuffCname = $connectStuffRow["StuffCname"];
		/*    
	self.nameLabel.text = dict[@"cName"];
    self.tStockQty.text = dict[@"tStockQty"];
    self.oStockQty.text = dict[@"oStockQty"];
*/
		$dataArray[]=array("oStockQty"=>"$oStockQty",
							 "tStockQty"=>"$tStockQty",
							 "cName"=>"$stuffId-$StuffCname",
							 "Id"=>"$stuffId"
							);
		
	}
}



$jsonArray= array("list"=>$dataArray);
 
?>