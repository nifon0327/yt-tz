<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "", $listFileName);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
			
	$rowSet = array(
		"height" => "57",
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
				$dataList = getDetailArray($SelMonth, "", $listFileName);	
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
		"Frame" => "20, 0, 60, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "80, 0, 60, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "170, 0, 75, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col4 = array(
		"Frame" => "250, 0, 50, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col5 = array(
		"Frame" => "23, 17, 125, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col6 = array(
		"Frame" => "135, 17, 80, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "230, 17, 70, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "13",
	);
	
	$remark = array(
		"Frame" => "20, 35, 280, 22",
		"Align" => "L",
		"Color" => "#888a8a",
		"FontSize" => "12",
	);
	
	$layoutData = array(
		"Col1" => $col1,
		"Col2" => $col2,
		"Col3" => $col3,
		"Col4" => $col4,
		"Col5" => $col5,
		"Col6" => $col6,
		"Remark" => $remark,
		"Amount" => $amount
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
	include "/syb/syb_".$ItemId.".php";	
	ob_end_clean();
	
	$domain = $_SERVER["SERVER_NAME"];
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		
	$dataList = array();
	$tmpData = array();
	for ($detailIndex = 0; $detailIndex < count($detailList); $detailIndex++) {
		
		$responseData = $detailList[$detailIndex];
		
		$tmpData["Col1"] = array(
			"text" => $responseData["Name"]
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["Branch"]
		);
		
		$tmpData["Col3"] = array(
			"text" => $responseData["TypeName"]
		);
		
		$tmpData["Col4"] = array(
			"text" => "第".$responseData["Time"]."次"
		);
		
		$tmpData["Col5"] = array(
			"text" => "¥".$responseData["AveAmount"],
			"lIcon" => "ic_bom_average.png"
		);
		
		$tmpData["Col6"] = array(
			"text" => $responseData["TotalRate"]."个月"
		);
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Content"]
		);
		
		$formatAmount = "¥".number_format($responseData["Amount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$dataList[$detailIndex] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
		
		//tap data
		$fullPath = "";
		if ($responseData["Bill"] == 1) {
			$fileName = $responseData["Number"].".jpg";			
			$fileLink = "/download/staff_subsidy/".$fileName;
			$fullPath = "$protocol://{$domain}{$fileLink}";		
		}
		$arrow = array(
			"location" => "R",
			"style" => "Gray"
		);
		$dataList[$detailIndex]["arrow"] = $arrow;	
		$dataList[$detailIndex]["onTap"] = array(
			"target" => "Picture",
			"value" => "0",
			"args" => array(
				$fullPath
			),
		);
	}
	
	return $dataList;
}
?>