<?php 
//客户退款审核
$mySql="SELECT 	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.Month,S.BuyerId,S.OPdatetime,P.Forshort,M.Name,A.StuffId,A.StuffCname,H.Date as OutDate,S.Amount,H.InvoiceNO,H.InvoiceFile,E.PreChar,Count(*) AS ShipCount 
 	FROM $DataIn.cw1_tkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
    LEFT JOIN $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=C.Mid		
    LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	WHERE  S.Estate=2  GROUP BY S.StockId ORDER BY S.Month";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "$donwloadFileIP/download/invoice/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
    $StuffId=$myRow["StuffId"];
    $TypeId=$myRow["TypeId"];
    $Forshort=$myRow["Forshort"];
    $StuffCname=$myRow["StuffCname"];//配件名称
    $Qty=$myRow["OrderQty"];    //订单数量
    $Price=$myRow["Price"];
    $Month=$myRow["Month"];
    $PreChar=$myRow["PreChar"];
    $Rate=$myRow["Rate"];
    $Picture=$myRow["Picture"];
    $StockId=$myRow["StockId"];
    
     include "submodel/stuffname_color.php";
    $ImageFile=$Picture>0?"$Dir".$StuffId. "_s.jpg":"";
     
   $ShipCount=$myRow["ShipCount"];
   $InvoiceFile=0;$ImageFile="";
   
	if ($ShipCount>1){
			//分批出货
			$InvoiceNOSTR="";
			$chResult=mysql_query("SELECT H.InvoiceNO,H.InvoiceFile FROM $DataIn.ch1_shipsheet E 
		                               LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid  
		                               WHERE E.PorderId='$POrderId' order by H.Date",$link_id);
		  if($chRow = mysql_fetch_array($chResult)){
			    $InvoiceNO=$chRow["InvoiceNO"];
                $InvoiceFile=$chRow["InvoiceFile"];
		    } 
		}
		else{
            $InvoiceNO=$myRow["InvoiceNO"];
	        $InvoiceFile=$myRow["InvoiceFile"];
	    }
	$ImageFile=$InvoiceFile>0?"$Dir".$InvoiceNO. ".pdf":"";
		    
    $sumQty+=$Qty;
    $Amount=sprintf("%.2f",$Qty*$Price);
    $sumAmount+=$Amount*$Rate;
    $Amount=number_format($Amount,2);
    
    $Operator=$myRow["BuyerId"];
     include "../../model/subprogram/staffname.php";

    $cgDate=$myRow["Date"];
    $OPdatetime=$myRow["OPdatetime"];
    //$Date=date("m-d H:i",strtotime($OPdatetime));
    $Date=GetDateTimeOutString($OPdatetime,'');
    //$opHours= geDifferDateTimeNum($OPdatetime,"",1);
      $Qty=number_format($Qty);
      
     $listArray=array();
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffCname","Color"=>"$StuffColor"),
	                     "Month"=>array("Text"=>"$Month"),
	                     "Col1"=>array("Text"=>"$StockId","Margin"=>"0,0,25,0"),
	                     "Col2"=>array("Text"=>"$Forshort","Margin"=>"25,0,15,0"),
	                     "Col3"=>array("Text"=>"$Qty","Margin"=>"45,0,0,0"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$InvoiceFile","Type"=>"PDF","ImageFile"=>"$ImageFile","data"=>$listArray)
                     );
 }

?>