<?php 
//读取出货InvoiceNO信息
$checkResult= mysql_query("SELECT M.Id,C.Forshort,D.Rate,D.PreChar,M.InvoiceFile,M.Date   
                    FROM $DataIn.ch1_shipmain M
                    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
                    LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                    WHERE   M.InvoiceNO='$InvoiceNO'  LIMIT 1",$link_id);
  if($checkRow = mysql_fetch_array($checkResult)){
	  $Mid=$checkRow["Id"];
	  $Date=date("m/d",strtotime($checkRow["Date"]));
	  $Forshort=$checkRow["Forshort"];
	  $Rate=$checkRow["Rate"];
	  $PreChar=$checkRow["PreChar"];
	  $InvoiceFile=$checkRow["InvoiceFile"];
	    if ($InvoiceFile>0){
	        $ImagePath="download/invoice/$InvoiceNO.pdf";
	    }
	    else{
	    $ImagePath=""; 
	    }
  }
          
    $sListSql = "SELECT S.POrderId,O.OrderPO,S.Qty,S.Price,S.Type,P.cName,P.eCode,P.TestStandard,M.Sign,N.OrderDate,M.Date AS chDate,PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1) AS Weeks,YEARWEEK(M.Date,1) AS chWeeks     
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=O.Id   
	WHERE  S.Mid='$Mid'   AND S.Type='1'
UNION ALL
	SELECT S.POrderId,O.SampPO AS OrderPO,S.Qty,S.Price,S.Type,O.SampName AS cName,O.Description AS eCode,'0' AS TestStandard,M.Sign,'0000-00-00' AS  OrderDate,M.Date AS chDate,'' AS Leadtime,'' AS Weeks,YEARWEEK(M.Date,1) AS chWeeks  
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$Mid' AND S.Type='2'
UNION ALL
	SELECT S.POrderId,'' AS OrderPO,S.Qty,S.Price,S.Type,O.Description AS cName,O.Description AS eCode,'0' AS TestStandard,M.Sign,'0000-00-00' AS  OrderDate,M.Date AS chDate,'' AS Leadtime,'' AS Weeks,YEARWEEK(M.Date,1) AS chWeeks     
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$Mid' AND S.Type='3'";
    $sListResult = mysql_query( $sListSql,$link_id);
     if ($StockRows = mysql_fetch_array($sListResult)) {
               
			do{
					$OrderPO=$StockRows["OrderPO"];
					$POrderId=$StockRows["POrderId"];
					$cName=$StockRows["cName"];
					$eCode=$StockRows["eCode"];
					$Qty=$StockRows["Qty"];
					$Price=$StockRows["Price"];
					$Sign=$StockRows["Sign"];
					$Type=$StockRows["Type"];
					$Amount=sprintf("%.2f",$Qty*$Price*$Sign);	
					$sumQty+=$Qty;
					$sumAmount+=$Amount;
					$Price=sprintf("%.2f",$Price);
					$TestStandard=$StockRows["TestStandard"];
				   	include "order_TestStandard.php";
					
					$scDays="";$dateSign=0;
					$OrderDate=$StockRows["OrderDate"];
					 $chDate=$StockRows["chDate"];
					 $dateColor="";
					 switch($Type){
						 case 1:
						    $scDays=$StockRows["Weeks"]>0?substr($StockRows["Weeks"], 4,2):"00";
						     $dateColor=$StockRows["chWeeks"]>$StockRows["Weeks"] && $StockRows["Weeks"]>0?"#FF0000":""; //显示红色
						    /*
						    if ($OrderDate!="0000-00-00"){
						           
								   $scDays=ceil((strtotime($chDate)-strtotime($OrderDate))/3600/24);
								  $Leadtime=$StockRows["Leadtime"];
		                           $dateColor=$chDate>$Leadtime?"#FF0000":""; //显示红色
                           }
                           */
                           break;
					      case 2:$scDays="样";break;
					      case 3:$scDays="扣";break;
					 }
					 
					 $tempArray=array(
	                       "Index"=>array("Text"=>"$scDays","Border"=>"1","bgColor"=>"$dateColor"),
	                       "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
	                       "Col1"=> array("Text"=>"$OrderPO"),
	                       "Col2"=>array("Text"=>"$Qty"),
	                       "Col3"=>array("Text"=>"$PreChar$Price"),
	                       "Col5"=>array("Text"=>"$PreChar$Amount"),
	                       "rTopTitle"=>array("Text"=>""),
	                      "rIcon"=>"ship$ShipType"
	                   );
                    $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
			 }while ($StockRows = mysql_fetch_array($sListResult));
			      $TotalQty=number_format($sumQty);
                  $TotalAmount=number_format($sumAmount,2);
                  $tempArray=array(
                      "Title"=>array("Text"=>"合计"),
                      "Col1"=>array("Text"=>"$TotalQty"),
                      "Col3"=>array("Text"=>"$PreChar$TotalAmount")
                   );
			       $dataArray[]=array("Tag"=>"Total","data"=>$tempArray);
			       
			        $headArray= array(
                      "onTap"=>array("Target"=>"Web","Args"=>"$ImagePath"),
                      "Title"=>array("Text"=>"$InvoiceNO","Color"=>"#FFA500"),
                      "Col3"=>array("Text"=>"$Date"),
                   );         
                   
			       $jsondata[]=array("head"=>$headArray,"data"=>$dataArray);
			       
			     // $dataArray[]=array( "合计","$TotalQty","$PreChar$TotalAmount"); 
			      //$jsonArray[]=array( "$InvoiceNO|$Date","$InvoiceFile","$ImagePath",$dataArray); 
			  $jsonArray=array("navTitle"=>"$Forshort","data"=>$jsondata);
}                       
?>