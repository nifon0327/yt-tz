<?php 
//供应商扣款审核
$mySql="SELECT M.Id,M.Date,M.BillNumber,M.TotalAmount,M.BillFile,M.Remark,M.OPdatetime,M.Picture,M.Operator,
S.Qty,S.Price,S.StuffName,P.Forshort,C.PreChar
        FROM $DataIn.cw15_gyskkmain M
        LEFT JOIN $DataIn.cw15_gyskksheet S ON S.Mid=M.Id  
        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId
        LEFT JOIN $DataPublic.currencydata  C ON C.Id=P.Currency
		WHERE  M.Estate=1 AND S.Mid>0";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/cgkkbill/";
 while($myRow = mysql_fetch_array($Result)) 
 {
      $Id=$myRow["Id"];
     $Date=$myRow["Date"];
     $StuffName=$myRow["StuffName"];
     $Forshort=$myRow["Forshort"];
     $Remark=$myRow["Remark"];
     $Qty=$myRow["Qty"];
     $Price=$myRow["Price"];
     $Amount=$myRow["TotalAmount"];
     $PreChar=$myRow["PreChar"];
     
     $BillNumber=$myRow["BillNumber"];
     $ListSign=$BillNumber==""?0:1;
     $ImageFile=$ListSign==1?$Dir . $BillNumber .".pdf":"";

     $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
    $OPdatetime=$myRow["OPdatetime"];
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     $Date=GetDateTimeOutString($OPdatetime,'');
      
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$ListSign","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffName"),
	                     "Col1"=>array("Text"=>"$Forshort"),
	                     "Col2"=>array("Text"=>"$Qty"),
	                     "Col3"=>array("Text"=>"$PreChar$Price"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$ListSign","Type"=>"PDF","ImageFile"=>"$ImageFile")
                     );
 }

?>