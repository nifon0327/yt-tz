<?php 
//行政费用审核
$cztest = ' AND s.Estate=2 ';
$czLimt = '';
/*
"社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险"

*/

$sbAndGjj = array(1,2,3);
$sbAndGjjTitle = array("社保","公积金","意外险");
$sbAndGjjCount = count($sbAndGjj);

$itor = 0;
for ($i=0; $i < $sbAndGjjCount; $i++) {
	$condi =" s.TypeId=". $sbAndGjj[$i];
	$mySql="SELECT s.Id,s.cAmount,s.mAmount,s.Month,P.Name,B.Name as Branch,J.Name as JobName 
	FROM $DataIn.sbpaysheet s  
	LEFT JOIN $DataPublic.staffmain P ON s.Number=P.Number 
	LEFT JOIN $DataPublic.jobdata J ON J.Id=s.JobId 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=s.BranchId 
	where 1 $cztest and $condi ORDER BY  s.Month DESC,s.BranchId,s.JobId,P.Number";
	$Result=mysql_query($mySql,$link_id);
	$rowCount = $totalCMAM = 0;
	$subList = array();
	while ($myRow = mysql_fetch_array($Result)) {
		$rowID = $myRow["Id"];
		$Name = $myRow["Name"];
		$Branch = $myRow["Branch"];
		$JobName = $myRow["JobName"];
		$mAmount = $myRow["mAmount"];
		$cAmount = $myRow["cAmount"];
		$Month = $myRow["Month"];
		
		$CMAmount = $mAmount+$cAmount;
		$totalCMAM += $CMAmount;
		$cAmount = sprintf("%.2f",$cAmount);
		$mAmount = sprintf("%.2f",$mAmount);
		$CMAmount = sprintf("%.2f",$CMAmount);
		
		$subList[] = array(
					"leaf"=>1,
					"onTap"=>array("Audit"=>"$AuditSign"),
					"Id"=>"$rowID",
					"Title"=>array("Text"=>"$Name"),
					"Col1"=>array("Text"=>"$Branch-$JobName"),
					"Col2"=>array("Text"=>"$Month"),
					"Col3"=>array("Text"=>"¥$mAmount"),
					"Col4"=>array("Text"=>"¥$cAmount"),
					"Col5"=>array("Text"=>"¥$CMAmount"),			
		);
		
		$rowCount ++;
	}
	$totalCMAM = sprintf("%.2f",$totalCMAM);
	$itor += $rowCount;
	$titleStr = $sbAndGjjTitle[$i];
	if ($rowCount > 0) {
 	$dataArray[]=array(
						 "leaf"=>0,	
	                   "Id"=>"multi-".$sbAndGjj[$i], //0 sb   1 gjj
	                   "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$i","Audit"=>"$AuditSign"),
						 "Title"=>array("Text"=>"$titleStr"),
						 "Col1"=>array("Text"=>"$rowCount"),
	                	 "Col2"=>array("Text"=>"¥$totalCMAM"),                   
	                   "List"=>$subList
                     );
}
}
 
?>