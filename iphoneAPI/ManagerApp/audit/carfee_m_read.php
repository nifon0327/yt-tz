<?php 
//行政费用审核
$cztest = ' AND S.Estate=2 ';
$czLimt = '';
/*
if ($test_cz) {
	$cztest = '';
	$czLimt = 'limit 0,3';
}
*/


$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.OPdatetime,S.Estate,S.Locks,S.Operator,T.Name AS Type,C.PreChar AS PreChar,D.CarNo
 	FROM $DataIn.carfee S 
	LEFT JOIN $DataPublic.carfee_type T ON S.TypeId=T.Id
    LEFT JOIN $DataPublic.cardata  D ON D.Id=S.CarId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1  $cztest  order by S.Date DESC $czLimt";

 $Result=mysql_query($mySql,$link_id);
 $Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/carfee/";
 while($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
     $Content=$myRow["Content"];
     
     $Type=$myRow["Type"];
     $PreChar=$myRow["PreChar"];
     $Symbol=$myRow["CarNo"];
     $Amount=$myRow["Amount"];
     $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
     
    $OPdatetime=$myRow["OPdatetime"];
 
  
     //$Date=date("m-d  H:i",strtotime($OPdatetime));
     $Date=GetDateTimeOutString($OPdatetime,'');
	
		
     $Bill=$myRow["Bill"];
     $ImageFile=$Bill==1?$Dir . "C".$Id.".jpg":"";
  $ImageList=array();  
     if ($Bill==1){
	     $ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>"$ImageFile" );
     }
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     
						 "Title"=>array("Text"=>"$Type","Text2"=>" $Symbol"),
	                     "Month"=>array("Text"=>"$PreChar$Amount" ),
	                     "Remark"=>array("Text"=>"$Content"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );
 }

?>