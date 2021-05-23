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
			
	$rowSet = array(
		"height" => "74",
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
		"Frame" => "90, 0, 150, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "20, 17, 280, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col4 = array(
		"Frame" => "20, 34, 55, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col5 = array(
		"Frame" => "90, 34, 50, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col6 = array(
		"Frame" => "152, 34, 45, 22",
		"Align" => "L",
		"Color" => "#d92126",
		"FontSize" => "12",
	);
	
	$col7 = array(
		"Frame" => "240, 0, 60, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "200, 34, 100, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$remark = array(
		"Frame" => "20, 51, 280, 22",
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
		"Col7" => $col7,
		"Amount" => $amount,
		"Remark" => $remark,
	);
	
	return $layoutData;
}

//取得指定月份的資料
function getDetailArray($Month, $DataT, $ItemId, $parameter) {

	$detailList = array();
	unset($detailList);
	
	$dataList = array();
	
	//傳給syb_10的參數 1.parameters = [種類]
	if (strlen($parameter) > 0)	{
		$Parameters = $parameter;	
	}
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "/syb/syb_".$ItemId.".php";	
	ob_end_clean();
	
	$domain = $_SERVER["SERVER_NAME"];
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
			
	//篩選出公司的資料
	//紀錄上一筆row的companyId
	$lastId = "";
	
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
			"text" => $responseData["GoodsName"]
		);
		
		$tmpData["Col4"] = array(
			"text" => $responseData["PurchaseID"]
		);
		
		$tmpData["Col5"] = array(
			"text" => $responseData["Qty"]
		);
		
		if ($responseData["wsQty"] > 0) {
			$tmpData["Col6"] = array(
				"text" => sprintf("%.1f", $responseData["wsQty"]),
				"lIcon" => "ic_bom_wsQty.png"
			);
		}		
		
		$tmpData["Col7"] = array(
			"text" => $responseData["Name"]
		);
		
		$formatAmount = $responseData["PreChar"].number_format($responseData["Amount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);	
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"]
		);
		
		$dataList[$detailIndex] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
		
/*
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
*/	
		
		$fileLink = $responseData["FilePath"];
		$arrow = array(
			"location" => "R",
			"style" => "Gray"
		);			
		$dataList[$detailIndex]["arrow"] = $arrow;
		
		$fullPath = ($responseData["FilePath"] == "") ? "" : "$protocol://{$domain}{$fileLink}";
		$dataList[$detailIndex]["onTap"] = array(
			"target" => "Picture",
			"value" => "0",
			"args" => $fullPath,
		);
	}
	
	return $dataList;
}
?>
		