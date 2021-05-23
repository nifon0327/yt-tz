<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

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

/* 	echo($dataT."<br>"); */
if($ModuleType == "ExtList")
{
	if ($SelCompany == "") {
		$dataList = getDetailArray($SelMonth, "", $listFileName, $parameter);
	}
	else if ($dataT != "") {
		$dataList = getCompanyDetailArray($SelCompany, "", $dataT, $listFileName, $parameter);
	}
	else {
		$dataList = getCompanyDetailArray($SelCompany, $SelMonth, $dataT, $listFileName, $parameter);	
	}
	
	$jsonArray = $dataList;
}
else
{
	include "../../syb/sybpath.php";

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
		
		$data = getDetailArray($SelMonth, $dataT, $listFileName, $parameter);
		
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
				"args" => $monthStrList[$monthIndex]
			);
			
			$arrow = array(
				"location" => "L",
				"style" => "GrayT"
			);
			
			//取得當月的已結付/未結付
			//這是從網頁版的資料取得的月份總合
			//傳入參數
			$Month = $monthStrList[$monthIndex];
			
			//傳給syb_101的參數 1.device 2.parameters = debit
			$device = "iphone";
			if (strlen($parameter) > 0)	{
				$Parameters = $parameter;	
			}
			else {
				$Parameters = "";
			}
			
			//記值用
			$appSumA = 0;
			$appSumW = 0;
			
			//採集月份的已結/未結
			ob_start();
			include "./syb/syb_".$ItemMid.".php";		
			ob_end_clean();
			
			$title = array(
				"text" => $monthStrList[$monthIndex],
			);
			
			$amount = array(
				"text" => "¥".number_format($appSumA)
			);
			
			$amountW = array(
				"text" => "¥".number_format($appSumW)
			);
			
			$headData = array(
				"rowSet" => $rowSet,
				"onTap" => $onTap,
				"arrow" => $arrow,
				"data" => array(
					"title" => $title,
					"amount" => $amount,
					"amountW" => $amountW
				)
			);
		
			//月份的資料
			$isOnTap = "0";
			$dataList = array();
			
			if ($monthStrList[$monthIndex] == $SelMonth) {
			
				$isOnTap = "1";
				$dataList = getDetailArray($SelMonth, "", $listFileName, $parameter);	
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
	
	$title = array(
		"Frame" => "20, 0, 110, 40",
		"Align" => "L",
		"Color" => "#56AED3",
		"FontSize" => "13",
	);	
		
	$amountW = array(
		"Frame" => "120, 0, 75, 40",
		"Align" => "R",
		"Color" => "#d92126",
		"FontSize" => "13",
	);
		
	$amount = array(
		"Frame" => "210, 0, 90, 40",
		"Align" => "R",
		"FontSize" => "13",
	);
	
	$layoutData = array(
		"title" => $title,
		"amount" => $amount,
		"amountW" => $amountW
	);
	
	return $layoutData;
}

//取得指定月份的資料
function getDetailArray($Month, $DataT, $ItemId, $parameter) {

	$detailList = array();
	unset($detailList);
	
	$linkList = array();
	unset($linkList);
	
	$dataList = array();
	
	//傳給syb_101的參數 1.device 2.parameters = debit
	$device = "iphone";
	if (strlen($parameter) > 0)	{
		$Parameters = $parameter;	
	}
	
	ob_start();
	if (file_exists("../syb/sybpath.php")) {
		include "../syb/sybpath.php";
	}
	else {
		include "../../syb/sybpath.php";
	}
	include "../basic/parameter.inc";
/* 	include "./syb/syb_".$ItemId.".php";	 */
	ob_end_clean();
	
	//取得公司的公司名稱和這個月的總金額
	$companyList = array();
	
	$shipType = ($parameter == "") ? "AND A.ShipType!='debit'" : "AND A.ShipType='debit'";

	$EstateSTR = "";
	switch($DataT) {
		case "Y":
			$EstateSTR = "AND (A.cwSign=0 OR A.cwSign=2)"; 
			break;
		case "W":
			$EstateSTR = "AND (A.cwSign=1 OR A.cwSign=2)";
			break;
	}
	
	$monthStr = "";
	if ((strlen($Month) > 0) && (($Month != "Payed") && ($Month != "NoPay") && ($Month != "Total"))) {
		$monthStr = "AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
	}
	
	$checkSql=mysql_query("SELECT A.CompanyId,C.Forshort,SUM(B.Qty*B.Price*D.Rate) AS AmountRMB,
							SUM(CASE WHEN (A.cwSign = 0) THEN B.Qty*B.Price*D.Rate ELSE 0 END) AS AmountRMB_Y,
							SUM(CASE WHEN (A.cwSign != 0) THEN B.Qty*B.Price*D.Rate ELSE 0 END) AS AmountRMB_W
							FROM $DataIn.ch1_shipmain A
							LEFT JOIN $DataIn.ch1_shipsheet B ON B.Mid=A.Id 
							LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
							LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
							WHERE A.Estate IN (0, 3) AND A.Sign=1 $shipType
							$monthStr $shipType $EstateSTR
							GROUP BY A.CompanyId 
							ORDER BY CompanyId ASC, A.Date DESC",$link_id);
						
	if($checkRow=mysql_fetch_array($checkSql)) {
		do {
		
			$companyList[] = $checkRow;
			
		} while ($checkRow=mysql_fetch_array($checkSql));
	}	
	
	foreach ($companyList as $companyData) {
			
		//head
		$rowSet = array(
			"height" => "40"
		);		
		
		$onTap = array(
			"value" => "0",
			"target" => "data",
			"args" => $Month."|".$companyData["CompanyId"],
		);
		
		$headData = array(
			"rowSet" => $rowSet,
			"onTap" => $onTap,
			"data" => array(
				"title" => array(
					"text" => $companyData["Forshort"]
				),
				"amount" => array(
					"text" => "¥".number_format($companyData["AmountRMB"])
				),			
				"amountW" => array(
					"text" => "¥".number_format($companyData["AmountRMB_W"])
				)
			)
		);
		
		//layout		
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
			"Frame" => "20, 15, 280, 22",
			"Align" => "L",
			"Color" => "#858787",
			"FontSize" => "12",
		);
		
		$col4 = array(
			"Frame" => "200, 15, 100, 22",
			"Align" => "R",
			"Color" => "#858787",
			"FontSize" => "12",
		);
		
		$amount = array(
			"Frame" => "200, 0, 100, 40",
			"Align" => "R",
			"Color" => "#858787",
			"FontSize" => "13",
		);
		
		$state = array(
			"Frame" => "285, 3, 15, 15",
		);
		
		$layoutData = array(
			"Col1" => $col1,
			"Col2" => $col2,
			"Col3" => $col3,
			"Col4" => $col4,
			"Amount" => $amount,
			"State" => $state,
		);
		
		$dataList[] = array(
			"tag" => "head",
			"onTap" => "0",
			"head" => $headData,
			"layout" => $layoutData,
			"data" => array()
		);
	}
		
	return $dataList;
}

function getCompanyDetailArray($companyId, $Month, $DataT, $ItemId, $parameter) {
	
	$detailList = array();
	unset($detailList);
	
	$linkList = array();
	unset($linkList);
	
	//傳給syb_101的參數 1.device 2.parameters = debit
	$device = "iphone";
	if (strlen($parameter) > 0)	{
		$Parameters = $parameter;	
	}
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "./syb/syb_".$ItemId.".php";	
	ob_end_clean();
	
/* 	echo($testSql."<br>"); */
	
	$dataList = array();
	
	$domain = $_SERVER["SERVER_NAME"];
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
	
	foreach ($detailList as $responseData) {
	
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
			"text" => $responseData["InvoiceNO"]
		);
		
		$formatAmount = "¥".number_format($responseData["AmountRMB"]);
		$tmpData["Col4"] = array(
			"text" => $formatAmount
		);
		
		$state = ($responseData["cwSign"] == 0) ? "0" : "3";
		$tmpData["State"] = array(
			"icon" => $state
		);	
		
		$detailItem = array(
			"tag" => "data",
			"data" => $tmpData,
		);
		
		$fileLink = $linkList[$responseData["Id"]];
		if (strlen($fileLink) > 0) {
			
			$fullPath = "$protocol://{$domain}{$fileLink}";
	
			$onTapData = array(
				"target" => "Picture",
				"value" => "0",
				"args" => $fullPath,
			);
			
			$arrow = array(
				"location" => "R",
				"style" => (strlen($fullPath) > 0) ? "Gray" : "None"
			);
			
			$detailItem["onTap"] = $onTapData;
			$detailItem["arrow"] = $arrow;
		}
			
		$dataList[] = $detailItem;		
	}
	
	return $dataList;
}

?>