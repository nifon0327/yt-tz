<?php 
//配件锁定审核
$mySql="SELECT L.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.BuyerId,YEARWEEK(S.DeliveryDate,1)  AS Weeks,
P.Forshort,A.StuffCname,A.Picture,L.Remark,L.Operator,L.OPdatetime ,C.PreChar
FROM $DataIn.cg1_lockstock  L 
LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=L.StockId
LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId  
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId  
LEFT JOIN $DataPublic.currencydata C ON C.id=P.currency
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
WHERE  S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0)  AND L.Estate=1  AND L.Locks=0 ORDER BY L.OPdatetime ";
 $Result=mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $StockId=$myRow["StockId"];
    $Forshort=$myRow["Forshort"];
    $StuffId=$myRow["StuffId"];
    $StuffCname=$myRow["StuffCname"];//配件名称
    $OrderQty=number_format($myRow["OrderQty"]);
    $Remark=$myRow["Remark"];
    
    $Picture=$myRow["Picture"];
     include "submodel/stuffname_color.php";
         
    $Price=sprintf("%.2f",$myRow["Price"]);
    $PreChar=$myRow["PreChar"];

    $Operator=$myRow["BuyerId"];
     include '../../model/subprogram/staffname.php';
    $Buyer=$Operator;
     
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";

    $OPdatetime=$myRow["OPdatetime"];
    
    $Date=GetDateTimeOutString($OPdatetime,'');
   
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) $OverNums++;
   
   $POrderId=$myRow["POrderId"];
      
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"采  购  ID:","Text"=>"$StockId","onTap"=>"1","ServerId"=>"$ServerId","Tag"=>"StuffDetail","Args"=>"$StockId");
      if (strlen($POrderId)==12){
	       $checkOrder=mysql_fetch_array(mysql_query("SELECT S.OrderPO,S.Qty,P.cName,P.TestStandard,C.Forshort  
										        FROM  $DataIn.yw1_ordersheet S 
										        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
										         LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId   
										        WHERE S.POrderId='$POrderId' ",$link_id));
		  $cName=$checkOrder["cName"];
		  $OrderPO=$checkOrder["OrderPO"];
		  $Y_Qty=number_format($checkOrder["Qty"]);
		  $Y_Forshort=$checkOrder["Forshort"];
		  $TestStandard=$checkOrder["TestStandard"];	
		  include "order/order_TestStandard.php";	
		  $listArray[]=array("Cols"=>"1","Name"=>"客      户:","Text"=>"$Y_Forshort");		
		  $listArray[]=array("Cols"=>"1","Name"=>"产品名称:","Text"=>"$cName","Color"=>"$TestStandardColor");
		  $listArray[]=array("Cols"=>"1","Name"=>"         PO:","Text"=>"$OrderPO");
         $listArray[]=array("Cols"=>"1","Name"=>"数       量:","Text"=>"$Y_Qty");
          			        
     } 
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$Forshort"),
	                     "Col2"=>array("Text"=>"$Buyer"),
	                     "Col3"=>array("Text"=>"$OrderQty","Margin"=>"30,0,0,0","IconType"=>"17",'fit'=>'1'),
	                     "Col4"=>array("Text"=>"$PreChar$Price"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"0","data"=>$listArray)
                     );
 }

?>