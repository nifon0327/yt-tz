<?php 
/* echo("$SelMonth, $ItemMid"); */
$monthCount;
$monthStrList;

$mCount = $monthCount;
$mStrList = $monthStrList;

$parameter = $itemParameter;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "", $listFileName, $itemParameter);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
			
	$rowSet = array(
		"height" => "44",
	);
							
	//layout
	$layoutData = getLayout();
	
	if (($SelMonth == "Payed") || ($SelMonth == "NoPay") || ($SelMonth == "Total")) {
					
		//head
		$onTap = array(
			"value" => "0",
			"target" => "",
			"args" => "hiddenField",
		);
		
		$headData = array(
			"rowSet" => $rowSet,
			"onTap" => $onTap,
			"hidden" => "1",
			"data" => array()
		);
		
		$dataT = "";
		if ($SelMonth == "Payed") {
			$dataT = "Y";
		}
		else if ($SelMonth == "NoPay") {
			$dataT = "W";
		}
		else if ($SelMonth == "Total") {
			$dataT = "A";
		}
		
		$data = getDetailArray("", $dataT, $listFileName, $itemParameter);
		
		$returnList[] = array(
			"tag" => "hidden",
			"head" => $headData,
			"layout" =>	$layoutData,
			"data" => $data
		);
		
	}
	else {
		
		for ($monthIndex = 0; $monthIndex < $mCount; $monthIndex ++) {
				
			//head		
			$onTap = array(
				"value" => "0",
				"target" => "data",
				"args" => $mStrList[$monthIndex],
			);
			
			$arrow = array(
				"location" => "L",
				"style" => "GrayT"
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
				"arrow" => $arrow,
				"data" => array(				
					"title" => $title,
					"amount" => $amount,
				)
			);
		
			//月份的資料
			$isOnTap = "0";
			$dataList = array();
			
			if ($mStrList[$monthIndex] == $SelMonth) {
			
				$isOnTap = "1";
				$dataList = getDetailArray($SelMonth, "", $listFileName, $itemParameter);	
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
	}

	$jsonArray = $returnList;	
}

//取得layout資料，讓月份統計與已結/未結/小計可以共用
function getLayout() {
	
	$col1 = array(
		"Frame" => "20, 0, 55, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "65, 0, 80, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$remark = array(
		"Frame" => "20, 17, 280, 22",
		"Align" => "L",
		"Color" => "#888a8a",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "200, 0, 100, 22",
		"Align" => "R",
		"Color" => "#858787",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "303, 3, 15, 15",
	);
	
	$layoutData = array(
		"Col1" => $col1,
		"Col2" => $col2,
		"Remark" => $remark,
		"Amount" => $amount,
		"State" => $state,
	);
	
	return $layoutData;
}

//取得指定月份的資料
function getDetailArray($Month, $DataT, $ItemId, $parameter) {
/* 	echo($Month); */
	$detailList = array();
	unset($detailList);
	
	if (strlen($parameter) > 0)	{
		$Parameters = $parameter;	
	}
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
			"text" => $responseData["Forshort"]
		);
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"]
		);
		
		$formatAmount = "¥".number_format($responseData["RmbAmount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$estate = "3";
		$mid = intval($responseData["Mid"]);
		if (intval($mid) > 0) {
			$estate = "0";	
		}		 
		$tmpData["State"] = array(
			"icon" => $estate
		);
		
		$dataList[] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
	}
	
	return $dataList;
}
?>