<?php 
//中港费用审核
$mySql="SELECT A.* FROM (
 SELECT M.Date,M.InvoiceNO,M.InvoiceFile,
	F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,L.Date AS LogDate,F.declarationCharge,F.checkCharge,F.PayType,F.CompanyId,F.TypeId,
	F.depotCharge,F.Remark,F.Estate,F.Locks,F.Date AS fDate,F.Operator,D.Forshort,I.Mid,W.forwardWG
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=I.chId	
    LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	LEFT JOIN $DataIn.ch4_logistics_date L ON L.Mid=F.Id
	WHERE 1 AND F.Estate='1'  AND F.TypeId='1'
UNION ALL 
  SELECT M.DeliveryDate AS Date,M.DeliveryNumber AS InvoiceNO,'0' AS InvoiceFile,
	F.Id,F.Termini,F.ExpressNO,F.BoxQty,F.mcWG,F.Price,L.Date AS LogDate,F.declarationCharge,F.checkCharge,F.PayType,F.CompanyId,F.TypeId,
	F.depotCharge,F.Remark,F.Estate,F.Locks,F.Date AS fDate,F.Operator,D.Forshort,I.Mid,W.forwardWG
	FROM $DataIn.ch4_freight_declaration F
	LEFT JOIN $DataIn.ch4_freight_invoice I ON I.Mid=F.Id
    LEFT JOIN $DataIn.ch1_deliverymain M ON M.Id=I.chId	
    LEFT JOIN $DataIn.ch3_forward W ON W.chId=I.chId
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=F.CompanyId 
	LEFT JOIN $DataIn.ch4_logistics_date L ON L.Mid=F.Id
	WHERE 1 AND F.Estate='1' AND F.TypeId='2'
)A ORDER BY A.CompanyId,A.Id ";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/samplemail/";
 $Dir2="http://".$_SERVER ['HTTP_HOST']. "/download/expressbill/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
    $Forshort=$myRow["Forshort"];
    $Termini=$myRow["Termini"];//目的地
    $Qty=$myRow["BoxQty"];    //数量
    $Weight=$myRow["forwardWG"];
    $Amount=$myRow["Amount"];
    $Remark=$myRow["Remark"];
    
    $StockId=$myRow["StockId"];
     
    $Amount=number_format($Amount,2);
    
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";

    $OPdatetime=$myRow["OPdatetime"];
    $Date=GetDateTimeOutString($OPdatetime,'');
    
     $ImageList=array();  
    $ExpressNO=$myRow["ExpressNO"];
	//提单
    $Lading="../../download/expressbill/".$ExpressNO.".jpg";
	if(file_exists($Lading)){
	    $ImageList[]=array("Title"=>"提单","ImageFile"=>$Dir2 .$ExpressNO.".jpg" );
	}
	$ImageList[]=array("Title"=>"发票","ImageFile"=>"http://".$_SERVER ['HTTP_HOST']. "/public/ch_invoicemodel.php?I=$Id");
	
	$checkImgSql=mysql_query("SELECT Id,Picture FROM $DataIn.ch10_samplepicture WHERE Mid='$Id' ORDER BY Id",$link_id);
	while($checkImgRow=mysql_fetch_array($checkImgSql)){
	     $Picture=$checkImgRow["Picture"];
	     $ImageList[]=array("Title"=>"样品","ImageFile"=>"$Dir$Picture");
	}
    
   
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Termini"),
	                     "Col1"=>array("Text"=>"$Freight"),
	                     "Col2"=>array("Text"=>$Qty."件" ),
	                     "Col3"=>array("Text"=>"$Weight". "kg"),
	                     "Col4"=>array("Text"=>"¥$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );
 }

?>