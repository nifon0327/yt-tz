<?php 
//今日新单
$dataArray=array();
$today=date("Y-m-d");
$checkDay=$checkDay==""?$today:$checkDay;
//$tempDay=$checkDay;
//do{
		$orderSql="SELECT M.CompanyId,C.Forshort,SUM(S.Price*S.Qty*D.Rate) AS Amount,SUM(S.Qty) AS Qty,A.ColorCode 
		                       FROM $DataIn.yw1_ordermain M
		                       LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
		                       LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		                       LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
		                       LEFT JOIN $DataIn.chart2_color A ON A.CompanyId=M.CompanyId 
		                       WHERE  M.OrderDate='$checkDay' 
		                       GROUP BY M.CompanyId ORDER BY Amount DESC";
		                      
		$orderResult = mysql_query($orderSql,$link_id);
		$i=0;$TotalAmount=0;$TotalQty=0;
		 if($orderRow = mysql_fetch_array($orderResult)) {
		        do{
		            $CompanyId=$orderRow["CompanyId"];
					$Forshort=$orderRow["Forshort"];
					$Amount=(float)$orderRow["Amount"];
					$TotalAmount+=$Amount;
					
					$Qty=$orderRow["Qty"];
					$TotalQty+=$Qty;
			        $ColorCode =$orderRow["ColorCode"]==""?"#000000":$orderRow["ColorCode"];   
					$dataArray[]=array( "$CompanyId","$Forshort","$Amount","$ColorCode","$Qty");  
				   $i++;
		  }while ($orderRow = mysql_fetch_array($orderResult));
		 $counts=count($dataArray);
		  $SubdataArray=array();
		  $k=0;$subTotalAmount=0;$subTotalQty=0;
		  for ($n=0;$n<$counts;$n++){
		      $subAmount=$dataArray[$n][2];
		       $subQty=$dataArray[$n][4];
		      if ($subAmount/$TotalAmount*100>=5){
		           $SubdataArray[]=$dataArray[$n];
		      }
		      else{
		          $k++;
		          $subTotalAmount+=$subAmount;
		          $subTotalQty+=$subQty;
		      }
		  }
		 if ($k>0 && $subTotalAmount>0){
		   if ($k==1){
		        $n=$counts-1;
		        $SubdataArray[]=$dataArray[$n];
		   }
		    else{
			    $SubdataArray[]=array( "0","其它","$subTotalAmount","CCCCCC","$subTotalQty");  
		    } 
		  }
		  $TotalAmount=number_format($TotalAmount);
		  $TotalQty=number_format($TotalQty);
		  /*
		    $curDate=date("Y-m-d");
		    $sDate=date("Y-m-d",strtotime("$curDate  -1   month"));
		    $dateResult=mysql_query("SELECT DISTINCT M.OrderDate AS Date FROM $DataIn.yw1_ordermain M WHERE M.OrderDate>='$sDate' ORDER BY Date ",$link_id);
			while($dateRow = mysql_fetch_array($dateResult)){
			            $dateArray[]=$dateRow["Date"];
			 }
		   */  
		   $jsonArray=array("Title"=>"新单金额分析图","Date"=>"$checkDay","DateType"=>"1","data1"=>array("Title"=>"下单金额","Total"=>"¥$TotalAmount","TotalQty"=>"$TotalQty" ,"PreChar"=>"¥","data"=>$SubdataArray));
		}
		if (count($jsonArray)<=0){
			  $jsonArray=array("Title"=>"新单金额分析图","Date"=>"$checkDay","DateType"=>"1","data1"=>array("Title"=>"下单金额","Total"=>"¥0","TotalQty"=>"0" ,"PreChar"=>"¥","data"=>array()));
		}
	//	$checkDay=date("Y-m-d",strtotime("$checkDay  -1   day"));
//}while(count($jsonArray)<=0  && $tempDay==$today);

//echo json_encode($jsonArray);
?>