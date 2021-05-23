<?php 
//供应商税款审核
 $mySql="SELECT S.Id,S.Mid,S.Forshort,S.InvoiceFile,S.Amount,S.Fpamount,S.Rate,S.Remark,S.Date,S.Estate,S.OPdatetime,S.Locks,S.Operator,C.PreChar 
 	FROM $DataIn.cw2_gyssksheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE S.Estate=2  ORDER BY S.OPdatetime,S.Id";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/cwgyssk/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
    $Forshort=$myRow["Forshort"];
    $PreChar=$myRow["PreChar"];
    $Amount=$myRow["Amount"];
    $Fpamount=$myRow["Fpamount"];
    $cgAmount=$Fpamount-$Amount;
    
    $Rate=$myRow["Rate"]*100;
    $Remark=$myRow["Remark"];
    $Date=$myRow["Date"];
                
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
    $OPdatetime=$myRow["OPdatetime"];
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
   // $Date=date("m-d H:i",strtotime($OPdatetime));
   $Date=GetDateTimeOutString($OPdatetime,'');
     $InvoiceFile=$myRow["InvoiceFile"];
     $ImageFile=$InvoiceFile==1?$Dir . "S".$Id.".pdf":"";

     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$InvoiceFile","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Forshort"),
	                     "Col1"=>array("Text"=>"$PreChar$cgAmount","IconType"=>"21"),
	                     "Col2"=>array("Text"=>"$PreChar$Amount($Rate%)","Frame"=>"120, 25, 105, 15","IconType"=>"22"),
	                     "Col3"=>array("Text"=>"$PreChar$Fpamount","IconType"=>"23"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$InvoiceFile","Type"=>"PDF","ImageFile"=>"$ImageFile")
                     );
 }

?>