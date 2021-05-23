<?php
	
	include "../../basic/parameter.inc";
	
	$questionSql = "SELECT A.Content, A.Date, B.Content AS Anwser, B.Date AS aDate, B.Operator
					FROM $DataPublic.msg5_question A
					LEFT JOIN (SELECT Content, DATE, Operator, State FROM $DataPublic.msg5_question WHERE TYPE =  'A') B ON B.State = A.Id
					WHERE A.Type =  'Q'
					ORDER BY A.Date DESC";
	
	$questionHolder = array();
	$questionResult = mysql_query($questionSql);
	while($questionRow = mysql_fetch_assoc($questionResult))
	{
		$question = $questionRow["Content"];
		$qDate = $questionRow["Date"];
		$anwser = ($questionRow["Anwser"])?$questionRow["Anwser"]:"";
		$aDate = ($questionRow["aDate"])?$questionRow["aDate"]:"";
		$operator = ($questionRow["Operator"])?$questionRow["Operator"]:"";
		
		$questionHolder[] = array("question"=> "$question", "qDate"=>"$qDate", "anwser"=>"$anwser", "aDate"=>"$aDate", "operator"=>"$operator");
		
	}
	
	echo json_encode($questionHolder);
	
?>