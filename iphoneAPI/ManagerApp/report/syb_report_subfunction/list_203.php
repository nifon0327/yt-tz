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
				"args" => $monthStrList[$monthIndex],
			);
			
			$arrow = array(
				"location" => "L",
				"style" => "GrayT"
			);
			
			$title = array(
				"text" => $monthStrList[$monthIndex],
			);
			
			//取得當月的已結付/未結付
			//傳入參數
			$Month = $monthStrList[$monthIndex];
			
			//記值用
			$appSumY = 0;
			$appSumW = 0;
			
			//採集月份的已結/未結
			ob_start();
			include "./syb/syb_".$ItemMid.".php";		
			ob_end_clean();
			
			$amount = array(
				"text" => "¥".number_format($appSumY + $appSumW)
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
		"Frame" => "225, 0, 75, 40",
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
	
	$dataList = array();
	
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
	
	//GysPayMode=1 现金，其余月结
	$gysPayMode = $parameter == 1 ? " AND B.GysPayMode = 1":" AND B.GysPayMode != 1";
	
	$EstateSTR = " AND A.Estate IN(0, 3) ";
	switch($DataT){
		case "Y":
			$EstateSTR = " AND A.Estate = 0 ";
		break;
		case "W":
			$EstateSTR = " AND A.Estate = 3 ";
		break;
	}
	
	$monthStr = "";
	if ((strlen($Month) > 0) && (($Month != "Payed") && ($Month != "NoPay") && ($Month != "Total"))) {
		$monthStr = "AND A.Month = '$Month'";
	}

	//AmountRMB >> 全部，AmountRMB_Y >> 已結，AmountRMB_W >> 未結
	$checkSql=mysql_query("SELECT 
							A.CompanyId,B.Forshort,SUM(A.Amount*C.Rate) AS AmountRMB,
							SUM(CASE WHEN A.Estate = 0 THEN A.Amount*C.Rate ELSE 0 END) AS AmountRMB_Y,
							SUM(CASE WHEN A.Estate = 3 THEN A.Amount*C.Rate ELSE 0 END) AS AmountRMB_W,
							SUM(CASE WHEN A.Estate IN (0, 3) THEN A.Amount*C.Rate ELSE 0 END) AS AmountRMB_A
							FROM $DataIn.cw1_tkoutsheet A
							LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId 
							LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
							LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
							WHERE 1
							$monthStr $EstateSTR
							GROUP BY A.CompanyId 
							ORDER BY A.CompanyId ASC, A.Month DESC,A.Id",$link_id);

	if($checkRow=mysql_fetch_array($checkSql)) {
		do {
		
			$companyList[] = $checkRow;
			
		} while ($checkRow=mysql_fetch_array($checkSql));
	}
	
	foreach ($companyList as $companyData) {
			
		//head
		$rowSet = array(
			"height" => "57"
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
					"text" => "¥".number_format($companyData["AmountRMB_A"])
				),			
				"amountW" => array(
					"text" => "¥".number_format($companyData["AmountRMB_W"])
				)
			)
		);
		
		//layout
		$title = array(
			"Frame" => "20, 0, 150, 40",
			"Align" => "L",
			"Color" => "#56aed3",
			"FontSize" => "13",
		);
		
		$col1 = array(
			"Frame" => "20, 0, 125, 22",
			"Align" => "L",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$col2 = array(
			"Frame" => "145, 0, 90, 22",
			"Align" => "L",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$col3 = array(
			"Frame" => "20, 17, 285, 22",
			"Align" => "L",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$col4 = array(
			"Frame" => "20, 34, 125, 22",
			"Align" => "L",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$col5 = array(
			"Frame" => "145, 34, 90, 22",
			"Align" => "L",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$amount = array(
			"Frame" => "220, 34, 80, 22",
			"Align" => "R",
			"Color" => "#555555",
			"FontSize" => "12",
		);
		
		$state = array(
			"Frame" => "280, 3, 15, 15",
		);
		
		$layoutData = array(
			"Title" => $title,
			"Col1" => $col1,
			"Col2" => $col2,
			"Col3" => $col3,
			"Col4" => $col4,
			"Col5" => $col5,
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
	
	//傳給syb_203的參數 1.device
	$device = "iphone";
	
	ob_start();
	include "../../basic/parameter.inc";
	include "../../syb/sybpath.php";
	include "./syb/syb_".$ItemId.".php";	
	ob_end_clean();
	
	$dataList = array();
	
	foreach ($detailList as $responseData) {
	
		//清空 tmpData
		$tmpData = array();
			
		$tmpData["Col1"] = array(
			"text" => $responseData["StockId"]
		);		
		
		$tmpData["Col2"] = array(
			"text" => $responseData["Forshort"]
		);
		
		$tmpData["Col3"] = array(
			"text" => $responseData["StuffCname"]
		);
		
		$tmpData["Col4"] = array(
			"text" => $responseData["OrderQty"]
		);
		
		$tmpData["Col5"] = array(
			"text" => "¥".$responseData["Price"]
		);
		
		$formatAmount = "¥".number_format($responseData["AmountRMB"]);
		$tmpData["Amount"] = array(
			"text" => $formatAmount
		);
		
		$tmpData["State"] = array(
			"icon" => $responseData["Estate"]
		);	
			
		$dataList[] = array(
			"tag" => "data",
			"data" => $tmpData,
		);
	}
	
	return $dataList;
}

?>