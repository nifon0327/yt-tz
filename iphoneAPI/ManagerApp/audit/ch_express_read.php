<?php 
//行政费用审核
/*
SELECT 
E.Id,E.Mid,E.Date,E.ExpressNO,E.BoxQty,E.Weight,E.Amount,E.Type,E.Operator,E.Remark,
E.Estate,E.Locks,P.Name AS HandledBy,D.Forshort 
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=E.HandledBy
WHERE 1 $SearchRows AND E.Estate='2'
ORDER BY E.Date DESC,E.Id DESC
*/
$cztest = ' AND E.Estate=2 ';
$czLimt = '';

	$mySql="SELECT 
E.CompanyId,sum(E.Amount) as GAmount,D.Forshort ,C.PreChar
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON D.Currency=C.Id 
WHERE 1 $cztest  
Group by E.CompanyId
ORDER BY GAmount DESC $czLimt ";
	$Result=mysql_query($mySql,$link_id);

 $Dir= "http://".$_SERVER ['HTTP_HOST']."/download/expressbill/";
 $itor = 0;
while ($myRow = mysql_fetch_array($Result)) {
	$comId = $myRow["CompanyId"];
	$comName = $myRow["Forshort"];
	$GAmount = $myRow["GAmount"];
	$preChar = $myRow["preChar"];
	
	
	$submySql="SELECT 
E.Id,E.Mid,E.Date,E.ExpressNO,E.BoxQty,E.Amount,E.Type,E.Operator,E.Remark,
E.Estate,E.Locks,P.Name AS HandledBy 
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=E.HandledBy
WHERE 1 $cztest
ORDER BY E.Date DESC,E.Id DESC  $czLimt ";
	$subResult=mysql_query($submySql,$link_id);
	
	$subList = array();
	
	while ($submyRow = mysql_fetch_array($subResult)) {
		$rowID = $submyRow["Id"];
		$Date = $submyRow["Date"];
	  $Date=GetDateTimeOutString($Date,'');
	  
		$Name = $submyRow["HandledBy"];
		$Amount = $submyRow["Amount"];
		$BoxQty = $submyRow["BoxQty"];
		$Remark = $submyRow["Remark"];
		$Type=$submyRow["Type"]==1?"到付":"寄付";	
		
		
	
		$ExpressNO=$submyRow["ExpressNO"];

		

		  $ImageList=array();  
     if ($ExpressNO != ''){
	     $ImageList[]=array("Title"=>"","Type"=>"JPG","ImageFile"=>$Dir.$ExpressNO.".jpg" );
     }
     $subList[]=array("subIndex"=>5,
	                     "Id"=>"$rowID",
	                     "onTap"=>array("Value"=>"1","hidden"=>"1","Args"=>"$rowID","Audit"=>"$AuditSign","Style"=>"1"),
	                     
						 "Title"=>array("Text"=>"$Type   ","Text2"=>"    ".$BoxQty."件"),
	                     "Month"=>array("Text"=>"$PreChar$Amount","Margin"=>"-20,0,0,0" ),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Name"),
	                     "List"=>array("ImageList"=>$ImageList)
                     );
					 $itor ++;
		
	}
	
 	$dataArray[]=array(
						 "leaf"=>0,	
	                   "Id"=>"mult-$comId", 
					   
	                   "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"","Audit"=>"$AuditSign"),
						 "Title"=>array("Text"=>"$comName"),
						 
	                	 "Col2"=>array("Text"=>"$preChar$GAmount"),                   
	                   "List"=>$subList
                     );

}
 
?>