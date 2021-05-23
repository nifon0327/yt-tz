<?php 
	
	include "../../basic/parameter.inc";
	
	$staffNm = $_POST["staffNum"];
	//$staffNm = 10688;
	$staffTransfer = array();
	$branchTransferSql = "SELECT J.Id,J.Number,J.ActionIn,J.ActionOut,J.Month,J.Remark,J.Date,J.Locks,J.Operator,M.Name
						  FROM $DataPublic.redeployb J 
						  LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
						  WHERE  J.Number = '$staffNm' ORDER BY J.Id DESC,M.Estate DESC,J.Month DESC,J.Number";
	
	$jobTransferSql = "SELECT J.Id,J.Number,J.ActionIn,J.ActionOut,J.Month,J.Remark,J.Date,J.Locks,J.Operator,M.Name
					   FROM $DataPublic.redeployj J 
					   LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
					   WHERE J.Number = '$staffNm' ORDER BY J.Id DESC,M.Estate DESC,J.Month DESC,J.Number";
	
	$kqTransferSql = "SELECT K.Id,K.Number,K.ActionIn,K.ActionOut,K.Month,K.Remark,K.Date,K.Locks,K.Operator,M.Name
					  FROM $DataPublic.redeployk K 
					  LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
					  WHERE K.Number = '$staffNm' ORDER BY K.Id DESC,M.Estate DESC,K.Month DESC,K.Number";

	$gradeTransferSql = "SELECT G.Id,G.Number,G.ActionIn,G.ActionOut,G.Month,G.Remark,
						 G.Date,G.Locks,G.Operator,M.Name
						 FROM $DataPublic.redeployg G 
						 LEFT JOIN $DataPublic.staffmain M ON M.Number=G.Number 
						 WHERE G.Number = '$staffNm' order by G.Id DESC,M.Estate DESC,G.Month DESC,G.Number";
	
	$sqlArray = array($branchTransferSql, $jobTransferSql, $kqTransferSql, $gradeTransferSql);
					  
	for($i=0;$i<4;$i++)
	{
		$result = mysql_query($sqlArray[$i]);
		$tmpTransfer = array();
		while($rows = mysql_fetch_array($result))
		{
			if($i<3)
			{	
				$tmpInName = inqueryTheName($rows[2], $i, $DataPublic);
				$tmpOutName = inqueryTheName($rows[3], $i, $DataPublic);
				$tmpTransfer[] = array($tmpInName, $tmpOutName, $rows[4], $rows[5]);
			}
			else
			{
				$tmpTransfer[] = array($rows[2], $rows[3], $rows[4], $rows[5]);
			}	
		}
		$staffTransfer[] = $tmpTransfer;
	}
	
	echo json_encode($staffTransfer);
	
	function inqueryTheName($target,$type,$DataPublic)
	{
		$tableArray = array("branchdata","jobdata","kqtype");
		$sql = "SELECT Name FROM $DataPublic.".$tableArray[$type]." WHERE Id='$target' Limit 1";
		$inqueryResutlt = mysql_query($sql);
		$inqueryRow = mysql_fetch_assoc($inqueryResutlt);
		return $inqueryRow["Name"];
	}
	
?>