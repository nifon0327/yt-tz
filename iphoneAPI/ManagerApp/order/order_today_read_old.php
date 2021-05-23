<?php 
//今日新单
 $jsondata=array();
$checkDate=$checkDate==""?date("Y-m-d"):$checkDate;
$SearchRows=$SearchText==""?"":" AND P.cName LIKE '%$SearchText%' ";

//权限
    $ReadPower=1;
  if ($LoginNumber!=""){
			    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
			    if($TRow = mysql_fetch_array($TResult)){
			       $ReadPower=1;
			    }
			    else{
			       $ReadPower=0;
			    }
}    

$InResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS Amount
                           FROM $DataIn.yw1_ordermain M
                           LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
                          LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
						  LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                           WHERE M.OrderDate='$checkDate'",$link_id));
                           $AllTotalAmount=$InResult["Amount"];
                           
$orderResult = mysql_query("
                           SELECT M.CompanyId,C.Forshort,D.Rate,D.PreChar,SUM(S.Price*S.Qty) AS Amount,SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS SortAmount 
                           FROM $DataIn.yw1_ordermain M
                           LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
                           LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
                           LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	                       LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                           WHERE  M.OrderDate='$checkDate'  and S.Id>0  $SearchRows 
                           GROUP BY M.CompanyId ORDER BY SortAmount DESC",$link_id);
    if($orderRow = mysql_fetch_array($orderResult)) {
           $sumAmount=0;$sumQty=0;$sumProfitRMB=0;
            do{
                $CompanyId=$orderRow["CompanyId"];
                $Forshort=$orderRow["Forshort"];
                $TotalQty=$orderRow["Qty"];
                $TotalAmount=$orderRow["Amount"];
                $Rate=$orderRow["Rate"];
                $PreChar=$orderRow["PreChar"];
                
                $sumQty+=$TotalQty;
                $sumAmount+=$TotalAmount*$Rate;
                
                 $dataArray=array();
		         $sumProfitRMB2=0;
                 $sListResult= mysql_query("SELECT S.POrderId,S.OrderPO,S.Qty,S.Price,S.ShipType,P.cName,P.TestStandard,P.eCode,C.Forshort,
                 YEARWEEK(substring(PI.Leadtime,1,10),1)  AS Weeks   
				FROM $DataIn.yw1_ordermain M 
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
				 LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
				LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
				LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id   
				WHERE  M.OrderDate='$checkDate' AND M.CompanyId='$CompanyId'  AND S.Qty>0 $SearchRows ",$link_id);
    if ($StockRows = mysql_fetch_array($sListResult)) {
                $n=1;
			do{
					$OrderPO=$StockRows["OrderPO"];
					$POrderId=$StockRows["POrderId"];
					$cName=$StockRows["cName"];
					$eCode=$StockRows["eCode"];
					$Qty=$StockRows["Qty"];
					$Price=$StockRows["Price"];
					$Amount=sprintf("%.0f",$Qty*$Price);	
					$Price=sprintf("%.2f",$Price);
					$TestStandard=$StockRows["TestStandard"];
					include "order_TestStandard.php";
					$ShipType=$StockRows["ShipType"];
					
					  if ($ReadPower==1){
		                     /*毛利计算*//////////// 
		                    $saleRmbAmount=sprintf("%.3f",$Amount*$Rate);//转成人民币的卖出金额
		                    include "order_Profit.php";
                   }
                   $Qty=number_format($Qty);
                   $Amount=number_format($Amount);

                   $Weeks=$StockRows["Weeks"];
                   $Weeks=$Weeks>0?substr($Weeks, 4,2):" ";
                   $tempArray=array(
                      "Id"=>"$POrderId",
                       "Index"=>array("Text"=>"$Weeks","Border"=>"1"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),//,"Ship"=>"$ShipType"
                      "Col1"=> array("Text"=>"$OrderPO"),
                      "Col2"=>array("Text"=>"$Qty"),
                      "Col3"=>array("Text"=>"$PreChar$Price"),
                      "Col4"=>array("Text"=>"$profitRMB2PC%","Color"=>"$profitColor"),
                      "Col5"=>array("Text"=>"$PreChar$Amount"),
                       "rTopTitle"=>array("Text"=>""),
                      "rIcon"=>"ship$ShipType"
                   );
                   $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
                   $n++;
			 }while ($StockRows = mysql_fetch_array($sListResult));
			       $AllPC=$AllTotalAmount==0?0:sprintf("%.0f",($TotalAmount*$Rate/$AllTotalAmount)*100);
	               $AllPC=$AllPC==0?"":"  $AllPC%";
	               
	              $POrderId="";
			      $profitRMB2PC=$TotalAmount==0?0:sprintf("%.0f",($sumProfitRMB2/($TotalAmount*$Rate))*100);
                   include "order_Profit.php";
			      $TotalQty=number_format($TotalQty);
                  $TotalAmount=number_format($TotalAmount);
                  $totalArray=array();
                  $tempArray=array(
                      "Id"=>"$CompanyId",
                      "Title"=>array("Text"=>"合计"),
                      "Col1"=>array("Text"=>"$TotalQty"),
                      "Col2"=>array("Text"=>"$profitRMB2PC%","Color"=>"$profitColor","Margin"=>"20,0,0,0"),
                      "Col3"=>array("Text"=>"$PreChar$TotalAmount")
                   );
			       $totalArray[]=array("Tag"=>"Total","data"=>$tempArray);
			       array_splice($dataArray,0,0,$totalArray);
        }
        $headArray= array(
                      "Id"=>"$CompanyId",
                      "Title"=>array("Text"=>"$Forshort" .$AllPC)
                   );                      
		$jsondata[]=array("head"=>$headArray,"data"=>$dataArray);         
      }while($orderRow = mysql_fetch_array($orderResult));
      $sumQty=number_format($sumQty);
			      
      $profitRMB2PC=$sumAmount==0?0:sprintf("%.0f",$sumProfitRMB*100/$sumAmount);
      $POrderId="";
      include "order_Profit.php";
      $sumAmount=number_format($sumAmount);
      $tempArray=array(
                      "Icon"=>array("Name"=>"statics"),//,"onTap"=>array("Target"=>"Chart","Args"=>"$checkDate")
                      "Title"=>array("Text"=>"总计"),
                      "Col1"=> array("Text"=>"$sumQty"),
                      "Col2"=>array("Text"=>"$profitRMB2PC%","Color"=>"$profitColor"),
                      "Col3"=>array("Text"=>"¥$sumAmount")
                   );
       $sumdataArray[]=array("Tag"=>"Total","onTap"=>array("Target"=>"Chart","Args"=>"$checkDate"),"data"=>$tempArray);
       $sumArray[]=array("head"=>array(),"data"=>$sumdataArray);
       array_splice($jsondata,0,0,$sumArray);
}
$jsonArray=array("picker"=>array("Style"=>"1","Text"=>"$checkDate","Width"=>"80","Planar"=>"0"),"search"=>array("Text"=>"产品名称"),"data"=>$jsondata);
?>