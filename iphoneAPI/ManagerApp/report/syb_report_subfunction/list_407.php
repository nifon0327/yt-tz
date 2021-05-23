<?php 
/* echo("$SelMonth, $ItemMid"); */

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "", $listFileName, $itemParameter);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
			
	//rowset			
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
	
		for ($monthIndex = 0; $monthIndex < $monthCount; $monthIndex ++) {
				
			//head			
			$onTap = array(
				"value" => "0",
				"target" => "",
				"args" => $monthStrList[$monthIndex],
			);
			
			$arrow = array(
				"location" => "L",
				"style" => "GrayT"
			);
			
			$title = array(
				"text" => $monthStrList[$monthIndex],
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
			
			if ($monthStrList[$monthIndex] == $SelMonth) {
			
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
		"Frame" => "20, 0, 250, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "20, 20, 280, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "200, 20, 100, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "285, 3, 15, 15",
	);
	
	$layoutData = array(
		"Col1" => $col1,
		"Col2" => $col2,
		"Amount" => $amount,
		"State" => $state,
	);
	
	return $layoutData;
}

//取得指定月份的資料
function getDetailArray($Month, $DataT, $ItemId, $Parameters) {

	$detailList = array();
	unset($detailList);
	
	//意外保險的TypeId = 3
	$Parameters = "3";
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "./syb/syb_".$ItemId.".php";	
	ob_end_clean();
		
	$dataList = array();
	$tmpData = array();
	for ($detailIndex = 0; $detailIndex < count($detailList); $detailIndex++) {
		
		$responseData = $detailList[$detailIndex];
		
		$tmpData["Col1"] = array(
			"text" => $responseData["Name"]
		);
		
		$jobString = $responseData["Branch"]."-".$responseData["Job"];
		$tmpData["Col2"] = array(
			"text" => $jobString
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