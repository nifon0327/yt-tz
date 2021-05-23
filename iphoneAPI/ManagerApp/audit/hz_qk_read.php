<?php 
//行政费用审核
$mySql="SELECT S.Id,S.Content,S.Amount,S.Bill,S.Date,S.Operator,S.OPdatetime,T.Name AS Type,C.PreChar,C.Symbol  
 	FROM $DataIn.hzqksheet S 
 	LEFT JOIN $DataPublic.staffmain M  ON M.Number=S.Operator 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE  S.Estate=2   $SearchRows ORDER BY S.OPdatetime,S.Id";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/cwadminicost/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
     $Content=$myRow["Content"];
     $Date=$myRow["Date"];
     $Type=$myRow["Type"];
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
     $ImageFile=$Bill==1?$Dir . "H".$Id.".jpg":"";
 $ImageList=array();  
     if ($Bill==1){
	     $ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>"$ImageFile" );
     }
		
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"$Bill","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$Type"),
	                     "Col1"=>array("Text"=>"$Symbol"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$Content"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     //"List"=>array("Value"=>"$Bill","Type"=>"JPG","ImageFile"=>"$ImageFile"),
						 "List"=>array("ImageList"=>$ImageList)
                     );
 }

?>