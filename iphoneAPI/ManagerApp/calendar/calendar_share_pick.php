<?
	$memberList = array();
	$memberList []= array("Id"=>"all","name"=>"所有人","branch"=>"");
	
	$memberSql = "SELECT B.Number AS Id, B.Name AS name, CONCAT(C.CShortName,'-',D.Name) AS branch
					FROM  staffmain B
					LEFT JOIN companys_group C ON C.cSign = B.cSign
					LEFT JOIN branchdata D ON D.Id = B.BranchId
					WHERE B.Estate >0
					
						AND B.Name IS NOT NULL 
					AND CONCAT(C.CShortName,'-',D.Name) IS NOT NULL
					ORDER BY B.cSign DESC, B.Estate DESC, D.SortId, B.Number";
	$memberResult = mysql_query($memberSql, $link_id);
	if ($row = mysql_fetch_object($memberResult)) {
		do {
			$memberList[] = $row;
		} while ($row = mysql_fetch_object($memberResult));
	}
	
	$jsonArray = array("member"=>$memberList);
?>