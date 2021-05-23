<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "A", $listFileName, $parameter);
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
		
		$data = getDetailArray("", $dataT, $listFileName, $parameter);
		
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
				$dataList = getDetailArray($SelMonth, "A", $listFileName, $parameter);	
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
		"Frame" => "80, 0, 60, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "140, 0, 145, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col4 = array(
		"Frame" => "25, 17, 125, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	/*
	$col5 = array(
		"Frame" => "155, 17, 125, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	*/
	
	$amount = array(
		"Frame" => "200, 17, 100, 22",
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
		"Col3" => $col3,
		"Col4" => $col4,
		//"Col5" => $col5,
		"Amount" => $amount,
		"State" => $state
	);
	
	return $layoutData;
}

//取得指定月份的資料
function getDetailArray($Month, $DataT, $ItemId, $parameter) {

	$detailList = array();
	unset($detailList);
	
	$dataList = array();
	
	//傳給syb_4的參數 1.parameters = [種類]
	if (strlen($parameter) > 0)	{
		$Parameters = $parameter;	
	}
	
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
			"text" => "¥".sprintf("%.2f",$responseData["declarationCharge"]),
			"lIcon" => "ic_ship_report.png"
		);
		
		/*
		$tmpData["Col5"] = array(
			"text" => "¥".number_format($checkRow["checkCharge"]),
			"lIcon" => "ic_ship_check.png"
		);
		*/
		
		$formatAmount = sprintf("%.2f", $responseData["declarationCharge"]) + sprintf("%.2f", $responseData["checkCharge"]);
		$tmpData["Amount"] = array(
			"text" => "¥".sprintf("%.2f", $formatAmount)
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
					$responseData["ExpressNO"],
				)
			);
			
			$arrow = array(
				"location" => "R",
				"style" => ((strlen($invoiceFilePath) > 0) || (strlen($billFilePath) > 0)) ? "Gray" : "None"
			);
			
			$dataList[$detailIndex]["onTap"] = $onTapData;
			$dataList[$detailIndex]["arrow"] = $arrow;
		}
	}
	
	return $dataList;
}
?>
		