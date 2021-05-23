<?php
//2013-09-25 ewen
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$checkSql=mysql_query("SELECT A.Exam,B.Name,B.ComeIn,C.Name AS Branch,D.Name AS Job  
					  FROM $DataPublic.aqsc08 A
					  LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
					  LEFT JOIN $DataPublic.branchdata C ON C.Id=B.BranchId
					  LEFT JOIN $DataPublic.jobdata D ON D.Id=B.JobId
					  WHERE A.ItemId='$tempValue' ORDER BY A.Id",$link_id);
$ReturnArray = array();
while($checkRow=mysql_fetch_array($checkSql)){
	$Name=$checkRow["Name"];
	$Branch=$checkRow["Branch"];
	$Job=$checkRow["Job"];
	$ComeIn=$checkRow["ComeIn"];
	$Exam=$checkRow["Exam"];
	$ReturnArray[]=array(0=>$Name,1=>$Branch,2=>$Job,3=>$ComeIn,4=>$Exam);
	}
$ReturnInfo = json_encode($ReturnArray);
echo $ReturnInfo;
?>
