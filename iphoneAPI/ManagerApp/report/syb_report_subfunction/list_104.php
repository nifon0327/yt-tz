<?php 
/* echo("$SelMonth, $ItemMid"); */
$monthCount;
$monthStrList;

$mCount = $monthCount;
$mStrList = $monthStrList;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "", $ItemMid);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
	
	//rowset
	$rowSet = array(
		"height" => "30",
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
		
		$data = getDetailArray("", $dataT, $ItemMid);
		
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
				"target" => "",
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
				$dataList = getDetailArray($SelMonth, "", $ItemMid);	
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
		"Frame" => "20, 0, 55, 30",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "90, 0, 100, 30",
		"Align" => "L",
		"Color" => "#ef7e1b",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "200, 0, 78, 30",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "285, 0, 17, 30",
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
function getDetailArray($Month, $DataT, $ItemId) {
/* 	echo($Month); */
	$detailList = array();
	unset($detailList);
	
	$linkList = array();
	unset($linkList);	
	
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
		
		$date = date_create($responseData["Taxdate"]);
		$tmpData["Col1"] = array(
			"text" => date_format($date, 'm-d')
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["TaxNo"]
		);
		
		$formatAmount = "¥".number_format($responseData["Taxamount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$tmpData["State"] = array(
			"icon" => $responseData["Estate"]
		);
		
		$fileLink = $linkList[$responseData["Id"]];
		$fullPath = "$protocol://{$domain}{$fileLink}";
		$onTapData = array(
			"target" => "Picture",
			"value" => "0",
			"args" => array(
				$fullPath
			),
		);
		
		$arrow = array(
			"location" => "R",
			"style" => (strlen($fullPath) > 0) ? "Gray" : "None"
		);
		
		$dataList[] = array(
			"tag" => "data",
			"onTap" => $onTapData,
			"arrow" => $arrow,
			"data" => $tmpData,
		);
	}
	
	return $dataList;
}
?>