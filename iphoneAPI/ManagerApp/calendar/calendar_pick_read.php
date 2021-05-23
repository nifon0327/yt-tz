<?php 
//讀取行事曆事件類別

$typeList = array();
/*
if ((versionToNumber($AppVersion) <= 300) && ($AppVersion != "testversion")) {
	$mySql = "SELECT Id, Name FROM $DataPublic.event_type Where Estate = 1 LIMIT 0, 3;";
	$myResult = mysql_query($mySql, $link_id);
	if($myRow = mysql_fetch_array($myResult)) {
		do {
			$typeList[] = array(
				"id" => $myRow["Id"],
				"name" => $myRow["Name"]
			);
			
		} while ($myRow = mysql_fetch_array($myResult));
	}
	
	//return json
	$jsonArray[] = array(
		"navTitle" => "新增事件",
		"data" => $typeList
	);
}
else*/ {

	//事件列表
	$estateString = "";
	$limitString = "";
	if ((versionToNumber($AppVersion) > 301) || ($AppVersion == "testversion"))
	{
		$estateString = "Estate = 1";	
	}
	else
	{
		$limitString = "LIMIT 0, 8";
		$estateString = "Estate IN (0, 1)";
	}
	
	$mySql = "SELECT Id, Name FROM $DataPublic.event_type Where $estateString $limitString;";
	$myResult = mysql_query($mySql, $link_id);
	if($myRow = mysql_fetch_array($myResult)) {
		do {
			$typeList[] = array(
				"id" => $myRow["Id"],
				"name" => $myRow["Name"]
			);
			
		} while ($myRow = mysql_fetch_array($myResult));
	}

	//邀請人
	$memberList = array();
	$memberList []= array("id"=>"all","name"=>"所有人","branch"=>"");
	
	// 
	/*
	SELECT A.Number AS id, B.Name AS name, CONCAT(C.CShortName,'-',D.Name) AS branch
					FROM $DataIn.UserTable A 
					LEFT JOIN $DataPublic.staffmain B ON B.Number = A.Number
					LEFT JOIN $DataPublic.companys_group C ON C.cSign = B.cSign
					LEFT JOIN $DataPublic.branchdata D ON D.Id = B.BranchId
					WHERE A.Estate = 1
					AND B.Name IS NOT NULL 
					AND CONCAT(C.CShortName,'-',D.Name) IS NOT NULL
					ORDER BY B.cSign DESC, A.Estate DESC, D.SortId, A.Number

	*/
	$memberSql = "SELECT B.Number AS id, B.Name AS name, CONCAT(C.CShortName,'-',D.Name) AS branch
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
	
	//提醒時間
	$notifyMinList = array();
	$notifyMinList[] = array(	//5min
		"text"	=> "5分钟",						
		"value"	=> "5",
	); 
	$notifyMinList[] = array(	//15min
		"text"	=> "15分钟",						
		"value" => "15",
	);
	$notifyMinList[] = array(	//30min
		"text"	=> "30分钟",						
		"value"	=> "30",
	);
	$notifyMinList[] = array(	//1hour
		"text"	=> "1小时",						
		"value"	=> sprintf("%u", 1 * 60),
	);
	$notifyMinList[] = array(	//2hour
		"text"	=> "2小时",						
		"value"	=> sprintf("%u", 2 * 60),
	);
	$notifyMinList[] = array(	//1day
		"text"	=> "1天",						
		"value"	=> sprintf("%u", 24 * 60),
	);
	
	//地點
	$placeList = array();
	$placeList[] = array(
		"text"	=> "1会议室",						
		"value"	=> "1会议室",
	);
	$placeList[] = array(
		"text"	=> "2会议室",						
		"value"	=> "2会议室",
	);
	$placeList[] = array(
		"text"	=> "3会议室",						
		"value"	=> "3会议室",
	);
	$placeList[] = array(
		"text"	=> "4会议室",						
		"value"	=> "4会议室",
	);
	$placeList[] = array(
		"text"	=> "5会议室",						
		"value"	=> "5会议室",
	);
	$placeList[] = array(
		"text"	=> "Fred’s Office",						
		"value"	=> "Fred’s Office",
	);
	$placeList[] = array(
		"text"	=> "48广场",						
		"value"	=> "48广场",
	);
	$placeList[] = array(
		"text"	=> "47广场",						
		"value"	=> "47广场",
	);	
	$placeList[] = array(
		"text"	=> "其他",						
		"value"	=> "其他",
	);
	
	//return json
	$jsonArray = array(
		"navTitle" => "新增事件",
		"data" => array(
			"types" 	=> $typeList,
			"members"	=> $memberList,
			"notifyMin"	=> $notifyMinList,
			"places"	=> $placeList,
		),
	);
}
?>