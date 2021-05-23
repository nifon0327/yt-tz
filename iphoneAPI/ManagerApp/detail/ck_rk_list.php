<?php 
//入库记录

$Result=mysql_query("SELECT M.Date,M.BillNumber,concat('1') AS Sign,R.Qty 
FROM $DataIn.ck1_rksheet R 
LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE R.StockId='$StockId'",$link_id);
 if($myRow = mysql_fetch_array($Result)) {
       $subArray=array();$sumQty=0;
     do {
            $Id=$myRow["Id"];
            $Date=$myRow["Date"];
            $BillNumber=$myRow["BillNumber"];
            $Qty=$myRow["Qty"];
            $sumQty+=$Qty;
            $subArray[]= array( 
               array("$Date","#000000"),
               array("$BillNumber",""),
               array("$Qty",""),"0"); 
        } while($myRow = mysql_fetch_array($Result));
          $subArray[]= array( 
               array("",""),
               array("",""),
               array("$sumQty","#000000"),"合计"); 
               
          $titleArray= array( 
               array("收货日期","100","L"),
               array("收货单号","80","L"),
               array("收货数量","80","R")
              ); 
               
          $jsonArray[]=array($titleArray,$subArray);
 }
?>