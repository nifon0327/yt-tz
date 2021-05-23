<?php
//研砼报关分析图

$dataArray=array();
$checkMonth=$checkMonth==""?date("Y-m"):$checkMonth;
  $shipSql="SELECT M.CompanyId,C.Forshort,SUM(S.Price*S.Qty*M.Sign*D.Rate) AS Amount,SUM(S.Qty) AS Qty,A.ColorCode      
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        LEFT JOIN $DataIn.chart2_color A ON A.CompanyId=M.CompanyId 
        WHERE  M.Estate=0 AND (S.Type=1 OR S.Type=3) AND T.Type=1  and DATE_FORMAT(M.Date,'%Y-%m')='$checkMonth' 
        GROUP BY M.CompanyId ORDER BY Amount DESC";
    $shipResult = mysql_query($shipSql,$link_id);
    $i=0;$TotalAmount=0;$TotalQty=0;
     if($shipRow = mysql_fetch_array($shipResult)) {
            do{
                $CompanyId=$shipRow["CompanyId"];
                $Forshort=$shipRow["Forshort"];
                $Amount=$shipRow["Amount"];
			    $TotalAmount+=$Amount;

			    $Qty=$shipRow["Qty"];
				$TotalQty+=$Qty;

               $ColorCode =$shipRow["ColorCode"];

				$dataArray[]=array( "$CompanyId","$Forshort","$Amount","$ColorCode","$Qty");
			   $i++;
      }while ($shipRow = mysql_fetch_array($shipResult));

	   $TotalQty=number_format($TotalQty);
      $TotalAmount=number_format($TotalAmount);

	   $jsonArray=array("Title"=>"研砼报关分析图","Date"=>"$checkMonth","DateType"=>"2",
							    "data1"=>array("Title"=>"报关金额","Total"=>"¥$TotalAmount","TotalQty"=>"$TotalQty" ,"PreChar"=>"¥","data"=>$dataArray)
							    );
}
// echo json_encode($jsonArray);
?>