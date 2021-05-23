<?php 
//订单拆分记录
$Result=mysql_query("SELECT M.OrderDate AS Date,S.POrderId,S.Qty,S.Estate  
			FROM $DataIn.yw1_ordersheet S
			LEFT JOIN $DataIn.yw1_ordermain M ON  M.OrderNumber=S.OrderNumber 
			WHERE  S.POrderId='$SPOrderId' 
		UNION ALL
           SELECT M.Date,S.POrderId,S.Qty,S.Estate  
			FROM $DataIn.yw1_ordersplit M
			LEFT JOIN $DataIn.yw1_ordersheet S ON M.OPOrderId=S.POrderId 
			WHERE 1 and M.SPOrderId='$SPOrderId'  ORDER BY Date",$link_id);
			
 if($myRow = mysql_fetch_array($Result)) {
       $subArray=array();$sumQty=0;
     do {
              $Date=$myRow["Date"];
		      $POrderId=$myRow["POrderId"];
		      $Qty=$myRow["Qty"];
		      $sumQty+=$Qty;
		    
		       $Estate=$myRow["Estate"];
		       $chDate="";
		       $InvoiceNO=""; $InvoiceFile=0;$ImagePath="";$InvoiceColor="";
		       if ($Estate==0) {
		           $chResult=mysql_fetch_array(mysql_query("SELECT M.InvoiceNO,M.Date,M.InvoiceFile FROM  $DataIn.ch1_shipsheet S 
		               LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		               WHERE S.POrderId=$POrderId",$link_id));
				       $InvoiceNO=$chResult["InvoiceNO"];
				       $Date=$chResult["Date"];
				       $InvoiceFile=$chResult["InvoiceFile"];
					    if ($InvoiceFile>0){
					        $ImagePath="download/invoice/$InvoiceNO.pdf";
					        $InvoiceColor="#FF7E1C";
					    }
             }
            
            $subArray[]= array( 
               array("$Date",""),
               array("$InvoiceNO","$InvoiceColor"),
               array("$Qty",""),"$InvoiceFile","Image","$ImagePath"); 
        } while($myRow = mysql_fetch_array($Result));
         $subArray[]= array( 
               array("",""),
               array("",""),
               array("$sumQty","#000000"),"合计"); 
               
           $titleArray= array( 
               array("出货日期","100","L"),
               array("Invoice","80","L"),
               array("数   量","80","R")
              ); 
               
          $jsonArray[]=array($titleArray,$subArray);
 }
?>