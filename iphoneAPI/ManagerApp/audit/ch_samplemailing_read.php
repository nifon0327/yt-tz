<?php 
//样品邮费审核
$mySql="SELECT 
S.Id,S.Mid,S.DataType,S.CompanyId,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator
,P.Name AS HandledBy,C.Forshort,D.Forshort AS Freight,M.Termini,S.OPdatetime 
FROM $DataIn.ch10_samplemail S
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.ch10_mailaddress M ON M.Id=S.LinkMan
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.HandledBy
WHERE   S.Estate='2'  ORDER BY S.OPdatetime";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/samplemail/";
 $Dir2="http://".$_SERVER ['HTTP_HOST']. "/download/expressbill/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
     $Freight=$myRow["Freight"];
    $Forshort=$myRow["Forshort"];
    $Termini=$myRow["Termini"];//目的地
    $Qty=$myRow["Qty"];    //数量
    $Weight=$myRow["Weight"];
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
	    $ImageList[]=array("Title"=>"提单","Type"=>"JPG","ImageFile"=>$Dir2 .$ExpressNO.".jpg" );
	}
	$ImageList[]=array("Title"=>"发票","Type"=>"URL","ImageFile"=>"http://".$_SERVER ['HTTP_HOST']. "/public/ch_invoicemodel.php?I=$Id");
	
	$checkImgSql=mysql_query("SELECT Id,Picture FROM $DataIn.ch10_samplepicture WHERE Mid='$Id' ORDER BY Id",$link_id);
	while($checkImgRow=mysql_fetch_array($checkImgSql)){
	     $Picture=$checkImgRow["Picture"];
	     $ImageList[]=array("Title"=>"样品","Type"=>"JPG","ImageFile"=>"$Dir$Picture");
	}
    $Pieces=$myRow["Pieces"];
   
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Forshort","Text2"=>"$Termini"),
	                     "Col1"=>array("Text"=>"$Freight"),
	                     "Col2"=>array("Text"=>$Pieces."件"),
	                     "Col3"=>array("Text"=>"$Weight". "kg"),
	                     "Col4"=>array("Text"=>"¥$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );
 }

?>