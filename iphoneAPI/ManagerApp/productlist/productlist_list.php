<?php 
$typeid;
$basePath = "../../download/productIcon/";
$basePathTest = "../../download/teststandard/";
$basePathStd = "http://www.middlecloud.com/download/teststandard/";
$CheckBranchId=mysql_fetch_array(mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id));
$BranchId=$CheckBranchId["BranchId"];

$precharSql = mysql_fetch_assoc(mysql_query("select C.PreChar
					FROM $DataPublic.currencydata C 
					left join $DataIn.trade_object M ON M.Currency = C.Id where M.CompanyId=$typeid "));
					
		$precharStr = $precharSql["PreChar"];
					

$nowDate = date("Y-m-d H:i:s");
$companyId = $typeid;
  $sql = mysql_query("SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Estate,Q.ShipQty,P.created as Date,
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
				ORDER BY Estate DESC,Id DESC  ");


$twoMonthSeconds = 60*60*24*60;
$halfYearSeconds = 3*$twoMonthSeconds;
 $jsonArray = array();
 while ($row = mysql_fetch_assoc($sql)) {
	 
	 /*P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Estate,Q.ShipQty,P.Date,
				(SELECT COUNT(DISTINCT(OS.OrderPO))) AS Orders,(MAX(OM.OrderDate)) AS LastMonth,
				(SELECT SUM(OS.Qty)) AS AllQty,Q.LastShipMonth 
				
				*/
	 
	 
	 
	 $canForbid = 0;
	 $StuffId = $row["ProductId"];
	 
	 
	 $singlePath = $basePath.$StuffId.".jpg";
	 $singlePathStd = $basePathStd."T".$StuffId.".jpg";
	 	 $Picture = "0";
	 if (file_exists($singlePath)) {
		  $Picture = "1";
	 } 
	 
	  $cName = $row["cName"];
	   $Estate = $row["Estate"];
	    $ShipQty = $row["ShipQty"];
		 $Date = $row["Date"];
		  $Orders = $row["Orders"];
		   $LastMonth = $row["LastMonth"];
		    $AllQty = $row["AllQty"];
	 $buyername = $row["buyername"];
	 $StuffCname = $row["StuffCname"];

	 $price = $row["price"];
	 $created = $row["created"];
	 
	 $Price=" ";
	 $ProductId = $StuffId;
	     if ($BranchId==3 || $LoginNumber==10001 || $LoginNumber==10691  || $LoginNumber==10007  || $LoginNumber==10009  || $LoginNumber==10130 || $LoginNumber==10005 || $LoginNumber==10868 || $LoginNumber == 11998 || $LoginNumber == 10341|| $LoginNumber == 11965){
	         $Price=$row["Price"];
	         include "product_profit.php";
		     $Price=number_format((float)$row["Price"], 2, '.', '');
	     }
	 
	 $modified = $row["modified"];
	 $Estate = $row["Estate"];
	 $tStockQty = $row["tStockQty"];
	  $oStockQty = $row["oStockQty"];
	 $TypeName = $row["TypeName"];
	 $eCode = $row["eCode"];
	 
	 $suppliername = $row["suppliername"];
	 
	 $created_ct =$LastMonth!="" ? GetDateTimeOutString($LastMonth,"") : "";
	
	//$profitRMB =$profitRMB * ($row["ShipQty"]);
	//$LastMonth = strtotime($LastMonth);
//$AllQty = number_format($AllQty);

$profitRMB *= $ShipQty;
$ShipQty = $ShipQty > 0 ? $ShipQty : "0";
$Date = $Picture < 1 ?  date("Y-m-d",strtotime($Date)) :"";
	 $jsonArray[]= array("stuffid"=>"$StuffId",
	 					   "stuffcname"=>"$eCode"."\n".$StuffId."-$cName",
						   "picture"=>"$Picture",
						   "price"=>"$precharStr"."$Price",
						   "created"=>"$created",
						   "modified"=>"$modified",
						   "tstockqty"=>"",
						   "typename"=>"$TypeName",
						   "unitname"=>"",
						   "suppliername"=>"",
						   "orderdate"=>"$LastMonth",
						   "orderqty"=>"$ShipQty",
						   "stock_count"=>"$Orders",
						   "pands_count"=>"$Orders",
						   "created_ct"=>"$created_ct",
						   "created_time"=>"$Date",
						   "created_time_color"=>"",
						   "canForbid"=>"0",
						   "Profit"        =>"$profitRMB",
						   "unitprice"=>"$price",
						   "shipqty"=>"$ShipQty",
						   "eCode"=>"$eCode",
						   "imgSTD"=>"$singlePathStd"
						   );
 }
 include "../../basic/downloadFileIP.php";

 $ImagePath="http://www.middlecloud.com/download/productIcon/";
 $jsonArray= array("rows"=>$jsonArray,
 
 					 "op"=>array("stuffimage_path"=>"$ImagePath"));
 
?>