<?php 
//待出订单总额/数量明细
$SearchRows= " AND S.Estate>=2 AND S.scFrom=0 ";
$today=date("Y-m-d");
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   

$TotalAllResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount                                   
                           FROM $DataIn.yw1_ordersheet S 
                           LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
                           LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	                       LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                           WHERE 1 $SearchRows",$link_id));
$TotalAllAmount=$TotalAllResult["Amount"];


$shipResult = mysql_query("
                           SELECT M.CompanyId,C.Forshort,D.Rate,D.PreChar,SUM(S.Price*S.Qty) AS Amount,SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS RMBAmount  
                           FROM $DataIn.yw1_ordermain M
                           LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
                           LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	                       LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                           WHERE 1 $SearchRows
                           GROUP BY M.CompanyId ORDER BY RMBAmount DESC",$link_id);
    if($shipRow = mysql_fetch_array($shipResult)) {
           $sumAmount=0;$sumQty=0;$sumOverQty=0;
            do{
                $CompanyId=$shipRow["CompanyId"];
                $Forshort=$shipRow["Forshort"];
                $TotalQty=$shipRow["Qty"];
                $TotalAmount=$shipRow["Amount"];
                $Rate=$shipRow["Rate"];
                $PreChar=$shipRow["PreChar"];
                $RMBAmount=$shipRow["RMBAmount"];
                $sumQty+=$TotalQty;
                $sumAmount+=$TotalAmount*$Rate;
                
                 $TotalQty=number_format($TotalQty);
                 $TotalAmount=number_format($TotalAmount);
                 $TotalPre=sprintf("%.1f",$RMBAmount/$TotalAllAmount*100);      
                 $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$CompanyId"),
				                      "Title"=>array("Text"=>"$Forshort    $TotalPre%","FontSize"=>"14"),
				                      "Col2"=>array("Text"=>"$TotalQty","Frame"=>"144, 2, 60, 30"),
				                      "Col3"=>array("Text"=>"$PreChar$TotalAmount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
				                   ); 
				$dataArray=array();
				
                 $sListResult= mysql_query("SELECT   S.POrderId,S.OrderPO,S.Qty,S.Price,S.ShipType,S.Estate,P.cName,P.TestStandard,
                 P.eCode,C.Forshort,M.OrderDate,PI.Leadtime,YEARWEEK(substring(PI.Leadtime,1,10),1) AS Weeks    
				FROM $DataIn.yw1_ordermain M 
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
				 LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
				LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
				LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
				WHERE M.CompanyId='$CompanyId' AND S.Qty>0 $SearchRows ",$link_id);
    if ($StockRows = mysql_fetch_array($sListResult)) {
			do{
					$OrderPO=$StockRows["OrderPO"];
					$POrderId=$StockRows["POrderId"];
					$cName=$StockRows["cName"];
					$eCode=$StockRows["eCode"];
					$Qty=$StockRows["Qty"];
					$Price=$StockRows["Price"];
					$Amount=sprintf("%.2f",$Qty*$Price);	
					$Price=sprintf("%.2f",$Price);
					$TestStandard=$StockRows["TestStandard"];
					 include "order/order_TestStandard.php";
					 
					$ShipType=$StockRows["ShipType"];
					 $CheckShipSplitResult=mysql_query("SELECT ShipType FROM $DataIn.ch1_shipsplit WHERE POrderId='$POrderId' AND Qty='$Qty'",$link_id);
					 if($CheckShipSplitRow=mysql_fetch_array($CheckShipSplitResult)){
							     $ShipType=$CheckShipSplitRow["ShipType"];
					 }
					$Leadtime=str_replace("*", "", $myRow["Leadtime"]);
					
				   //待出日期
                    	$CheckscDate=mysql_fetch_array(mysql_query("SELECT Max(C.Date) AS scDate FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
                    	$scDate=$CheckscDate["scDate"];
                    	if ($scDate==""){
	                    	$CheckscDate=mysql_fetch_array(mysql_query("SELECT Max(CONCAT(M.Date,' ',M.Time)) AS scDate FROM $DataIn.ck5_llsheet  S 
	                    	LEFT JOIN  $DataIn.ck5_llmain  M ON M.Id=S.Mid 
	                    	WHERE S.POrderId='$POrderId'",$link_id));
                    	    $scDate=$CheckscDate["scDate"];
                    	}
                    	$DateSTR=substr($scDate, 5, 2) ."/". substr($scDate, 8, 2) . " " . substr($scDate, 11,5);
                    	$scDays=$DateSTR==""?0:round((strtotime($today)-strtotime($CheckscDate["scDate"]))/3600/24);
                    	$DateColor=$scDays>=5?"#FF0000":""; //4显示红色
                       $sumOverQty+=$scDays>=5?$Qty:0;
                       
                     //下单到现在时间
	                   $OrderDate=$StockRows["OrderDate"];
	                   $odDays=(strtotime($today)-strtotime($OrderDate))/3600/24;
	                   $Weeks=$StockRows["Weeks"];
	                   $bgColor= $Weeks<$curWeeks?"#FF0000":"";
	                   
                       $Weeks=substr($Weeks, 4,2);

	                    $Locks=0; $Remark="";
                       $checkExpress=mysql_query("SELECT Type,Remark FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=2 LIMIT 1",$link_id);
						if($checkExpressRow = mysql_fetch_array($checkExpress)){
						       $Remark=$checkExpressRow["Remark"];
	                           $Locks=1;
						}
						
				    $Estate=$StockRows["Estate"];
				    $Badge=$Estate>2?"iwaitch":"";
				    $Locks=$Estate>2?"":$Locks;
				    
				   $Qty=number_format($Qty);
				   $tempArray=array(
                    "Id"=>"$POrderId",
                   "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$Badge"),
                   "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                   "Col1"=> array("Text"=>"$OrderPO"),
                   "Col3"=>array("Text"=>"$Qty"),
                   "Col5"=>array("Text"=>"$DateSTR","Color"=>"$DateColor"),
                  "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                   "rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
                  "rIcon"=>"ship$ShipType"
               );
               $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
			 }while ($StockRows = mysql_fetch_array($sListResult));			      		                                              	                   
	    }   
        $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
      }while($shipRow = mysql_fetch_array($shipResult));

      $sumOverQty=number_format($sumOverQty);
      $sumQty=number_format($sumQty);
      $sumAmount=number_format($sumAmount);
       $tempArray=array(
				                      "Id"=>"Total",
				                      "Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),
				                      "Col1"=>array("Text"=>"$sumOverQty","Margin"=>"-30,0,0,0","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col2"=>array("Text"=>"$sumQty","Color"=>"#000000","Margin"=>"-24,0,0,0","FontSize"=>"14"),
				                      "Col3"=>array("Text"=>"¥$sumAmount","FontSize"=>"14")
				                   );
		$dataArray=array();		                   
		 $dataArray[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$dataArray); 
          array_splice($jsondata,0,0,$totalArray);
}
$jsonArray=array("rButton"=>array(),"data"=>$jsondata); 
?>