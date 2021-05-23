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
/*
	echo("monthStrList<br>".var_export($monthStrList)."<br>");
	echo("monthSubTotal<br>".var_export($monthSubTotal)."<br>");
*/
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
			$dataT = "W";
		}
		else if ($SelMonth == "NoPay") {
			$dataT = "Y";
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
		"Frame" => "80, 0, 130, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "160, 0, 80, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$col4 = array(
		"Frame" => "150, 17, 80, 22",
		"Align" => "L",
		"Color" => "#6CAE2F",
		"FontSize" => "12",
	);
	
	$col5 = array(
		"Frame" => "250, 17, 50, 22",
		"Align" => "R",
		"Color" => "#6CAE2F",
		"FontSize" => "12",
	);
	
	/*
$col3 = array(
		"Frame" => "240, 0, 60, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "160, 0, 80, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
*/
	
	$amount = array(
		"Frame" => "23, 17, 80, 22",
		"Align" => "L",
		"Color" => "#555555",
		"FontSize" => "12",
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
		"Amount" => $amount,
		"Remark" => $remark
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
/* 	$DataT = "A"; */
	
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
		
		$date = date_create($responseData["PayDate"]);
		$tmpData["Col1"] = array(
			"text" => date_format($date, 'm-d')
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["Name"],
		);
		
		/*
$Operator = $responseData["Operator"];
		include "../model/subprogram/staffname.php";
*/
		$tmpData["Col3"] = array(
			"text" => "员工借支",
		);
		
		$formatAmount = "¥".number_format($responseData["Amount"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount,
			"lIcon" => "ic_borrow_borrow.png",
		);
		
		$backDate = $responseData["InDate"];
		if (strlen($backDate) > 0) {
			
			$tmpData["Col4"] = array(
				"text" => $formatAmount,
				"lIcon" => "ic_borrow_back.png",
			);	
			
			$backDate = date_create($backDate);
			$tmpData["Col5"] = array(
				"text" => date_format($backDate, 'm-d'),
			);		
		}
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"],
		);
		
		$dataList[$detailIndex] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
		
		$fileLink = $responseData["FilePath"];
		if (strlen($fileLink) > 0) {		
			
			$fullPath = "$protocol://{$domain}{$fileLink}";
			$dataList[$detailIndex]["onTap"] = array(
				"target" => "Picture",
				"value" => "0",
				"args" => array(
					$fullPath
				),
			);
		}		
			
		$arrow = array(
			"location" => "R",
			"style" => (strlen($fileLink) > 0) ? "Gray" : "None"
		);			
		$dataList[$detailIndex]["arrow"] = $arrow;
	}
	
	return $dataList;
}
?>
		