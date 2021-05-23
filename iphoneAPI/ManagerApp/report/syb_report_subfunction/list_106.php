<?php 
/* echo("$SelMonth, $ItemMid"); */
$monthCount;
$monthStrList;

$mCount = $monthCount;
$mStrList = $monthStrList;

if($ModuleType == "ExtList")
{
	$dataList = getMonthDetailArray($SelMonth, $ItemMid);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
	
	for ($monthIndex = 0; $monthIndex < $mCount; $monthIndex ++) {
			
		//head
		$rowSet = array(
			"height" => "44",
		);
		
		$onTap = array(
			"value" => "0",
			"target" => "",
			"args" => $mStrList[$monthIndex],
		);
		
		$title = array(
			"text" => $mStrList[$monthIndex],
		);
		
		//這是從網頁版的資料取得的月份總合
		$amount = array(
			"text" => "¥".number_format($monthSubTotal[$monthStrList[$monthIndex]]),
		);
		
		$headData = array(
			"rowSet" => $rowSet,
			"onTap" => $onTap,
			"title" => $title,
			"amount" => $amount,
		);
						
		//layout
		$col1 = array(
			"Frame" => "10, 0, 55, 22",
			"Align" => "L",
			"Color" => "#56aed3",
			"FontSize" => "12",
		);
		
		$col2 = array(
			"Frame" => "65, 0, 150, 22",
			"Align" => "L",
			"Color" => "#ef7e1b",
			"FontSize" => "12",
		);
		
		$remark = array(
			"Frame" => "10, 20, 285, 22",
			"Align" => "L",
			"Color" => "#888a8a",
			"FontSize" => "12",
		);
		
		$amount = array(
			"Frame" => "215, 0, 78, 22",
			"Align" => "R",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$state = array(
			"Frame" => "300, 0, 17, 17",
		);
		
		$layoutData = array(
			"Col1" => $col1,
			"Col2" => $col2,
			"Remark" => $remark,
			"Amount" => $amount,
			"State" => $state,
		);
	
		//月份的資料
		$isOnTap = "0";
		$dataList = array();
		
		if ($mStrList[$monthIndex] == $SelMonth) {
		
			$isOnTap = "1";
			$dataList = getMonthDetailArray($SelMonth, $ItemMid);	
			$headData["onTap"]["value"] = "1";
			
		}
		
		$monthData = array(
			"tag" => "head",
			"onTap" => $isOnTap,
			"head" => $headData,
			"layout" => $layoutData,
			"data" => $dataList,
		);

		$returnList[] = $monthData;
	}

	$jsonArray = $returnList;	
}

//取得指定月份的資料
function getMonthDetailArray($Month, $ItemId) {
/* 	echo($Month); */
	$detailList = array();
	unset($detailList);
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "/syb/syb_".$ItemId.".php";	
	ob_end_clean();
		
	$dataList = array();
	$tmpData = array();
	for ($detailIndex = 0; $detailIndex < count($detailList); $detailIndex++) {
		
		$responseData = $detailList[$detailIndex];
		
		$date = date_create($responseData["PayDate"]);
		$tmpData["Col1"] = array(
			"text" => date_format($date, 'm-d')
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["BillNumber"]
		);
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"]
		);
		
		$formatAmount = "¥".number_format($responseData["Amount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$tmpData["State"] = array(
			"icon" => $responseData["Estate"]
		);
		
		$dataList[] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
	}
	
	return $dataList;
}
?>