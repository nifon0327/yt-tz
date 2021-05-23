<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "A", $listFileName);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
			
	//rowset
	$rowSet = array(
		"height" => "40",
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
		
		$data = getDetailArray("", $dataT, $listFileName);
		
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
				$dataList = getDetailArray($SelMonth, "A", $listFileName);	
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
		"Color" => "#858787",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "65, 0, 80, 22",
		"Align" => "L",
		"Color" => "#858787",
		"FontSize" => "12",
	);
	
	$remark = array(
		"Frame" => "20, 17, 280, 22",
		"Align" => "L",
		"Color" => "#888a8a",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "180, 0, 100, 22",
		"Align" => "R",
		"Color" => "#858787",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "285, 3, 15, 15",
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
function getDetailArray($Month, $DataT, $ItemId) {

	$detailList = array();
	unset($detailList);
	
	$dataList = array();
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "./syb/syb_".$ItemId.".php";
	ob_end_clean();
	
	$domain = $_SERVER["SERVER_NAME"];
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
	
	for ($detailIndex = 0; $detailIndex < count($detailList); $detailIndex++) {
		
		$tmpData = array();
		$responseData = $detailList[$detailIndex];
		
		//清空 tmpData
		$tmpData = array();
		
		$date = date_create($responseData["Date"]);
		$tmpData["Col1"] = array(
			"text" => $responseData["Name"]
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["ChildName"]
		);
		
		$formatAmount = "¥".number_format($responseData["Amount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"]
		);
		
		$tmpData["State"] = array(
			"icon" => $responseData["Estate"]
		);
		
		$dataList[$detailIndex] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
		
		$fileLink = $responseData["FilePath"];
		if (strlen($fileLink) > 0) {	
		
			$arrow = array(
				"location" => "R",
				"style" => "Gray"
			);
			$dataList[$detailIndex]["arrow"] = $arrow;	
			
			$fullPath = "$protocol://{$domain}{$fileLink}";
			$dataList[$detailIndex]["onTap"] = array(
				"target" => "Picture",
				"value" => "0",
				"args" => $fullPath,
			);
		}		
	}
	
	return $dataList;
}
?>
		