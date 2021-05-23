<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

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
		
		$data = getDetailArray("", $dataT, $ItemMid);
		
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
							
			//layout
		
			//月份的資料
			$isOnTap = "0";
			$dataList = array();
			
			if ($monthStrList[$monthIndex] == $SelMonth) {
			
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
		"Frame" => "20, 0, 60, 22",
		"Align" => "L",
		"Color" => "#56AED3",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "80, 0, 150, 22",
		"Align" => "L",
		"Color" => "#56AED3",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "20, 17, 125, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col4 = array(
		"Frame" => "150, 17, 65, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col5 = array(
		"Frame" => "268, 17, 30, 22",
		"Align" => "M",
		"Color" => "#56AED3",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "215, 17, 85, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "285, 3, 15, 15",
	);
	
	$remark = array(
		"Frame" => "20, 34, 280, 22",
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
	
	$linkList = array();
	unset($linkList);
	
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
		
		$date = date_create($responseData["Date"]);
		$tmpData["Col1"] = array(
			"text" => date_format($date, 'm-d')
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["Forshort"]
		);
		
		$tmpData["Col3"] = array(
			"text" => $responseData["InvoiceNUM"]
		);
		
		$formatFPAmount = "¥".number_format($responseData["Fpamount"]);
		$tmpData["Col4"] = array(
			"text" => $formatFPAmount,
			"lIcon" => "ic_bom_invoiceAmount.png"
		);
		
		$formatRate = sprintf("%.0f%%", $responseData["Rate"] * 100);
		$tmpData["Col5"] = array(
			"text" => $formatRate
		);
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"]
		);
		
		$formatAmount = "¥".number_format($responseData["AmountRMB"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount."(　 　)"
		);
		
		$tmpData["State"] = array(
			"icon" => $responseData["Estate"]
		);
		
		$detailItem = array(
			"tag" => "data",
			"data" => $tmpData,
		);
			
		$fileLink = $linkList[$responseData["Id"]];
		$fullPath = "";
		if (strlen($fileLink) > 0) {
			$fullPath = "$protocol://{$domain}{$fileLink}";
		}
		
		$onTapData = array(
			"target" => "Picture",
			"value" => "0",
			"args" => array(
				$fullPath
			),
		);
		
		$arrow = array(
			"location" => "R",
			"style" => "Gray"
		);
		
		$detailItem["onTap"] = $onTapData;
		$detailItem["arrow"] = $arrow;
		
		$dataList[] = $detailItem;
		
	}
	
	return $dataList;
}
?>