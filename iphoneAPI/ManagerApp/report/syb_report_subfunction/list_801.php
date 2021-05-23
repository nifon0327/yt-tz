<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "", $listFileName, $parameter, $HKD_Rate);
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
		
		$data = getDetailArray("", $dataT, $listFileName, $parameter, $HKD_Rate);
		
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
				"target" => "data",
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
				$dataList = getDetailArray($SelMonth, "", $listFileName, $parameter, $HKD_Rate);	
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
		"Frame" => "80, 0, 100, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "160, 0, 75, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col4 = array(
		"Frame" => "20, 17, 125, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col5 = array(
		"Frame" => "90, 17, 125, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col6 = array(
		"Frame" => "200, 17, 100, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "180, 17, 60, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "285, 3, 15, 15",
	);
	
	$layoutData = array(
		"Col1" => $col1,
		"Col2" => $col2,
		"Col3" => $col3,
		"Col4" => $col4,
		"Col5" => $col5,
		"Col6" => $col6,
		"Amount" => $amount,
		"State" => $state
	);
	
	return $layoutData;
}

//取得指定月份的資料
function getDetailArray($Month, $DataT, $ItemId, $parameter, $HKD_Rate) {

	$detailList = array();
	unset($detailList);
	
	$dataList = array();
	
	if (strlen($parameter) > 0)	{
		$Parameters = $parameter;	
	}
	//傳給 syb_801 撈已請款的資料
	$DataT = "A";
	
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
			"text" => date_format($date, 'm-d')
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["Forshort"]
		);
		
		$tmpData["Col3"] = array(
			"text" => $responseData["Termini"]
		);
		
		$tmpData["Col4"] = array(
			"text" => sprintf("%.2fkg", $responseData["mcWG"])
		);
		
		$tmpData["Col5"] = array(
			"text" => ($responseData["forwardWG"] == "") ? "" : sprintf("%.2fkg", $responseData["forwardWG"]),
			"lIcon" => "ic_ship_flower.png"
		);
		
		$tmpData["Col6"] = array(
			"text" => sprintf("HK$%.2f", $responseData["depotCharge"])
		);
		
		$formatAmount = "¥".number_format($responseData["AmountRMB"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$tmpData["State"] = array(
			"icon" => $responseData["Estate"]
		);
		
		$dataList[$detailIndex] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
		
		$invoiceFilePath = $responseData["invoiceFilePath"];
		$billFilePath = $responseData["billFilePath"];
		if ((strlen($invoiceFilePath) > 0) || (strlen($billFilePath) > 0)) {
		
			$onTapData = array(
				"target" => "Picture",
				"value" => "0",
				"args" => array(
					"$protocol://{$domain}{$invoiceFilePath}",
					"$protocol://{$domain}{$billFilePath}"
				),
				"notes" => array(
					$responseData["InvoiceNO"],
					$responseData["ExpressNO"]
				)
			);
			$dataList[$detailIndex]["onTap"] = $onTapData;
			
			$arrow = array(
				"location" => "R",
				"style" => ((strlen($invoiceFilePath) > 0) || (strlen($billFilePath) > 0)) ? "Gray" : "None"
			);
			$dataList[$detailIndex]["arrow"] = $arrow;
		}
	}
	
	return $dataList;
}
?>
		