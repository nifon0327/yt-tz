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
		"Frame" => "20, 0, 55, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col2 = array(
		"Frame" => "75, 0, 150, 22",
		"Align" => "L",
		"Color" => "#56aed3",
		"FontSize" => "12",
	);
	
	$col3 = array(
		"Frame" => "20, 17, 285, 22",
		"Align" => "L",
		"Color" => "#ef7e1b",
		"FontSize" => "12",
	);
	
	$remark = array(
		"Frame" => "20, 34, 280, 22",
		"Align" => "L",
		"Color" => "#888a8a",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "180, 0, 100, 22",
		"Align" => "R",
		"Color" => "#555555",
		"FontSize" => "12",
	);
	
	$state = array(
		"Frame" => "285, 0, 15, 22",
	);
	
	$layoutData = array(
		"Col1" => $col1,
		"Col2" => $col2,
		"Col3" => $col3,
		"Remark" => $remark,
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
	
	//随机码（载体）
	$ReferenceMark = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$motherSTR[] = array();
	for($i=0;$i<32;$i++) {
		$motherSTR[$i] = $ReferenceMark[rand(0,60)];
	}
	//渗透序号码(不变)
	$SinkOrder = "xacdefghijklmbnopqrstuvwyz";
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "/syb/syb_".$ItemId.".php";
	ob_end_clean();
		
	$domain = $_SERVER["SERVER_NAME"];
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
	
	$dataList = array();
	
	for ($detailIndex = 0; $detailIndex < count($detailList); $detailIndex++) {
		
		$tmpData = array();
		$responseData = $detailList[$detailIndex];
		
		$date = date_create($responseData["payDate"]);
		$tmpData["Col1"] = array(
			"text" => date_format($date, 'm-d')
		);
		
		$tmpData["Col2"] = array(
			"text" => $responseData["TypeName"]
		);
		
		$tmpData["Col3"] = array(
			"text" => $responseData["getmoneyNO"]
		);
		
		$tmpData["Remark"] = array(
			"text" => $responseData["Remark"]
		);
		
		$formatAmount = "¥".number_format($responseData["AmountRMB"]);
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
			"style" => (strlen($fileLink) > 0) ? "Gray" : "None"
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