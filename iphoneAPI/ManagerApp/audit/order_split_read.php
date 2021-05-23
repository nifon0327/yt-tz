<?php 
//拆分订单审核
 $mySql="SELECT O.Id,O.POrderId,O.Qty,O.Qty1,O.Qty2,O.Remark,O.Date,O.Operator,O.OPdatetime,
 P.ProductId,P.cName,P.TestStandard,C.Forshort,S.OrderPO,S.Price,D.PreChar    
		FROM $DataIn.yw10_ordersplit O 
		LEFT JOIN  $DataIn.yw1_ordersheet S ON O.POrderId=S.POrderId 
		LEFT JOIN  $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency   
		WHERE  O.Estate=0 AND S.Estate>0 AND S.Estate<4 Order BY O.Id" ;

 $Result=mysql_query($mySql,$link_id);
 $Dir=  "http://".$_SERVER ['HTTP_HOST']. "/download/teststandard/";
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $POrderId=$myRow["POrderId"];
    $ProductId=$myRow["ProductId"];
    $Forshort=$myRow["Forshort"];
    $cName=$myRow["cName"];//产品名称
    $OrderPO=$myRow["OrderPO"];
    $Price=number_format($myRow["Price"],3);
    
	$TestStandard=$myRow["TestStandard"];	
	include "order/order_TestStandard.php";	
	$ImageFile=$Dir . "T$ProductId.jpg";
		  
    $Qty=number_format($myRow["Qty"]);
    $Qty1=number_format($myRow["Qty1"]);
    $Qty2=number_format($myRow["Qty2"]);
    $Date=$myRow["Date"];
    
    $OPdatetime=$myRow["OPdatetime"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
   $Date=GetDateTimeOutString($OPdatetime,'');
   
    $PreChar=$myRow["PreChar"];
    $Remark=$myRow["Remark"];
   /*
    $timeOut=getWorkLimitedTime(0,$dModuleId,$DataIn,$link_id);
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>$timeOut[0]) $OverNums++;
     */
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"订  单  ID:","Text"=>"$POrderId","onTap"=>"1","Tag"=>"Order","ServerId"=>"$ServerId","Args"=>"$POrderId");
     $listArray[]=array("Cols"=>"1","Name"=>"单       价:","Text"=>"$PreChar$Price");
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
	                     "Col1"=>array("Text"=>"$OrderPO"),
	                     "Col2"=>array("Text"=>"$Forshort"),
	                     "Col3"=>array("Text"=>"$Qty"),
	                     "Col4"=>array("Text"=>"$Qty1/$Qty2"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$TestStandard","Type"=>"JPG","ImageFile"=>"$ImageFile","data"=>$listArray)
                     );
 }

?>