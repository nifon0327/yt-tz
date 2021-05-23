<?php 
$typeid;
$nowDate = date("Y-m-d H:i:s");
  $sql = mysql_query("SELECT  Stuff.Id, Stuff.Id AS recid, Stuff.StuffId, Stuff.StuffCname, Stuff.StuffEname, Stuff.TypeId, 
                              Stuff.Gfile, Stuff.Gstate, Stuff.Picture, Stuff.Pjobid, Stuff.Jobid, Stuff.Gremark, CONCAT(C.PreChar,ROUND(Stuff.Price, 2)) AS price, 
                              Stuff.SendFloor, Stuff.jhDays, Stuff.Spec, Stuff.Remark, Stuff.Weight, Stuff.GcheckNumber, 
                              Stuff.created, Stuff.modified, 
                              Stuff.DevelopState, Stuff.BoxPcs, Stuff.GfileDate, Stuff.ForcePicSpe, Stuff.CheckSign, Stuff.Unit,
                              Stuff.Estate, Stuff.modified,Stuff.Locks,Stuff.PLocks, Stock.mStockQty, Stock.tStockQty, 
                              Stock.oStockQty, 
                              Type.TypeName, Type.ForcePicSign, Type.jhDays AS typejhdays, 
                              Unit.Name AS unitname, 
                              Bps.CompanyId, Bps.BuyerId, 
                              Buyer.name AS buyername,
                              Trade.Forshort AS suppliername,
                              Base.Remark AS warehouse,
                              Staff.name AS modifiername,
                              MAX(StkMain.Date) AS orderdate
                        FROM $DataIn.stuffdata           AS Stuff
                        LEFT JOIN $DataIn.cg1_stocksheet AS StkSheet  ON StkSheet.StuffId=Stuff.StuffId
                        LEFT JOIN $DataIn.cg1_stockmain  AS StkMain   ON StkSheet.Mid=StkMain.Id
                        LEFT JOIN $DataIn.bps            AS Bps       ON Bps.StuffId = Stuff.StuffId
                        LEFT JOIN $DataIn.providerdata   AS P         ON P.companyid=Bps.companyid
                        LEFT JOIN $DataPublic.currencydata   AS C         ON C.id =P.currency
                        LEFT JOIN $DataPublic.staffmain      AS Buyer     ON Bps.BuyerId=Buyer.Number
                        LEFT JOIN $DataIn.stuffunit      AS Unit      ON Unit.Id = Stuff.Unit
                        LEFT JOIN $DataIn.trade_object   AS Trade     ON Trade.CompanyId = Bps.CompanyId
                        LEFT JOIN $DataIn.ck9_stocksheet AS Stock     ON Stock.StuffId = Stuff.StuffId
                        LEFT JOIN $DataIn.stufftype      AS Type      ON Type.TypeId = Stuff.TypeId
                        LEFT JOIN $DataIn.base_mposition AS Base      ON Base.Id = Stuff.SendFloor
                        LEFT JOIN $DataIn.stuffdevelop   AS Developer ON Developer.StuffId=Stuff.StuffId
                        LEFT JOIN $DataPublic.staffmain      AS Staff     ON Staff.Number = Stuff.modifier 
where Stuff.TypeId=$typeid and Stuff.Estate > 0 
GROUP BY Stuff.Id order by orderdate DESC ");


$twoMonthSeconds = 60*60*24*60;
$halfYearSeconds = 3*$twoMonthSeconds;
 $jsonArray = array();
 while ($row = mysql_fetch_assoc($sql)) {
	 
	 $canForbid = 0;
	 $StuffId = $row["StuffId"];
	 $buyername = $row["buyername"];
	 $StuffCname = $row["StuffCname"];
	 $Picture = $row["Picture"];
	 $price = $row["price"];
	 $created = $row["created"];
	 
	 $modified = $row["modified"];
	 $Estate = $row["Estate"];
	 $tStockQty = $row["tStockQty"];
	  $oStockQty = $row["oStockQty"];
	 $TypeName = $row["TypeName"];
	 $unitname = $row["unitname"];
	 
	 $suppliername = $row["suppliername"];
	 $orderdate = $row["orderdate"]?$row["orderdate"]:"";
	 $created_ct = "";
	 if  ($orderdate!="") {
		 $created_ct = GetDateTimeOutString($orderdate,"");
	 }
	 $created_time = "";$created_time_color = "";
	 $intervalOfOrder = 0;
	 if ((int)$Picture <= 0) {
		 $created_time = date("Y-m-d",strtotime($created));
		 $intervalOfOrder = (strtotime($nowDate)-strtotime($created));
		 $created_time_color = $intervalOfOrder>$twoMonthSeconds ? "#FF0000" : "";
	 }
	/*
	 
	 */
	 //$orderdate =date("Y-m-d H:i:s",strtotime($orderdate));
	       //get order num, from `cg1_stocksheet`
		   
	$stuffpropertys = array();
    
   	$isSample = 0;
    $propertysql = mysql_query("SELECT property,stuffid FROM stuffproperty WHERE StuffId=$StuffId");
			while ($propertysqlRow = mysql_fetch_assoc($propertysql)) {
				$stuffpropertys[]=$propertysqlRow["property"];
				if ($propertysqlRow["property"]==11) {
					$isSample = 1;
				}
			}
			
			if ($isSample == 1) {
			continue;	
			}
			
			$isClientSupplied = "AND Mid>0";
			
			if (count($stuffpropertys)==1 && in_array("2",$stuffpropertys)) {
				$isClientSupplied = "";
			}
			
          

		   $orderqty = "0";$stock_count = "0";
            $sqlLim = mysql_query("SELECT COUNT(Id) AS StockCount,MAX(ywOrderDTime) AS OrderDate, 
                           SUM(FactualQty + AddQty) AS OrderQty
                        FROM $DataIn.cg1_stocksheet 
                        WHERE StuffId=$StuffId $isClientSupplied");
						
						if ($rowLim = mysql_fetch_assoc($sqlLim)) {
							$orderqty = $rowLim["OrderQty"];
							$stock_count = $rowLim["StockCount"];
							if ($isClientSupplied=="" && $orderqty>0 ){
								$orderdate=substr($rowLim["OrderDate"], 0,19);
								$created_ct = GetDateTimeOutString($orderdate,"");
							}
						}
		  
         
            //get PandS related
			 $pandscount = "0";
            $sqlLim2 = mysql_query("SELECT COUNT(*) AS PandSCount FROM $DataIn.pands  S
			 left join $DataIn.productdata P on P.ProductId=S.ProductId
			 WHERE StuffId='$StuffId' and P.Estate>0");
            if ($rowLim2 = mysql_fetch_assoc($sqlLim2)) {
					$pandscount = $rowLim2["PandSCount"];
			}

//可禁用条件 ： 1.无产品关联；2.无订单库存和实物库存；3.半年没有下单
	if ((int)$pandscount<=0 && $oStockQty<=0 && $tStockQty<=0 &&
	    ($orderdate=="" || $intervalOfOrder > $halfYearSeconds)
	   )
	   {
		   $canForbid = 1;
	   }
$tStockQty = number_format($tStockQty);
	 $jsonArray[]= array("stuffid"=>"$StuffId",
	 					   "stuffcname"=>"$StuffCname",
						   "picture"=>"$Picture",
						   "price"=>"$price",
						   "created"=>"$created",
						   "modified"=>"$modified",
						   "tstockqty"=>"$tStockQty",
						   "ostockqty"=>"$oStockQty",
						   "typename"=>"$TypeName",
						   "unitname"=>"$unitname",
						   "suppliername"=>"$suppliername",
						   "orderdate"=>"$orderdate",
						   "orderqty"=>"$orderqty",
						   "stock_count"=>"$stock_count",
						   "pands_count"=>"$pandscount",
						   "created_ct"=>"$created_ct",
						   "created_time"=>"$created_time",
						   "created_time_color"=>"$created_time_color",
						   "canForbid"=>"$canForbid",
						   "unitprice"=>"$price",
						   "buyername"=>"$buyername"
						   );
 }
 include "../../basic/downloadFileIP.php";
 $ImagePath="$donwloadFileIP/download/stufffile/";
 $jsonArray= array("rows"=>$jsonArray,
 
 					 "op"=>array("stuffimage_path"=>"$ImagePath"));
 
?>