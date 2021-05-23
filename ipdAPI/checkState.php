<?php
	
	include_once "../basic/parameter.inc";
	//include "../model/kq_YearHolday.php";
	
	$pbSumQty = mysql_query("SELECT Sum(qty) as qty, StockId FROM $DataIn.ck5_llsheet Where StuffId= '117481' group by StockId");
	while($pbSumQtyRow = mysql_fetch_assoc($pbSumQty))
	{
		$StockId = $pbSumQtyRow["StockId"];
		$qty = $pbSumQtyRow["qty"];
		
		$orderQtySql = mysql_query("Select OrderQty From $DataIn.cg1_stocksheet Where StockId = '$StockId' Limit 1");
		$orderQtyResult = mysql_fetch_assoc($orderQtySql);
		$orderQty = $orderQtyResult["OrderQty"];
		
		if($qty != $orderQty)
		{
			echo "$StockId   ll:$qty    order:$orderQty <br>";
		}
		
	}

	
	
// 	$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Date,J.Operator,M.cSign,M.Number,J.Note, J.type,M.WorkAdd,M.Name,M.KqSign,M.JobId,M.BranchId
// 			FROM $DataPublic.bxSheet J 
// 			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number";	
// 	$myResult = mysql_query($mySql);
	
// 	mysql_query("BEGIN");
// 	while($myRow = mysql_fetch_assoc($myResult))
// 	{
// 		$Id=$myRow["Id"];
// 		$StartDate=$myRow["StartDate"];
// 		$EndDate=$myRow["EndDate"];
// 		$calculateType = $myRow["type"];
// 		$hours = ($calculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
		
// 		$updateHours = "update $DataPublic.bxSheet set hours = '$hours' where Id = '$Id'";
// 		mysql_query($updateHours);
// 	}
	
// 	if(mysql_errno())
// {
// 	mysql_query("rollback");
// 	echo "error";
// }
// else
// {
// 	mysql_query("commit");
// }

// mysql_query("END"); 
	
?>