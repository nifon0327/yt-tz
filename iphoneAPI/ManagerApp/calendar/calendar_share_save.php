<?
	
	$recordId;
	$shareWith;
	$OperationResult = "N";
	
	$updateSql = @mysql_query("update event_recordsheet set ShareWith='$shareWith' where Id=$recordId");
	if ($updateSql) {
	$OperationResult = "Y";	
	}
	$jsonArray = array(
				"ActionId" => "$ActionId",
				"Result" => "$OperationResult",
				"Info"=>"",
				"RID"=>"$recordId"
			);
	
?>