<?php 
//加工工序登记明细
//$StockId|$ProcessId
$StockId = $info[0];
$StuffId = $info[1];
$POrderId=substr($StockId,0,12);
$jsonArray = array(); 
$Sid = $info[2];
$frame = mysql_fetch_assoc(mysql_query("select FrameCapacity from stuffdata where StuffId='$StuffId' limit 1 "));
	$frameQty = $frame["FrameCapacity"]; 



$djListSql = mysql_query(
"select  C.Date OPdatetime,C.Qty,M.Name as Operator from $DataIn.qc_cjtj C 
left join $DataPublic.staffmain M on M.Number=C.Operator
where   C.Sid=$Sid order by  C.Date asc",$link_id);
$iCount = 0;
$curQty = 0;
$hasTail = 0;
$realTimeNow = date('Y-m-d H:i:s');
$realTime = $OPdatetime= "";
while ($djListRow = mysql_fetch_assoc($djListSql)) {
	$realTime = $djListRow["OPdatetime"];
	$realTime = $realTime==""?$realTimeNow:$realTime;
	$OPdatetime = GetDateTimeOutString($djListRow["OPdatetime"],'');
	
	$Operator = $djListRow["Operator"];
	$Qty = $djListRow["Qty"];
	$curQty  += $Qty;
	if ($frameQty>0 && $curQty % $frameQty>0) {
		$hasTail ++;
		if ($hasTail==1 && $Qty<$frameQty) 
		continue;
		
	} 
	{
	
	$tempArr = array("Col1"=>array("Text"=>$frameQty>0?"$frameQty":$Qty),
						"Col2"=>array("Text"=>"$OPdatetime"),
						"Col3"=>array("Text"=>"$Operator"),
						"CurQty"=>"$curQty",'time'=>"$realTime"
						);
	
	$jsonArray[]=array("data"=>$tempArr);
	$iCount ++;
	}
}
if ($frameQty>0 && $hasTail > 0 && ($curQty % $frameQty>0)) {
	$Qty = $curQty % $frameQty ;
	$Qty = $Qty > 0 ? $Qty : $frameQty;
	$realTime = $realTime==""?$realTimeNow:$realTime;
		$tempArr = array("Col1"=>array("Text"=>"$Qty"),
						"Col2"=>array("Text"=>"$OPdatetime"),
						"Col3"=>array("Text"=>"$Operator"),
						"CurQty"=>"$curQty",'time'=>"$realTime"
						);
	
	$jsonArray[]=array("data"=>$tempArr);
	$iCount ++;
		
	}
$newArr = array();
for ($i = $iCount; $i>0; $i-- ) {
	$new = $i;

	$jsonArray[$i-1]["index"]="$new";
	$newArr[]=$jsonArray[$i-1];
}

$jsonArray = $newArr;
?>
