<?php 
/* echo("$SelMonth, $ItemMid"); */

$parameter = $itemParameter;

if($ModuleType == "ExtList")
{
	$dataList = getDetailArray($SelMonth, "", $ItemMid, $parameter);
	$jsonArray = $dataList;
}
else
{
	$returnList = array();
			
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
		
		$data = getDetailArray("", $dataT, $ItemMid, $itemParameter);
		
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
				$dataList = getDetailArray($SelMonth, "", $ItemMid, $parameter);	
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
		"Frame" => "20, 17, 250, 22",
		"Align" => "L",
		"Color" => "#858787",
		"FontSize" => "12",
	);
	
	$amount = array(
		"Frame" => "200, 17, 100, 22",
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
		
	/*
	//取得公司的公司名稱和這個月的總金額
	$companyList = array();
	
	$shipType = ($parameter == "") ? "AND A.ShipType!='debit'" : "AND A.ShipType='debit'";
	
	$checkSql=mysql_query("SELECT A.CompanyId,C.Forshort,SUM(B.Qty*B.Price*D.Rate) AS AmountRMB
							FROM $DataIn.ch1_shipmain A
							LEFT JOIN $DataIn.ch1_shipsheet B ON B.Mid=A.Id 
							LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
							LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
							WHERE A.Estate IN (0, 3) AND A.Sign=1 $shipType
							AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'
							GROUP BY A.CompanyId 
							ORDER BY CompanyId ASC, A.Date DESC",$link_id);

						
	if($checkRow=mysql_fetch_array($checkSql)) {
		do {
		
			$companyList[] = $checkRow;
			
		} while ($checkRow=mysql_fetch_array($checkSql));
	}
	*/
	
	$domain = $_SERVER["SERVER_NAME"];
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
			
	//篩選出公司的資料
	//紀錄上一筆row的companyId
	$lastId = "";
	
	for ($detailIndex = 0; $detailIndex < count($detailList); $detailIndex++) {
		
		$tmpData = array();
		$responseData = $detailList[$detailIndex];
		
		/*
		//detailList會依照公司做排序，目前的companyId跟上一筆row的companyId 不一樣，代表要進入下一間公司的資料了
		if ($lastId != $responseData["CompanyId"]) {
			echo($lastId."<br>");
			//供應商的相關資料，是此公司交易資料的第一個row

			//篩選出來的第一筆資料			
			$newfunc = create_function('$var', 'return (is_array($var) && $var["CompanyId"] == '.$responseData["CompanyId"].');');
			$companyInfo = reset(array_filter($companyList, $newfunc));

			$tmpData["Title"] = array(
				"text" => $companyInfo["Forshort"]
			);
			
			$formatAmount = "¥".number_format($companyInfo["AmountRMB"]);
			$tmpData["Amount"] = array(
				"text" => $formatAmount
			);
						
			$dataList[] = array(
				"tag" => "data",
				"data" => $tmpData,
			);
			
			$lastId = $responseData["CompanyId"];
		}			
		*/
		
		/*
		//清空 tmpData
		$tmpData = array();
		*/
			
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
		$tmpData["Amount"] = array(
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