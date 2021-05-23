<?php 
//其他收入审核
$mySql="SELECT I.Id,I.Amount,I.Remark,I.Bill,I.Date,I.Operator,I.OPdatetime,C.Symbol,C.PreChar,T.Name AS TypeName  
FROM $DataIn.cw4_otherin  I
LEFT JOIN $DataPublic.currencydata C ON C.Id=I.Currency 
LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=I.TypeId 
WHERE  I.Estate=1 ORDER BY I.Date";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/otherin/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
     $Remark=$myRow["Remark"];
     $Date=$myRow["Date"];
     $TypeName=$myRow["TypeName"];
     $PreChar=$myRow["PreChar"];
     $Symbol=$myRow["Symbol"];
     $Amount=$myRow["Amount"];
     $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
    $OPdatetime=$myRow["OPdatetime"];
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
     //$Date=date("m-d  H:i",strtotime($OPdatetime));
     $Date=GetDateTimeOutString($OPdatetime,'');
     $Bill=$myRow["Bill"];
     $ImageFile=$Bill==1?$Dir . "O".$Id.".jpg":"";

     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$TypeName"),
	                     "Col1"=>array("Text"=>"$Symbol"),
	                     "Col3"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"$Bill","Type"=>"JPG","ImageFile"=>"$ImageFile")
                     );
 }

?>