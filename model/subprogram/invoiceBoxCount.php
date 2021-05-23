<?php
	
	function boxCount($Mid,$DataIn)
	{
		include "../basic/parameter.inc";
		$Box_Sql = mysql_query("SELECT M.InvoiceNO,M.Date,M.Remark,M.ModelId,M.PreSymbol,SUM(L.BoxQty) AS BoxTotal,SUM(L.BoxRow*L.BoxQty) AS 	LableSUM,D.CompanyId,D.StartPlace,D.EndPlace,D.LabelModel
								FROM $DataIn.ch2_packinglist L
								LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=L.Mid
								LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId
								WHERE L.Mid=$Mid and D.LabelModel>0 AND BoxRow>0 GROUP BY L.Mid",$link_id);
								
		if($BoxRow=mysql_fetch_array($Box_Sql))
		{
			$BoxTotal=$BoxRow["BoxTotal"];
			$PackingResult = mysql_query("SELECT L.POrderId,L.BoxRow,L.BoxPcs,L.BoxQty,L.WG,L.BoxSpec 
	FROM $DataIn.ch2_packinglist L 
	WHERE L.Mid='$Mid' ORDER BY L.Id",$link_id);
		}
	
	}
?>