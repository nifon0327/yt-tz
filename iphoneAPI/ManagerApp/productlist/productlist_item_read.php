<?php 
//讀取產品列表
//产品价格权限设置
$CheckBranchId=mysql_fetch_array(mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id));
$BranchId=$CheckBranchId["BranchId"];

$companyList = array();
$productList = array();


$companyId = $info[0];
if(strlen($companyId) == 0) {
	/*
	$comListSql = "SELECT M.CompanyId, M.Forshort 
					FROM $DataIn.trade_object M 
					WHERE M.Estate = 1 AND M.ObjectSign IN (1,2)
					ORDER BY M.Id";
    */
	$comListSql = "SELECT M.CompanyId, M.Forshort,Count(*) AS ProductCounts, C.PreChar
					FROM $DataIn.trade_object M 
					LEFT JOIN $DataIn.productdata P ON P.CompanyId = M.CompanyId 
					LEFT JOIN $DataPublic.currencydata C ON M.Currency = C.Id
					WHERE M.Estate = 1 
					AND M.ObjectSign IN (1,2)
					AND P.Estate = 1 
					GROUP BY M.CompanyId ORDER BY ProductCounts DESC";
					
	//公司列表
	$comListResult = mysql_query($comListSql, $link_id);
	if ($row = mysql_fetch_array($comListResult)) {
		do {
			$companyList[] = array(
				"Id" => $row["CompanyId"],
				"Name" => $row["Forshort"],
				"CurrencySign" => $row["PreChar"],
				"ProductCount" => $row["ProductCounts"],
			);
		}
		while ($row = mysql_fetch_array($comListResult));
	}
	
	$companyId = $companyList[0]["Id"];
}



//產品列表
$pageInfo = "";
$searchInfo = "";
if ($dModuleId == "main") {
	$startIndex = $info[1];
	$countPerPage = $info[2];
	if ((strlen($startIndex) > 0) && (strlen($countPerPage) > 0)) {
		$pageInfo = "LIMIT $startIndex, $countPerPage";
	}
}
else if ($dModuleId == "search") {
	$searchString = $info[1];
	$searchInfo = "AND CONCAT(P.Id,P.ProductId,P.cName,P.eCode) LIKE '%$searchString%' ";
}
/*
$proListSql = "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Estate,Q.ShipQty,P.Date,
				(SELECT COUNT(DISTINCT(OS.OrderPO))) AS Orders,DATE_FORMAT(MAX(OM.OrderDate),'%Y-%m') AS LastMonth,
				(SELECT SUM(OS.Qty)) AS AllQty
				FROM $DataIn.productdata P
				LEFT JOIN (
					SELECT ProductId, SUM(Qty) AS ShipQty 
				        FROM $DataIn.ch1_shipsheet
				        GROUP BY ProductId
				) Q ON Q.ProductId = P.ProductId
				LEFT JOIN $DataIn.yw1_ordersheet OS ON OS.ProductId = P.ProductId
				LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber= OS.OrderNumber 
				LEFT JOIN (
					SELECT TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId            
					FROM $DataIn.ch1_shipmain M 
					LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
					WHERE 1 GROUP BY S.ProductId ORDER BY M.Date DESC
				) E ON E.ProductId = P.ProductId
				WHERE P.CompanyId = '$companyId' AND P.Estate = 1 $searchInfo
				GROUP BY P.ProductId
				ORDER BY Estate DESC,Id DESC $pageInfo";
*/
$proListSql = "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Estate,Q.ShipQty,P.Date,
				(SELECT COUNT(DISTINCT(OS.OrderPO))) AS Orders,DATE_FORMAT(MAX(OM.OrderDate),'%Y-%m') AS LastMonth,
				(SELECT SUM(OS.Qty)) AS AllQty,Q.LastShipMonth 
				FROM $DataIn.productdata P
				LEFT JOIN (
					   SELECT S.ProductId, SUM(S.Qty) AS ShipQty,DATE_FORMAT(MAX(M.Date),'%Y-%m') AS   LastShipMonth 
				        FROM  $DataIn.ch1_shipsheet S 
				        LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid   
                        LEFT JOIN $DataIn.productdata P ON S.ProductId = P.ProductId 
                        WHERE P.CompanyId = '$companyId' AND P.Estate = 1 
				        GROUP BY ProductId
				) Q ON Q.ProductId = P.ProductId
				LEFT JOIN $DataIn.yw1_ordersheet OS ON OS.ProductId = P.ProductId
				LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber= OS.OrderNumber 
				WHERE P.CompanyId = '$companyId' AND P.Estate = 1 $searchInfo
				GROUP BY P.ProductId
				ORDER BY Estate DESC,Id DESC $pageInfo";
				
$proListResult = mysql_query($proListSql, $link_id);
if ($row = mysql_fetch_array($proListResult)) {
	do {
	    $ProductId = $row["ProductId"];
		$domain = $_SERVER["SERVER_NAME"];
		$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		$Price=" ";
	     if ($BranchId==3 || $LoginNumber==10001 || $LoginNumber==10691  || $LoginNumber==10007  || $LoginNumber==10009  || $LoginNumber==10130 || $LoginNumber==10005 || $LoginNumber==10868 || $LoginNumber == 11998 || $LoginNumber == 10341 ||  $LoginNumber == 11965){
	         $Price=$row["Price"];
	         include "product_profit.php";
		     $Price=number_format((float)$row["Price"], 2, '.', '');
	     }
		
		$productIcon = "/download/productIcon/" .$ProductId .".jpg";
	 $created_ct =(strlen($row["LastMonth"]) > 0) ?  GetDateTimeOutString($row["LastMonth"],"") :"";
	 
	 $profitRMB *= $row["ShipQty"]*1.0;
		$productList[] = array(
			"Id"			=> $row["Id"],
			"ProductId"		=> $ProductId ,
			"ProductName"	=> $row["cName"],
			"ProductIcon"	=> "$protocol://{$domain}{$productIcon}",
			"ProductImage"	=> "download/teststandard/T".$ProductId.".jpg",
			"Code" 			=> $row["eCode"],
			"Price" 		=> $Price,
			"Profit"        =>$profitRMB,
			"Orders" 		=> $row["Orders"],
			"Date" 			=> $row["Date"],
			"AllQty" 		=> strlen($row["AllQty"]) > 0 ? $row["AllQty"] : "",
			"ShipQty" 		=>strlen($row["ShipQty"]) > 0 ? $row["ShipQty"] : "",
			"LastMonth"		=> (strlen($row["LastMonth"]) > 0) ? $row["LastMonth"] : "",
			"LastMonthSh"=>"$created_ct",
			"LastShipMonth"=> (strlen($row["LastShipMonth"]) > 0) ? $row["LastShipMonth"] : ""
		);
	}
	while ($row = mysql_fetch_array($proListResult));
}

//產品總數
$productListData = array(
	"data" => $productList,
);
if (versionToNumber($AppVersion) <= 300) {	
	$totalSql = "SELECT COUNT( P.Id ) AS Total
					FROM $DataIn.productdata P
					WHERE P.CompanyId = '$companyId' AND P.Estate = 1";
	$totalRow = mysql_fetch_array(mysql_query($totalSql, $link_id));
	
	$productListData["total"] = $totalRow["Total"];
}

$jsonArray = array(
	"CompanyList" => $companyList,
	"ProductList" => $productListData,
);


?>