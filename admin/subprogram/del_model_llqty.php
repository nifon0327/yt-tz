<?php   
//领料记录删除 需传递参数StockId $DataIn.电信---yang 20120801
     $CheckllQty = mysql_fetch_array(mysql_query("SELECT StuffId,SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE  StockId='$StockId' group by StockId",$link_id));
	 $llQty=$CheckllQty["llQty"];
	 $llStuffId=$CheckllQty["StuffId"];
	 if ($llQty!=0){
		 //删除领料记录
		 $delSql="DELETE FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'";
	    $delResult=mysql_query($delSql);	
		if($delResult){
			$Log.="配件需求单( $StockId )的领料记录已成功删除.<br>";
			//更新在库
        } else $Log.="<div class=redB>配件需求单( $StockId )的领料记录已删除失败.</div><br>";
	 }
?>