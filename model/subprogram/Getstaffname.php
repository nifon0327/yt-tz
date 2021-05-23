<?php 
//$DataPublic.staffmain$DataIn.电信---yang 20120801
//二合一已更新
function GetSName_Date($Id,$TempTable,$Type,$DataIn,$DataPublic,$link_id){
	$result="";		
	$pResult = mysql_query("SELECT Y.Name,X.SDate,Y.Number,X.Estate  FROM  $DataPublic.staffmain  Y
								Left join   $DataPublic.$TempTable  X ON X.User=Y.Number
								WHERE X.Mid=$Id AND X.UserType=$Type ORDER BY X.SDate desc LIMIT 1",$link_id);

	if ($pResult) {
		if($pRow = mysql_fetch_array($pResult)){
			$StaffName=$pRow["Name"];
			$SDate=$pRow["SDate"];
			$SNumber=$pRow["Number"];
			$SEstate=$pRow["Estate"];
			$result=$StaffName."|".$SDate."|".$SNumber."|".$SEstate;
			}
	}
	return $result;
}


function GetBranchName($BranchId,$DataIn,$DataPublic,$link_id){
	$result="";
	$pResult = mysql_query("SELECT Y.Name FROM $DataPublic.branchdata  Y
								WHERE Y.id=$BranchId LIMIT 1",$link_id);
	if($pRow = mysql_fetch_array($pResult)){
		$Name=$pRow["Name"];
		$result=$Name;
		}
	return $result;
}

function GetCode($code,$Len,$zero,$isp){  //生成标准13位的条码,送进来的是12位的,zero是一个补数，为0则补0，$isp=1表示补，0表示不补
	if($isp==1) {
		$code=str_pad($code,$Len-1,$zero,STR_PAD_LEFT); //$Len表示要生成代码的长度,13位则要12位字，不够就前面补0
	}
	$ncode=$code;
	$even = 0; $odd = 0;
	for ($x=0;$x<12;$x++)
	{
	if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
	}
	
	$code.=(10 - (($odd * 3 + $even) % 10)) % 10;
	return $code;
	
	/* Create the bar encoding using a binary string */
	/*
	$bars=$ends;
	$bars.=$Lencode[$code[0]];
	for($x=1;$x<6;$x++)
	{
	$bars.=$Lencode[$code[$x]];
	}
	
	$bars.=$center;
	
	for($x=6;$x<12;$x++)
	{
	$bars.=$Rencode[$code[$x]];
	}
	*/
}
?>